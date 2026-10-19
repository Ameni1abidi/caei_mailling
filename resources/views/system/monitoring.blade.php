<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8">
        
        <!-- Messages Flash -->
        <x-flash-messages />

        <!-- En-tête -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-gradient-to-br from-[#03123F] to-[#1E3A8A] text-white rounded-2xl shadow-lg shadow-blue-950/20">
                    <svg class="w-7 h-7 text-[#C57A1E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 uppercase tracking-wider">Centre de Contrôle</span>
                        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Surveillance & Santé du Système</h1>
                    </div>
                    <p class="mt-0.5 text-xs text-slate-500">Supervision en direct : Quotas OVH, état de la file d'attente, campagnes actives et alertes d'envoi.</p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <!-- Bouton Forcer un Batch -->
                <form action="{{ route('system.dispatch-batch') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 bg-[#03123F] hover:bg-[#061b58] text-white px-4 py-2.5 rounded-xl text-xs font-bold shadow-md shadow-blue-900/10 transition">
                        <svg class="w-4 h-4 text-[#C57A1E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Déclencher un Batch (2/campagne)
                    </button>
                </form>

                <!-- Bouton Vider la File d'attente -->
                <form action="{{ route('system.clear-queue') }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir vider la file d\'attente ? Cela supprimera les jobs accumulés sans toucher à vos contacts.');">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 px-4 py-2.5 rounded-xl text-xs font-bold transition">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Purger la File ({{ number_format($jobsCount) }})
                    </button>
                </form>
            </div>
        </div>

        <!-- ── 1. Indicateurs Majeurs (KPIs Santé) ────────────────────────── -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- État de la File -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">File d'attente (Jobs)</span>
                    <span class="p-2 {{ $jobsCount > 100 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }} rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </span>
                </div>
                <div class="text-3xl font-black {{ $jobsCount > 100 ? 'text-rose-600' : 'text-slate-900' }}">
                    {{ number_format($jobsCount) }}
                </div>
                <div class="text-[11px] mt-1 {{ $jobsCount > 100 ? 'text-rose-500 font-bold' : 'text-slate-400' }}">
                    @if($jobsCount === 0)
                        File vide (prête pour le prochain batch)
                    @elseif($jobsCount <= 50)
                        Flux régulier et sain (< 50)
                    @else
                        ⚠️ Embouteillage détecté !
                    @endif
                </div>
            </div>

            <!-- Dernier Envoi Réussi -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Dernier Envoi Réussi</span>
                    <span class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="text-xl font-black text-slate-900">
                    {{ $lastSentLog ? $lastSentLog->updated_at->diffForHumans() : 'Aucun envoi récent' }}
                </div>
                <div class="text-[11px] text-slate-400 mt-1">
                    {{ $lastSentLog ? $lastSentLog->updated_at->format('d/m/Y H:i:s') : 'N/A' }}
                </div>
            </div>

            <!-- Total Envoyés Aujourd'hui -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Envoyés Aujourd'hui</span>
                    <span class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                </div>
                <div class="text-3xl font-black text-emerald-600">
                    {{ number_format($globalTodaySent) }}
                </div>
                <div class="text-[11px] text-slate-400 mt-1">Emails délivrés avec succès</div>
            </div>

            <!-- Échecs Aujourd'hui -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Échecs Aujourd'hui</span>
                    <span class="p-2 {{ $globalTodayFailed > 50 ? 'bg-rose-50 text-rose-600' : 'bg-slate-50 text-slate-600' }} rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </span>
                </div>
                <div class="text-3xl font-black {{ $globalTodayFailed > 50 ? 'text-rose-600' : 'text-slate-700' }}">
                    {{ number_format($globalTodayFailed) }}
                </div>
                <div class="text-[11px] text-slate-400 mt-1">
                    En attente globale : {{ number_format($globalTodayPending) }}
                </div>
            </div>
        </div>

        <!-- ── 2. Jauges des Quotas SMTP par Boîte (Anti-Blocage OVH) ───────── -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-2 mb-6">
                <div>
                    <h3 class="text-lg font-bold text-[#03123F] flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#C57A1E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Surveillance des Quotas SMTP OVH (Limite : 200 emails / heure)
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Consommation en direct sur la dernière heure glissante pour chaque compte configuré.</p>
                </div>
                <div class="flex items-center gap-3 text-xs font-semibold">
                    <span class="flex items-center gap-1.5 text-emerald-700"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Normal (< 130/h)</span>
                    <span class="flex items-center gap-1.5 text-amber-700"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Proche limite (130-180/h)</span>
                    <span class="flex items-center gap-1.5 text-rose-700"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Quota critique (> 180/h)</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($smtpStats as $st)
                    <div class="bg-slate-50/70 border border-slate-200/80 rounded-xl p-4 transition hover:bg-white hover:shadow-sm">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <div class="font-bold text-slate-900 text-sm truncate" title="{{ $st['email'] }}">
                                    {{ $st['email'] }}
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $st['host'] }}:{{ $st['port'] }} ({{ $st['encryption'] }})
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase shrink-0 {{ $st['status'] === 'danger' ? 'bg-rose-100 text-rose-700' : ($st['status'] === 'warning' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">
                                {{ $st['sent_last_h'] }} / 200 h
                            </span>
                        </div>

                        <!-- Barre de progression de quota -->
                        <div class="mt-3">
                            <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500 {{ $st['status'] === 'danger' ? 'bg-rose-500' : ($st['status'] === 'warning' ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $st['pct_hour'] }}%"></div>
                            </div>
                        </div>

                        <div class="mt-3 flex items-center justify-between text-[11px] text-slate-500 pt-2 border-t border-slate-200/50">
                            <span>Aujourd'hui : <strong>{{ number_format($st['sent_today']) }}</strong> envoyés</span>
                            <span>
                                @if($st['campaigns']->count() > 1)
                                    <span class="text-rose-600 font-extrabold" title="Plusieurs campagnes partagent cette boîte !">⚠️ {{ $st['campaigns']->count() }} campagnes</span>
                                @elseif($st['campaigns']->count() === 1)
                                    <span class="text-emerald-700 font-bold">1 campagne</span>
                                @else
                                    <span class="text-slate-400">Aucune campagne</span>
                                @endif
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- ── 3. Campagnes Actives et Actions Rapides ──────────────────────── -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
            <div class="pb-4 border-b border-slate-100 mb-6">
                <h3 class="text-lg font-bold text-[#03123F] flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#C57A1E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Campagnes Actives en Cours d'Envoi ({{ $campaignsData->count() }})
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Gérez et priorisez vos campagnes en direct sans passer par la base de données.</p>
            </div>

            @if($campaignsData->isEmpty())
                <div class="text-center py-12 text-slate-400 text-sm">
                    Aucune campagne active actuellement. Toutes les campagnes sont terminées ou en brouillon.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600 border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-[11px] uppercase font-bold text-slate-500 tracking-wider border-b border-slate-200">
                                <th class="px-4 py-3">Campagne</th>
                                <th class="px-4 py-3">Compte SMTP</th>
                                <th class="px-4 py-3 text-center text-emerald-700">Envoyés</th>
                                <th class="px-4 py-3 text-center text-amber-700">En attente</th>
                                <th class="px-4 py-3 text-center text-rose-700">Échecs</th>
                                <th class="px-4 py-3 text-center">Progression</th>
                                <th class="px-4 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($campaignsData as $item)
                                @php
                                    $c = $item['campaign'];
                                    $smtp = $item['smtp'];
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-4 py-3.5">
                                        <div class="font-bold text-slate-900 flex items-center gap-2">
                                            #{{ $c->id }} {{ $c->nom }}
                                            @if($c->statut === 'en_pause')
                                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-100 text-amber-700">En pause</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-700">En cours</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">Créée le {{ $c->created_at->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-4 py-3.5 text-xs font-semibold text-slate-700">
                                        {{ $smtp?->sender_email ?? $smtp?->username ?? 'Par défaut' }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center font-bold text-emerald-700">
                                        {{ number_format($item['sent']) }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center font-bold text-amber-600">
                                        {{ number_format($item['pending']) }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center font-bold text-rose-600">
                                        {{ number_format($item['failed']) }}
                                    </td>
                                    <td class="px-4 py-3.5 w-44">
                                        <div class="w-full bg-slate-100 rounded-full h-3 border border-slate-200 overflow-hidden">
                                            <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $item['pct'] }}%"></div>
                                        </div>
                                        <div class="text-[10px] text-slate-500 font-semibold mt-1 text-center">{{ $item['pct'] }}% envoyé</div>
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <!-- Mettre en pause / Reprendre -->
                                            <form action="{{ route('system.campaign.pause', $c) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="p-1.5 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-600 text-xs font-bold transition" title="{{ $c->statut === 'en_cours' ? 'Mettre en pause' : 'Reprendre' }}">
                                                    @if($c->statut === 'en_cours')
                                                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    @endif
                                                </button>
                                            </form>

                                            <!-- Clôturer -->
                                            <form action="{{ route('system.campaign.terminate', $c) }}" method="POST" onsubmit="return confirm('Voulez-vous marquer cette campagne comme terminée ? Les emails en attente ne seront plus envoyés.');">
                                                @csrf
                                                <button type="submit" class="p-1.5 rounded-lg border border-slate-200 hover:bg-slate-100 text-rose-600 text-xs font-bold transition" title="Clôturer la campagne">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- ── 4. Dernières Erreurs Détectées ──────────────────────────────── -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
            <div class="pb-4 border-b border-slate-100 mb-6 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Journal des Dernières Erreurs SMTP
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Permet de repérer immédiatement une boîte bloquée, un quota dépassé ou une adresse invalide.</p>
                </div>
            </div>

            @if($recentErrors->isEmpty())
                <div class="text-center py-8 text-slate-400 text-sm">
                    ✅ Aucune erreur récente ! Tous les derniers envois se sont déroulés avec succès.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left text-slate-600 border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] uppercase font-bold text-slate-400 tracking-wider border-b border-slate-200">
                                <th class="px-4 py-2.5">Date / Heure</th>
                                <th class="px-4 py-2.5">Campagne</th>
                                <th class="px-4 py-2.5">Destinataire</th>
                                <th class="px-4 py-2.5">Message d'erreur SMTP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($recentErrors as $err)
                                <tr class="hover:bg-rose-50/30 transition">
                                    <td class="px-4 py-2.5 font-semibold text-slate-700 whitespace-nowrap">
                                        {{ $err->updated_at->format('d/m/Y H:i:s') }}
                                    </td>
                                    <td class="px-4 py-2.5 font-bold text-slate-900">
                                        #{{ $err->campaign_id }} {{ $err->campaign?->nom ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-2.5 text-slate-600 font-mono">
                                        {{ $err->contact?->email ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-2.5 text-rose-600 font-medium">
                                        {{ Str::limit($err->error_message, 120) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
