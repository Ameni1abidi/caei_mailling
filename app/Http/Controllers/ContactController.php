<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Imports\ContactsImport;
use Maatwebsite\Excel\Facades\Excel;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::query();

        // Filtres
        if ($request->filled('pays')) {
            $query->where('pays', $request->pays);
        }
        if ($request->filled('secteur_activite')) {
            $query->where('secteur_activite', $request->secteur_activite);
        }
        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'like', "%{$request->search}%")
                  ->orWhere('prenom', 'like', "%{$request->search}%")
                  ->orWhere('entreprise', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $contacts = $query->latest()->paginate(25);

        return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
        return view('contacts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts,email',
            'entreprise' => 'nullable|string|max:255',
            'fonction' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'pays' => 'nullable|string|max:100',
            'ville' => 'nullable|string|max:100',
            'secteur_activite' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'categorie' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        Contact::create($validated);

        return redirect()->route('contacts.index')->with('success', 'Contact créé avec succès.');
    }

    public function edit(Contact $contact)
    {
        return view('contacts.edit', compact('contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts,email,' . $contact->id,
            'entreprise' => 'nullable|string|max:255',
            'fonction' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'pays' => 'nullable|string|max:100',
            'ville' => 'nullable|string|max:100',
            'secteur_activite' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'categorie' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $contact->update($validated);

        return redirect()->route('contacts.index')->with('success', 'Contact mis à jour.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'Contact supprimé.');
    }

    public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,csv,txt|max:10240', // 10 Mo max
    ]);

    $import = new ContactsImport();
    Excel::import($import, $request->file('file'));

    $errors = $import->errors();
    $message = $errors->count() > 0
        ? "Import terminé avec {$errors->count()} erreurs."
        : "Import réussi.";

    return redirect()->route('contacts.index')->with('success', $message);
}
}