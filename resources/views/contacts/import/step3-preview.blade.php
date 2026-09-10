<x-app-layout>
<div class="p-6 space-y-6">

    {{-- Header / Stepper --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('contacts.import.mapping', $importLog->id) }}" class="p-2 hover:bg-slate-100 rounded-lg text-slate-500 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Prévisualisation</h1>
                <p class="text-sm text-slate-500">Vérifiez les 10 premières lignes avant l'import</p>
            </div>
        </div>
        <div class="flex items-center gap-0">
            @foreach([['①','Fichier',false,true],['②','Mapping',false,true],['③','Aperçu',true,false],['④','Import',false,false],['⑤','Résultat',false,false]] as [$num,$label,$active,$done])
            <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
                <div class="flex flex-col items-center">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold {{ $active ? 'bg-indigo-600 text-white shadow-md' : ($done ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400') }}">
                        @if($done) ✓ @else {{ $num }} @endif
                    </div>
                    <span class="text-xs mt-1 font-semibold {{ $active ? 'text-indigo-600' : 'text-slate-400' }}">{{ $label }}</span>
                </div>
                @if(!$loop->last)<div class="flex-1 h-0.5 mb-4 bg-slate-200 mx-2"></div>@endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Stats aperçu --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm flex items-center gap-3">
            <div class="p-2.5 bg-slate-50 text-slate-600 rounded-xl"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
            <div><div class="text-xl font-extrabold text-slate-900">{{ number_format($importLog->total_rows) }}</div><div class="text-xs text-slate-500">Total estimé</div></div>
        </div>
        <div class="bg-emerald-50 p-4 rounded-xl border border-emerald-100 shadow-sm flex items-center gap-3">
            <div class="p-2.5 bg-emerald-100 text-emerald-600 rounded-xl"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <div><div class="text-xl font-extrabold text-emerald-700">{{ $validCount }}</div><div class="text-xs text-emerald-600">Valides (aperçu)</div></div>
        </div>
        <div class="bg-rose-50 p-4 rounded-xl border border-rose-100 shadow-sm flex items-center gap-3">
            <div class="p-2.5 bg-rose-100 text-rose-600 rounded-xl"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <div><div class="text-xl font-extrabold text-rose-700">{{ $errorCount }}</div><div class="text-xs text-rose-600">Erreurs (aperçu)</div></div>
        </div>
    </div>

    {{-- Tableau de prévisualisation --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="font-bold text-slate-800">Aperçu des 10 premières lignes</h2>
            <p class="text-xs text-slate-400 mt-0.5">Les lignes en rouge contiennent des erreurs et seront ignorées lors de l'import</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        @foreach($mappedFields as $field)
                            <th class="px-4 py-3 capitalize">{{ $allFields[$field] ?? $field }}</th>
                        @endforeach
                        <th class="px-4 py-3">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($previewMapped as $i => $row)
                    <tr class="{{ $row['has_error'] ? 'bg-rose-50' : 'hover:bg-slate-50' }} transition">
                        <td class="px-4 py-2.5 text-slate-400 font-mono">{{ $i + 2 }}</td>
                        @foreach($mappedFields as $field)
                            <td class="px-4 py-2.5 text-slate-700 max-w-[150px] truncate" title="{{ $row['data'][$field] ?? '' }}">
                                {{ $row['data'][$field] ?? '—' }}
                            </td>
                        @endforeach
                        <td class="px-4 py-2.5">
                            @if($row['has_error'])
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    {{ $row['error_msg'] }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Valide
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Options choisies --}}
    @if(!empty($categories) && $categories->count() > 0)
    <div class="bg-violet-50 border border-violet-100 rounded-xl p-4 text-sm flex items-center gap-3">
        <svg class="w-5 h-5 text-violet-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 11h10M7 15h10"/></svg>
        <span class="text-violet-800 font-medium">Listes assignées :</span>
        @foreach($categories as $cat)
            <span class="px-2 py-0.5 bg-violet-100 text-violet-700 rounded-full text-xs font-bold">{{ $cat->name }}</span>
        @endforeach
    </div>
    @endif

    {{-- Navigation --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('contacts.import.mapping', $importLog->id) }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Modifier le mapping
        </a>

        <form action="{{ route('contacts.import.execute', $importLog->id) }}" method="POST">
            @csrf
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2.5 rounded-xl shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Lancer l'import
            </button>
        </form>
    </div>

</div>
</x-app-layout>

