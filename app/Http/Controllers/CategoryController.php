<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('contacts')->latest()->get();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'couleur' => 'nullable|string|max:7',
            'icone' => 'nullable|string|max:50',
        ], [
            'name.required' => 'Le nom de la liste est obligatoire.',
            'name.unique' => 'Une liste avec ce nom existe déjà.',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Liste créée avec succès.');
    }

    public function show(Request $request, Category $category)
    {
        $query = $category->contacts();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('contacts.nom', 'like', "%{$search}%")
                  ->orWhere('contacts.prenom', 'like', "%{$search}%")
                  ->orWhere('contacts.email', 'like', "%{$search}%")
                  ->orWhere('contacts.entreprise', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->filled('pays')) {
            $query->where('contacts.pays', $request->pays);
        }
        if ($request->filled('entreprise')) {
            $query->where('contacts.entreprise', $request->entreprise);
        }
        if ($request->filled('fonction')) {
            $query->where('contacts.fonction', $request->fonction);
        }
        if ($request->filled('secteur_activite')) {
            $query->where('contacts.secteur_activite', $request->secteur_activite);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('contacts.created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('contacts.created_at', '<=', $request->date_to);
        }

        $contacts = $query->latest('contacts.created_at')->paginate(25)->withQueryString();

        // Get distinct values for filter dropdowns via SQL (évite le chargement de TOUS les contacts en RAM)
        $pays        = $category->contacts()->whereNotNull('pays')->distinct()->orderBy('pays')->pluck('pays');
        $entreprises = $category->contacts()->whereNotNull('entreprise')->distinct()->orderBy('entreprise')->pluck('entreprise');
        $fonctions   = $category->contacts()->whereNotNull('fonction')->distinct()->orderBy('fonction')->pluck('fonction');
        $secteurs    = $category->contacts()->whereNotNull('secteur_activite')->distinct()->orderBy('secteur_activite')->pluck('secteur_activite');

        // Available contacts to add (not already in this list) — limité à 300 pour éviter OutOfMemory
        $availableContacts = Contact::whereDoesntHave('categories', function ($q) use ($category) {
            $q->where('categories.id', $category->id);
        })->orderBy('nom')->limit(300)->get(['id', 'nom', 'prenom', 'email', 'entreprise']);

        return view('categories.show', compact(
            'category', 'contacts', 'pays', 'entreprises', 'fonctions', 'secteurs', 'availableContacts'
        ));
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'couleur' => 'nullable|string|max:7',
            'icone' => 'nullable|string|max:50',
        ], [
            'name.required' => 'Le nom de la liste est obligatoire.',
            'name.unique' => 'Une liste avec ce nom existe déjà.',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Liste mise à jour.');
    }

    public function destroy(Category $category)
    {
        $category->contacts()->detach();
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Liste supprimée.');
    }

    public function addContacts(Request $request, Category $category)
    {
        $request->validate([
            'contacts' => 'required|array|min:1',
            'contacts.*' => 'exists:contacts,id',
        ], [
            'contacts.required' => 'Veuillez sélectionner au moins un contact.',
            'contacts.min' => 'Veuillez sélectionner au moins un contact.',
        ]);

        $category->contacts()->syncWithoutDetaching($request->contacts);

        return redirect()->route('categories.show', $category)->with('success', count($request->contacts) . ' contact(s) ajouté(s).');
    }

    public function removeContact(Category $category, Contact $contact)
    {
        $category->contacts()->detach($contact->id);
        return redirect()->route('categories.show', $category)->with('success', 'Contact retiré de la liste.');
    }

    public function export(Request $request, Category $category)
    {
        $slug = \Illuminate\Support\Str::slug($category->name);
        $fileName = "contacts_liste_{$slug}_" . date('Y-m-d_H-i') . ".csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $query = $category->contacts();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('contacts.nom', 'like', "%{$search}%")
                  ->orWhere('contacts.prenom', 'like', "%{$search}%")
                  ->orWhere('contacts.email', 'like', "%{$search}%")
                  ->orWhere('contacts.entreprise', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->filled('pays')) {
            $query->where('contacts.pays', $request->pays);
        }
        if ($request->filled('entreprise')) {
            $query->where('contacts.entreprise', $request->entreprise);
        }
        if ($request->filled('fonction')) {
            $query->where('contacts.fonction', $request->fonction);
        }
        if ($request->filled('secteur_activite')) {
            $query->where('contacts.secteur_activite', $request->secteur_activite);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('contacts.created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('contacts.created_at', '<=', $request->date_to);
        }

        $contacts = $query->latest('contacts.created_at')->get();

        $callback = function() use ($contacts) {
            $file = fopen('php://output', 'w');
            // BOM UTF-8 pour ouverture correcte dans Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // En-tête du fichier CSV
            fputcsv($file, [
                'Nom', 'Prénom', 'Email', 'Entreprise', 'Fonction',
                'Téléphone', 'WhatsApp', 'Pays', 'Ville', 'Secteur d\'activité', 'Statut'
            ], ';');

            foreach ($contacts as $c) {
                fputcsv($file, [
                    $c->nom,
                    $c->prenom,
                    $c->email,
                    $c->entreprise,
                    $c->fonction,
                    $c->telephone,
                    $c->whatsapp,
                    $c->pays,
                    $c->ville,
                    $c->secteur_activite,
                    $c->prospect_status
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

