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

        $stats = [
            'total' => Campaign::count(),
            'brouillon' => Campaign::where('statut', 'brouillon')->count(),
            'en_cours' => Campaign::where('statut', 'en_cours')->count(),
            'envoyee' => Campaign::where('statut', 'envoyee')->count(),
            'annulee' => Campaign::where('statut', 'annulee')->count(),
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
            ->with('contact')
            ->get();

        if ($failedLogs->isEmpty()) {
            return back()->with('error', 'Aucun email en échec à relancer pour cette campagne.');
        }

        $smtp = SmtpSetting::where('is_active', true)->first();
        $rateLimit = max(1, (int) ($smtp?->rate_limit ?? 60));
        $delayBetweenEmails = (int) ceil(60 / $rateLimit);

        // Determine the queue connection: prefer Redis, fall back to database
        $queueConnection = config('queue.default', 'database');
        try {
            if ($queueConnection === 'redis') {
                \Illuminate\Support\Facades\Redis::connection()->ping();
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Redis non disponible, basculement sur la queue database pour retryFailed: ' . $e->getMessage());
            $queueConnection = 'database';
        }

        $relances = 0;
        foreach ($failedLogs as $log) {
            if (!$log->contact || !filter_var($log->contact->email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $log->update([
                'status' => EmailLog::STATUS_PENDING,
                'error_message' => null,
            ]);

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

        EmailLog::where('campaign_id', $campaign->id)
            ->where('status', EmailLog::STATUS_PENDING)
            ->update([
                'status' => EmailLog::STATUS_FAILED,
                'error_message' => 'Campagne annulée par l\'utilisateur',
            ]);

        return redirect()->route('campaigns.index')
            ->with('success', 'La campagne a été annulée avec succès.');
    }

    /**
     * API endpoint : returns the distinct recipient count for given target (import, category IDs, or all).
     * GET /campaigns/recipient-count?import_log_id=1
     * GET /campaigns/recipient-count?category_ids[]=1&category_ids[]=2
     * GET /campaigns/recipient-count  (no params → all contacts)
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
        if ($campaign->statut !== 'brouillon') {
            return back()->with('error', 'Impossible de supprimer une campagne déjà envoyée ou en cours.');
        }

        $campaign->delete();

        return redirect()->route('campaigns.index')->with('success', 'Campagne supprimée.');
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
            'campaign' => $campaign,
            'nom_seminaire' => $campaign->nom,
            'date' => $campaign->date_envoi?->format('d/m/Y') ?? now()->format('d/m/Y'),
        ];

        $contenuPersonnalise = EmailTemplate::renderContent($campaign->contenu, $contact, $context);
        $objetPersonnalise = $this->personnaliser($campaign->objet, $contact, $context);

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
            'nom' => $contact?->nom ?? $extraVariables['nom'] ?? null,
            'prenom' => $contact?->prenom ?? $extraVariables['prenom'] ?? null,
            'entreprise' => $contact?->entreprise ?? $extraVariables['entreprise'] ?? null,
            'fonction' => $contact?->fonction ?? $extraVariables['fonction'] ?? null,
            'pays' => $contact?->pays ?? $extraVariables['pays'] ?? null,
            'nom_seminaire' => $extraVariables['nom_seminaire'] ?? $campaign?->nom,
            'date' => $extraVariables['date'] ?? $campaign?->date_envoi?->format('d/m/Y') ?? now()->format('d/m/Y'),
            'lien' => $extraVariables['lien'] ?? config('app.url'),
        ];

        $replacements = [];
        foreach ($variables as $key => $value) {
            $value = (string) ($value ?? '');
            $replacements['{{' . $key . '}}'] = $value;
            $replacements['{{' . ucfirst($key) . '}}'] = $value;
            $replacements['{{' . strtoupper($key) . '}}'] = $value;
        }

        return strtr($texte, $replacements);
    }

    public function send(Campaign $campaign)
    {
        if ($campaign->statut !== 'brouillon') {
            return back()->with('error', 'Cette campagne a déjà été envoyée ou est en cours.');
        }

        if (! EmailTemplate::hasValidContent($campaign->contenu)) {
            return back()->with('error', "Impossible d'envoyer une campagne sans contenu valide.");
        }

        if ($campaign->import_log_id) {
            $contacts = Contact::query()->where('import_log_id', $campaign->import_log_id)->get();
        } else {
            $categoryIds = $campaign->categoryIds();
            $contacts = $categoryIds !== []
                ? Contact::query()->whereHas('categories', function ($query) use ($categoryIds) {
                    $query->whereIn('categories.id', $categoryIds);
                })->get()
                : Contact::all();
        }

        if ($contacts->isEmpty()) {
            return back()->with('error', 'Aucun contact à qui envoyer.');
        }

        $smtp = SmtpSetting::where('is_active', true)->first();
        $rateLimit = max(1, (int) ($smtp?->rate_limit ?? 60));
        $delayBetweenEmails = (int) ceil(60 / $rateLimit);

        // Determine the queue connection: prefer Redis, fall back to database
        $queueConnection = config('queue.default', 'database');
        try {
            // Test Redis connectivity before dispatching
            if ($queueConnection === 'redis') {
                \Illuminate\Support\Facades\Redis::connection()->ping();
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Redis non disponible, basculement sur la queue database: ' . $e->getMessage());
            $queueConnection = 'database';
        }

        foreach ($contacts as $index => $contact) {
            $emailLog = EmailLog::create([
                'campaign_id' => $campaign->id,
                'contact_id' => $contact->id,
                'status' => EmailLog::STATUS_PENDING,
            ]);

            SendCampaignEmailJob::dispatch($campaign, $contact, $emailLog->id)
                ->delay(now()->addSeconds($index * $delayBetweenEmails))
                ->onQueue('emails')
                ->onConnection($queueConnection);
        }

        $campaign->update(['statut' => 'en_cours']);

        return redirect()->route('campaigns.index')
            ->with('success', "Campagne lancée : {$contacts->count()} emails en file d'attente.");
    }

    private function validatedCampaign(Request $request): array
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'objet' => 'required|string|max:255',
            'contenu' => 'required|string|min:3',
            'category_id' => 'nullable|exists:categories,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:categories,id',
            'import_log_id' => 'nullable|exists:import_logs,id',
            'import_batch_id' => 'nullable|exists:import_logs,id',
            'all_contacts' => 'nullable|boolean',
            'targeting_mode' => 'nullable|string',
            'auto_retry' => 'nullable|boolean',
            'max_auto_retries' => 'nullable|integer|min:1|max:5',
        ]);

        $validated['auto_retry'] = $request->boolean('auto_retry', true);
        $validated['max_auto_retries'] = max(1, (int) $request->input('max_auto_retries', 3));

        $mode = $request->input('targeting_mode');
        $importLogId = $request->input('import_log_id', $request->input('import_batch_id'));

        if ($mode === 'import' || ($importLogId && !$request->boolean('all_contacts') && empty($request->input('category_ids')))) {
            $validated['import_log_id'] = (int) $importLogId;
            $validated['category_id'] = null;
            $validated['category_ids'] = [];
        } elseif ($mode === 'all' || $request->boolean('all_contacts')) {
            $validated['import_log_id'] = null;
            $validated['category_id'] = null;
            $validated['category_ids'] = [];
        } else {
            $categoryIds = $request->input('category_ids', []);
            if (empty($categoryIds) && $request->filled('category_id')) {
                $categoryIds = [(int) $request->input('category_id')];
            }
            $categoryIds = array_values(array_unique(array_map('intval', $categoryIds)));

            $validated['import_log_id'] = null;
            $validated['category_id'] = $categoryIds !== [] && count($categoryIds) === 1 ? $categoryIds[0] : null;
            $validated['category_ids'] = $categoryIds;
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

