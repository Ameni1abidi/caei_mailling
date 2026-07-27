@props([
    'categories',        // Collection of Category models with contacts_count
    'selectedIds' => [], // Pre-selected category IDs (array of ints)
    'allSelected' => false, // Whether "all contacts" is pre-checked
    'totalContacts' => 0,  // Total contact count across all lists
    'recipientCountUrl',  // URL for the API endpoint
    'broadcastChanges' => false, // If true, dispatches window event on count change
])

@php
    $initialSelectedIds = array_values(array_map('intval', (array) $selectedIds));
    $initialAllSelected = (bool) $allSelected;
    // Initial count displayed before JS kicks in
    $initialCount = $initialAllSelected || count($initialSelectedIds) === 0
        ? $totalContacts
        : null; // JS will calculate
@endphp

<div
    x-data="audienceSelector({
        initialAllContacts: {{ $initialAllSelected ? 'true' : 'false' }},
        initialCategoryIds: {{ json_encode($initialSelectedIds) }},
        totalContacts: {{ (int) $totalContacts }},
        recipientCountUrl: '{{ $recipientCountUrl }}',
        broadcastChanges: {{ $broadcastChanges ? 'true' : 'false' }}
    })"
    class="space-y-3"
>
    {{-- Hidden inputs submitted with form --}}
    <template x-if="allContacts">
        <input type="hidden" name="all_contacts" value="1">
    </template>
    <template x-for="id in selectedIds" :key="id">
        <input type="hidden" name="category_ids[]" :value="id">
    </template>

    {{-- Recipient count badge --}}
    <div class="flex items-center gap-3 p-3.5 rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-50 to-blue-50/60 shadow-sm">
        <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
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
        <div x-show="!allContacts && selectedIds.length === 0" class="text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-xl shrink-0">
            ⚠ Aucune liste
        </div>
    </div>

    {{-- "All contacts" option --}}
    <button
        type="button"
        @click="toggleAllContacts()"
        :class="allContacts
            ? 'border-indigo-400 bg-indigo-50 text-indigo-900 ring-2 ring-indigo-200'
            : 'border-slate-200 bg-slate-50 text-slate-700 hover:border-indigo-200 hover:bg-indigo-50/40'"
        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl border-2 transition-all duration-150 cursor-pointer text-left"
    >
        <div
            :class="allContacts ? 'bg-indigo-500 border-indigo-500' : 'bg-white border-slate-300'"
            class="w-5 h-5 rounded-md border-2 flex items-center justify-center shrink-0 transition-all duration-150"
        >
            <svg x-show="allContacts" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <span class="font-bold text-sm">Tous les contacts</span>
            <span class="ml-2 text-xs font-semibold px-2 py-0.5 rounded-full"
                :class="allContacts ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-500'"
            >{{ number_format($totalContacts, 0, ',', ' ') }} contacts</span>
        </div>
        <svg x-show="allContacts" class="w-4 h-4 text-indigo-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
    </button>

    {{-- Separator --}}
    <div class="flex items-center gap-3">
        <div class="flex-1 h-px bg-slate-200"></div>
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Ou choisir des listes</span>
        <div class="flex-1 h-px bg-slate-200"></div>
    </div>

    {{-- Category list --}}
    @if($categories->isEmpty())
        <div class="text-center py-6 text-sm text-slate-400 bg-slate-50 rounded-xl border border-slate-200 border-dashed">
            Aucune liste disponible. <a href="{{ route('categories.create') }}" class="text-indigo-600 hover:underline font-semibold">Créer une liste</a>
        </div>
    @else
        <div class="space-y-2 max-h-[260px] overflow-y-auto pr-1 custom-scrollbar">
            @foreach($categories as $cat)
                @php $catId = (int) $cat->id; @endphp
                <button
                    type="button"
                    @click="toggleCategory({{ $catId }})"
                    :class="isCategorySelected({{ $catId }}) && !allContacts
                        ? 'border-indigo-400 bg-indigo-50/70 ring-1 ring-indigo-200'
                        : allContacts
                            ? 'border-slate-100 bg-slate-50/50 opacity-50 cursor-not-allowed'
                            : 'border-slate-200 bg-white hover:border-indigo-200 hover:bg-slate-50/80'"
                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl border transition-all duration-100 text-left"
                    :disabled="allContacts"
                >
                    {{-- Checkbox visual --}}
                    <div
                        :class="isCategorySelected({{ $catId }}) && !allContacts
                            ? 'bg-indigo-500 border-indigo-500'
                            : 'bg-white border-slate-300'"
                        class="w-4.5 h-4.5 rounded border-2 flex items-center justify-center shrink-0 transition-all duration-100"
                        style="width:18px;height:18px;"
                    >
                        <svg x-show="isCategorySelected({{ $catId }}) && !allContacts" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>

                    {{-- Color dot --}}
                    @if($cat->couleur)
                        <span class="w-2.5 h-2.5 rounded-full shrink-0 border border-white shadow-sm" style="background-color: {{ $cat->couleur }}"></span>
                    @endif

                    {{-- Name --}}
                    <span class="flex-1 text-sm font-semibold text-slate-800 truncate">
                        {{ $cat->icone ? $cat->icone . ' ' : '' }}{{ $cat->name }}
                    </span>

                    {{-- Contact count badge --}}
                    <span
                        :class="isCategorySelected({{ $catId }}) && !allContacts
                            ? 'bg-indigo-100 text-indigo-700 border-indigo-200'
                            : 'bg-slate-100 text-slate-500 border-slate-200'"
                        class="text-[11px] font-bold px-2 py-0.5 rounded-full border shrink-0 tabular-nums transition-colors"
                    >{{ number_format($cat->contacts_count, 0, ',', ' ') }}</span>
                </button>
            @endforeach
        </div>

        {{-- Quick actions --}}
        <div class="flex items-center gap-2 pt-1" x-show="!allContacts">
            <button type="button" @click="selectAll()" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline transition">
                Tout sélectionner
            </button>
            <span class="text-slate-300 text-xs">·</span>
            <button type="button" @click="deselectAll()" class="text-xs font-semibold text-slate-500 hover:text-slate-700 hover:underline transition" x-show="selectedIds.length > 0">
                Désélectionner
            </button>
            <span class="flex-1"></span>
            <span class="text-xs font-medium text-slate-400" x-show="selectedIds.length > 0">
                <span x-text="selectedIds.length"></span> liste<span x-show="selectedIds.length > 1">s</span> choisie<span x-show="selectedIds.length > 1">s</span>
            </span>
        </div>
    @endif
</div>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<script>
function audienceSelector({ initialAllContacts, initialCategoryIds, totalContacts, recipientCountUrl, broadcastChanges = false }) {
    return {
        allContacts: initialAllContacts,
        selectedIds: initialCategoryIds,
        recipientCount: initialAllContacts || initialCategoryIds.length === 0 ? totalContacts : 0,
        totalContacts: totalContacts,
        loading: false,
        _fetchTimer: null,
        _broadcastChanges: broadcastChanges,

        init() {
            // If we have pre-selected categories, fetch the actual count
            if (!this.allContacts && this.selectedIds.length > 0) {
                this.fetchCount();
            } else {
                this.recipientCount = totalContacts;
            }
        },

        isCategorySelected(id) {
            return this.selectedIds.includes(id);
        },

        toggleCategory(id) {
            if (this.allContacts) return;
            const idx = this.selectedIds.indexOf(id);
            if (idx === -1) {
                this.selectedIds.push(id);
            } else {
                this.selectedIds.splice(idx, 1);
            }
            this.scheduleCountFetch();
        },

        toggleAllContacts() {
            this.allContacts = !this.allContacts;
            if (this.allContacts) {
                this.recipientCount = this.totalContacts;
                if (this._broadcastChanges) window.dispatchEvent(new CustomEvent('audience-count-changed', { detail: { count: this.totalContacts } }));
            } else {
                this.scheduleCountFetch();
            }
        },

        selectAll() {
            this.selectedIds = {{ json_encode($categories->pluck('id')->map(fn($id) => (int) $id)->values()->toArray()) }};
            this.scheduleCountFetch();
        },

        deselectAll() {
            this.selectedIds = [];
            this.recipientCount = this.totalContacts;
            if (this._broadcastChanges) window.dispatchEvent(new CustomEvent('audience-count-changed', { detail: { count: this.totalContacts } }));
        },

        scheduleCountFetch() {
            clearTimeout(this._fetchTimer);
            if (this.selectedIds.length === 0) {
                this.recipientCount = this.totalContacts;
                return;
            }
            this._fetchTimer = setTimeout(() => this.fetchCount(), 350);
        },

        async fetchCount() {
            if (this.selectedIds.length === 0) {
                this.recipientCount = this.totalContacts;
                if (this._broadcastChanges) window.dispatchEvent(new CustomEvent('audience-count-changed', { detail: { count: this.totalContacts } }));
                return;
            }
            this.loading = true;
            try {
                const params = new URLSearchParams();
                this.selectedIds.forEach(id => params.append('category_ids[]', id));
                const res = await fetch(recipientCountUrl + '?' + params.toString(), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                this.recipientCount = data.count ?? 0;
                if (this._broadcastChanges) window.dispatchEvent(new CustomEvent('audience-count-changed', { detail: { count: this.recipientCount } }));
            } catch (e) {
                // Silently fail — keep last known value
            } finally {
                this.loading = false;
            }
        }
    };
}
</script>
