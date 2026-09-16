<x-app-layout>
    <div class="py-8 bg-slate-50 min-h-screen text-slate-800 font-sans">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- ── Header & Action Buttons ────────────────────────────────────────── --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-4 border-b border-slate-200/80 bg-white p-6 rounded-2xl border shadow-sm">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-[#03123F] flex items-center gap-3">
                        <span class="w-3 h-8 rounded-full bg-gradient-to-b from-[#D9822B] via-[#C57A1E] to-[#B86D18] inline-block shadow-sm"></span>
                        Gestion de la Base Contacts
                    </h1>
                    <p class="text-sm text-slate-500 mt-1">
                        Organisez, segmentez et suivez l'engagement de votre audience pour vos campagnes de mailing
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    @if(Route::has('contacts.export'))
                        <a href="{{ route('contacts.export', request()->all()) }}"
                           title="Exporter la liste actuelle au format CSV"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-[#C57A1E] bg-amber-50 hover:bg-amber-100/80 border border-amber-200 transition-all duration-200 shadow-sm">
                            <svg class="w-4 h-4 text-[#C57A1E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Exporter les contacts (CSV)</span>
                        </a>
                    @endif

                    <a href="{{ route('contacts.import-history') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 border border-slate-200/80 rounded-xl text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 transition-all duration-200 shadow-sm">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Historique des imports</span>
                    </a>

                    <a href="{{ route('contacts.import.upload') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 border border-slate-200/80 rounded-xl text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 transition-all duration-200 shadow-sm">
                        <svg class="w-4 h-4 text-[#C57A1E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Importer CSV/Excel</span>
                    </a>

                    <a href="{{ route('contacts.create') }}"
                       class="inline-flex items-center gap-2 bg-gradient-to-r from-[#D9822B] via-[#C57A1E] to-[#B86D18] hover:from-[#E58E26] hover:to-[#C57A1E] text-white text-xs font-extrabold px-4 py-2.5 rounded-xl shadow-md shadow-amber-600/20 transition-all duration-200 transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>+ Nouveau contact</span>
                    </a>
                </div>
            </div>

            {{-- ── Flash Notifications ────────────────────────────────────────────── --}}
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
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            @if(session('warning'))
                <div x-data="{ show: true }" x-show="show" x-transition class="flex items-center justify-between gap-3 bg-amber-50 border border-amber-200 text-amber-900 p-4 rounded-xl text-sm font-medium shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="p-1 bg-amber-100 rounded-lg text-amber-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                        </div>
                        <span>{{ session('warning') }}</span>
                    </div>
                    <button @click="show = false" class="text-amber-500 hover:text-amber-700 p-1 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition class="flex items-center justify-between gap-3 bg-rose-50 border border-rose-200 text-rose-900 p-4 rounded-xl text-sm font-medium shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="p-1 bg-rose-100 rounded-lg text-rose-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-rose-500 hover:text-rose-700 p-1 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            {{-- ── Context Banner : Import Filter Active ────────────────────────────── --}}
            @if(isset($importContext) && $importContext)
                <div class="flex items-center justify-between gap-4 bg-amber-50 border border-amber-200 text-amber-950 p-4 rounded-2xl text-sm font-medium shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-amber-100 rounded-xl text-[#C57A1E]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-extrabold text-[#03123F]">Filtrage par fichier d'import :</span>
                            <span class="font-semibold text-slate-800">{{ $importContext->filename }}</span>
                            <span class="ml-2 text-xs text-slate-500">({{ $importContext->created_at->format('d/m/Y \à H:i') }} &bull; {{ $importContext->imported }} contacts)</span>
                        </div>
                    </div>
                    <a href="{{ route('contacts.import-history') }}" class="text-xs text-[#C57A1E] hover:text-amber-800 font-bold flex items-center gap-1">
                        &larr; Retour à l'historique d'import
                    </a>
                </div>
            @endif

            {{-- ── 4 Top Stats Cards ─────────────────────────────────────────────── --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Stat 1 : Total Contacts -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 hover:border-[#C57A1E]/40 transition-all duration-300 shadow-sm group relative overflow-hidden">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total contacts</span>
                        <div class="p-2 bg-slate-100 rounded-xl text-[#03123F]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-black text-[#03123F] tracking-tight group-hover:text-[#C57A1E] transition-colors">
                        {{ number_format($totalContacts) }}
                    </div>
                    <div class="text-xs text-slate-500 mt-2 font-medium">Base de destinataires qualifiée</div>
                </div>

                <!-- Stat 2 : Listes & Catégories -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 hover:border-[#C57A1E]/40 transition-all duration-300 shadow-sm group relative overflow-hidden">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Listes & Catégories</span>
                        <div class="p-2 bg-amber-50 text-[#C57A1E] rounded-xl border border-amber-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 11h10M7 15h10"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-black text-slate-900 tracking-tight group-hover:text-[#C57A1E] transition-colors">
                        {{ $categories->count() }}
                    </div>
                    <div class="text-xs text-slate-500 mt-2 font-medium">Segments de ciblage actifs</div>
                </div>

                <!-- Stat 3 : Pays distincts -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 hover:border-[#C57A1E]/40 transition-all duration-300 shadow-sm group relative overflow-hidden">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pays distincts</span>
                        <div class="p-2 bg-sky-50 text-sky-600 rounded-xl border border-sky-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h1.5a2.5 2.5 0 002.5-2.5V7a2 2 0 00-2-2h-2c0-1.1.9-2 2-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-black text-slate-900 tracking-tight group-hover:text-sky-600 transition-colors">
                        {{ $paysOptions->count() }}
                    </div>
                    <div class="text-xs text-slate-500 mt-2 font-medium">Couverture géographique</div>
                </div>

                <!-- Stat 4 : Secteurs d'activité -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 hover:border-[#C57A1E]/40 transition-all duration-300 shadow-sm group relative overflow-hidden">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Secteurs d'activité</span>
                        <div class="p-2 bg-emerald-50 text-emerald-600 rounded-xl border border-emerald-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h5m-5 0V10m0 0V5"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-black text-slate-900 tracking-tight group-hover:text-emerald-600 transition-colors">
                        {{ $secteurOptions->count() }}
                    </div>
                    <div class="text-xs text-slate-500 mt-2 font-medium">Domaines d'entreprises</div>
                </div>
            </div>

            {{-- ── Filters Toolbar Card ────────────────────────────────────────── --}}
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                <form action="{{ route('contacts.index') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
                        <!-- Recherche -->
                        <div class="relative sm:col-span-2 lg:col-span-2">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4 text-[#C57A1E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="search" name="search" value="{{ request('search') }}"
                                   placeholder="Rechercher par nom, prénom, email, entreprise..."
                                   class="w-full text-sm pl-10 pr-4 py-2.5 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-[#C57A1E]/30 focus:border-[#C57A1E] bg-slate-50/70 text-slate-800 placeholder-slate-400 transition">
                        </div>

                        <!-- Liste / Catégorie -->
                        <div>
                            <select name="category_id" onchange="this.form.submit()"
                                    class="w-full text-sm py-2.5 px-3 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-[#C57A1E]/30 focus:border-[#C57A1E] bg-slate-50/70 text-slate-700 font-semibold transition">
                                <option value="">Toutes les listes</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>
                                        {{ $category->name }} ({{ $category->contacts_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Statut Prospect -->
                        <div>
                            <select name="status" onchange="this.form.submit()"
                                    class="w-full text-sm py-2.5 px-3 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-[#C57A1E]/30 focus:border-[#C57A1E] bg-slate-50/70 text-slate-700 font-semibold transition">
                                <option value="">Tous les statuts CRM</option>
                                @foreach($statusOptions as $key => $st)
                                    <option value="{{ $key }}" @selected(request('status') === $key)>
                                        {{ $st['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Pays -->
                        <div>
                            <select name="pays" onchange="this.form.submit()"
                                    class="w-full text-sm py-2.5 px-3 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-[#C57A1E]/30 focus:border-[#C57A1E] bg-slate-50/70 text-slate-700 font-semibold transition">
                                <option value="">Tous les pays</option>
                                @foreach($paysOptions as $pays)
                                    <option value="{{ $pays }}" @selected(request('pays') === $pays)>
                                        {{ $pays }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-slate-100">
                        <div class="flex items-center gap-3">
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2 bg-gradient-to-r from-[#D9822B] via-[#C57A1E] to-[#B86D18] hover:from-[#E58E26] hover:to-[#C57A1E] text-white text-xs font-extrabold rounded-xl transition shadow-md shadow-amber-600/10">
                                <span>Filtrer les contacts</span>
                            </button>

                            @if(request()->hasAny(['search', 'category_id', 'pays', 'secteur_activite', 'status']))
                                <a href="{{ route('contacts.index') }}"
                                   class="px-3 py-2 text-slate-500 hover:text-slate-800 text-xs font-semibold flex items-center gap-1.5 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Réinitialiser les filtres
                                </a>
                            @endif
                        </div>

                        <div class="text-xs text-slate-500 font-semibold">
                            @if($contacts && $contacts->total() > 0)
                                Affichage de <span class="font-extrabold text-[#03123F]">{{ $contacts->firstItem() }}</span> à <span class="font-extrabold text-[#03123F]">{{ $contacts->lastItem() }}</span> sur <span class="font-extrabold text-[#03123F]">{{ number_format($contacts->total()) }}</span> contact(s)
                            @else
                                Aucun contact trouvé
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            {{-- ── Contacts Data Table Card ────────────────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50/80 text-[11px] uppercase font-bold text-slate-500 border-b border-slate-200/80 tracking-wider">
                            <tr>
                                <th class="px-6 py-4">Contact</th>
                                <th class="px-6 py-4">Entreprise &amp; Poste</th>
                                <th class="px-6 py-4">Statut CRM</th>
                                <th class="px-6 py-4">Coordonnées</th>
                                <th class="px-6 py-4">Localisation</th>
                                <th class="px-6 py-4">Listes / Catégories</th>
                                <th class="px-6 py-4">Date d'ajout</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($contacts as $contact)
                                @php
                                    $initials = strtoupper(substr($contact->prenom ?? '', 0, 1) . substr($contact->nom ?? '', 0, 1));
                                    if (!$initials) $initials = 'CT';

                                    $bgGradients = [
                                        'from-[#03123F] to-[#1E3A8A]',
                                        'from-[#D9822B] to-[#C57A1E]',
                                        'from-emerald-600 to-teal-700',
                                        'from-purple-600 to-indigo-700',
                                    ];
                                    $bgGradient = $bgGradients[abs(crc32($contact->email)) % 4];

                                    $stMeta = $statusOptions[$contact->prospect_status] ?? [
                                        'label' => $contact->prospect_status ?? 'Nouveau prospect',
                                        'badge' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        'dot'   => 'bg-slate-400'
                                    ];
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition group">
                                    <!-- Contact Name & Avatar -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $bgGradient }} text-white font-extrabold text-xs flex items-center justify-center shadow-sm shrink-0 ring-2 ring-white">
                                                {{ $initials }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 group-hover:text-[#C57A1E] transition">
                                                    {{ $contact->prenom }} {{ $contact->nom }}
                                                </div>
                                                @if($contact->source)
                                                    <div class="text-[11px] text-slate-400 mt-0.5">Source : {{ $contact->source }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Company & Role -->
                                    <td class="px-6 py-4">
                                        @if($contact->entreprise || $contact->fonction)
                                            <div class="font-bold text-slate-800">{{ $contact->entreprise ?: '-' }}</div>
                                            <div class="text-xs text-slate-500 font-medium mt-0.5">{{ $contact->fonction ?: ($contact->secteur_activite ?: '') }}</div>
                                        @else
                                            <span class="text-slate-400 italic text-xs">-</span>
                                        @endif
                                    </td>

                                    <!-- Statut Prospect -->
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border {{ $stMeta['badge'] }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $stMeta['dot'] }}"></span>
                                            {{ $stMeta['label'] }}
                                        </span>
                                    </td>

                                    <!-- Coordonnées -->
                                    <td class="px-6 py-4 space-y-1">
                                        <div class="flex items-center gap-1.5 text-slate-700">
                                            <svg class="w-3.5 h-3.5 text-[#C57A1E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                            <a href="mailto:{{ $contact->email }}" class="hover:text-[#C57A1E] font-medium truncate max-w-[200px]" title="{{ $contact->email }}">
                                                {{ $contact->email }}
                                            </a>
                                        </div>
                                        @if($contact->whatsapp || $contact->telephone)
                                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                                @if($contact->whatsapp)
                                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact->whatsapp) }}" target="_blank"
                                                       class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-800 font-bold" title="Ouvrir WhatsApp">
                                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-1.156 4.221 4.239-1.111z"/>
                                                        </svg>
                                                        {{ $contact->whatsapp }}
                                                    </a>
                                                @elseif($contact->telephone)
                                                    <span class="font-medium">{{ $contact->telephone }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Localisation -->
                                    <td class="px-6 py-4 text-xs font-bold text-slate-700">
                                        @if($contact->pays || $contact->ville)
                                            <div>{{ $contact->pays ?: '-' }}</div>
                                            <div class="text-slate-400 font-medium text-[11px]">{{ $contact->ville ?: '' }}</div>
                                        @else
                                            <span class="text-slate-400 font-normal italic">-</span>
                                        @endif
                                    </td>

                                    <!-- Listes / Catégories -->
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1 max-w-xs">
                                            @forelse($contact->categories as $cat)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-[#C57A1E] border border-amber-200/80">
                                                    {{ $cat->name }}
                                                </span>
                                            @empty
                                                <span class="text-xs text-slate-400 italic">Aucune liste</span>
                                            @endforelse
                                        </div>
                                    </td>

                                    <!-- Date d'ajout -->
                                    <td class="px-6 py-4 text-xs text-slate-500 font-medium">
                                        {{ $contact->created_at ? $contact->created_at->format('d/m/Y') : '-' }}
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('contacts.edit', $contact) }}"
                                               class="p-2 text-slate-500 hover:text-[#C57A1E] hover:bg-amber-50 rounded-xl transition border border-transparent hover:border-amber-200"
                                               title="Modifier le contact">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>

                                            <form action="{{ route('contacts.destroy', $contact) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce contact ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition border border-transparent hover:border-rose-200"
                                                        title="Supprimer le contact">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                        <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-base font-bold text-slate-800">Aucun contact trouvé</h3>
                                        <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">Aucun contact ne correspond à vos critères de recherche.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($contacts && $contacts->hasPages())
                    <div class="px-6 py-4 border-t border-slate-200/80 bg-slate-50/50">
                        {{ $contacts->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
