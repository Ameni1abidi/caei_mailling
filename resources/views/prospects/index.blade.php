<x-app-layout>
    <div class="p-6 space-y-6" x-data="{ 
        viewMode: '{{ $viewMode }}',
        statusModalOpen: false,
        selectedContactId: null,
        selectedContactName: '',
        selectedStatus: '',
        noteText: '',
        openStatusModal(contactId, contactName, currentStatus) {
            this.selectedContactId = contactId;
            this.selectedContactName = contactName;
            this.selectedStatus = currentStatus;
            this.noteText = '';
            this.statusModalOpen = true;
        }
    }">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3.5 bg-gradient-to-br from-indigo-600 via-blue-600 to-sky-500 text-white rounded-2xl shadow-md shadow-indigo-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18l-7 8v6l-4 2v-8L3 4z" />
                    </svg>
                </div>
                <div>
                    <div class="inline-flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">MODULE 9</span>
                        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Suivi des prospects</h1>
                    </div>
                    <p class="text-sm text-slate-500 mt-0.5">Après campagne : Gérez et qualifiez vos contacts à chaque étape du tunnel de conversion</p>
                </div>
            </div>

            <!-- View Switcher -->
            <div class="flex items-center gap-2 bg-slate-100 p-1.5 rounded-xl border border-slate-200/60 self-start md:self-auto">
                <a href="{{ route('prospects.index', array_merge(request()->query(), ['view' => 'kanban'])) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-bold transition {{ $viewMode === 'kanban' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2m0 10V7m6 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                    </svg>
                    <span>Pipeline (Kanban)</span>
                </a>
                <a href="{{ route('prospects.index', array_merge(request()->query(), ['view' => 'table'])) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-bold transition {{ $viewMode === 'table' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    <span>Vue Tableau</span>
                </a>
            </div>
        </div>

        <!-- Flash messages -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition class="flex items-center justify-between gap-3 bg-emerald-50 border border-emerald-200 text-emerald-900 p-4 rounded-xl text-sm font-medium shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-1 bg-emerald-100 rounded-lg text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span>{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 p-1 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        <!-- KPI Cards Statuts -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
            @foreach($statuses as $statusKey => $meta)
                @php
                    $count = $stats[$statusKey] ?? 0;
                    $percent = $totalProspects > 0 ? round(($count / $totalProspects) * 100, 1) : 0;
                    $isActive = request('status') === $statusKey;
                @endphp
                <a href="{{ route('prospects.index', array_merge(request()->except('status', 'page'), $isActive ? [] : ['status' => $statusKey])) }}"
                   class="bg-white p-4 rounded-2xl border transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md relative overflow-hidden group {{ $isActive ? 'ring-2 ring-indigo-600 border-indigo-200 shadow-sm' : 'border-slate-200/80 shadow-sm' }}">
                    <div class="flex items-center justify-between mb-2">
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-bold border {{ $meta['badge'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $meta['dot'] }}"></span>
                            {{ $meta['label'] }}
                        </span>
                    </div>
                    <div class="flex items-baseline justify-between mt-2">
                        <span class="text-2xl font-black text-slate-900 tracking-tight">{{ $count }}</span>
                        <span class="text-[11px] font-semibold text-slate-400">{{ $percent }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1 mt-2.5 overflow-hidden">
                        <div class="h-1 rounded-full {{ str_replace('text-', 'bg-', str_replace('border-', '', explode(' ', $meta['badge'])[1] ?? 'bg-indigo-500')) }}" style="width: {{ $percent }}%"></div>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Filters Bar -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
            <form method="GET" action="{{ route('prospects.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                <input type="hidden" name="view" value="{{ $viewMode }}">

                <!-- Search -->
                <div class="lg:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Recherche</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, entreprise, email..."
                               class="w-full pl-9 pr-3 py-2 text-sm border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- Statut Filter -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Statut</label>
                    <select name="status" onchange="this.form.submit()" class="w-full py-2 text-sm border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50">
                        <option value="">Tous les statuts</option>
                        @foreach($statuses as $statusKey => $meta)
                            <option value="{{ $statusKey }}" {{ request('status') === $statusKey ? 'selected' : '' }}>
                                {{ $meta['label'] }} ({{ $stats[$statusKey] ?? 0 }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Liste / Catégorie</label>
                    <select name="category_id" onchange="this.form.submit()" class="w-full py-2 text-sm border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50">
                        <option value="">Toutes les listes</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Campaign Filter -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Campagne</label>
                    <select name="campaign_id" onchange="this.form.submit()" class="w-full py-2 text-sm border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50">
                        <option value="">Toutes les campagnes</option>
                        @foreach($campaigns as $camp)
                            <option value="{{ $camp->id }}" {{ request('campaign_id') == $camp->id ? 'selected' : '' }}>{{ $camp->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Reset button -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-semibold py-2 px-3 rounded-xl text-sm transition">
                        Filtrer
                    </button>
                    @if(request()->hasAny(['search', 'status', 'category_id', 'campaign_id', 'pays', 'secteur_activite']))
                        <a href="{{ route('prospects.index', ['view' => $viewMode]) }}" class="p-2 text-slate-400 hover:text-slate-600 bg-slate-100 rounded-xl transition" title="Réinitialiser">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Content according to view mode -->
        @if($viewMode === 'kanban')
            <!-- KANBAN PIPELINE VIEW -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-4 items-start overflow-x-auto pb-4">
                @foreach($statuses as $statusKey => $meta)
                    @php
                        $prospectsInCol = $kanbanData[$statusKey] ?? collect();
                    @endphp
                    <div class="bg-slate-100/70 rounded-2xl border border-slate-200/80 p-3 min-w-[260px] flex flex-col max-h-[800px]">
                        <!-- Column Header -->
                        <div class="flex items-center justify-between pb-3 px-1 border-b border-slate-200 mb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full {{ $meta['dot'] }}"></span>
                                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">{{ $meta['label'] }}</h3>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-white text-slate-700 border border-slate-200">
                                {{ $prospectsInCol->count() }}
                            </span>
                        </div>

                        <!-- Column Cards -->
                        <div class="space-y-3 overflow-y-auto pr-1 flex-1">
                            @forelse($prospectsInCol as $prospect)
                                <a href="{{ route('prospects.show', $prospect) }}" class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all group relative block">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <div class="font-bold text-slate-900 text-sm leading-snug hover:text-indigo-600 transition">
                                                {{ $prospect->prenom }} {{ $prospect->nom }}
                                            </div>
                                            @if($prospect->entreprise)
                                                <div class="text-xs font-medium text-slate-500 mt-0.5 flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                    </svg>
                                                    {{ $prospect->entreprise }}
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Quick Status Modal Trigger -->
                                        <button @click.prevent="openStatusModal({{ $prospect->id }}, '{{ addslashes($prospect->prenom . ' ' . $prospect->nom) }}', '{{ $prospect->status }}')"
                                                class="text-slate-400 hover:text-indigo-600 p-1 rounded-lg hover:bg-slate-100 transition" title="Changer le statut">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="text-xs text-slate-500 truncate mt-2">
                                        {{ $prospect->email }}
                                    </div>

                                    <!-- Categories Badges -->
                                    @if($prospect->categories->isNotEmpty())
                                        <div class="flex flex-wrap gap-1 mt-2.5">
                                            @foreach($prospect->categories->take(2) as $cat)
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 text-indigo-700">
                                                    {{ $cat->name }}
                                                </span>
                                            @endforeach
                                            @if($prospect->categories->count() > 2)
                                                <span class="text-[10px] text-slate-400 font-semibold">+{{ $prospect->categories->count() - 2 }}</span>
                                            @endif
                                        </div>
                                    @endif

                                    <!-- Notes snippet if present -->
                                    @if($prospect->notes)
                                        <div class="mt-2 pt-2 border-t border-slate-100 text-[11px] text-slate-500 italic line-clamp-2" title="{{ $prospect->notes }}">
                                            "{{ Str::limit($prospect->notes, 60) }}"
                                        </div>
                                    @endif
                                </a>
                            @empty
                                <div class="p-6 text-center text-xs font-semibold text-slate-400 border border-dashed border-slate-200 rounded-xl bg-white/50">
                                    Aucun prospect
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- TABLE VIEW -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200/80 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3.5 px-4">Prospect</th>
                                <th class="py-3.5 px-4">Entreprise / Poste</th>
                                <th class="py-3.5 px-4">Statut Actuel</th>
                                <th class="py-3.5 px-4">Listes</th>
                                <th class="py-3.5 px-4">Pays</th>
                                <th class="py-3.5 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($contacts as $contact)
                                @php
                                    $meta = $statuses[$contact->status] ?? [
                                        'label' => $contact->status ?? 'Inconnu',
                                        'badge' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        'dot' => 'bg-slate-400'
                                    ];
                                @endphp
                                <tr class="hover:bg-slate-50/60 transition">
                                    <!-- Prospect Name & Email -->
                                    <td class="py-3.5 px-4">
                                        <a href="{{ route('prospects.show', $contact) }}" class="group">
                                            <div class="font-bold text-slate-900 group-hover:text-indigo-600 transition">
                                                {{ $contact->prenom }} {{ $contact->nom }}
                                            </div>
                                            <div class="text-xs text-slate-500">
                                                {{ $contact->email }}
                                            </div>
                                        </a>
                                    </td>

                                    <!-- Company / Role -->
                                    <td class="py-3.5 px-4">
                                        <div class="font-medium text-slate-700">
                                            {{ $contact->entreprise ?? '-' }}
                                        </div>
                                        <div class="text-xs text-slate-400">
                                            {{ $contact->fonction ?? '' }}
                                        </div>
                                    </td>

                                    <!-- Status Badge with Selector -->
                                    <td class="py-3.5 px-4">
                                        <form action="{{ route('prospects.update-status', $contact) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" onchange="this.form.submit()"
                                                    class="py-1 px-2.5 rounded-full text-xs font-bold border transition cursor-pointer focus:ring-2 focus:ring-indigo-500 {{ $meta['badge'] }}">
                                                @foreach($statuses as $stKey => $stMeta)
                                                    <option value="{{ $stKey }}" {{ $contact->status === $stKey ? 'selected' : '' }}>
                                                        {{ $stMeta['label'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>

                                    <!-- Lists / Categories -->
                                    <td class="py-3.5 px-4">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($contact->categories as $cat)
                                                <span class="px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700">
                                                    {{ $cat->name }}
                                                </span>
                                            @empty
                                                <span class="text-xs text-slate-400 italic">Aucune</span>
                                            @endforelse
                                        </div>
                                    </td>

                                    <!-- Country -->
                                    <td class="py-3.5 px-4 text-xs font-medium text-slate-600">
                                        {{ $contact->pays ?? '-' }}
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button @click="openStatusModal({{ $contact->id }}, '{{ addslashes($contact->prenom . ' ' . $contact->nom) }}', '{{ $contact->status }}')"
                                                    class="px-2.5 py-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                                                Qualifier / Note
                                            </button>
                                            <a href="{{ route('contacts.edit', $contact) }}" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg transition" title="Éditer le contact">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400">
                                        Aucun prospect ne correspond aux critères.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($contacts && $contacts->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $contacts->links() }}
                    </div>
                @endif
            </div>
        @endif

        <!-- Quick Status & Note Modal -->
        <div x-show="statusModalOpen" x-transition class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" style="display: none;">
            <div @click.away="statusModalOpen = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 relative">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <h3 class="text-lg font-extrabold text-slate-900">Qualifier le prospect</h3>
                    <button @click="statusModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form :action="'/prospects/' + selectedContactId + '/status'" method="POST" class="mt-4 space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Prospect</label>
                        <div class="font-bold text-slate-900 text-base" x-text="selectedContactName"></div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Nouveau Statut</label>
                        <select name="status" x-model="selectedStatus" class="w-full py-2.5 text-sm border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 bg-slate-50/50 font-semibold">
                            @foreach($statuses as $stKey => $stMeta)
                                <option value="{{ $stKey }}">{{ $stMeta['label'] }} — {{ $stMeta['description'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Note de suivi (Optionnel)</label>
                        <textarea name="note" x-model="noteText" rows="3" placeholder="Ajoutez un commentaire sur cette qualification ou l'échange réalisé..."
                                  class="w-full p-3 text-sm border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 bg-slate-50/50"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="statusModalOpen = false" class="px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                            Annuler
                        </button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md transition">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
