<?php

namespace App\Http\Controllers;

use App\Jobs\SendCampaignEmailJob;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Contact;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SmtpSetting;
use Illuminate\Validation\ValidationException;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::with('category')->latest()->paginate(20);

        $stats = [
            'total' => Campaign::count(),
            'brouillon' => Campaign::where('statut', 'brouillon')->count(),
            'en_cours' => Campaign::where('statut', 'en_cours')->count(),
            'envoyee' => Campaign::where('statut', 'envoyee')->count(),
        ];

        return view('campaigns.index', compact('campaigns', 'stats'));
    }

    public function create(Request $request)
    {
        $categories = Category::orderBy('name')->get();
        $template = null;

        if ($request->filled('template_id')) {
            $template = EmailTemplate::where('is_active', true)->findOrFail($request->template_id);
        }

        return view('campaigns.create', compact('categories', 'template'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatedCampaign($request);
        $validated['created_by'] = Auth::id();
        $validated['statut'] = 'brouillon';

        $campaign = Campaign::create($validated);

        return redirect()->route('campaigns.edit', $campaign)
            ->with('success', 'Campagne creee. Vous pouvez maintenant la previsualiser.');
    }

    public function edit(Campaign $campaign)
    {
        $campaign->load('attachments');
        $categories = Category::orderBy('name')->get();

        $nbDestinataires = $campaign->category_id
            ? $campaign->category->contacts()->count()
            : Contact::count();

        return view('campaigns.edit', compact('campaign', 'categories', 'nbDestinataires'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $campaign->update($this->validatedCampaign($request));

        return redirect()->route('campaigns.edit', $campaign)->with('success', 'Campagne mise a jour.');
    }

    public function destroy(Campaign $campaign)
    {
        if ($campaign->statut !== 'brouillon') {
            return back()->with('error', 'Impossible de supprimer une campagne deja envoyee ou en cours.');
        }

        $campaign->delete();

        return redirect()->route('campaigns.index')->with('success', 'Campagne supprimee.');
    }

    public function preview(Campaign $campaign, Request $request)
    {
        $campaign->load('attachments');
        $contactsQuery = $campaign->category_id
            ? $campaign->category->contacts()
            : Contact::query();

        $contactsDisponibles = $contactsQuery->orderBy('nom')->get(['contacts.id', 'nom', 'prenom', 'email', 'entreprise', 'fonction', 'pays']);

        if ($contactsDisponibles->isEmpty()) {
            return back()->with('error', 'Aucun contact disponible pour la previsualisation.');
        }

        $contact = $request->filled('contact_id')
            ? Contact::findOrFail($request->contact_id)
            : $contactsDisponibles->first();

        $context = [
            'campaign' => $campaign,
            'nom_seminaire' => $campaign->nom,
            'date' => $campaign->date_envoi?->format('d/m/Y') ?? now()->format('d/m/Y'),
        ];

        $contenuPersonnalise = $this->personnaliser($campaign->contenu, $contact, $context);
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
            return back()->with('error', 'Cette campagne a deja ete envoyee ou est en cours.');
        }

        if (! EmailTemplate::hasValidContent($campaign->contenu)) {
            return back()->with('error', "Impossible d'envoyer une campagne sans contenu valide.");
        }

        $contacts = $campaign->category_id
            ? $campaign->category->contacts
            : Contact::all();

        if ($contacts->isEmpty()) {
            return back()->with('error', 'Aucun contact a qui envoyer.');
        }

        $smtp = SmtpSetting::where('is_active', true)->first();
        $rateLimit = max(1, (int) ($smtp?->rate_limit ?? 60));
        $delayBetweenEmails = (int) ceil(60 / $rateLimit);

        foreach ($contacts as $index => $contact) {
            $emailLog = EmailLog::create([
                'campaign_id' => $campaign->id,
                'contact_id' => $contact->id,
                'status' => 'pending',
            ]);

            SendCampaignEmailJob::dispatch($campaign, $contact, $emailLog->id)
                ->delay(now()->addSeconds($index * $delayBetweenEmails))
                ->onQueue('emails');
        }

        $campaign->update(['statut' => 'en_cours']);

        return redirect()->route('campaigns.index')
            ->with('success', "Campagne lancee : {$contacts->count()} emails en file d'attente.");
    }

    private function validatedCampaign(Request $request): array
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'objet' => 'required|string|max:255',
            'contenu' => 'required|string|min:3',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $validated['contenu'] = EmailTemplate::sanitizeContent($validated['contenu']);
        if (! EmailTemplate::hasValidContent($validated['contenu'])) {
            throw ValidationException::withMessages([
                'contenu' => 'Le contenu de la campagne doit contenir du texte valide.',
            ]);
        }

        return $validated;
    }
}
