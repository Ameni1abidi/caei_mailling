<x-app-layout>
<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div class="flex items-center gap-4 mb-6">
            <div class="p-3 {{ $importLog->status === 'failed' ? 'bg-gradient-to-br from-rose-500 to-red-600' : 'bg-gradient-to-br from-emerald-500 to-teal-600' }} text-white rounded-2xl shadow-md">
                @if($importLog->status === 'failed')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @else
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @endif
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Rapport d'import</h1>
                <p class="text-sm text-slate-500">{{ $importLog->filename }}</p>
            </div>
        </div>
        {{-- Stepper --}}
        <div class="flex items-center gap-0">
            @foreach([['①','Fichier'],['②','Mapping'],['③','Aperçu'],['④','Import'],['⑤','Résultat']] as [$num,$label])
            <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
                <div class="flex flex-col items-center">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold {{ $loop->last ? 'bg-emerald-500 text-white shadow-md' : 'bg-emerald-100 text-emerald-600' }}">
                        @if($loop->last) ⑤ @else ✓ @endif
                    </div>
                    <span class="text-xs mt-1 font-semibold {{ $loop->last ? 'text-emerald-600' : 'text-slate-400' }}">{{ $label }}</span>
                </div>
                @if(!$loop->last)<div class="flex-1 h-0.5 mb-4 bg-emerald-100 mx-2"></div>@endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Stats finales --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-sm text-center">
            <div class="text-3xl font-extrabold text-slate-900">{{ number_format($importLog->total_rows) }}</div>
            <div class="text-xs font-semibold text-slate-500 mt-1">Lignes lues</div>
        </div>
        <div class="bg-emerald-50 p-5 rounded-xl border border-emerald-100 shadow-sm text-center">
            <div class="text-3xl font-extrabold text-emerald-700">{{ number_format($importLog->imported) }}</div>
            <div class="text-xs font-semibold text-emerald-600 mt-1">✓ Importés</div>
        </div>
        <div class="bg-amber-50 p-5 rounded-xl border border-amber-100 shadow-sm text-center">
            <div class="text-3xl font-extrabold text-amber-700">{{ number_format($importLog->duplicates) }}</div>
            <div class="text-xs font-semibold text-amber-600 mt-1">⚠ Doublons ignorés</div>
        </div>
        <div class="bg-rose-50 p-5 rounded-xl border border-rose-100 shadow-sm text-center">
            <div class="text-3xl font-extrabold text-rose-700">{{ number_format($importLog->errors) }}</div>
            <div class="text-xs font-semibold text-rose-600 mt-1">✕ Erreurs</div>
        </div>
    </div>

    {{-- Listes associées --}}
    @if($categories->count() > 0)
    <div class="bg-violet-50 border border-violet-100 rounded-xl p-4 text-sm flex items-center gap-3 flex-wrap">
        <svg class="w-5 h-5 text-violet-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 11h10M7 15h10"/></svg>
        <span class="text-violet-800 font-medium">Contacts assignés aux listes :</span>
        @foreach($categories as $cat)
            <span class="px-2 py-0.5 bg-violet-100 text-violet-700 rounded-full text-xs font-bold">{{ $cat->name }}</span>
        @endforeach
    </div>
    @endif

    {{-- Détails des erreurs --}}
    @if(!empty($errorDetails))
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-bold text-slate-800">Détail des erreurs ({{ count($errorDetails) }})</h2>
            <a href="{{ route('contacts.import.errors', $importLog->id) }}"
               class="inline-flex items-center gap-2 text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Télécharger CSV des erreurs
            </a>
        </div>
        <div class="overflow-x-auto max-h-80 overflow-y-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs text-slate-500 font-bold border-b border-slate-100 sticky top-0">
                    <tr>
                        <th class="px-6 py-3 text-left">Ligne</th>
                        <th class="px-6 py-3 text-left">Erreur</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach(array_slice($errorDetails, 0, 100) as $err)
                    <tr class="hover:bg-rose-50/50 transition">
                        <td class="px-6 py-2.5">
                            <span class="font-mono text-xs bg-rose-100 text-rose-700 px-2 py-0.5 rounded">Ligne {{ $err['row'] ?? '?' }}</span>
                        </td>
                        <td class="px-6 py-2.5 text-rose-700 text-xs">{{ $err['error'] ?? '' }}</td>
                    </tr>
                    @endforeach
                    @if(count($errorDetails) > 100)
                    <tr>
                        <td colspan="2" class="px-6 py-3 text-center text-xs text-slate-400">
                            ... et {{ count($errorDetails) - 100 }} erreur(s) supplémentaires.
                            <a href="{{ route('contacts.import.errors', $importLog->id) }}" class="text-indigo-600 hover:underline">Télécharger le rapport complet</a>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Actions --}}
    <div class="flex items-center justify-between">
        <div class="flex gap-3">
            <a href="{{ route('contacts.import-history') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 transition shadow-sm">
                Historique des imports
            </a>
            <a href="{{ route('contacts.import.upload') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 transition shadow-sm">
                Nouvel import
            </a>
        </div>

        @if($importLog->imported > 0)
        <a href="{{ route('contacts.index', ['import_log_id' => $importLog->id]) }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-xl shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Voir les {{ number_format($importLog->imported) }} contacts importés
        </a>
        @endif
    </div>

</div>
</x-app-layout>

