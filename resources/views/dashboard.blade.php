<x-app-layout>
    <div class="py-8 bg-[#0B0D11] min-h-screen text-slate-100 font-sans">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- ── Header & Action Buttons ────────────────────────────────────────── --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-2 border-b border-slate-800/80">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-white flex items-center gap-3">
                        <span class="w-3 h-8 rounded-full bg-gradient-to-b from-[#D9822B] via-[#C57A1E] to-[#B86D18] inline-block shadow-[0_0_12px_rgba(197,122,30,0.5)]"></span>
                        Tableau de bord
                    </h1>
                    <p class="text-sm text-slate-400 mt-1">
                        Aperçu de l'activité — {{ now()->format('d/m/Y') }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('contacts.import.upload') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-amber-200/90 bg-[#1A1F2B] hover:bg-[#242B3B] border border-[#C57A1E]/30 hover:border-[#C57A1E]/70 transition-all duration-200 shadow-sm">
                        <svg class="w-4 h-4 text-[#D9822B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Importer des contacts
                    </a>

                    <a href="{{ route('campaigns.create') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-slate-950 bg-gradient-to-r from-[#D9822B] via-[#C57A1E] to-[#B86D18] hover:from-[#E58E26] hover:to-[#C57A1E] transition-all duration-200 shadow-[0_4px_20px_rgba(197,122,30,0.35)] transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        + Nouvelle campagne
                    </a>
                </div>
            </div>

            {{-- ── 4 Top KPI Cards ───────────────────────────────────────────────── --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- KPI 1 : Contacts Actifs -->
                <div class="bg-[#141822] rounded-2xl p-5 border border-slate-800 hover:border-[#C57A1E]/40 transition-all duration-300 shadow-lg group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-[#C57A1E]/5 rounded-bl-full pointer-events-none"></div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Contacts actifs</span>
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            +4.2%
                        </span>
                    </div>
                    <div class="text-3xl font-black text-white tracking-tight group-hover:text-amber-400 transition-colors">
                        {{ number_format($totalContacts) }}
                    </div>
                    <div class="text-xs text-slate-500 mt-2 font-medium">
                        sur {{ number_format($totalImportedLogsSum) }} importés au total
                    </div>
                </div>

                <!-- KPI 2 : Campagnes envoyées -->
                <div class="bg-[#141822] rounded-2xl p-5 border border-slate-800 hover:border-[#C57A1E]/40 transition-all duration-300 shadow-lg group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-[#C57A1E]/5 rounded-bl-full pointer-events-none"></div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Campagnes envoyées</span>
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">
                            +{{ $campagnesCeMois }}
                        </span>
                    </div>
                    <div class="text-3xl font-black text-white tracking-tight group-hover:text-amber-400 transition-colors">
                        {{ $campagnesEnvoyees }}
                    </div>
                    <div class="text-xs text-slate-500 mt-2 font-medium">
                        {{ $campagnesCeMois }} créées ce mois-ci
                    </div>
                </div>

                <!-- KPI 3 : Taux de livraison -->
                <div class="bg-[#141822] rounded-2xl p-5 border border-slate-800 hover:border-[#C57A1E]/40 transition-all duration-300 shadow-lg group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-[#C57A1E]/5 rounded-bl-full pointer-events-none"></div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Taux de livraison</span>
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20">
                            stable
                        </span>
                    </div>
                    <div class="text-3xl font-black text-white tracking-tight group-hover:text-amber-400 transition-colors">
                        {{ $tauxLivraison }}%
                    </div>
                    <div class="text-xs text-slate-500 mt-2 font-medium">
                        {{ $emailsRejetes }} échecs sur {{ number_format($emailsEnvoyes) }} envois
                    </div>
                </div>

                <!-- KPI 4 : Jobs en attente -->
                <div class="bg-[#141822] rounded-2xl p-5 border border-slate-800 hover:border-[#C57A1E]/40 transition-all duration-300 shadow-lg group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-[#C57A1E]/5 rounded-bl-full pointer-events-none"></div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Jobs en attente</span>
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full {{ $jobsEnAttente > 0 ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-slate-700/30 text-slate-400 border border-slate-700' }}">
                            {{ $jobsEnAttente }}
                        </span>
                    </div>
                    <div class="text-3xl font-black text-white tracking-tight group-hover:text-amber-400 transition-colors">
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
                <div class="lg:col-span-2 bg-[#141822] rounded-2xl p-6 border border-slate-800 shadow-xl flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-white tracking-wide">Envois sur 14 jours</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Évolution quotidienne des emails distribués vs erreurs</p>
                        </div>
                        <div class="flex items-center gap-4 text-xs font-semibold">
                            <span class="flex items-center gap-1.5 text-amber-400">
                                <span class="w-3 h-3 rounded-full bg-[#C57A1E]"></span> Envoyés
                            </span>
                            <span class="flex items-center gap-1.5 text-rose-400">
                                <span class="w-3 h-3 rounded-full bg-rose-500"></span> Échecs
                            </span>
                        </div>
                    </div>

                    <div class="relative h-64 w-full">
                        <canvas id="sendingChart"></canvas>
                    </div>
                </div>

                <!-- Panneau : File d'attente Laravel Queue & Worker -->
                <div class="bg-[#141822] rounded-2xl p-6 border border-slate-800 shadow-xl flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-4 border-b border-slate-800/80 mb-5">
                            <h3 class="text-lg font-bold text-white tracking-wide flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#D9822B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                File d'attente Laravel Queue
                            </h3>
                        </div>

                        <div class="space-y-4">
                            <!-- En attente -->
                            <div class="flex items-center justify-between p-3.5 rounded-xl bg-[#1A1F2B] border border-slate-800">
                                <div>
                                    <div class="text-xs font-bold text-amber-400 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                        En attente
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">SendCampaignEmailJob</div>
                                </div>
                                <div class="text-2xl font-black text-white">{{ $jobsEnAttente }}</div>
                            </div>

                            <!-- En cours -->
                            <div class="flex items-center justify-between p-3.5 rounded-xl bg-[#1A1F2B] border border-slate-800">
                                <div>
                                    <div class="text-xs font-bold text-emerald-400 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                                        En cours
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Queue worker actif</div>
                                </div>
                                <div class="text-2xl font-black text-white">1</div>
                            </div>

                            <!-- Échoués -->
                            <div class="flex items-center justify-between p-3.5 rounded-xl bg-[#1A1F2B] border border-slate-800">
                                <div>
                                    <div class="text-xs font-bold text-rose-400 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                        Échoués (dernières 24h)
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">table failed_jobs</div>
                                </div>
                                <div class="text-2xl font-black text-white">{{ $jobsEchoues24h }}</div>
                            </div>

                            <!-- Traités aujourd'hui -->
                            <div class="flex items-center justify-between p-3.5 rounded-xl bg-[#1A1F2B] border border-slate-800">
                                <div>
                                    <div class="text-xs font-bold text-slate-400 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                                        Traités aujourd'hui
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Emails distribués</div>
                                </div>
                                <div class="text-2xl font-black text-white">{{ number_format($jobsTraitesAujourdhui) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-800/80 text-xs flex items-center justify-between text-slate-400 mt-4">
                        <span>SMTP Active Status</span>
                        <span class="font-bold text-emerald-400 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            {{ $activeSmtp ? $activeSmtp->provider : 'Hostinger' }} OK
                        </span>
                    </div>
                </div>
            </div>

            {{-- ── Campagnes Récentes ────────────────────────────────────────────── --}}
            <div class="bg-[#141822] rounded-2xl border border-slate-800 shadow-xl overflow-hidden">
                <div class="p-6 flex items-center justify-between border-b border-slate-800/80">
                    <div>
                        <h3 class="text-lg font-bold text-white tracking-wide">Campagnes récentes</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Aperçu rapide des dernières campagnes de mailing créées</p>
                    </div>
                    <a href="{{ route('campaigns.index') }}" class="text-xs font-bold text-[#D9822B] hover:text-amber-300 transition">
                        Toutes les campagnes &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-[#181D2A] text-[11px] uppercase font-bold text-slate-400 border-b border-slate-800 tracking-wider">
                            <tr>
                                <th class="px-6 py-3.5">Campagne</th>
                                <th class="px-6 py-3.5">Statut</th>
                                <th class="px-6 py-3.5 text-center">Destinataires</th>
                                <th class="px-6 py-3.5 text-center">Ouverts</th>
                                <th class="px-6 py-3.5 text-center">Échecs</th>
                                <th class="px-6 py-3.5 text-right">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            @forelse($campaignsWithStats as $camp)
                                <tr class="hover:bg-[#1A202C] transition group">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-white group-hover:text-amber-400 transition">{{ $camp->nom }}</div>
                                        <div class="text-xs text-slate-500 mt-0.5">Par : {{ $camp->creator?->name ?? 'Admin' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($camp->statut === 'envoyee')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Envoyée
                                            </span>
                                        @elseif($camp->statut === 'en_cours')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 animate-pulse">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> En cours
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-700/40 text-slate-300 border border-slate-700">
                                                {{ ucfirst($camp->statut) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-white">{{ number_format($camp->envoyes_count) }}</td>
                                    <td class="px-6 py-4 text-center font-bold text-emerald-400">{{ number_format($camp->ouverts_count) }}</td>
                                    <td class="px-6 py-4 text-center font-bold text-rose-400">{{ number_format($camp->erreurs_count) }}</td>
                                    <td class="px-6 py-4 text-right text-xs text-slate-400 font-medium">
                                        {{ $camp->created_at ? $camp->created_at->format('d M. Y') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-slate-500">
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
                <div class="bg-[#141822] rounded-2xl p-6 border border-slate-800 shadow-xl">
                    <h3 class="text-lg font-bold text-white tracking-wide mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#D9822B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Activité récente
                    </h3>

                    <div class="space-y-4">
                        @forelse($recentActivities as $act)
                            <div class="flex items-start gap-3.5 p-3 rounded-xl bg-[#1A1F2B] border border-slate-800/80">
                                <div class="p-2 rounded-lg {{ $act['icon_bg'] }} border shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        {!! $act['icon_svg'] !!}
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-semibold text-slate-200 truncate">{{ $act['title'] }}</div>
                                    <div class="text-[11px] text-slate-500 mt-1 font-medium">{{ $act['time_human'] }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-xs text-slate-500">Aucune activité récente enregistrée.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Répartition des contacts -->
                <div class="bg-[#141822] rounded-2xl p-6 border border-slate-800 shadow-xl flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-white tracking-wide mb-5 flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#D9822B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Répartition des contacts
                        </h3>

                        <div class="space-y-3">
                            @foreach($categoriesBreakdown as $cat)
                                <div class="flex items-center justify-between p-3 rounded-xl bg-[#1A1F2B] border border-slate-800/80">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $cat->couleur ?? '#C57A1E' }}"></span>
                                        <span class="text-xs font-semibold text-slate-200">{{ $cat->name }}</span>
                                    </div>
                                    <span class="text-sm font-black text-white">{{ number_format($cat->contacts_count) }}</span>
                                </div>
                            @endforeach

                            @if($uncategorizedCount > 0)
                                <div class="flex items-center justify-between p-3 rounded-xl bg-[#1A1F2B] border border-slate-800/80">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-slate-600"></span>
                                        <span class="text-xs font-semibold text-slate-400">Non catégorisés</span>
                                    </div>
                                    <span class="text-sm font-black text-slate-300">{{ number_format($uncategorizedCount) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-5 border-t border-slate-800/80 mt-5">
                        <div class="flex items-center justify-between text-xs mb-2">
                            <span class="text-slate-400 font-semibold">Capacité cible de la base</span>
                            <span class="font-bold text-white">{{ number_format($totalContacts) }} / 50 000</span>
                        </div>
                        @php
                            $capPercent = min(100, round(($totalContacts / 50000) * 100, 1));
                        @endphp
                        <div class="w-full h-2 rounded-full bg-slate-800 overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-[#B86D18] via-[#C57A1E] to-[#D9822B] rounded-full transition-all duration-500" style="width: {{ $capPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Pipeline Prospects (Module 9 - Admin) ────────────────────────── --}}
            @if(Auth::user()?->hasRole('admin'))
                <div class="bg-[#141822] rounded-2xl p-6 border border-slate-800 shadow-xl">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-[#C57A1E]/10 border border-[#C57A1E]/30 text-[#D9822B] rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18l-7 8v6l-4 2v-8L3 4z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white tracking-wide">Pipeline Prospects</h3>
                                <p class="text-xs text-slate-400">Cycle de qualification des prospects après campagnes</p>
                            </div>
                        </div>
                        <a href="{{ route('prospects.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-slate-950 bg-gradient-to-r from-[#D9822B] to-[#C57A1E] hover:from-[#E58E26] hover:to-[#C57A1E] rounded-xl transition shadow-md">
                            Ouvrir le Pipeline &rarr;
                        </a>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                        <div class="p-3.5 rounded-xl bg-[#1A1F2B] border border-slate-800">
                            <div class="text-[11px] font-bold text-slate-400 mb-1 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-slate-400"></span> Nouveau
                            </div>
                            <div class="text-xl font-black text-white">{{ $prospectStats['nouveau'] ?? 0 }}</div>
                        </div>
                        <div class="p-3.5 rounded-xl bg-[#1A1F2B] border border-slate-800">
                            <div class="text-[11px] font-bold text-blue-400 mb-1 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-blue-400"></span> Envoyé
                            </div>
                            <div class="text-xl font-black text-white">{{ $prospectStats['envoye'] ?? 0 }}</div>
                        </div>
                        <div class="p-3.5 rounded-xl bg-[#1A1F2B] border border-slate-800">
                            <div class="text-[11px] font-bold text-purple-400 mb-1 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-purple-400"></span> Ouvert
                            </div>
                            <div class="text-xl font-black text-white">{{ $prospectStats['ouvert'] ?? 0 }}</div>
                        </div>
                        <div class="p-3.5 rounded-xl bg-[#1A1F2B] border border-slate-800">
                            <div class="text-[11px] font-bold text-amber-400 mb-1 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-400"></span> Intéressé
                            </div>
                            <div class="text-xl font-black text-white">{{ $prospectStats['interesse'] ?? 0 }}</div>
                        </div>
                        <div class="p-3.5 rounded-xl bg-[#1A1F2B] border border-slate-800">
                            <div class="text-[11px] font-bold text-rose-400 mb-1 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-rose-400"></span> À relancer
                            </div>
                            <div class="text-xl font-black text-white">{{ $prospectStats['relancer'] ?? 0 }}</div>
                        </div>
                        <div class="p-3.5 rounded-xl bg-[#1A1F2B] border border-slate-800">
                            <div class="text-[11px] font-bold text-emerald-400 mb-1 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Client
                            </div>
                            <div class="text-xl font-black text-white">{{ $prospectStats['client'] ?? 0 }}</div>
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
            gradientGold.addColorStop(0, 'rgba(197, 122, 30, 0.35)');
            gradientGold.addColorStop(1, 'rgba(197, 122, 30, 0.0)');

            const gradientRed = ctx.createLinearGradient(0, 0, 0, 250);
            gradientRed.addColorStop(0, 'rgba(244, 63, 94, 0.25)');
            gradientRed.addColorStop(1, 'rgba(244, 63, 94, 0.0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        {
                            label: 'Envoyés',
                            data: @json($chartSentData),
                            borderColor: '#D9822B',
                            backgroundColor: gradientGold,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#D9822B',
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Échecs',
                            data: @json($chartErrorData),
                            borderColor: '#F43F5E',
                            backgroundColor: gradientRed,
                            borderWidth: 2,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#F43F5E',
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
                            backgroundColor: '#1E2533',
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
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: { color: '#64748B', font: { size: 11 }, beginAtZero: true }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
