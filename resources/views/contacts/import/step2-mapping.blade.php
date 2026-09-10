<x-app-layout>
<div class="p-6 space-y-6">

    {{-- Header / Stepper --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('contacts.import.upload') }}" class="p-2 hover:bg-slate-100 rounded-lg text-slate-500 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Mapping des colonnes</h1>
                <p class="text-sm text-slate-500">Associez les colonnes de votre fichier aux champs CAEI</p>
            </div>
        </div>
        {{-- Stepper --}}
        <div class="flex items-center gap-0">
            @foreach([['①','Fichier',false],['②','Mapping',true],['③','Aperçu',false],['④','Import',false],['⑤','Résultat',false]] as [$num,$label,$active])
            <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
                <div class="flex flex-col items-center">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold {{ $active ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : ($loop->first ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400') }}">
                        @if($loop->first) ✓ @else {{ $num }} @endif
                    </div>
                    <span class="text-xs mt-1 font-semibold {{ $active ? 'text-indigo-600' : 'text-slate-400' }}">{{ $label }}</span>
                </div>
                @if(!$loop->last)<div class="flex-1 h-0.5 mb-4 bg-slate-200 mx-2"></div>@endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Infos fichier --}}
    <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 flex items-center gap-4 text-sm">
        <svg class="w-5 h-5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <span class="font-semibold text-indigo-800">{{ $importLog->filename }}</span>
        <span class="text-indigo-500">—</span>
        <span class="text-indigo-700">{{ number_format($importLog->total_rows) }} lignes détectées</span>
        <span class="text-indigo-500">—</span>
        <span class="text-indigo-700">{{ count($headers) }} colonnes</span>
    </div>

    {{-- Erreurs de validation --}}
    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl text-sm font-medium">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <form action="{{ route('contacts.import.save-mapping', $importLog->id) }}" method="POST">
        @csrf

        {{-- Tableau de mapping --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="font-bold text-slate-800">Association des colonnes</h2>
                <span class="text-xs text-slate-500 bg-slate-100 px-3 py-1 rounded-full font-semibold">
                    <span class="text-rose-600">*</span> Email obligatoire
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500 font-bold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3 text-left">Colonne dans le fichier</th>
                            <th class="px-6 py-3 text-left">Exemple de valeur</th>
                            <th class="px-6 py-3 text-left">Champ CAEI</th>
                            <th class="px-6 py-3 text-center">Détection auto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($headers as $header)
                        @php
                            $detected = $autoMapping[$header] ?? null;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-3">
                                <span class="font-semibold text-slate-800">{{ $header }}</span>
                            </td>
                            <td class="px-6 py-3 text-slate-400 text-xs font-mono max-w-[200px] truncate">
                                —
                            </td>
                            <td class="px-6 py-3">
                                <select name="mapping[{{ $header }}]"
                                        class="w-full text-sm border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 {{ $detected === 'email' ? 'ring-2 ring-indigo-400 border-indigo-400 bg-indigo-50' : '' }}">
                                    <option value="__ignore__">— Ignorer cette colonne —</option>
                                    @foreach($mappableFields as $field => $label)
                                        <option value="{{ $field }}" {{ $detected === $field ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-6 py-3 text-center">
                                @if($detected)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Détecté
                                    </span>
                                @else
                                    <span class="text-xs text-slate-300">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Options d'import --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">

            {{-- Gestion des doublons --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
                <h3 class="font-bold text-slate-800 mb-3">Gestion des doublons</h3>
                <div class="space-y-2">
                    <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 has-[:checked]:border-indigo-400 has-[:checked]:bg-indigo-50 transition">
                        <input type="radio" name="duplicate_strategy" value="ignore" checked class="mt-0.5 text-indigo-600">
                        <div>
                            <div class="font-semibold text-sm text-slate-800">Ignorer les doublons</div>
                            <div class="text-xs text-slate-500">Les contacts déjà présents (même email) sont ignorés</div>
                        </div>
                    </label>
                    <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 has-[:checked]:border-indigo-400 has-[:checked]:bg-indigo-50 transition">
                        <input type="radio" name="duplicate_strategy" value="update" class="mt-0.5 text-indigo-600">
                        <div>
                            <div class="font-semibold text-sm text-slate-800">Mettre à jour les contacts existants</div>
                            <div class="text-xs text-slate-500">Si un contact existe, ses données sont mises à jour</div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Associer à des listes --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
                <h3 class="font-bold text-slate-800 mb-3">Associer à des listes <span class="text-xs font-normal text-slate-400">(optionnel)</span></h3>
                @if($categories->isEmpty())
                    <p class="text-sm text-slate-400 italic">Aucune liste créée. <a href="{{ route('categories.create') }}" class="text-indigo-600 hover:underline">Créer une liste</a></p>
                @else
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach($categories as $cat)
                        <label class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 cursor-pointer transition">
                            <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}" class="text-indigo-600 rounded">
                            <span class="text-sm font-medium text-slate-700">{{ $cat->name }}</span>
                        </label>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Boutons de navigation --}}
        <div class="flex items-center justify-between mt-6">
            <a href="{{ route('contacts.import.upload') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Retour
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-xl shadow-sm transition">
                Prévisualiser
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </form>

</div>
</x-app-layout>

