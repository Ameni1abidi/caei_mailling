<x-app-layout>
    <div class="py-8 bg-slate-50 min-h-screen text-slate-800 font-sans">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- ── Header & Title Card ───────────────────────────────────────────── --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-4 border-b border-slate-200/80 bg-white p-6 rounded-2xl border shadow-sm">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-[#03123F] flex items-center gap-3">
                        <span class="w-3 h-8 rounded-full bg-gradient-to-b from-[#D9822B] via-[#C57A1E] to-[#B86D18] inline-block shadow-sm"></span>
                        Statistiques & Performance
                    </h1>
                    <p class="text-sm text-slate-500 mt-1">
                        Analyse détaillée des délivrabilités, ouvertures, clics et comportement des prospects
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    @if($campaignId)
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-bold text-[#C57A1E] bg-amber-50 border border-amber-200 shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-[#C57A1E] animate-pulse"></span>
                            Filtre actif : Campagne #{{ $campaignId }}
                        </span>
                    @endif

                    <a href="{{ route('campaigns.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 transition-all duration-200 shadow-sm">
                        <svg class="w-4 h-4 text-[#C57A1E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                        </svg>
                        Retour aux campagnes
                    </a>
                </div>
            </div>

            {{-- ── Filters Toolbar Card ────────────────────────────────────────── --}}
            <form method="GET" action="{{ route('statistics.index') }}"
                  class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1">
                        <!-- Période -->
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wider flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-[#C57A1E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Période d'analyse
                            </label>
                            <select name="period"
                                    class="w-full rounded-xl border border-slate-200/80 bg-slate-50/80 px-3.5 py-2.5 text-sm font-semibold text-slate-700 focus:bg-white focus:ring-2 focus:ring-[#C57A1E]/30 focus:border-[#C57A1E] focus:outline-none transition-all">
                                @foreach(['7' => '7 derniers jours', '30' => '30 derniers jours', '90' => '90 derniers jours', '365' => 'Cette année', 'all' => 'Toutes les données (Historique complet)'] as $val => $label)
                                    <option value="{{ $val }}" {{ $period === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Campagne -->
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wider flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-[#C57A1E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Filtrer par campagne
                            </label>
                            <select name="campaign_id"
                                    class="w-full rounded-xl border border-slate-200/80 bg-slate-50/80 px-3.5 py-2.5 text-sm font-semibold text-slate-700 focus:bg-white focus:ring-2 focus:ring-[#C57A1E]/30 focus:border-[#C57A1E] focus:outline-none transition-all">
                                <option value="">— Toutes les campagnes —</option>
                                @foreach($campaigns as $c)
                                    <option value="{{ $c->id }}" {{ (string)$campaignId === (string)$c->id ? 'selected' : '' }}>
                                        {{ $c->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex items-center gap-3">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-[#D9822B] via-[#C57A1E] to-[#B86D18] hover:from-[#E58E26] hover:to-[#C57A1E] text-white text-sm font-extrabold rounded-xl transition-all duration-200 shadow-md shadow-amber-600/20 transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Appliquer le filtre
                        </button>

                        @if($period !== '30' || $campaignId)
                            <a href="{{ route('statistics.index') }}"
                               class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition shadow-sm">
                                Réinitialiser
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            {{-- ── 5 Top KPI Cards ─────────────────────────────────────────────── --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- KPI 1 : Envoyés -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 hover:border-[#C57A1E]/40 transition-all duration-300 shadow-sm group">
                    <div class="flex items-center justify-between mb-2.5">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Envoyés</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-[#03123F]"></span>
                    </div>
                    <div class="text-3xl font-black text-[#03123F] tracking-tight group-hover:text-[#C57A1E] transition-colors">
                        {{ number_format($totalSent) }}
                    </div>
                    <div class="text-[11px] text-slate-500 mt-2 font-medium flex items-center gap-1">
                        <span class="font-bold text-emerald-600">{{ $deliveryRate }}%</span> taux de livraison
                    </div>
                </div>

                <!-- KPI 2 : Ouverts -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 hover:border-[#C57A1E]/40 transition-all duration-300 shadow-sm group">
                    <div class="flex items-center justify-between mb-2.5">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Ouverts</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-[#0EA5E9]"></span>
                    </div>
                    <div class="text-3xl font-black text-slate-900 tracking-tight group-hover:text-[#0EA5E9] transition-colors">
                        {{ number_format($totalOpened) }}
                    </div>
                    <div class="text-[11px] text-slate-500 mt-2 font-medium flex items-center gap-1">
                        <span class="font-bold text-[#0EA5E9]">{{ $openRate }}%</span> taux d'ouverture
                    </div>
                </div>

                <!-- KPI 3 : Clics -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 hover:border-[#C57A1E]/40 transition-all duration-300 shadow-sm group">
                    <div class="flex items-center justify-between mb-2.5">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Clics</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                    </div>
                    <div class="text-3xl font-black text-slate-900 tracking-tight group-hover:text-purple-600 transition-colors">
                        {{ number_format($totalClicked) }}
                    </div>
                    <div class="text-[11px] text-slate-500 mt-2 font-medium flex items-center gap-1">
                        <span class="font-bold text-purple-600">{{ $clickRate }}%</span> taux de clic
                    </div>
                </div>

                <!-- KPI 4 : Rebonds -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 hover:border-[#C57A1E]/40 transition-all duration-300 shadow-sm group">
                    <div class="flex items-center justify-between mb-2.5">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Rebonds</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    </div>
                    <div class="text-3xl font-black text-slate-900 tracking-tight group-hover:text-amber-600 transition-colors">
                        {{ number_format($totalBounced) }}
                    </div>
                    <div class="text-[11px] text-slate-500 mt-2 font-medium flex items-center gap-1">
                        <span class="font-bold text-amber-600">{{ $bounceRate }}%</span> taux de rebond
                    </div>
                </div>

                <!-- KPI 5 : Rejetés / Erreurs -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 hover:border-[#C57A1E]/40 transition-all duration-300 shadow-sm group">
                    <div class="flex items-center justify-between mb-2.5">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Rejetés</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                    </div>
                    <div class="text-3xl font-black text-slate-900 tracking-tight group-hover:text-rose-600 transition-colors">
                        {{ number_format($totalRejected) }}
                    </div>
                    <div class="text-[11px] text-slate-500 mt-2 font-medium">
                        {{ $totalFailed + $totalInvalid }} échecs / invalides
                    </div>
                </div>
            </div>

            {{-- ── Charts Row : Donut + Line Chart ─────────────────────────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Donut Chart : Répartition Globale -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-[#03123F] tracking-wide mb-1">Répartition des emails</h3>
                        <p class="text-xs text-slate-500 mb-4">Volume global des statuts de délivrabilité</p>

                        <div class="relative flex items-center justify-center my-4" style="height:210px">
                            <canvas id="donutChart"></canvas>
                            <div class="absolute text-center pointer-events-none">
                                <div class="text-3xl font-black text-[#03123F]">{{ number_format($totalAll) }}</div>
                                <div class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">emails au total</div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 space-y-2">
                        @php
                            $donutItems = [
                                ['label' => 'Envoyés avec succès', 'value' => $totalSent,                   'color' => '#03123F'],
                                ['label' => 'Emails Ouverts',       'value' => $totalOpened,                 'color' => '#0EA5E9'],
                                ['label' => 'Liens Cliqués',        'value' => $totalClicked,                'color' => '#A855F7'],
                                ['label' => 'Rebonds (Bounced)',    'value' => $totalBounced,                'color' => '#F59E0B'],
                                ['label' => 'Échoués / Invalides', 'value' => $totalFailed + $totalInvalid, 'color' => '#EF4444'],
                            ];
                        @endphp
                        @foreach($donutItems as $item)
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:{{ $item['color'] }}"></span>
                                    <span class="text-slate-600 font-semibold">{{ $item['label'] }}</span>
                                </div>
                                <span class="font-extrabold text-slate-900">{{ number_format($item['value']) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Line Chart : Évolution Quotidienne -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                            <div>
                                <h3 class="text-lg font-bold text-[#03123F] tracking-wide">Évolution quotidienne des envois</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Tendance des emails distribués vs ouvertures et erreurs sur la période</p>
                            </div>
                            <div class="flex items-center gap-3 text-xs font-semibold">
                                <span class="flex items-center gap-1.5 text-slate-700">
                                    <span class="w-3 h-3 rounded-full bg-[#03123F]"></span> Envoyés
                                </span>
                                <span class="flex items-center gap-1.5 text-sky-600">
                                    <span class="w-3 h-3 rounded-full bg-[#0EA5E9]"></span> Ouverts
                                </span>
                                <span class="flex items-center gap-1.5 text-rose-600">
                                    <span class="w-3 h-3 rounded-full bg-rose-500"></span> Rejetés
                                </span>
                            </div>
                        </div>

                        @if($dailyEmails->isEmpty())
                            <div class="flex items-center justify-center h-64 text-slate-400 text-sm">
                                Aucune donnée d'envoi enregistrée pour la période sélectionnée.
                            </div>
                        @else
                            <div class="relative h-64 w-full">
                                <canvas id="lineChart"></canvas>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── Entonnoir de Qualification Prospects (CRM Funnel) ────────────── --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-lg font-bold text-[#03123F] tracking-wide flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#C57A1E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18l-7 8v6l-4 2v-8L3 4z"/>
                            </svg>
                            Entonnoir de qualification des prospects (CRM)
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Conversion progressive des prospects depuis l'importation brute jusqu'à la signature de contrat</p>
                    </div>

                    @if(Auth::user()?->hasRole('admin'))
                        <a href="{{ route('prospects.index') }}" class="text-xs font-bold text-[#C57A1E] hover:text-amber-700 transition">
                            Accéder au Kanban prospects &rarr;
                        </a>
                    @endif
                </div>

                @php
                    $funnelSteps = [
                        ['key' => 'Nouveau prospect', 'label' => '1. Nouveau prospect', 'color' => 'bg-slate-400'],
                        ['key' => 'Email envoyé',      'label' => '2. Email envoyé',    'color' => 'bg-blue-500'],
                        ['key' => 'Email ouvert',      'label' => '3. Email ouvert',    'color' => 'bg-sky-500'],
                        ['key' => 'Intéressé',         'label' => '4. Intéressé (Hot)', 'color' => 'bg-[#C57A1E]'],
                        ['key' => 'À relancer',        'label' => '5. À relancer',      'color' => 'bg-rose-500'],
                        ['key' => 'Client',            'label' => '6. Client signé',    'color' => 'bg-emerald-500'],
                    ];
                    $funnelTotal = max(1, $prospectFunnel->sum());
                @endphp

                <div class="space-y-4">
                    @foreach($funnelSteps as $step)
                        @php
                            $count = (int) ($prospectFunnel->get($step['key'], 0));
                            $pct   = round(($count / $funnelTotal) * 100, 1);
                        @endphp
                        <div class="flex items-center gap-4">
                            <div class="w-40 text-xs font-bold text-slate-700 text-right shrink-0">{{ $step['label'] }}</div>
                            <div class="flex-1 bg-slate-100 rounded-xl h-6 overflow-hidden p-0.5 border border-slate-200/60 relative">
                                <div class="{{ $step['color'] }} h-full rounded-lg transition-all duration-700"
                                     style="width: {{ max(2, $pct) }}%"></div>
                            </div>
                            <div class="w-28 text-xs font-black text-slate-900 flex items-center justify-between">
                                <span>{{ number_format($count) }}</span>
                                <span class="text-slate-500 font-semibold text-[11px] bg-slate-100 px-2 py-0.5 rounded-md">{{ $pct }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Statistiques Détaillées par Campagne (Tableau) ──────────────── --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-[#03123F] tracking-wide flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#C57A1E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Statistiques Détaillées par Campagne
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Performance individuelle de chaque campagne d'emailing</p>
                    </div>

                    <span class="text-xs font-semibold text-slate-500">
                        Total : {{ $perCampaign->total() }} campagne(s)
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50/80 text-[11px] uppercase font-bold text-slate-500 border-b border-slate-200/80 tracking-wider">
                            <tr>
                                <th class="px-6 py-3.5">Campagne</th>
                                <th class="px-4 py-3.5 text-center">Statut</th>
                                <th class="px-4 py-3.5 text-center">Total Contacts</th>
                                <th class="px-4 py-3.5 text-center">Envoyés</th>
                                <th class="px-4 py-3.5 text-center">Ouverts</th>
                                <th class="px-4 py-3.5 text-center">Clics</th>
                                <th class="px-4 py-3.5 text-center">Rebonds</th>
                                <th class="px-4 py-3.5 text-center">Échecs</th>
                                <th class="px-4 py-3.5 text-center">Taux Ouverture</th>
                                <th class="px-5 py-3.5 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($perCampaign as $c)
                                @php
                                    $openPct = $c->sent_count > 0 ? round(($c->opened_count / $c->sent_count) * 100, 1) : 0;
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition group">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-900 group-hover:text-[#C57A1E] transition">{{ $c->nom }}</div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">Créée le : {{ $c->created_at->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        @if($c->statut === 'envoyee')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Envoyée
                                            </span>
                                        @elseif($c->statut === 'en_cours')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 animate-pulse">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> En cours
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ ucfirst($c->statut) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-center font-extrabold text-slate-900">{{ number_format($c->total_count) }}</td>
                                    <td class="px-4 py-4 text-center font-bold text-[#03123F]">{{ number_format($c->sent_count) }}</td>
                                    <td class="px-4 py-4 text-center font-bold text-[#0EA5E9]">{{ number_format($c->opened_count) }}</td>
                                    <td class="px-4 py-4 text-center font-bold text-purple-600">{{ number_format($c->clicked_count) }}</td>
                                    <td class="px-4 py-4 text-center font-bold text-amber-600">{{ number_format($c->bounced_count) }}</td>
                                    <td class="px-4 py-4 text-center font-bold text-rose-600">{{ number_format($c->failed_count) }}</td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 font-extrabold text-xs">
                                            {{ $openPct }}%
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <a href="{{ route('statistics.index', ['campaign_id' => $c->id]) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg transition shadow-sm">
                                            <span>Filtrer</span>
                                            <svg class="w-3.5 h-3.5 text-[#C57A1E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-12 text-center text-slate-500">
                                        Aucune campagne trouvée pour cette période.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($perCampaign->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $perCampaign->links() }}
                    </div>
                @endif
            </div>

            {{-- ── Top 5 Domaines avec Rebonds ──────────────────────────────────── --}}
            @if($topBouncedDomains->isNotEmpty())
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                    <h3 class="text-lg font-bold text-[#03123F] tracking-wide mb-1 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Top 5 domaines d'emails ayant généré le plus de rebonds
                    </h3>
                    <p class="text-xs text-slate-500 mb-5">Permet d'identifier les fournisseurs de messagerie posant problème (spam, filtres d'entreprise)</p>

                    @php $maxBounce = $topBouncedDomains->first()->cnt; @endphp
                    <div class="space-y-3.5">
                        @foreach($topBouncedDomains as $domain)
                            @php $pct = round(($domain->cnt / $maxBounce) * 100); @endphp
                            <div class="flex items-center gap-4">
                                <div class="w-48 font-mono text-xs font-bold text-slate-700 text-right shrink-0 truncate">@ {{ $domain->domain }}</div>
                                <div class="flex-1 bg-slate-100 rounded-full h-4 overflow-hidden p-0.5 border border-slate-200/60">
                                    <div class="bg-gradient-to-r from-amber-400 to-rose-500 h-full rounded-full transition-all duration-700" style="width:{{ $pct }}%"></div>
                                </div>
                                <div class="text-xs font-black text-slate-900 w-16 text-right">{{ number_format($domain->cnt) }} err.</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- ── Chart.js Scripts ─────────────────────────────────────────────── --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ── Donut Chart ──────────────────────────────────────────────────
            const donutCtx = document.getElementById('donutChart');
            if (donutCtx) {
                new Chart(donutCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Envoyés avec succès', 'Ouverts', 'Cliqués', 'Rebondis', 'Échoués / Invalides'],
                        datasets: [{
                            data: [
                                {{ $totalSent }},
                                {{ $totalOpened }},
                                {{ $totalClicked }},
                                {{ $totalBounced }},
                                {{ $totalFailed + $totalInvalid }}
                            ],
                            backgroundColor: ['#03123F', '#0EA5E9', '#A855F7', '#F59E0B', '#EF4444'],
                            borderWidth: 0,
                            hoverOffset: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '76%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#03123F',
                                titleColor: '#FFFFFF',
                                bodyColor: '#CBD5E1',
                                borderColor: '#C57A1E',
                                borderWidth: 1,
                                padding: 10,
                                callbacks: {
                                    label: ctx => ` ${ctx.label}: ${ctx.parsed.toLocaleString()} emails`
                                }
                            }
                        },
                        animation: { animateRotate: true, duration: 800 }
                    }
                });
            }

            // ── Line Chart ───────────────────────────────────────────────────
            const lineCtx = document.getElementById('lineChart');
            if (lineCtx) {
                const ctx = lineCtx.getContext('2d');

                const gradBlue = ctx.createLinearGradient(0, 0, 0, 250);
                gradBlue.addColorStop(0, 'rgba(3, 18, 63, 0.25)');
                gradBlue.addColorStop(1, 'rgba(3, 18, 63, 0.0)');

                const gradSky = ctx.createLinearGradient(0, 0, 0, 250);
                gradSky.addColorStop(0, 'rgba(14, 165, 233, 0.2)');
                gradSky.addColorStop(1, 'rgba(14, 165, 233, 0.0)');

                const days   = @json($dailyEmails->pluck('day'));
                const sent   = @json($dailyEmails->pluck('sent'));
                const opened = @json($dailyEmails->pluck('opened'));
                const rej    = @json($dailyEmails->pluck('rejected'));

                new Chart(lineCtx, {
                    type: 'line',
                    data: {
                        labels: days,
                        datasets: [
                            {
                                label: 'Envoyés',
                                data: sent,
                                borderColor: '#03123F',
                                backgroundColor: gradBlue,
                                borderWidth: 3,
                                tension: 0.35,
                                fill: true,
                                pointBackgroundColor: '#03123F',
                                pointRadius: 4,
                                pointHoverRadius: 6
                            },
                            {
                                label: 'Ouverts',
                                data: opened,
                                borderColor: '#0EA5E9',
                                backgroundColor: gradSky,
                                borderWidth: 2,
                                tension: 0.35,
                                fill: true,
                                pointBackgroundColor: '#0EA5E9',
                                pointRadius: 3,
                                pointHoverRadius: 5
                            },
                            {
                                label: 'Rejetés',
                                data: rej,
                                borderColor: '#EF4444',
                                backgroundColor: 'transparent',
                                borderWidth: 2,
                                borderDash: [4, 4],
                                tension: 0.35,
                                pointBackgroundColor: '#EF4444',
                                pointRadius: 3,
                                pointHoverRadius: 5
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#03123F',
                                titleColor: '#FFFFFF',
                                bodyColor: '#CBD5E1',
                                borderColor: '#C57A1E',
                                borderWidth: 1,
                                padding: 10
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: '#64748B', font: { size: 11 }, maxTicksLimit: 12 }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(226, 232, 240, 0.8)' },
                                ticks: { color: '#64748B', font: { size: 11 } }
                            }
                        },
                        animation: { duration: 600 }
                    }
                });
            }
        });
    </script>
</x-app-layout>
