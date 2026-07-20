<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Contact;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::with('category')->latest()->paginate(20);
        return view('campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        // Liste de toutes les catégories pour le select
        $categories = Category::orderBy('name')->get();

        return view('campaigns.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'objet' => 'required|string|max:255',
            'contenu' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
        ]);

       $validated['created_by'] = Auth::id();
        $validated['statut'] = 'brouillon';

        $campaign = Campaign::create($validated);

        return redirect()->route('campaigns.edit', $campaign)
            ->with('success', 'Campagne créée. Vous pouvez maintenant la prévisualiser.');
    }

    public function edit(Campaign $campaign)
    {
        $categories = Category::orderBy('name')->get();

        // Compte les destinataires ciblés pour affichage
        $nbDestinataires = $campaign->category_id
            ? $campaign->category->contacts()->count()
            : Contact::count();

        return view('campaigns.edit', compact('campaign', 'categories', 'nbDestinataires'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'objet' => 'required|string|max:255',
            'contenu' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $campaign->update($validated);

        return redirect()->route('campaigns.edit', $campaign)->with('success', 'Campagne mise à jour.');
    }

    public function destroy(Campaign $campaign)
    {
        if ($campaign->statut !== 'brouillon') {
            return back()->with('error', 'Impossible de supprimer une campagne déjà envoyée ou en cours.');
        }

        $campaign->delete();
        return redirect()->route('campaigns.index')->with('success', 'Campagne supprimée.');
    }

    /**
     * Aperçu personnalisé avec un vrai contact (pour preview avant envoi)
     */
    public function preview(Campaign $campaign, Request $request)
    {
        // Liste des contacts ciblés par cette campagne (pour le menu déroulant)
        $contactsQuery = $campaign->category_id
            ? $campaign->category->contacts()
            : Contact::query();

        $contactsDisponibles = $contactsQuery->orderBy('nom')->get(['contacts.id', 'nom', 'prenom', 'email']);

        if ($contactsDisponibles->isEmpty()) {
            return back()->with('error', 'Aucun contact disponible pour la prévisualisation.');
        }

        // Si un contact précis est demandé via ?contact_id=X, on l'utilise
        if ($request->filled('contact_id')) {
            $contact = Contact::findOrFail($request->contact_id);
        } else {
            $contact = $contactsDisponibles->first();
        }

        $contenuPersonnalise = $this->personnaliser($campaign->contenu, $contact);
        $objetPersonnalise = $this->personnaliser($campaign->objet, $contact);

        return view('campaigns.preview', compact(
            'campaign', 'contact', 'contenuPersonnalise', 'objetPersonnalise', 'contactsDisponibles'
        ));
    }

    /**
     * Remplace les variables {{Nom}}, {{Prenom}}, etc. par les valeurs du contact
     */
    public static function personnaliser(string $texte, Contact $contact): string
    {
        $variables = [
            '{{Nom}}' => $contact->nom,
            '{{Prenom}}' => $contact->prenom,
            '{{Entreprise}}' => $contact->entreprise,
            '{{Fonction}}' => $contact->fonction,
            '{{Pays}}' => $contact->pays,
        ];

        return str_replace(array_keys($variables), array_values($variables), $texte);
    }
}