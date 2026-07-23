<x-app-layout>
    <div class="p-6 max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3.5 bg-gradient-to-br from-amber-500 to-orange-600 text-white rounded-2xl shadow-md shadow-amber-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Modifier : {{ $emailTemplate->nom }}</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Mettez à jour le contenu et la configuration de ce modèle d'email</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('email-templates.builder', $emailTemplate) }}" class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                    </svg>
                    <span>Éditeur Visuel</span>
                </a>
                @if($emailTemplate->is_active)
                    <a href="{{ route('campaigns.create', ['template_id' => $emailTemplate->id]) }}" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <span>Utiliser dans une campagne</span>
                    </a>
                @endif
                <a href="{{ route('email-templates.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-4 py-2.5 rounded-xl border border-slate-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Retour à la liste</span>
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <form action="{{ route('email-templates.update', $emailTemplate) }}" method="POST">
            @csrf
            @method('PUT')

            @include('email-templates._form', ['emailTemplate' => $emailTemplate])

            <div class="flex items-center justify-end gap-3 pt-6 mt-6 border-t border-slate-200/80 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                <a href="{{ route('email-templates.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-900 transition">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow-md shadow-indigo-100 transition duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Enregistrer les modifications</span>
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
