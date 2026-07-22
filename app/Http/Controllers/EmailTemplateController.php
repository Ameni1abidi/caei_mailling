<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EmailTemplateController extends Controller
{
    public function index(Request $request)
    {
        $templates = EmailTemplate::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                        ->orWhere('sujet', 'like', "%{$search}%")
                        ->orWhere('contenu', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->type))
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('is_active', $request->status === 'active');
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $types = EmailTemplate::TYPES;

        $stats = [
            'total' => EmailTemplate::count(),
            'active' => EmailTemplate::where('is_active', true)->count(),
            'newsletter' => EmailTemplate::where('type', 'newsletter')->count(),
            'promotion' => EmailTemplate::where('type', 'promotion')->count(),
        ];

        return view('email-templates.index', compact('templates', 'types', 'stats'));
    }

    public function create()
    {
        $types = EmailTemplate::TYPES;
        $variables = EmailTemplate::availableVariables();

        return view('email-templates.create', compact('types', 'variables'));
    }

    public function store(Request $request)
    {
        EmailTemplate::create($this->validatedTemplate($request));

        return redirect()->route('email-templates.index')->with('success', 'Template cree avec succes.');
    }

    public function edit(EmailTemplate $emailTemplate)
    {
        $types = EmailTemplate::TYPES;
        $variables = EmailTemplate::availableVariables();

        return view('email-templates.edit', compact('emailTemplate', 'types', 'variables'));
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $emailTemplate->update($this->validatedTemplate($request, $emailTemplate));

        return redirect()->route('email-templates.index')->with('success', 'Template mis a jour.');
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();

        return redirect()->route('email-templates.index')->with('success', 'Template supprime.');
    }

    public function preview(EmailTemplate $emailTemplate)
    {
        $preview = $emailTemplate->renderPreview();
        $variables = EmailTemplate::previewValues();

        return view('email-templates.preview', compact('emailTemplate', 'preview', 'variables'));
    }

    public function toggle(EmailTemplate $emailTemplate)
    {
        $emailTemplate->update(['is_active' => ! $emailTemplate->is_active]);

        return redirect()->route('email-templates.index')->with('success', 'Statut du template mis a jour.');
    }

    public function duplicate(EmailTemplate $emailTemplate)
    {
        $copy = $emailTemplate->replicate();
        $copy->nom = $emailTemplate->nom . ' (Copie)';
        $copy->save();

        return redirect()->route('email-templates.index')->with('success', 'Template dupliqué avec succès.');
    }

    public function installDefaults()
    {
        $created = EmailTemplate::installDefaults();

        $message = $created > 0
            ? $created . ' template(s) exemple(s) ajoute(s).'
            : 'Les templates exemples existent deja.';

        return redirect()->route('email-templates.index')->with('success', $message);
    }

    private function validatedTemplate(Request $request, ?EmailTemplate $emailTemplate = null): array
    {
        $validated = $request->validate([
            'nom' => [
                'required',
                'string',
                'max:255',
                Rule::unique('email_templates', 'nom')->ignore($emailTemplate?->id),
            ],
            'sujet' => 'nullable|string|max:255',
            'type' => ['required', Rule::in(array_keys(EmailTemplate::TYPES))],
            'contenu' => 'required|string|min:3',
            'is_active' => 'nullable|boolean',
        ], [
            'nom.required' => 'Le nom du template est obligatoire.',
            'nom.unique' => 'Un template avec ce nom existe deja.',
            'type.required' => 'Le type du template est obligatoire.',
            'type.in' => 'Le type selectionne est invalide.',
            'contenu.required' => 'Le contenu du template est obligatoire.',
        ]);

        $validated['contenu'] = EmailTemplate::sanitizeContent($validated['contenu']);
        if (! EmailTemplate::hasValidContent($validated['contenu'])) {
            throw ValidationException::withMessages([
                'contenu' => 'Le contenu du template doit contenir du texte valide.',
            ]);
        }

        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
