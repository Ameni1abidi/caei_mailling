<x-app-layout>
    <div class="py-8 bg-slate-50 min-h-screen text-slate-800 font-sans">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- ── Header & Action Buttons ────────────────────────────────────────── --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-4 border-b border-slate-200/80 bg-white p-6 rounded-2xl border shadow-sm">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-[#03123F] flex items-center gap-3">
                        <span class="w-3 h-8 rounded-full bg-gradient-to-b from-[#D9822B] via-[#C57A1E] to-[#B86D18] inline-block shadow-sm"></span>
                        Tableau de bord
                    </h1>
                    <p class="text-sm text-slate-500 mt-1">
                        Aperçu de l'activité — {{ now()->format('d/m/Y') }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('contacts.import.upload') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 transition-all duration-200 shadow-sm">
                        <svg class="w-4 h-4 text-[#C57A1E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Importer des contacts
                    </a>

                    <a href="{{ route('campaigns.create') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-[#D9822B] via-[#C57A1E] to-[#B86D18] hover:from-[#E58E26] hover:to-[#C57A1E] transition-all duration-200 shadow-md shadow-amber-600/20 transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        + Nouvelle campagne
                    </a>
                </div>
            </div>

            {{-- ── 4 Top KPI Cards ───────────────────────────────────────────────── --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- KPI 1 : Contacts Actifs -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 hover:border-[#C57A1E]/40 transition-all duration-300 shadow-sm group relative overflow-hidden">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Contacts actifs</span>
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                            +4.2%
                        </span>
                    </div>
                    <div class="text-3xl font-black text-slate-900 tracking-tight group-hover:text-[#C57A1E] transition-colors">
                        {{ number_format($totalContacts) }}
                    </div>
                    <div class="text-xs text-slate-500 mt-2 font-medium">
                        sur {{ number_format($totalImportedLogsSum) }} importés au total
                    </div>
                </div>

                <!-- KPI 2 : Campagnes envoyées -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 hover:border-[#C57A1E]/40 transition-all duration-300 shadow-sm group relative overflow-hidden">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Campagnes envoyées</span>
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200">
                            +{{ $campagnesCeMois }}
                        </span>
                    </div>
                    <div class="text-3xl font-black text-slate-900 tracking-tight group-hover:text-[#C57A1E] transition-colors">
                        {{ $campagnesEnvoyees }}
                    </div>
                    <div class="text-xs text-slate-500 mt-2 font-medium">
                        {{ $campagnesCeMois }} créées ce mois-ci
                    </div>
                </div>

                <!-- KPI 3 : Taux de livraison -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 hover:border-[#C57A1E]/40 transition-all duration-300 shadow-sm group relative overflow-hidden">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Taux de livraison</span>
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                            stable
                        </span>
                    </div>
                    <div class="text-3xl font-black text-slate-900 tracking-tight group-hover:text-[#C57A1E] transition-colors">
                        {{ $tauxLivraison }}%
                    </div>
                    <div class="text-xs text-slate-500 mt-2 font-medium">
                        {{ $emailsRejetes }} échecs sur {{ number_format($emailsEnvoyes) }} envois
                    </div>
                </div>

                <!-- KPI 4 : Jobs en attente -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 hover:border-[#C57A1E]/40 transition-all duration-300 shadow-sm group relative overflow-hidden">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Jobs en attente</span>
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full {{ $jobsEnAttente > 0 ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                            {{ $jobsEnAttente }}
                        </span>
                    </div>
                    <div class="text-3xl font-black text-slate-900 tracking-tight group-hover:text-[#C57A1E] transition-colors">
                        {{ $jobsEnAttente }}
                    </div>
                    <div class="text-xs text-slate-500 mt-2 font-medium flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        file Laravel Queue · worker actif
                    </div>
                </div>
            </div>

            {{-- ── Middle Section : Graphique 14j + File d'attente Queue ──────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Graphique : Envois sur 14 jours -->
                <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-[#03123F] tracking-wide">Envois sur 14 jours</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Évolution quotidienne des emails distribués vs erreurs</p>
                        </div>
                        <div class="flex items-center gap-4 text-xs font-semibold">
                            <span class="flex items-center gap-1.5 text-slate-700">
                                <span class="w-3 h-3 rounded-full bg-[#C57A1E]"></span> Envoyés
                            </span>
                            <span class="flex items-center gap-1.5 text-rose-600">
                                <span class="w-3 h-3 rounded-full bg-rose-500"></span> Échecs
                            </span>
                        </div>
                    </div>

                    <div class="relative h-64 w-full">
                        <canvas id="sendingChart"></canvas>
                    </div>
                </div>

                <!-- Panneau : File d'attente Laravel Queue & Worker -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                            <h3 class="text-lg font-bold text-slate-900 tracking-wide flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#C57A1E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                File d'attente Laravel Queue
                            </h3>
                        </div>

                        <div class="space-y-3.5">
                            <!-- En attente -->
                            <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                                <div>
                                    <div class="text-xs font-bold text-amber-800 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                        En attente
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">SendCampaignEmailJob</div>
                                </div>
                                <div class="text-2xl font-black text-slate-900">{{ $jobsEnAttente }}</div>
                            </div>

                            <!-- En cours -->
                            <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                                <div>
                                    <div class="text-xs font-bold text-emerald-700 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                        En cours
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Queue worker actif</div>
                                </div>
                                <div class="text-2xl font-black text-slate-900">1</div>
                            </div>

                            <!-- Échoués -->
                            <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                                <div>
                                    <div class="text-xs font-bold text-rose-700 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                        Échoués (dernières 24h)
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">table failed_jobs</div>
                                </div>
                                <div class="text-2xl font-black text-slate-900">{{ $jobsEchoues24h }}</div>
                            </div>

                            <!-- Traités aujourd'hui -->
                            <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                                <div>
                                    <div class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                        Traités aujourd'hui
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Emails distribués</div>
                                </div>
                                <div class="text-2xl font-black text-slate-900">{{ number_format($jobsTraitesAujourdhui) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 text-xs flex items-center justify-between text-slate-500 mt-4">
                        <span>Statut SMTP Principal</span>
                        <span class="font-bold text-emerald-600 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ $activeSmtp ? $activeSmtp->provider : 'Hostinger' }} OK
                        </span>
                    </div>
                </div>
            </div>

            {{-- ── Statistiques Détaillées par Campagne ────────────────────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-lg font-bold text-[#03123F] tracking-wide flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#C57A1E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Statistiques par Campagne
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Performance globale : total ciblés, envois réels, ouvertures, clics et taux de succès</p>
                    </div>
                    <a href="{{ route('statistics.index') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-[#C57A1E] bg-amber-50 hover:bg-amber-100/80 border border-amber-200 rounded-xl transition">
                        <span>Voir toutes les statistiques &rarr;</span>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50/80 text-[11px] uppercase font-bold text-slate-500 border-b border-slate-200/80 tracking-wider">
                            <tr>
                                <th class="px-5 py-3.5">Campagne</th>
                                <th class="px-4 py-3.5 text-center">Statut</th>
                                <th class="px-4 py-3.5 text-center">Total Contacts</th>
                                <th class="px-4 py-3.5 text-center">Emails Envoyés</th>
                                <th class="px-4 py-3.5 text-center">Ouverts</th>
                                <th class="px-4 py-3.5 text-center">Clics</th>
                                <th class="px-4 py-3.5 text-center">Échecs</th>
                                <th class="px-4 py-3.5 text-center">Taux Ouverture</th>
                                <th class="px-5 py-3.5 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($campaignsWithStats as $camp)
                                @php
                                    $openRate = $camp->envoyes_count > 0 ? round(($camp->ouverts_count / $camp->envoyes_count) * 100, 1) : 0;
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition group">
                                    <td class="px-5 py-4">
                                        <div class="font-bold text-slate-900 group-hover:text-[#C57A1E] transition">{{ $camp->nom }}</div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">Par : {{ $camp->creator?->name ?? 'Admin' }} &bull; {{ $camp->created_at ? $camp->created_at->format('d/m/Y') : '-' }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        @if($camp->statut === 'envoyee')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Envoyée
                                            </span>
                                        @elseif($camp->statut === 'en_cours')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 animate-pulse">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> En cours
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ ucfirst($camp->statut) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-center font-extrabold text-slate-900">{{ number_format($camp->total_targets_count) }}</td>
                                    <td class="px-4 py-4 text-center font-bold text-blue-700">{{ number_format($camp->envoyes_count) }}</td>
                                    <td class="px-4 py-4 text-center font-bold text-emerald-600">{{ number_format($camp->ouverts_count) }}</td>
                                    <td class="px-4 py-4 text-center font-bold text-purple-600">{{ number_format($camp->clics_count) }}</td>
                                    <td class="px-4 py-4 text-center font-bold text-rose-600">{{ number_format($camp->erreurs_count) }}</td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 font-extrabold text-xs">
                                            {{ $openRate }}%
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <a href="{{ route('statistics.index', ['campaign_id' => $camp->id]) }}"
                                           title="Voir détails statistiques de cette campagne"
                                           class="inline-flex items-center gap-1 px-3 py-1 text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg transition shadow-sm">
                                            <span>Stats</span>
                                            <svg class="w-3.5 h-3.5 text-[#C57A1E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-10 text-center text-slate-500">
                                        Aucune campagne trouvée. Cliquez sur « + Nouvelle campagne » pour démarrer.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ── Bottom Section : Activité récente + Répartition des contacts ───── --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Activité récente -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 tracking-wide mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#C57A1E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Activité récente
                    </h3>

                    <div class="space-y-3.5">
                        @forelse($recentActivities as $act)
                            <div class="flex items-start gap-3.5 p-3.5 rounded-xl bg-slate-50 border border-slate-200/60">
                                <div class="p-2 rounded-lg bg-amber-50 text-[#C57A1E] border border-amber-200 shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        {!! $act['icon_svg'] !!}
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-semibold text-slate-800 truncate">{{ $act['title'] }}</div>
                                    <div class="text-[11px] text-slate-500 mt-1 font-medium">{{ $act['time_human'] }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-xs text-slate-500">Aucune activité récente enregistrée.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Répartition des contacts -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 tracking-wide mb-5 flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#C57A1E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Répartition des contacts
                        </h3>

                        <div class="space-y-3">
                            @foreach($categoriesBreakdown as $cat)
                                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/60">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $cat->couleur ?? '#C57A1E' }}"></span>
                                        <span class="text-xs font-semibold text-slate-800">{{ $cat->name }}</span>
                                    </div>
                                    <span class="text-sm font-black text-slate-900">{{ number_format($cat->contacts_count) }}</span>
                                </div>
                            @endforeach

                            @if($uncategorizedCount > 0)
                                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/60">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                                        <span class="text-xs font-semibold text-slate-600">Non catégorisés</span>
                                    </div>
                                    <span class="text-sm font-black text-slate-700">{{ number_format($uncategorizedCount) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-5 border-t border-slate-100 mt-5">
                        <div class="flex items-center justify-between text-xs mb-2">
                            <span class="text-slate-600 font-semibold">Capacité cible de la base</span>
                            <span class="font-bold text-slate-900">{{ number_format($totalContacts) }} / 200 000</span>
                        </div>
                        @php
                            $capPercent = min(100, round(($totalContacts / 200000) * 100, 1));
                        @endphp
                        <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-[#D9822B] via-[#C57A1E] to-[#B86D18] rounded-full transition-all duration-500" style="width: {{ $capPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Pipeline Prospects (Module 9 - Admin) ────────────────────────── --}}
            @if(Auth::user()?->hasRole('admin'))
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm relative overflow-hidden">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3.5">
                            <div class="p-2.5 bg-amber-50 border border-amber-200 text-[#C57A1E] rounded-xl shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 4h18l-7 8v6l-4 2v-8L3 4z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-[#C57A1E] border border-amber-200">
                                        PIPELINE CRM &bull; MODULE 9
                                    </span>
                                    <span class="text-xs font-bold text-slate-500">
                                        {{ number_format($prospectStats['total']) }} prospects au total
                                    </span>
                                </div>
                                <h3 class="text-xl font-bold text-slate-900 tracking-wide mt-1">Pipeline de Qualification des Prospects</h3>
                            </div>
                        </div>

                        <a href="{{ route('prospects.index') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold text-white bg-gradient-to-r from-[#D9822B] via-[#C57A1E] to-[#B86D18] hover:from-[#E58E26] hover:to-[#C57A1E] rounded-xl transition-all duration-200 shadow-md shadow-amber-600/20 transform hover:-translate-y-0.5 self-start sm:self-auto">
                            <span>Ouvrir le Pipeline Kanban</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>

                    <!-- Funnel Stepper Cards Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3.5 relative">
                        @php
                            $nouveauCnt = $prospectStats['nouveau'] ?? 0;
                            $envoyeCnt  = $prospectStats['envoye'] ?? 0;
                            $ouvertCnt  = $prospectStats['ouvert'] ?? 0;
                            $interesseCnt = $prospectStats['interesse'] ?? 0;
                            $relancerCnt  = $prospectStats['relancer'] ?? 0;
                            $clientCnt    = $prospectStats['client'] ?? 0;

                            $pctEnvoye = $nouveauCnt > 0 ? round(($envoyeCnt / $nouveauCnt) * 100, 1) : 0;
                            $pctOuvert = $envoyeCnt > 0 ? round(($ouvertCnt / $envoyeCnt) * 100, 1) : 0;
                        @endphp

                        <!-- Etape 1: Nouveau -->
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-200/80 hover:border-slate-400 transition-all duration-200 relative overflow-hidden group">
                            <div class="h-1.5 w-full bg-slate-400 absolute top-0 left-0"></div>
                            <div class="flex items-center justify-between mb-2 pt-1">
                                <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                    Nouveau
                                </span>
                                <span class="text-[10px] font-semibold text-slate-500 bg-slate-200/80 px-1.5 py-0.5 rounded">Étape 1</span>
                            </div>
                            <div class="text-2xl font-black text-slate-900 group-hover:text-[#C57A1E] transition">
                                {{ number_format($nouveauCnt) }}
                            </div>
                            <div class="text-[11px] text-slate-500 mt-1 font-medium">Base brute reçue</div>
                        </div>

                        <!-- Etape 2: Envoyé -->
                        <div class="bg-slate-50 rounded-xl p-4 border border-blue-200 hover:border-blue-400 transition-all duration-200 relative overflow-hidden group">
                            <div class="h-1.5 w-full bg-blue-500 absolute top-0 left-0"></div>
                            <div class="flex items-center justify-between mb-2 pt-1">
                                <span class="text-xs font-bold text-blue-700 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                    Envoyé
                                </span>
                                <span class="text-[10px] font-semibold text-blue-700 bg-blue-100 px-1.5 py-0.5 rounded border border-blue-200">Étape 2</span>
                            </div>
                            <div class="text-2xl font-black text-slate-900 group-hover:text-blue-600 transition">
                                {{ number_format($envoyeCnt) }}
                            </div>
                            <div class="text-[11px] text-blue-600 mt-1 font-medium">
                                {{ $pctEnvoye }}% de la base
                            </div>
                        </div>

                        <!-- Etape 3: Ouvert -->
                        <div class="bg-slate-50 rounded-xl p-4 border border-purple-200 hover:border-purple-400 transition-all duration-200 relative overflow-hidden group">
                            <div class="h-1.5 w-full bg-purple-500 absolute top-0 left-0"></div>
                            <div class="flex items-center justify-between mb-2 pt-1">
                                <span class="text-xs font-bold text-purple-700 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                    Ouvert
                                </span>
                                <span class="text-[10px] font-semibold text-purple-700 bg-purple-100 px-1.5 py-0.5 rounded border border-purple-200">Étape 3</span>
                            </div>
                            <div class="text-2xl font-black text-slate-900 group-hover:text-purple-600 transition">
                                {{ number_format($ouvertCnt) }}
                            </div>
                            <div class="text-[11px] text-purple-600 mt-1 font-medium">
                                {{ $pctOuvert }}% d'ouverture
                            </div>
                        </div>

                        <!-- Etape 4: Intéressé -->
                        <div class="bg-slate-50 rounded-xl p-4 border border-amber-200 hover:border-amber-400 transition-all duration-200 relative overflow-hidden group">
                            <div class="h-1.5 w-full bg-gradient-to-r from-[#D9822B] to-[#C57A1E] absolute top-0 left-0"></div>
                            <div class="flex items-center justify-between mb-2 pt-1">
                                <span class="text-xs font-bold text-amber-800 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                    Intéressé
                                </span>
                                <span class="text-[10px] font-semibold text-amber-800 bg-amber-100 px-1.5 py-0.5 rounded border border-amber-200">Hot Lead</span>
                            </div>
                            <div class="text-2xl font-black text-[#C57A1E] group-hover:text-amber-700 transition">
                                {{ number_format($interesseCnt) }}
                            </div>
                            <div class="text-[11px] text-amber-700 mt-1 font-medium">Signaux forts</div>
                        </div>

                        <!-- Etape 5: À relancer -->
                        <div class="bg-slate-50 rounded-xl p-4 border border-rose-200 hover:border-rose-400 transition-all duration-200 relative overflow-hidden group">
                            <div class="h-1.5 w-full bg-rose-500 absolute top-0 left-0"></div>
                            <div class="flex items-center justify-between mb-2 pt-1">
                                <span class="text-xs font-bold text-rose-700 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                    À relancer
                                </span>
                                <span class="text-[10px] font-semibold text-rose-700 bg-rose-100 px-1.5 py-0.5 rounded border border-rose-200">Priorité</span>
                            </div>
                            <div class="text-2xl font-black text-slate-900 group-hover:text-rose-600 transition">
                                {{ number_format($relancerCnt) }}
                            </div>
                            <div class="text-[11px] text-rose-600 mt-1 font-medium">Relance commerciale</div>
                        </div>

                        <!-- Etape 6: Client -->
                        <div class="bg-slate-50 rounded-xl p-4 border border-emerald-200 hover:border-emerald-400 transition-all duration-200 relative overflow-hidden group">
                            <div class="h-1.5 w-full bg-emerald-500 absolute top-0 left-0"></div>
                            <div class="flex items-center justify-between mb-2 pt-1">
                                <span class="text-xs font-bold text-emerald-700 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Client
                                </span>
                                <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded border border-emerald-200">Converti</span>
                            </div>
                            <div class="text-2xl font-black text-emerald-600 group-hover:text-emerald-700 transition">
                                {{ number_format($clientCnt) }}
                            </div>
                            <div class="text-[11px] text-emerald-600 mt-1 font-medium">Contrats signés</div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- ── Chart.js Script pour le graphique 14 jours ────────────────────── --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('sendingChart').getContext('2d');

            const gradientGold = ctx.createLinearGradient(0, 0, 0, 250);
            gradientGold.addColorStop(0, 'rgba(197, 122, 30, 0.25)');
            gradientGold.addColorStop(1, 'rgba(197, 122, 30, 0.0)');

            const gradientRed = ctx.createLinearGradient(0, 0, 0, 250);
            gradientRed.addColorStop(0, 'rgba(239, 68, 68, 0.2)');
            gradientRed.addColorStop(1, 'rgba(239, 68, 68, 0.0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        {
                            label: 'Envoyés',
                            data: @json($chartSentData),
                            borderColor: '#C57A1E',
                            backgroundColor: gradientGold,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#C57A1E',
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Échecs',
                            data: @json($chartErrorData),
                            borderColor: '#EF4444',
                            backgroundColor: gradientRed,
                            borderWidth: 2,
                            fill: true,
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
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1E293B',
                            titleColor: '#FFFFFF',
                            bodyColor: '#CBD5E1',
                            borderColor: '#C57A1E',
                            borderWidth: 1,
                            padding: 10,
                            displayColors: true
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#64748B', font: { size: 11 } }
                        },
                        y: {
                            grid: { color: 'rgba(226, 232, 240, 0.8)' },
                            ticks: { color: '#64748B', font: { size: 11 }, beginAtZero: true }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
