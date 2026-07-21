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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'couleur' => 'nullable|string|max:7',
            'icone' => 'nullable|string|max:50',
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
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('entreprise', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->filled('pays')) {
            $query->where('pays', $request->pays);
        }
        if ($request->filled('entreprise')) {
            $query->where('entreprise', $request->entreprise);
        }
        if ($request->filled('fonction')) {
            $query->where('fonction', $request->fonction);
        }
        if ($request->filled('secteur_activite')) {
            $query->where('secteur_activite', $request->secteur_activite);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('contacts.created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('contacts.created_at', '<=', $request->date_to);
        }

        $contacts = $query->latest()->paginate(25)->withQueryString();

        // Get distinct values for filter dropdowns (from contacts in this category)
        $allContacts = $category->contacts;
        $pays = $allContacts->pluck('pays')->filter()->unique()->sort()->values();
        $entreprises = $allContacts->pluck('entreprise')->filter()->unique()->sort()->values();
        $fonctions = $allContacts->pluck('fonction')->filter()->unique()->sort()->values();
        $secteurs = $allContacts->pluck('secteur_activite')->filter()->unique()->sort()->values();

        // Available contacts to add (not already in this list)
        $availableContacts = Contact::whereDoesntHave('categories', function ($q) use ($category) {
            $q->where('categories.id', $category->id);
        })->orderBy('nom')->get();

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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'couleur' => 'nullable|string|max:7',
            'icone' => 'nullable|string|max:50',
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
            'contacts' => 'required|array',
            'contacts.*' => 'exists:contacts,id',
        ]);

        $category->contacts()->syncWithoutDetaching($request->contacts);

        return redirect()->route('categories.show', $category)->with('success', count($request->contacts) . ' contact(s) ajouté(s).');
    }

    public function removeContact(Category $category, Contact $contact)
    {
        $category->contacts()->detach($contact->id);
        return redirect()->route('categories.show', $category)->with('success', 'Contact retiré de la liste.');
    }
}
