<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Category;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProspectController extends Controller
{
    public function index(Request $request)
    {
        $statuses = Contact::getProspectStatuses();
        $query = Contact::query()->with(['categories', 'emailLogs.campaign', 'lastCampaign', 'interactions' => function($q) {
            $q->latest()->limit(1);
        }]);

        // Filtre par statut
        if ($request->filled('status') && array_key_exists($request->status, $statuses)) {
            $query->where('prospect_status', $request->status);
        }

        // Filtre par catégorie / liste
        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        // Filtre par campagne (contacts ayant reçu au moins un mail de cette campagne)
        if ($request->filled('campaign_id')) {
            $query->whereHas('emailLogs', function ($q) use ($request) {
                $q->where('campaign_id', $request->campaign_id);
            });
        }

        // Filtres pays et secteur
        if ($request->filled('pays')) {
            $query->where('pays', $request->pays);
        }
        if ($request->filled('secteur_activite')) {
            $query->where('secteur_activite', $request->secteur_activite);
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('entreprise', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Calcul des statistiques globales par statut
        $statusCounts = Contact::query()
            ->selectRaw('prospect_status, count(*) as total')
            ->groupBy('prospect_status')
            ->pluck('total', 'prospect_status')
            ->toArray();

        $stats = [];
        $totalProspects = 0;
        foreach ($statuses as $key => $meta) {
            $count = $statusCounts[$key] ?? 0;
            $stats[$key] = $count;
            $totalProspects += $count;
        }

        // Mode d'affichage : 'kanban' ou 'table' (par défaut 'kanban')
        $viewMode = $request->input('view', 'kanban');

        if ($viewMode === 'kanban') {
            // Grouper les prospects par statut pour les colonnes Kanban (limité à 50 par statut pour la performance)
            $kanbanData = [];
            foreach (array_keys($statuses) as $statusKey) {
                $columnQuery = (clone $query)->where('prospect_status', $statusKey);
                $kanbanData[$statusKey] = $columnQuery->latest()->take(50)->get();
            }
            $contacts = null;
        } else {
            $kanbanData = null;
            $contacts = $query->latest()->paginate(25)->withQueryString();
        }

        $categories = Category::orderBy('name')->get(['id', 'name']);
        $campaigns = Campaign::latest()->get(['id', 'nom']);
        $paysOptions = Contact::whereNotNull('pays')->where('pays', '!=', '')->distinct()->pluck('pays');
        $secteurOptions = Contact::whereNotNull('secteur_activite')->where('secteur_activite', '!=', '')->distinct()->pluck('secteur_activite');

        return view('prospects.index', compact(
            'statuses',
            'stats',
            'totalProspects',
            'kanbanData',
            'contacts',
            'viewMode',
            'categories',
            'campaigns',
            'paysOptions',
            'secteurOptions'
        ));
    }

    public function updateStatus(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(Contact::getProspectStatuses()))],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $contact->updateStatusWithLog($validated['status'], $validated['note'] ?? null);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Statut mis à jour vers '{$validated['status']}'",
                'contact' => $contact->fresh()
            ]);
        }

        return back()->with('success', "Statut du prospect {$contact->prenom} {$contact->nom} mis à jour : {$validated['status']}");
    }

    /**
     * Show prospect details with interaction history.
     */
    public function show(Request $request, Contact $contact)
    {
        $contact->load([
            'categories',
            'emailLogs.campaign',
            'interactions',
            'lastCampaign'
        ]);

        $statuses = Contact::getProspectStatuses();
        $interactionTypes = \App\Models\ProspectInteraction::getInteractionTypes();

        return view('prospects.show', compact(
            'contact',
            'statuses',
            'interactionTypes'
        ));
    }

    /**
     * Add a note to a prospect.
     */
    public function addNote(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'note' => ['required', 'string', 'max:2000'],
        ]);

        $contact->addNote($validated['note']);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Note ajoutée avec succès',
                'contact' => $contact->fresh()->load('interactions'),
            ]);
        }

        return back()->with('success', 'Note ajoutée avec succès');
    }

    /**
     * Schedule a follow-up for a prospect.
     */
    public function scheduleFollowUp(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'followup_date' => ['required', 'date', 'after:now'],
            'followup_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $followUpDate = new \DateTime($validated['followup_date']);
        $contact->scheduleFollowUp($followUpDate, $validated['followup_note'] ?? null);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Relance planifiée avec succès',
                'contact' => $contact->fresh(),
            ]);
        }

        return back()->with('success', 'Relance planifiée avec succès');
    }
}
