<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Category;
use App\Models\ImportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Imports\ContactsImport;
use Maatwebsite\Excel\Facades\Excel;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $hasStatusColumn = Schema::hasColumn('contacts', 'prospect_status');

        $importLogId = $request->input('import_log_id');
        $importContext = $importLogId ? ImportLog::find($importLogId) : null;

        $query = Contact::query()->with('categories');

        if ($request->filled('pays')) {
            $query->where('pays', $request->pays);
        }
        if ($request->filled('secteur_activite')) {
            $query->where('secteur_activite', $request->secteur_activite);
        }
        $categoryFilter = $request->input('category_id', $request->input('categorie'));
        if ($categoryFilter) {
            $query->whereHas('categories', function ($q) use ($categoryFilter) {
                $q->where('categories.id', $categoryFilter);
            });
        }
        if ($hasStatusColumn && $request->filled('status')) {
            $query->where('prospect_status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'like', "%{$request->search}%")
                  ->orWhere('prenom', 'like', "%{$request->search}%")
                  ->orWhere('entreprise', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }
        if ($importLogId) {
            $query->where('import_log_id', $importLogId);
        }

        $contacts = $query->latest()->paginate(25)->withQueryString();

        $categories = Category::withCount('contacts')->orderBy('name')->get(['id', 'name', 'contacts_count']);
        $paysOptions = Contact::whereNotNull('pays')
            ->where('pays', '!=', '')
            ->distinct()
            ->orderBy('pays')
            ->pluck('pays');
        $secteurOptions = Contact::whereNotNull('secteur_activite')
            ->where('secteur_activite', '!=', '')
            ->distinct()
            ->orderBy('secteur_activite')
            ->pluck('secteur_activite');
        $statusOptions = Contact::getProspectStatuses();
        $totalContacts = Contact::count();

        return view('contacts.index', compact(
            'contacts', 'categories', 'paysOptions', 'secteurOptions',
            'statusOptions', 'hasStatusColumn', 'totalContacts',
            'importContext'
        ));
    }

    public function create()
    {
        $categories = Category::all();
        $statuses = Contact::getProspectStatuses();
        return view('contacts.create', compact('categories', 'statuses'));
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
            'prospect_status' => 'nullable|string|max:100',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'notes' => 'nullable|string',
        ]);

        if (empty($validated['prospect_status'])) {
            $validated['prospect_status'] = Contact::STATUS_NOUVEAU;
        }

        $categoryIds = $validated['categories'] ?? [];
        unset($validated['categories']);

        $contact = Contact::create($validated);
        if ($categoryIds) {
            $contact->categories()->sync($categoryIds);
        }

        return redirect()->route('contacts.index')->with('success', 'Contact créé avec succès.');
    }

    public function edit(Contact $contact)
    {
        $categories = Category::all();
        $statuses = Contact::getProspectStatuses();
        return view('contacts.edit', compact('contact', 'categories', 'statuses'));
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
            'prospect_status' => 'nullable|string|max:100',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'notes' => 'nullable|string',
        ]);

        $categoryIds = $validated['categories'] ?? [];
        unset($validated['categories']);

        $contact->update($validated);
        $contact->categories()->sync($categoryIds);

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
            'file' => 'required|mimes:xlsx,csv,txt|max:10240',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();

        // Créer un enregistrement d'import en avance (pour lier les contacts)
        $importLog = ImportLog::create([
            'filename'   => $originalName,
            'user_id'    => Auth::id(),
            'imported'   => 0,
            'duplicates' => 0,
            'errors'     => 0,
            'total_rows' => 0,
        ]);

        $import = new ContactsImport($importLog->id);
        Excel::import($import, $file);

        // Mettre à jour les statistiques après import
        $importLog->update([
            'total_rows' => $import->getTotalRows(),
            'imported'   => $import->getImported(),
            'duplicates' => $import->getDuplicates(),
            'errors'     => $import->getErrorCount(),
        ]);

        $errCount = $import->getErrorCount();
        $imported = $import->getImported();
        $dupes    = $import->getDuplicates();

        $message = "Import terminé : {$imported} contact(s) ajouté(s)";
        if ($dupes > 0)  $message .= ", {$dupes} doublon(s) ignoré(s)";
        if ($errCount > 0) $message .= ", {$errCount} erreur(s)";
        $message .= '.';

        return redirect()->route('contacts.index')->with('success', $message);
    }

    /**
     * Display the import history.
     */
    public function importHistory()
    {
        $imports = ImportLog::with('user')
            ->withCount('contacts')
            ->latest()
            ->paginate(20);

        return view('contacts.import-history', compact('imports'));
    }
}
