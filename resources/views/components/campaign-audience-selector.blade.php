@props([
    'categories',                 // Collection of Category models with contacts_count
    'importLogs' => [],           // Collection of ImportLog models
    'selectedCategoryIds' => [],  // Pre-selected category IDs (array of ints)
    'selectedImportId' => null,   // Pre-selected import log ID (int or null)
    'allSelected' => false,       // Whether "all contacts" is pre-checked
    'totalContacts' => 0,         // Total contact count across all lists
    'recipientCountUrl',         // URL for the API endpoint
    'broadcastChanges' => false,  // If true, dispatches window event on count change
])

@php
    $initialCategoryIds = array_values(array_map('intval', (array) $selectedCategoryIds));
    $initialImportId = $selectedImportId ? (int) $selectedImportId : null;
    $initialAllSelected = (bool) $allSelected;

    if ($initialImportId) {
        $initialMode = 'import';
    } elseif ($initialAllSelected || (count($initialCategoryIds) === 0 && !$initialImportId)) {
        $initialMode = 'all';
    } else {
        $initialMode = 'category';
    }
@endphp

<div
    x-data="audienceSelector({
        initialMode: '{{ $initialMode }}',
        initialAllContacts: {{ $initialAllSelected ? 'true' : 'false' }},
        initialCategoryIds: {{ json_encode($initialCategoryIds) }},
        initialImportId: {{ json_encode($initialImportId) }},
        totalContacts: {{ (int) $totalContacts }},
        recipientCountUrl: '{{ $recipientCountUrl }}',
        broadcastChanges: {{ $broadcastChanges ? 'true' : 'false' }}
    })"
    class="space-y-4"
>
    {{-- Hidden inputs submitted with form according to active mode --}}
    <input type="hidden" name="targeting_mode" :value="mode">

    <template x-if="mode === 'all'">
        <input type="hidden" name="all_contacts" value="1">
    </template>

    <template x-if="mode === 'category'">
        <div>
            <template x-for="id in selectedCategoryIds" :key="id">
                <input type="hidden" name="category_ids[]" :value="id">
            </template>
        </div>
    </template>

    <template x-if="mode === 'import'">
        <div>
            <input type="hidden" name="import_log_id" :value="selectedImportId">
            <input type="hidden" name="import_batch_id" :value="selectedImportId">
        </div>
    </template>

    {{-- Recipient count badge --}}
    <div class="flex items-center gap-3 p-3.5 rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-50 to-blue-50/60 shadow-sm">
        <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <div class="text-[10px] font-bold uppercase tracking-wider text-indigo-600">Destinataires estimés</div>
            <div class="flex items-center gap-2 mt-0.5">
                <span
                    class="text-xl font-extrabold text-indigo-900 tabular-nums transition-all duration-300"
                    x-text="loading ? '…' : recipientCount.toLocaleString('fr-FR')"
                ></span>
                <span class="text-sm font-semibold text-indigo-700">contacts</span>
                <span
                    x-show="loading"
                    class="inline-block w-3.5 h-3.5 border-2 border-indigo-400 border-t-transparent rounded-full animate-spin"
                ></span>
            </div>
        </div>
        <div x-show="mode === 'category' && selectedCategoryIds.length === 0" class="text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-xl shrink-0">
            ⚠ Aucune liste sélectionnée
        </div>
        <div x-show="mode === 'import' && !selectedImportId" class="text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-xl shrink-0">
            ⚠ Aucun fichier sélectionné
        </div>
    </div>

    {{-- Mode Options (3 Mutually Exclusive Modes) --}}
    <div class="space-y-3">

        {{-- MODE 1 : Tous les contacts --}}
        <div
            @click="setMode('all')"
            :class="mode === 'all'
                ? 'border-indigo-400 bg-indigo-50/80 ring-2 ring-indigo-200'
                : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50'"
            class="p-3.5 rounded-2xl border-2 transition-all cursor-pointer"
        >
            <div class="flex items-center gap-3">
                <div
                    :class="mode === 'all' ? 'bg-indigo-600 border-indigo-600' : 'bg-white border-slate-300'"
                    class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-all"
                >
                    <div x-show="mode === 'all'" class="w-2 h-2 rounded-full bg-white"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-sm text-slate-800">Tous les contacts</div>
                    <div class="text-xs text-slate-500 mt-0.5">Envoyer la campagne à l'ensemble de la base de données</div>
                </div>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-700 border border-indigo-200 shrink-0">
                    {{ number_format($totalContacts, 0, ',', ' ') }} contacts
                </span>
            </div>
        </div>

        {{-- MODE 2 : Par catégories / listes --}}
        <div
            :class="mode === 'category'
                ? 'border-indigo-400 bg-indigo-50/40 ring-2 ring-indigo-200'
                : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50'"
            class="p-3.5 rounded-2xl border-2 transition-all"
        >
            <div @click="setMode('category')" class="flex items-center gap-3 cursor-pointer">
                <div
                    :class="mode === 'category' ? 'bg-indigo-600 border-indigo-600' : 'bg-white border-slate-300'"
                    class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-all"
                >
                    <div x-show="mode === 'category'" class="w-2 h-2 rounded-full bg-white"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-sm text-slate-800">Choisir une ou plusieurs catégories</div>
                    <div class="text-xs text-slate-500 mt-0.5">Cibler les contacts rattachés à des listes thématiques</div>
                </div>
                <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full shrink-0">
                    {{ count($categories) }} liste(s)
                </span>
            </div>

            {{-- Category selection list when mode === 'category' --}}
            <div x-show="mode === 'category'" class="mt-3.5 pt-3 border-t border-slate-200/80 space-y-2.5">
                @if($categories->isEmpty())
                    <div class="text-center py-4 text-xs text-slate-400 bg-white rounded-xl border border-slate-200 border-dashed">
                        Aucune catégorie disponible.
                    </div>
                @else
                    <div class="space-y-1.5 max-h-[220px] overflow-y-auto pr-1 custom-scrollbar">
                        @foreach($categories as $cat)
                            @php $catId = (int) $cat->id; @endphp
                            <button
                                type="button"
                                @click="toggleCategory({{ $catId }})"
                                :class="isCategorySelected({{ $catId }})
                                    ? 'border-indigo-400 bg-indigo-50/90 shadow-sm'
                                    : 'border-slate-200 bg-white hover:border-indigo-200 hover:bg-slate-50'"
                                class="w-full flex items-center gap-3 px-3 py-2 rounded-xl border transition-all text-left"
                            >
                                <div
                                    :class="isCategorySelected({{ $catId }}) ? 'bg-indigo-600 border-indigo-600' : 'bg-white border-slate-300'"
                                    class="w-4 h-4 rounded border-2 flex items-center justify-center shrink-0 transition-all"
                                >
                                    <svg x-show="isCategorySelected({{ $catId }})" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span class="flex-1 text-xs font-semibold text-slate-800 truncate">
                                    {{ $cat->name }}
                                </span>
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200 shrink-0">
                                    {{ number_format($cat->contacts_count, 0, ',', ' ') }}
                                </span>
                            </button>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-between pt-1 text-xs">
                        <div class="flex items-center gap-2">
                            <button type="button" @click="selectAllCategories()" class="font-semibold text-indigo-600 hover:underline">Tout sélectionner</button>
                            <span class="text-slate-300">·</span>
                            <button type="button" @click="deselectAllCategories()" class="font-semibold text-slate-500 hover:underline" x-show="selectedCategoryIds.length > 0">Désélectionner</button>
                        </div>
                        <span class="text-slate-400 font-medium" x-show="selectedCategoryIds.length > 0">
                            <span x-text="selectedCategoryIds.length"></span> sélectionnée(s)
                        </span>
                    </div>
                @endif
            </div>
        </div>

        {{-- MODE 3 : Par fichier importé (ImportBatch) --}}
        <div
            :class="mode === 'import'
                ? 'border-indigo-400 bg-indigo-50/40 ring-2 ring-indigo-200'
                : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50'"
            class="p-3.5 rounded-2xl border-2 transition-all"
        >
            <div @click="setMode('import')" class="flex items-center gap-3 cursor-pointer">
                <div
                    :class="mode === 'import' ? 'bg-indigo-600 border-indigo-600' : 'bg-white border-slate-300'"
                    class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-all"
                >
                    <div x-show="mode === 'import'" class="w-2 h-2 rounded-full bg-white"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-sm text-slate-800">Choisir un fichier importé</div>
                    <div class="text-xs text-slate-500 mt-0.5">Cibler uniquement les contacts issus d'un import CSV/Excel spécifique</div>
                </div>
                <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full shrink-0">
                    {{ count($importLogs) }} fichier(s)
                </span>
            </div>

            {{-- Import file list when mode === 'import' --}}
            <div x-show="mode === 'import'" class="mt-3.5 pt-3 border-t border-slate-200/80 space-y-2">
                @if(empty($importLogs) || count($importLogs) === 0)
                    <div class="text-center py-4 text-xs text-slate-400 bg-white rounded-xl border border-slate-200 border-dashed">
                        Aucun fichier importé disponible.
                    </div>
                @else
                    <div class="space-y-1.5 max-h-[220px] overflow-y-auto pr-1 custom-scrollbar">
                        @foreach($importLogs as $imp)
                            @php $impId = (int) $imp->id; @endphp
                            <button
                                type="button"
                                @click="selectImport({{ $impId }})"
                                :class="selectedImportId === {{ $impId }}
                                    ? 'border-indigo-400 bg-indigo-50/90 shadow-sm'
                                    : 'border-slate-200 bg-white hover:border-indigo-200 hover:bg-slate-50'"
                                class="w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl border transition-all text-left"
                            >
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div
                                        :class="selectedImportId === {{ $impId }} ? 'bg-indigo-600 border-indigo-600' : 'bg-white border-slate-300'"
                                        class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 transition-all"
                                    >
                                        <div x-show="selectedImportId === {{ $impId }}" class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                    </div>
                                    <div class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-xs text-slate-800 truncate" title="{{ $imp->filename }}">
                                            {{ $imp->filename }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">
                                            Importé le {{ $imp->created_at ? $imp->created_at->format('d/m/Y') : '-' }}
                                        </div>
                                    </div>
                                </div>

                                <span
                                    :class="selectedImportId === {{ $impId }} ? 'bg-indigo-100 text-indigo-700 border-indigo-200' : 'bg-slate-100 text-slate-600 border-slate-200'"
                                    class="text-[11px] font-bold px-2 py-0.5 rounded-full border shrink-0 tabular-nums"
                                >
                                    {{ number_format($imp->imported, 0, ',', ' ') }} contact(s)
                                </span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<script>
function audienceSelector({
    initialMode,
    initialAllContacts,
    initialCategoryIds,
    initialImportId,
    totalContacts,
    recipientCountUrl,
    broadcastChanges = false
}) {
    return {
        mode: initialMode,
        allContacts: initialAllContacts,
        selectedCategoryIds: initialCategoryIds,
        selectedImportId: initialImportId,
        recipientCount: 0,
        totalContacts: totalContacts,
        loading: false,
        _fetchTimer: null,
        _broadcastChanges: broadcastChanges,

        init() {
            this.updateCount();
        },

        setMode(newMode) {
            this.mode = newMode;
            this.updateCount();
        },

        isCategorySelected(id) {
            return this.selectedCategoryIds.includes(id);
        },

        toggleCategory(id) {
            this.mode = 'category';
            const idx = this.selectedCategoryIds.indexOf(id);
            if (idx === -1) {
                this.selectedCategoryIds.push(id);
            } else {
                this.selectedCategoryIds.splice(idx, 1);
            }
            this.scheduleCountFetch();
        },

        selectImport(id) {
            this.mode = 'import';
            this.selectedImportId = id;
            this.scheduleCountFetch();
        },

        selectAllCategories() {
            this.mode = 'category';
            this.selectedCategoryIds = {{ json_encode($categories->pluck('id')->map(fn($id) => (int) $id)->values()->toArray()) }};
            this.scheduleCountFetch();
        },

        deselectAllCategories() {
            this.mode = 'category';
            this.selectedCategoryIds = [];
            this.scheduleCountFetch();
        },

        scheduleCountFetch() {
            clearTimeout(this._fetchTimer);
            this._fetchTimer = setTimeout(() => this.updateCount(), 250);
        },

        async updateCount() {
            if (this.mode === 'all') {
                this.recipientCount = this.totalContacts;
                this.notifyChanges();
                return;
            }

            if (this.mode === 'import') {
                if (!this.selectedImportId) {
                    this.recipientCount = 0;
                    this.notifyChanges();
                    return;
                }
                this.fetchCount({ import_log_id: this.selectedImportId });
                return;
            }

            if (this.mode === 'category') {
                if (this.selectedCategoryIds.length === 0) {
                    this.recipientCount = 0;
                    this.notifyChanges();
                    return;
                }
                this.fetchCount({ category_ids: this.selectedCategoryIds });
                return;
            }
        },

        async fetchCount(paramsObj) {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                if (paramsObj.import_log_id) {
                    params.append('import_log_id', paramsObj.import_log_id);
                }
                if (paramsObj.category_ids) {
                    paramsObj.category_ids.forEach(id => params.append('category_ids[]', id));
                }

                const res = await fetch(recipientCountUrl + '?' + params.toString(), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                this.recipientCount = data.count ?? 0;
                this.notifyChanges();
            } catch (e) {
                // keep last count
            } finally {
                this.loading = false;
            }
        },

        notifyChanges() {
            if (this._broadcastChanges) {
                window.dispatchEvent(new CustomEvent('audience-count-changed', { detail: { count: this.recipientCount } }));
            }
        }
    };
}
</script>
