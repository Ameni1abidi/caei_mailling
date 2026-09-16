<x-app-layout>
    <div class="py-8 bg-[#03123F] min-h-screen text-slate-100 font-sans">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- ── Header & Action Buttons ────────────────────────────────────────── --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-4 border-b border-[#1E3A8A]/50">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-white flex items-center gap-3">
                        <span class="w-3 h-8 rounded-full bg-gradient-to-b from-[#D9822B] via-[#C57A1E] to-[#B86D18] inline-block shadow-[0_0_15px_rgba(197,122,30,0.6)]"></span>
                        Tableau de bord
                    </h1>
                    <p class="text-sm text-slate-300/80 mt-1">
                        Aperçu de l'activité — {{ now()->format('d/m/Y') }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('contacts.import.upload') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-amber-200 bg-[#0A1C50] hover:bg-[#0E2568] border border-[#C57A1E]/40 hover:border-[#C57A1E]/80 transition-all duration-200 shadow-md">
                        <svg class="w-4 h-4 text-[#D9822B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Importer des contacts
                    </a>

                    <a href="{{ route('campaigns.create') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-slate-950 bg-gradient-to-r from-[#D9822B] via-[#C57A1E] to-[#B86D18] hover:from-[#E58E26] hover:to-[#C57A1E] transition-all duration-200 shadow-[0_4px_22px_rgba(197,122,30,0.4)] transform hover:-translate-y-0.5">
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
                <div class="bg-[#0A1C50] rounded-2xl p-5 border border-[#1E3A8A]/60 hover:border-[#C57A1E]/60 transition-all duration-300 shadow-xl group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-[#C57A1E]/10 rounded-bl-full pointer-events-none"></div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-300/80 uppercase tracking-wider">Contacts actifs</span>
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                            +4.2%
                        </span>
                    </div>
                    <div class="text-3xl font-black text-white tracking-tight group-hover:text-amber-400 transition-colors">
                        {{ number_format($totalContacts) }}
                    </div>
                    <div class="text-xs text-slate-400/80 mt-2 font-medium">
                        sur {{ number_format($totalImportedLogsSum) }} importés au total
                    </div>
                </div>

                <!-- KPI 2 : Campagnes envoyées -->
                <div class="bg-[#0A1C50] rounded-2xl p-5 border border-[#1E3A8A]/60 hover:border-[#C57A1E]/60 transition-all duration-300 shadow-xl group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-[#C57A1E]/10 rounded-bl-full pointer-events-none"></div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-300/80 uppercase tracking-wider">Campagnes envoyées</span>
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full bg-amber-500/15 text-amber-400 border border-amber-500/30">
                            +{{ $campagnesCeMois }}
                        </span>
                    </div>
                    <div class="text-3xl font-black text-white tracking-tight group-hover:text-amber-400 transition-colors">
                        {{ $campagnesEnvoyees }}
                    </div>
                    <div class="text-xs text-slate-400/80 mt-2 font-medium">
                        {{ $campagnesCeMois }} créées ce mois-ci
                    </div>
                </div>

                <!-- KPI 3 : Taux de livraison -->
                <div class="bg-[#0A1C50] rounded-2xl p-5 border border-[#1E3A8A]/60 hover:border-[#C57A1E]/60 transition-all duration-300 shadow-xl group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-[#C57A1E]/10 rounded-bl-full pointer-events-none"></div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-300/80 uppercase tracking-wider">Taux de livraison</span>
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full bg-sky-500/15 text-sky-400 border border-sky-500/30">
                            stable
                        </span>
                    </div>
                    <div class="text-3xl font-black text-white tracking-tight group-hover:text-amber-400 transition-colors">
                        {{ $tauxLivraison }}%
                    </div>
                    <div class="text-xs text-slate-400/80 mt-2 font-medium">
                        {{ $emailsRejetes }} échecs sur {{ number_format($emailsEnvoyes) }} envois
                    </div>
                </div>

                <!-- KPI 4 : Jobs en attente -->
                <div class="bg-[#0A1C50] rounded-2xl p-5 border border-[#1E3A8A]/60 hover:border-[#C57A1E]/60 transition-all duration-300 shadow-xl group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-[#C57A1E]/10 rounded-bl-full pointer-events-none"></div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-300/80 uppercase tracking-wider">Jobs en attente</span>
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full {{ $jobsEnAttente > 0 ? 'bg-amber-500/15 text-amber-400 border border-amber-500/30' : 'bg-slate-700/50 text-slate-300 border border-slate-600' }}">
                            {{ $jobsEnAttente }}
                        </span>
                    </div>
                    <div class="text-3xl font-black text-white tracking-tight group-hover:text-amber-400 transition-colors">
                        {{ $jobsEnAttente }}
                    </div>
                    <div class="text-xs text-slate-400/80 mt-2 font-medium flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        file Laravel Queue · worker actif
                    </div>
                </div>
            </div>

            {{-- ── Middle Section : Graphique 14j + File d'attente Queue ──────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Graphique : Envois sur 14 jours -->
                <div class="lg:col-span-2 bg-[#0A1C50] rounded-2xl p-6 border border-[#1E3A8A]/60 shadow-2xl flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-white tracking-wide">Envois sur 14 jours</h3>
                            <p class="text-xs text-slate-300/80 mt-0.5">Évolution quotidienne des emails distribués vs erreurs</p>
                        </div>
                        <div class="flex items-center gap-4 text-xs font-semibold">
                            <span class="flex items-center gap-1.5 text-amber-400">
                                <span class="w-3 h-3 rounded-full bg-[#D9822B]"></span> Envoyés
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
                <div class="bg-[#0A1C50] rounded-2xl p-6 border border-[#1E3A8A]/60 shadow-2xl flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-4 border-b border-[#1E3A8A]/60 mb-5">
                            <h3 class="text-lg font-bold text-white tracking-wide flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#D9822B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                File d'attente Laravel Queue
                            </h3>
                        </div>

                        <div class="space-y-3.5">
                            <!-- En attente -->
                            <div class="flex items-center justify-between p-3.5 rounded-xl bg-[#081748] border border-[#1E3A8A]/50">
                                <div>
                                    <div class="text-xs font-bold text-amber-400 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                        En attente
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">SendCampaignEmailJob</div>
                                </div>
                                <div class="text-2xl font-black text-white">{{ $jobsEnAttente }}</div>
                            </div>

                            <!-- En cours -->
                            <div class="flex items-center justify-between p-3.5 rounded-xl bg-[#081748] border border-[#1E3A8A]/50">
                                <div>
                                    <div class="text-xs font-bold text-emerald-400 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                                        En cours
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">Queue worker actif</div>
                                </div>
                                <div class="text-2xl font-black text-white">1</div>
                            </div>

                            <!-- Échoués -->
                            <div class="flex items-center justify-between p-3.5 rounded-xl bg-[#081748] border border-[#1E3A8A]/50">
                                <div>
                                    <div class="text-xs font-bold text-rose-400 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                        Échoués (dernières 24h)
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">table failed_jobs</div>
                                </div>
                                <div class="text-2xl font-black text-white">{{ $jobsEchoues24h }}</div>
                            </div>

                            <!-- Traités aujourd'hui -->
                            <div class="flex items-center justify-between p-3.5 rounded-xl bg-[#081748] border border-[#1E3A8A]/50">
                                <div>
                                    <div class="text-xs font-bold text-slate-300 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                        Traités aujourd'hui
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">Emails distribués</div>
                                </div>
                                <div class="text-2xl font-black text-white">{{ number_format($jobsTraitesAujourdhui) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-[#1E3A8A]/60 text-xs flex items-center justify-between text-slate-300 mt-4">
                        <span>Statut SMTP Principal</span>
                        <span class="font-bold text-emerald-400 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            {{ $activeSmtp ? $activeSmtp->provider : 'Hostinger' }} OK
                        </span>
                    </div>
                </div>
            </div>

            {{-- ── Campagnes Récentes ────────────────────────────────────────────── --}}
            <div class="bg-[#0A1C50] rounded-2xl border border-[#1E3A8A]/60 shadow-2xl overflow-hidden">
                <div class="p-6 flex items-center justify-between border-b border-[#1E3A8A]/60">
                    <div>
                        <h3 class="text-lg font-bold text-white tracking-wide">Campagnes récentes</h3>
                        <p class="text-xs text-slate-300/80 mt-0.5">Aperçu rapide des dernières campagnes de mailing créées</p>
                    </div>
                    <a href="{{ route('campaigns.index') }}" class="text-xs font-bold text-[#D9822B] hover:text-amber-300 transition">
                        Toutes les campagnes &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-200">
                        <thead class="bg-[#081748] text-[11px] uppercase font-bold text-amber-200/90 border-b border-[#1E3A8A]/60 tracking-wider">
                            <tr>
                                <th class="px-6 py-3.5">Campagne</th>
                                <th class="px-6 py-3.5">Statut</th>
                                <th class="px-6 py-3.5 text-center">Destinataires</th>
                                <th class="px-6 py-3.5 text-center">Ouverts</th>
                                <th class="px-6 py-3.5 text-center">Échecs</th>
                                <th class="px-6 py-3.5 text-right">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#1E3A8A]/40">
                            @forelse($campaignsWithStats as $camp)
                                <tr class="hover:bg-[#0E2568] transition group">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-white group-hover:text-amber-400 transition">{{ $camp->nom }}</div>
                                        <div class="text-xs text-slate-400 mt-0.5">Par : {{ $camp->creator?->name ?? 'Admin' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($camp->statut === 'envoyee')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Envoyée
                                            </span>
                                        @elseif($camp->statut === 'en_cours')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 animate-pulse">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> En cours
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-700/60 text-slate-300 border border-slate-600">
                                                {{ ucfirst($camp->statut) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-white">{{ number_format($camp->envoyes_count) }}</td>
                                    <td class="px-6 py-4 text-center font-bold text-emerald-400">{{ number_format($camp->ouverts_count) }}</td>
                                    <td class="px-6 py-4 text-center font-bold text-rose-400">{{ number_format($camp->erreurs_count) }}</td>
                                    <td class="px-6 py-4 text-right text-xs text-slate-300/80 font-medium">
                                        {{ $camp->created_at ? $camp->created_at->format('d M. Y') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-slate-400">
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
                <div class="bg-[#0A1C50] rounded-2xl p-6 border border-[#1E3A8A]/60 shadow-2xl">
                    <h3 class="text-lg font-bold text-white tracking-wide mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#D9822B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Activité récente
                    </h3>

                    <div class="space-y-3.5">
                        @forelse($recentActivities as $act)
                            <div class="flex items-start gap-3.5 p-3.5 rounded-xl bg-[#081748] border border-[#1E3A8A]/50">
                                <div class="p-2 rounded-lg {{ $act['icon_bg'] }} border shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        {!! $act['icon_svg'] !!}
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-semibold text-slate-100 truncate">{{ $act['title'] }}</div>
                                    <div class="text-[11px] text-slate-400 mt-1 font-medium">{{ $act['time_human'] }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-xs text-slate-400">Aucune activité récente enregistrée.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Répartition des contacts -->
                <div class="bg-[#0A1C50] rounded-2xl p-6 border border-[#1E3A8A]/60 shadow-2xl flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-white tracking-wide mb-5 flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#D9822B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Répartition des contacts
                        </h3>

                        <div class="space-y-3">
                            @foreach($categoriesBreakdown as $cat)
                                <div class="flex items-center justify-between p-3 rounded-xl bg-[#081748] border border-[#1E3A8A]/50">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $cat->couleur ?? '#C57A1E' }}"></span>
                                        <span class="text-xs font-semibold text-slate-100">{{ $cat->name }}</span>
                                    </div>
                                    <span class="text-sm font-black text-white">{{ number_format($cat->contacts_count) }}</span>
                                </div>
                            @endforeach

                            @if($uncategorizedCount > 0)
                                <div class="flex items-center justify-between p-3 rounded-xl bg-[#081748] border border-[#1E3A8A]/50">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-slate-500"></span>
                                        <span class="text-xs font-semibold text-slate-300">Non catégorisés</span>
                                    </div>
                                    <span class="text-sm font-black text-slate-300">{{ number_format($uncategorizedCount) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-5 border-t border-[#1E3A8A]/60 mt-5">
                        <div class="flex items-center justify-between text-xs mb-2">
                            <span class="text-slate-300 font-semibold">Capacité cible de la base</span>
                            <span class="font-bold text-white">{{ number_format($totalContacts) }} / 50 000</span>
                        </div>
                        @php
                            $capPercent = min(100, round(($totalContacts / 50000) * 100, 1));
                        @endphp
                        <div class="w-full h-2 rounded-full bg-[#081748] overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-[#B86D18] via-[#C57A1E] to-[#D9822B] rounded-full transition-all duration-500" style="width: {{ $capPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Pipeline Prospects (Module 9 - Admin) ────────────────────────── --}}
            @if(Auth::user()?->hasRole('admin'))
                <div class="bg-[#0A1C50] rounded-2xl p-6 border border-[#1E3A8A]/70 shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-[#C57A1E]/5 rounded-bl-full pointer-events-none"></div>

                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-[#1E3A8A]/60">
                        <div class="flex items-center gap-3.5">
                            <div class="p-2.5 bg-gradient-to-br from-[#D9822B] to-[#B86D18] text-slate-950 rounded-xl shadow-lg shadow-amber-950/30">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 4h18l-7 8v6l-4 2v-8L3 4z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-[#C57A1E]/20 text-amber-300 border border-[#C57A1E]/40">
                                        PIPELINE CRM &bull; MODULE 9
                                    </span>
                                    <span class="text-xs font-bold text-slate-400">
                                        {{ number_format($prospectStats['total']) }} prospects au total
                                    </span>
                                </div>
                                <h3 class="text-xl font-bold text-white tracking-wide mt-1">Pipeline de Qualification des Prospects</h3>
                            </div>
                        </div>

                        <a href="{{ route('prospects.index') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold text-slate-950 bg-gradient-to-r from-[#D9822B] via-[#C57A1E] to-[#B86D18] hover:from-[#E58E26] hover:to-[#C57A1E] rounded-xl transition-all duration-200 shadow-lg shadow-amber-950/40 transform hover:-translate-y-0.5 self-start sm:self-auto">
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
                        <div class="bg-[#081748] rounded-xl p-4 border border-slate-700/60 hover:border-slate-400/50 transition-all duration-200 relative overflow-hidden group">
                            <div class="h-1.5 w-full bg-slate-400 absolute top-0 left-0"></div>
                            <div class="flex items-center justify-between mb-2 pt-1">
                                <span class="text-xs font-bold text-slate-300 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                    Nouveau
                                </span>
                                <span class="text-[10px] font-semibold text-slate-400 bg-slate-800 px-1.5 py-0.5 rounded">Étape 1</span>
                            </div>
                            <div class="text-2xl font-black text-white group-hover:text-slate-200 transition">
                                {{ number_format($nouveauCnt) }}
                            </div>
                            <div class="text-[11px] text-slate-400 mt-1 font-medium">Base brute reçue</div>
                        </div>

                        <!-- Etape 2: Envoyé -->
                        <div class="bg-[#081748] rounded-xl p-4 border border-blue-500/30 hover:border-blue-400/60 transition-all duration-200 relative overflow-hidden group">
                            <div class="h-1.5 w-full bg-blue-500 absolute top-0 left-0"></div>
                            <div class="flex items-center justify-between mb-2 pt-1">
                                <span class="text-xs font-bold text-blue-400 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                                    Envoyé
                                </span>
                                <span class="text-[10px] font-semibold text-blue-300 bg-blue-950 px-1.5 py-0.5 rounded border border-blue-800/50">Étape 2</span>
                            </div>
                            <div class="text-2xl font-black text-white group-hover:text-blue-300 transition">
                                {{ number_format($envoyeCnt) }}
                            </div>
                            <div class="text-[11px] text-blue-300/80 mt-1 font-medium">
                                {{ $pctEnvoye }}% de la base
                            </div>
                        </div>

                        <!-- Etape 3: Ouvert -->
                        <div class="bg-[#081748] rounded-xl p-4 border border-purple-500/30 hover:border-purple-400/60 transition-all duration-200 relative overflow-hidden group">
                            <div class="h-1.5 w-full bg-purple-500 absolute top-0 left-0"></div>
                            <div class="flex items-center justify-between mb-2 pt-1">
                                <span class="text-xs font-bold text-purple-400 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                                    Ouvert
                                </span>
                                <span class="text-[10px] font-semibold text-purple-300 bg-purple-950 px-1.5 py-0.5 rounded border border-purple-800/50">Étape 3</span>
                            </div>
                            <div class="text-2xl font-black text-white group-hover:text-purple-300 transition">
                                {{ number_format($ouvertCnt) }}
                            </div>
                            <div class="text-[11px] text-purple-300/80 mt-1 font-medium">
                                {{ $pctOuvert }}% d'ouverture
                            </div>
                        </div>

                        <!-- Etape 4: Intéressé -->
                        <div class="bg-[#081748] rounded-xl p-4 border border-[#C57A1E]/40 hover:border-[#C57A1E]/80 transition-all duration-200 relative overflow-hidden group">
                            <div class="h-1.5 w-full bg-gradient-to-r from-[#D9822B] to-[#C57A1E] absolute top-0 left-0"></div>
                            <div class="flex items-center justify-between mb-2 pt-1">
                                <span class="text-xs font-bold text-amber-400 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                    Intéressé
                                </span>
                                <span class="text-[10px] font-semibold text-amber-300 bg-amber-950/60 px-1.5 py-0.5 rounded border border-amber-800/50">Hot Lead</span>
                            </div>
                            <div class="text-2xl font-black text-amber-400 group-hover:text-amber-300 transition">
                                {{ number_format($interesseCnt) }}
                            </div>
                            <div class="text-[11px] text-amber-300/80 mt-1 font-medium">Signaux forts</div>
                        </div>

                        <!-- Etape 5: À relancer -->
                        <div class="bg-[#081748] rounded-xl p-4 border border-rose-500/30 hover:border-rose-400/60 transition-all duration-200 relative overflow-hidden group">
                            <div class="h-1.5 w-full bg-rose-500 absolute top-0 left-0"></div>
                            <div class="flex items-center justify-between mb-2 pt-1">
                                <span class="text-xs font-bold text-rose-400 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                                    À relancer
                                </span>
                                <span class="text-[10px] font-semibold text-rose-300 bg-rose-950 px-1.5 py-0.5 rounded border border-rose-800/50">Priorité</span>
                            </div>
                            <div class="text-2xl font-black text-white group-hover:text-rose-300 transition">
                                {{ number_format($relancerCnt) }}
                            </div>
                            <div class="text-[11px] text-rose-300/80 mt-1 font-medium">Relance commerciale</div>
                        </div>

                        <!-- Etape 6: Client -->
                        <div class="bg-[#081748] rounded-xl p-4 border border-emerald-500/40 hover:border-emerald-400/80 transition-all duration-200 relative overflow-hidden group">
                            <div class="h-1.5 w-full bg-emerald-500 absolute top-0 left-0"></div>
                            <div class="flex items-center justify-between mb-2 pt-1">
                                <span class="text-xs font-bold text-emerald-400 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Client
                                </span>
                                <span class="text-[10px] font-semibold text-emerald-300 bg-emerald-950 px-1.5 py-0.5 rounded border border-emerald-800/50">Converti</span>
                            </div>
                            <div class="text-2xl font-black text-emerald-400 group-hover:text-emerald-300 transition">
                                {{ number_format($clientCnt) }}
                            </div>
                            <div class="text-[11px] text-emerald-300/80 mt-1 font-medium">Contrats signés</div>
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
            gradientGold.addColorStop(0, 'rgba(217, 130, 43, 0.45)');
            gradientGold.addColorStop(1, 'rgba(217, 130, 43, 0.0)');

            const gradientRed = ctx.createLinearGradient(0, 0, 0, 250);
            gradientRed.addColorStop(0, 'rgba(244, 63, 94, 0.35)');
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
                            backgroundColor: '#081748',
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
                            ticks: { color: '#94A3B8', font: { size: 11 } }
                        },
                        y: {
                            grid: { color: 'rgba(30, 58, 138, 0.4)' },
                            ticks: { color: '#94A3B8', font: { size: 11 }, beginAtZero: true }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
