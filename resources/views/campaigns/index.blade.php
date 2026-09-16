<x-app-layout>
    <div class="p-6 space-y-6" x-data="{ search: '', statusFilter: 'all' }">
        <!-- Header Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-amber-500 rounded-l-2xl"></div>
            
            <div class="flex items-start sm:items-center gap-4 pl-2">
                <div class="p-3.5 bg-[#03123F] text-amber-400 rounded-2xl shadow-md shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-2xl font-black text-[#03123F] tracking-tight">
                            @if($isAdmin)
                                Toutes les campagnes
                            @else
                                Mes campagnes
                            @endif
                        </h1>
                        @if($isAdmin)
                            <span class="text-xs font-bold px-2.5 py-0.5 bg-amber-50 text-amber-700 rounded-full border border-amber-200/80 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                Vue Admin
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-slate-500 mt-1">
                        @if($isAdmin)
                            Supervision globale de toutes les campagnes email marketing de l'organisation.
                        @else
                            Créez, planifiez et mesurez l'impact de vos campagnes email marketing CAEI.
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('campaigns.create') }}" 
                   class="inline-flex items-center gap-2.5 text-white font-bold text-sm px-5 py-3 rounded-xl shadow-md transition-all duration-200 hover:shadow-lg hover:scale-[1.02] active:scale-[0.98]"
                   style="background: linear-gradient(135deg, #C57A1E 0%, #E5983B 100%);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Nouvelle campagne</span>
                </a>
            </div>
        </div>

        <!-- Flash messages -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition class="flex items-center justify-between gap-3 bg-emerald-50 border border-emerald-200 text-emerald-900 p-4 rounded-xl text-sm font-medium shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-1.5 bg-emerald-500 text-white rounded-lg shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
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

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition class="flex items-center justify-between gap-3 bg-rose-50 border border-rose-200 text-rose-900 p-4 rounded-xl text-sm font-medium shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-1.5 bg-rose-500 text-white rounded-lg shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <span>{{ session('error') }}</span>
                </div>
                <button @click="show = false" class="text-rose-500 hover:text-rose-700 p-1 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        <!-- Quick Executive Stats Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Total Card -->
            <div @click="statusFilter = 'all'" :class="statusFilter === 'all' ? 'ring-2 ring-indigo-500 border-indigo-300' : ''" class="cursor-pointer bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between transition-all hover:border-indigo-300 hover:shadow-md">
                <div class="space-y-1">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Total</div>
                    <div class="text-2xl font-black text-[#03123F]">{{ $stats['total'] ?? 0 }}</div>
                </div>
                <div class="p-3 bg-[#03123F]/5 text-[#03123F] rounded-xl shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>

            <!-- Brouillons Card -->
            <div @click="statusFilter = 'brouillon'" :class="statusFilter === 'brouillon' ? 'ring-2 ring-slate-500 border-slate-400' : ''" class="cursor-pointer bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between transition-all hover:border-slate-400 hover:shadow-md">
                <div class="space-y-1">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Brouillons</div>
                    <div class="text-2xl font-black text-slate-800">{{ $stats['brouillon'] ?? 0 }}</div>
                </div>
                <div class="p-3 bg-slate-100 text-slate-600 rounded-xl shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
            </div>

            <!-- En Cours Card -->
            <div @click="statusFilter = 'en_cours'" :class="statusFilter === 'en_cours' ? 'ring-2 ring-amber-500 border-amber-400' : ''" class="cursor-pointer bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between transition-all hover:border-amber-400 hover:shadow-md">
                <div class="space-y-1">
                    <div class="text-xs font-bold uppercase tracking-wider text-amber-600 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        En cours
                    </div>
                    <div class="text-2xl font-black text-amber-900">{{ $stats['en_cours'] ?? 0 }}</div>
                </div>
                <div class="p-3 bg-amber-50 text-amber-600 rounded-xl shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Programmées Card -->
            <div @click="statusFilter = 'programmee'" :class="statusFilter === 'programmee' ? 'ring-2 ring-purple-500 border-purple-400' : ''" class="cursor-pointer bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between transition-all hover:border-purple-400 hover:shadow-md">
                <div class="space-y-1">
                    <div class="text-xs font-bold uppercase tracking-wider text-purple-600">Programmées</div>
                    <div class="text-2xl font-black text-purple-900">{{ $stats['programmee'] ?? 0 }}</div>
                </div>
                <div class="p-3 bg-purple-50 text-purple-600 rounded-xl shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>

            <!-- Envoyées Card -->
            <div @click="statusFilter = 'envoyee'" :class="statusFilter === 'envoyee' ? 'ring-2 ring-emerald-500 border-emerald-400' : ''" class="cursor-pointer bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between transition-all hover:border-emerald-400 hover:shadow-md">
                <div class="space-y-1">
                    <div class="text-xs font-bold uppercase tracking-wider text-emerald-600">Envoyées</div>
                    <div class="text-2xl font-black text-emerald-900">{{ $stats['envoyee'] ?? 0 }}</div>
                </div>
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Main Card & Toolbar -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <!-- Toolbar: Search & Filter Tabs -->
            <div class="p-4 sm:p-5 bg-slate-50/70 border-b border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-4">
                <!-- Status Filter Pills -->
                <div class="flex items-center gap-1.5 bg-slate-200/60 p-1 rounded-xl w-full sm:w-auto overflow-x-auto text-xs font-bold">
                    <button @click="statusFilter = 'all'" 
                            :class="statusFilter === 'all' ? 'bg-white text-[#03123F] shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                            class="px-3 py-1.5 rounded-lg transition shrink-0">
                        Toutes ({{ $stats['total'] ?? 0 }})
                    </button>
                    <button @click="statusFilter = 'brouillon'" 
                            :class="statusFilter === 'brouillon' ? 'bg-white text-[#03123F] shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                            class="px-3 py-1.5 rounded-lg transition shrink-0">
                        Brouillons ({{ $stats['brouillon'] ?? 0 }})
                    </button>
                    <button @click="statusFilter = 'en_cours'" 
                            :class="statusFilter === 'en_cours' ? 'bg-white text-amber-700 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                            class="px-3 py-1.5 rounded-lg transition shrink-0">
                        En cours ({{ $stats['en_cours'] ?? 0 }})
                    </button>
                    <button @click="statusFilter = 'programmee'" 
                            :class="statusFilter === 'programmee' ? 'bg-white text-purple-700 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                            class="px-3 py-1.5 rounded-lg transition shrink-0">
                        Programmées ({{ $stats['programmee'] ?? 0 }})
                    </button>
                    <button @click="statusFilter = 'envoyee'" 
                            :class="statusFilter === 'envoyee' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                            class="px-3 py-1.5 rounded-lg transition shrink-0">
                        Envoyées ({{ $stats['envoyee'] ?? 0 }})
                    </button>
                </div>

                <!-- Live Search Box -->
                <div class="relative w-full sm:w-72">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           x-model="search" 
                           placeholder="Rechercher une campagne..." 
                           class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-[#03123F] focus:border-transparent outline-none transition placeholder-slate-400">
                    <button x-show="search.length > 0" @click="search = ''" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100/70 border-b border-slate-200/80 text-[11px] uppercase tracking-wider font-extrabold text-slate-500">
                            <th class="px-6 py-3.5">Nom de la Campagne</th>
                            <th class="px-6 py-3.5">Objet de l'email</th>
                            @if($isAdmin)
                                <th class="px-6 py-3.5">Créateur</th>
                            @endif
                            <th class="px-6 py-3.5">Audience Cible</th>
                            <th class="px-6 py-3.5">Statut</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        @forelse($campaigns as $campaign)
                            @php
                                $campaignSearchKey = strtolower(addslashes($campaign->nom . ' ' . $campaign->objet));
                            @endphp
                            <tr x-show="(statusFilter === 'all' || statusFilter === '{{ $campaign->statut }}') && ('{{ $campaignSearchKey }}'.includes(search.toLowerCase()))"
                                class="hover:bg-slate-50/80 transition duration-150">
                                
                                <!-- Nom de la Campagne -->
                                <td class="px-6 py-4 font-bold text-slate-900">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2.5 bg-[#03123F]/5 text-[#03123F] rounded-xl shrink-0 border border-[#03123F]/10">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <a href="{{ route('campaigns.preview', $campaign) }}" class="hover:text-amber-600 transition truncate max-w-xs block font-extrabold text-[#03123F]" title="{{ $campaign->nom }}">
                                                {{ $campaign->nom }}
                                            </a>
                                            <span class="text-[11px] font-normal text-slate-400 block mt-0.5">
                                                Créée le {{ $campaign->created_at?->format('d/m/Y à H:i') ?? '—' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Objet -->
                                <td class="px-6 py-4 text-slate-600">
                                    <span class="truncate max-w-xs block text-xs font-medium" title="{{ $campaign->objet }}">
                                        {{ $campaign->objet }}
                                    </span>
                                </td>

                                <!-- Créateur (Admin) -->
                                @if($isAdmin)
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-lg">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            {{ $campaign->creator?->name ?? '—' }}
                                        </span>
                                    </td>
                                @endif

                                <!-- Audience Cible -->
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        @php
                                            $targetLabels = [];
                                            if ($campaign->importLog) {
                                                $targetLabels[] = 'Import: ' . $campaign->importLog->filename;
                                            } else {
                                                foreach ($campaign->categoryIds() as $categoryId) {
                                                    $category = \App\Models\Category::find($categoryId);
                                                    if ($category) {
                                                        $targetLabels[] = $category->name;
                                                    }
                                                }
                                            }
                                        @endphp
                                        <span class="truncate max-w-[180px]">
                                            {{ $targetLabels !== [] ? implode(', ', $targetLabels) : 'Tous les contacts' }}
                                        </span>
                                    </span>
                                </td>

                                <!-- Statut -->
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold border transition shadow-2xs
                                        @if($campaign->statut === 'brouillon') bg-slate-100 text-slate-700 border-slate-200
                                        @elseif($campaign->statut === 'programmee') bg-purple-50 text-purple-700 border-purple-200
                                        @elseif($campaign->statut === 'envoyee')
                                            @if(($campaign->failed_count ?? 0) > 0) bg-rose-50 text-rose-700 border-rose-200
                                            @else bg-emerald-50 text-emerald-700 border-emerald-200 @endif
                                        @elseif($campaign->statut === 'annulee') bg-rose-50 text-rose-700 border-rose-200
                                        @else bg-amber-50 text-amber-700 border-amber-200 @endif">
                                        <span class="w-2 h-2 rounded-full
                                            @if($campaign->statut === 'brouillon') bg-slate-400
                                            @elseif($campaign->statut === 'programmee') bg-purple-500 animate-pulse
                                            @elseif($campaign->statut === 'envoyee')
                                                @if(($campaign->failed_count ?? 0) > 0) bg-rose-500
                                                @else bg-emerald-500 @endif
                                            @elseif($campaign->statut === 'annulee') bg-rose-500
                                            @else bg-amber-500 animate-pulse @endif"></span>
                                        <span>
                                            @if($campaign->statut === 'brouillon') Brouillon
                                            @elseif($campaign->statut === 'programmee')
                                                📅 {{ $campaign->date_envoi?->format('d/m à H:i') ?? 'Programmée' }}
                                            @elseif($campaign->statut === 'envoyee')
                                                @if(($campaign->failed_count ?? 0) > 0) 
                                                    Envoyée ({{ $campaign->failed_count }} échec{{ $campaign->failed_count > 1 ? 's' : '' }})
                                                @else Envoyée @endif
                                            @elseif($campaign->statut === 'annulee') Annulée
                                            @else En cours... @endif
                                        </span>
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Relancer échecs -->
                                        @if(($campaign->failed_count ?? 0) > 0 && $campaign->statut !== 'annulee')
                                            <form action="{{ route('campaigns.retry-failed', $campaign) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir relancer les {{ $campaign->failed_count }} email(s) en échec ?')">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-extrabold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg border border-rose-200 transition shadow-2xs" title="Relancer les emails en échec">
                                                    <svg class="w-3.5 h-3.5 text-rose-600 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                    </svg>
                                                    <span>Relancer ({{ $campaign->failed_count }})</span>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Statistiques -->
                                        <a href="{{ route('statistics.index', ['campaign_id' => $campaign->id]) }}" 
                                           class="p-2 text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition" 
                                           title="Voir les statistiques de cette campagne">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                            </svg>
                                        </a>

                                        <!-- Aperçu -->
                                        <a href="{{ route('campaigns.preview', $campaign) }}" 
                                           class="p-2 text-slate-500 hover:text-[#03123F] hover:bg-slate-100 rounded-xl transition" 
                                           title="Aperçu du rendu HTML">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>

                                        <!-- Modifier -->
                                        @if($campaign->statut !== 'annulee')
                                            <a href="{{ route('campaigns.edit', $campaign) }}" 
                                               class="p-2 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition" 
                                               title="Modifier la campagne">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                        @endif

                                        <!-- Annuler -->
                                        @if(in_array($campaign->statut, ['brouillon', 'en_cours', 'programmee']))
                                            <form action="{{ route('campaigns.cancel', $campaign) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette campagne ? L\'envoi des emails restants sera stoppé.')">
                                                @csrf
                                                <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition" title="Annuler la campagne">
                                                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Supprimer -->
                                        @if($campaign->statut !== 'en_cours')
                                            <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer la campagne « {{ addslashes($campaign->nom) }} » ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition" title="Supprimer la campagne">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isAdmin ? 6 : 5 }}" class="px-6 py-16 text-center text-slate-400 bg-white">
                                    <div class="w-16 h-16 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-200/80 shadow-xs">
                                        <svg class="w-8 h-8 text-[#03123F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-extrabold text-[#03123F]">Aucune campagne trouvée</h3>
                                    <p class="text-xs text-slate-500 max-w-xs mx-auto mt-1">Créez votre première campagne d'emails en cliquant sur le bouton « Nouvelle campagne » ci-dessus.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($campaigns->hasPages())
            <div class="pt-2">
                {{ $campaigns->links() }}
            </div>
        @endif
    </div>
</x-app-layout>