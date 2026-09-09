<?php

namespace App\Http\Controllers;

use App\Jobs\SendCampaignEmailJob;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Contact;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\ImportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SmtpSetting;
use Illuminate\Validation\ValidationException;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::with(['category', 'importLog'])
            ->withCount([
                'emailLogs as failed_count' => function ($query) {
                    $query->whereIn('status', [EmailLog::STATUS_FAILED, EmailLog::STATUS_BOUNCED]);
                }
            ])
            ->latest()
            ->paginate(20);

        // Single query instead of 5 separate COUNT queries
        $statsByStatus = Campaign::selectRaw('statut, COUNT(*) as cnt')
            ->groupBy('statut')
            ->pluck('cnt', 'statut');

        $stats = [
            'total'      => $statsByStatus->sum(),
            'brouillon'  => $statsByStatus->get('brouillon', 0),
            'programmee' => $statsByStatus->get('programmee', 0),
            'en_cours'   => $statsByStatus->get('en_cours', 0),
            'envoyee'    => $statsByStatus->get('envoyee', 0),
            'annulee'    => $statsByStatus->get('annulee', 0),
        ];

        return view('campaigns.index', compact('campaigns', 'stats'));
    }

    public function create(Request $request)
    {
        $categories = Category::withCount('contacts')->orderBy('name')->get();
        $importLogs = ImportLog::where('imported', '>', 0)->latest()->get();
        $totalContacts = Contact::count();
        $template = null;

        if ($request->filled('template_id')) {
            $template = EmailTemplate::where('is_active', true)->findOrFail($request->template_id);
        }

        return view('campaigns.create', compact('categories', 'importLogs', 'template', 'totalContacts'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatedCampaign($request);
        $validated['created_by'] = Auth::id();
        $validated['statut'] = 'brouillon';

        $campaign = Campaign::create($validated);

        return redirect()->route('campaigns.edit', $campaign)
            ->with('success', 'Campagne créée. Vous pouvez maintenant la prévisualiser.');
    }

    public function edit(Campaign $campaign)
    {
        $campaign->load(['attachments', 'importLog']);
        $categories = Category::withCount('contacts')->orderBy('name')->get();
        $importLogs = ImportLog::where('imported', '>', 0)->latest()->get();
        $totalContacts = Contact::count();

        if ($campaign->import_log_id) {
            $nbDestinataires = Contact::where('import_log_id', $campaign->import_log_id)->count();
        } else {
            $categoryIds = $campaign->categoryIds();
            $nbDestinataires = $categoryIds !== []
                ? Contact::query()->whereHas('categories', function ($query) use ($categoryIds) {
                    $query->whereIn('categories.id', $categoryIds);
                })->distinct()->count('contacts.id')
                : $totalContacts;
        }

        $failedLogs = EmailLog::where('campaign_id', $campaign->id)
            ->whereIn('status', [EmailLog::STATUS_FAILED, EmailLog::STATUS_BOUNCED])
            ->with('contact')
            ->get();
        $failedCount = $failedLogs->count();

        return view('campaigns.edit', compact(
            'campaign', 'categories', 'importLogs', 'nbDestinataires', 'totalContacts', 'failedLogs', 'failedCount'
        ));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $campaign->update($this->validatedCampaign($request));

        return redirect()->route('campaigns.edit', $campaign)->with('success', 'Campagne mise à jour.');
    }

    /**
     * Relancer les emails en échec pour une campagne donnée.
     */
    public function retryFailed(Request $request, Campaign $campaign)
    {
        $failedLogs = EmailLog::where('campaign_id', $campaign->id)
            ->whereIn('status', [EmailLog::STATUS_FAILED, EmailLog::STATUS_BOUNCED])
            ->with('contact:id,email')
            ->get();

        if ($failedLogs->isEmpty()) {
            return back()->with('error', 'Aucun email en échec à relancer pour cette campagne.');
        }

        $smtp = SmtpSetting::where('is_active', true)->first();
        $rateLimit = max(1, (int) ($smtp?->rate_limit ?? 60));
        $delayBetweenEmails = (int) ceil(60 / $rateLimit);

        $queueConnection = $this->resolveQueueConnection();

        $relances = 0;
        $logIdsToReset = [];

        foreach ($failedLogs as $log) {
            if (! $log->contact || ! filter_var($log->contact->email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            $logIdsToReset[] = $log->id;
        }

        // Bulk reset status instead of individual updates
        if (! empty($logIdsToReset)) {
            EmailLog::whereIn('id', $logIdsToReset)->update([
                'status'        => EmailLog::STATUS_PENDING,
                'error_message' => null,
            ]);
        }

        foreach ($failedLogs as $log) {
            if (! $log->contact || ! filter_var($log->contact->email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            SendCampaignEmailJob::dispatch($campaign, $log->contact, $log->id)
                ->delay(now()->addSeconds($relances * $delayBetweenEmails))
                ->onQueue('emails')
                ->onConnection($queueConnection);

            $relances++;
        }

        if ($relances > 0) {
            $campaign->update(['statut' => 'en_cours']);
        }

        return redirect()->back()->with('success', "Relance d'envoi initiée pour {$relances} email(s) en échec.");
    }

    /**
     * Annuler une campagne en cours ou enregistrée.
     */
    public function cancel(Campaign $campaign)
    {
        if (in_array($campaign->statut, ['envoyee', 'annulee'])) {
            return back()->with('error', 'Cette campagne ne peut plus être annulée (déjà envoyée ou annulée).');
        }

        $campaign->update(['statut' => 'annulee']);

        // Bulk cancel all pending logs in one query
        EmailLog::where('campaign_id', $campaign->id)
            ->where('status', EmailLog::STATUS_PENDING)
            ->update([
                'status'        => EmailLog::STATUS_FAILED,
                'error_message' => 'Campagne annulée par l\'utilisateur',
            ]);

        return redirect()->route('campaigns.index')
            ->with('success', 'La campagne a été annulée avec succès.');
    }

    /**
     * API endpoint : returns the distinct recipient count for given target.
     */
    public function recipientCount(Request $request): \Illuminate\Http\JsonResponse
    {
        $importLogId = $request->input('import_log_id', $request->input('import_batch_id'));
        if ($importLogId) {
            $count = Contact::where('import_log_id', $importLogId)->count();
            return response()->json(['count' => $count]);
        }

        $categoryIds = array_values(array_unique(array_filter(
            array_map('intval', (array) $request->input('category_ids', []))
        )));

        $count = $categoryIds !== []
            ? Contact::query()->whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            })->distinct()->count('contacts.id')
            : Contact::count();

        return response()->json(['count' => $count]);
    }

    public function destroy(Campaign $campaign)
    {
        if ($campaign->statut === 'en_cours') {
            return back()->with('error', 'Impossible de supprimer une campagne en cours d\'envoi. Veuillez d\'abord l\'annuler.');
        }

        $campaign->delete();

        return redirect()->route('campaigns.index')->with('success', 'Campagne supprimée avec succès.');
    }

    public function preview(Campaign $campaign, Request $request)
    {
        $campaign->load(['attachments', 'importLog']);

        if ($campaign->import_log_id) {
            $contactsQuery = Contact::query()->where('import_log_id', $campaign->import_log_id);
        } else {
            $categoryIds = $campaign->categoryIds();
            $contactsQuery = $categoryIds !== []
                ? Contact::query()->whereHas('categories', function ($query) use ($categoryIds) {
                    $query->whereIn('categories.id', $categoryIds);
                })
                : Contact::query();
        }

        $contactsDisponibles = $contactsQuery->orderBy('nom')->get(['contacts.id', 'nom', 'prenom', 'email', 'entreprise', 'fonction', 'pays']);

        if ($contactsDisponibles->isEmpty()) {
            return back()->with('error', 'Aucun contact disponible pour la prévisualisation.');
        }

        $contact = $request->filled('contact_id')
            ? Contact::findOrFail($request->contact_id)
            : $contactsDisponibles->first();

        $context = [
            'campaign'      => $campaign,
            'nom_seminaire' => $campaign->nom,
            'date'          => $campaign->date_envoi?->format('d/m/Y') ?? now()->format('d/m/Y'),
        ];

        $contenuPersonnalise = EmailTemplate::renderContent($campaign->contenu, $contact, $context);
        $objetPersonnalise   = $this->personnaliser($campaign->objet, $contact, $context);

        return view('campaigns.preview', compact(
            'campaign',
            'contact',
            'contenuPersonnalise',
            'objetPersonnalise',
            'contactsDisponibles'
        ));
    }

    public static function personnaliser(string $texte, ?Contact $contact = null, array $extraVariables = []): string
    {
        $campaign = $extraVariables['campaign'] ?? null;
        $variables = [
            'nom'           => $contact?->nom ?? $extraVariables['nom'] ?? null,
            'prenom'        => $contact?->prenom ?? $extraVariables['prenom'] ?? null,
            'entreprise'    => $contact?->entreprise ?? $extraVariables['entreprise'] ?? null,
            'fonction'      => $contact?->fonction ?? $extraVariables['fonction'] ?? null,
            'pays'          => $contact?->pays ?? $extraVariables['pays'] ?? null,
            'nom_seminaire' => $extraVariables['nom_seminaire'] ?? $campaign?->nom,
            'date'          => $extraVariables['date'] ?? $campaign?->date_envoi?->format('d/m/Y') ?? now()->format('d/m/Y'),
            'lien'          => $extraVariables['lien'] ?? config('app.url'),
        ];

        $replacements = [];
        foreach ($variables as $key => $value) {
            $value = (string) ($value ?? '');
            $replacements['{{' . $key . '}}']          = $value;
            $replacements['{{' . ucfirst($key) . '}}'] = $value;
            $replacements['{{' . strtoupper($key) . '}}'] = $value;
        }

        return strtr($texte, $replacements);
    }

    public function send(Campaign $campaign)
    {
        // Protection anti-doublon : verrouiller la ligne et vérifier le statut
        $campaign = Campaign::lockForUpdate()->find($campaign->id);
        if (! $campaign || ! in_array($campaign->statut, ['brouillon', 'programmee'])) {
            return back()->with('error', 'Cette campagne a déjà été envoyée ou est en cours.');
        }

        if (! EmailTemplate::hasValidContent($campaign->contenu)) {
            return back()->with('error', "Impossible d'envoyer une campagne sans contenu valide.");
        }

        // Passer immédiatement en "en_cours" pour bloquer les double-clics
        $campaign->update(['statut' => 'en_cours']);

        // ─────────────────────────────────────────────────────────────────
        // Build the base query — select only the columns we need
        // ─────────────────────────────────────────────────────────────────
        $contactQuery = Contact::query()
            ->whereNull('unsubscribed_at')
            ->select(['id', 'email', 'nom', 'prenom', 'entreprise', 'fonction', 'pays', 'prospect_status', 'import_log_id']);

        if ($campaign->import_log_id) {
            $contactQuery->where('import_log_id', $campaign->import_log_id);
        } else {
            $categoryIds = $campaign->categoryIds();
            if ($categoryIds !== []) {
                $contactQuery->whereHas('categories', function ($query) use ($categoryIds) {
                    $query->whereIn('categories.id', $categoryIds);
                });
            }
        }

        // Pre-load existing logs for this campaign to avoid per-contact DB check
        $existingContactIds = EmailLog::where('campaign_id', $campaign->id)
            ->whereIn('status', [EmailLog::STATUS_PENDING, EmailLog::STATUS_SENT])
            ->pluck('contact_id')
            ->flip(); // O(1) lookup

        $smtp              = SmtpSetting::where('is_active', true)->first();
        $rateLimit         = max(1, (int) ($smtp?->rate_limit ?? 60));
        $delayBetweenEmails = (int) ceil(60 / $rateLimit);
        $queueConnection   = $this->resolveQueueConnection();

        $totalDispatched = 0;
        $jobIndex        = 0;
        $chunkSize       = 500;

        // ─────────────────────────────────────────────────────────────────
        // Process contacts in chunks of 500 — never loads all into RAM
        // ─────────────────────────────────────────────────────────────────
        $contactQuery->chunkById($chunkSize, function ($contacts) use (
            $campaign,
            $existingContactIds,
            $delayBetweenEmails,
            $queueConnection,
            &$jobIndex,
            &$totalDispatched
        ) {
            $now = now();
            $logsToInsert = [];

            // Collect contacts that need a new log
            $newContacts = $contacts->filter(function ($contact) use ($existingContactIds) {
                return ! isset($existingContactIds[$contact->id]);
            });

            if ($newContacts->isEmpty()) {
                return;
            }

            // Build bulk insert payload
            foreach ($newContacts as $contact) {
                $logsToInsert[] = [
                    'campaign_id' => $campaign->id,
                    'contact_id'  => $contact->id,
                    'status'      => EmailLog::STATUS_PENDING,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }

            // Single INSERT for the whole chunk (500 rows = 1 query)
            DB::table('email_logs')->insert($logsToInsert);

            // Retrieve the auto-incremented IDs for the just-inserted logs
            $insertedLogs = EmailLog::where('campaign_id', $campaign->id)
                ->where('status', EmailLog::STATUS_PENDING)
                ->whereIn('contact_id', $newContacts->pluck('id'))
                ->get(['id', 'contact_id'])
                ->keyBy('contact_id');

            // Dispatch one job per contact (non-blocking — they are queued)
            foreach ($newContacts as $contact) {
                $log = $insertedLogs->get($contact->id);
                if (! $log) {
                    continue;
                }

                SendCampaignEmailJob::dispatch($campaign, $contact, $log->id)
                    ->delay(now()->addSeconds($jobIndex * $delayBetweenEmails))
                    ->onQueue('emails')
                    ->onConnection($queueConnection);

                $jobIndex++;
                $totalDispatched++;
            }
        });

        if ($totalDispatched === 0) {
            $campaign->update(['statut' => 'brouillon']);
            return back()->with('error', 'Aucun contact actif à qui envoyer.');
        }

        return redirect()->route('campaigns.index')
            ->with('success', "Campagne lancée : {$totalDispatched} emails en file d'attente.");
    }

    /**
     * Programmer une campagne pour envoi à une date/heure future.
     */
    public function scheduleCampaign(Campaign $campaign, Request $request)
    {
        $campaign = Campaign::lockForUpdate()->find($campaign->id);
        if (! $campaign || ! in_array($campaign->statut, ['brouillon', 'programmee'])) {
            return back()->with('error', 'Seules les campagnes en brouillon peuvent être programmées.');
        }

        $request->validate([
            'date_envoi' => 'required|date|after:now',
        ], [
            'date_envoi.required' => 'Veuillez sélectionner une date et heure d\'envoi.',
            'date_envoi.after'    => 'La date d\'envoi doit être dans le futur.',
        ]);

        if (! EmailTemplate::hasValidContent($campaign->contenu)) {
            return back()->with('error', "Impossible de programmer une campagne sans contenu valide.");
        }

        $campaign->update([
            'statut'     => 'programmee',
            'date_envoi' => $request->input('date_envoi'),
        ]);

        $dateFormatee = \Carbon\Carbon::parse($request->input('date_envoi'))->format('d/m/Y à H:i');
        return redirect()->route('campaigns.index')
            ->with('success', "Campagne programmée pour le {$dateFormatee}. Elle sera envoyée automatiquement.");
    }

    /**
     * Annuler la programmation d'une campagne (retour en brouillon).
     */
    public function unscheduleCampaign(Campaign $campaign)
    {
        if ($campaign->statut !== 'programmee') {
            return back()->with('error', 'Cette campagne n\'est pas programmée.');
        }

        $campaign->update(['statut' => 'brouillon', 'date_envoi' => null]);

        return back()->with('success', 'Programmation annulée. La campagne est repassée en brouillon.');
    }

    // ─────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Determine the queue connection, preferring Redis when available.
     */
    private function resolveQueueConnection(): string
    {
        $connection = config('queue.default', 'database');
        if ($connection === 'redis') {
            try {
                \Illuminate\Support\Facades\Redis::connection()->ping();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Redis non disponible, basculement sur la queue database: ' . $e->getMessage());
                $connection = 'database';
            }
        }
        return $connection;
    }

    private function validatedCampaign(Request $request): array
    {
        $validated = $request->validate([
            'nom'              => 'required|string|max:255',
            'objet'            => 'required|string|max:255',
            'contenu'          => 'required|string|min:3',
            'category_id'      => 'nullable|exists:categories,id',
            'category_ids'     => 'nullable|array',
            'category_ids.*'   => 'integer|exists:categories,id',
            'import_log_id'    => 'nullable|exists:import_logs,id',
            'import_batch_id'  => 'nullable|exists:import_logs,id',
            'all_contacts'     => 'nullable|boolean',
            'targeting_mode'   => 'nullable|string',
            'auto_retry'       => 'nullable|boolean',
            'max_auto_retries' => 'nullable|integer|min:1|max:5',
        ]);

        $validated['auto_retry']       = $request->boolean('auto_retry', true);
        $validated['max_auto_retries'] = max(1, (int) $request->input('max_auto_retries', 3));

        $mode        = $request->input('targeting_mode');
        $importLogId = $request->input('import_log_id', $request->input('import_batch_id'));

        if ($mode === 'import' || ($importLogId && ! $request->boolean('all_contacts') && empty($request->input('category_ids')))) {
            $validated['import_log_id']  = (int) $importLogId;
            $validated['category_id']    = null;
            $validated['category_ids']   = [];
        } elseif ($mode === 'all' || $request->boolean('all_contacts')) {
            $validated['import_log_id']  = null;
            $validated['category_id']    = null;
            $validated['category_ids']   = [];
        } else {
            $categoryIds = $request->input('category_ids', []);
            if (empty($categoryIds) && $request->filled('category_id')) {
                $categoryIds = [(int) $request->input('category_id')];
            }
            $categoryIds = array_values(array_unique(array_map('intval', $categoryIds)));

            $validated['import_log_id'] = null;
            $validated['category_id']   = $categoryIds !== [] && count($categoryIds) === 1 ? $categoryIds[0] : null;
            $validated['category_ids']  = $categoryIds;
        }

        $validated['contenu'] = EmailTemplate::sanitizeContent($validated['contenu']);
        if (! EmailTemplate::hasValidContent($validated['contenu'])) {
            throw ValidationException::withMessages([
                'contenu' => 'Le contenu de la campagne doit contenir du texte valide.',
            ]);
        }

        return $validated;
    }
}
