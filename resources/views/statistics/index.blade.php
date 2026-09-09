<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight tracking-tight">
            Statistiques
        </h2>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ── Filters ─────────────────────────────────────────── --}}
            <form method="GET" action="{{ route('statistics.index') }}"
                  class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <div class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Période</label>
                        <select name="period"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700 focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                            @foreach(['7' => '7 derniers jours', '30' => '30 derniers jours', '90' => '90 derniers jours', '365' => 'Cette année', 'all' => 'Tout'] as $val => $label)
                                <option value="{{ $val }}" {{ $period === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 min-w-[220px]">
                        <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Campagne</label>
                        <select name="campaign_id"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700 focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                            <option value="">— Toutes les campagnes —</option>
                            @foreach($campaigns as $c)
                                <option value="{{ $c->id }}" {{ (string)$campaignId === (string)$c->id ? 'selected' : '' }}>
                                    {{ $c->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                            class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow transition">
                        Filtrer
                    </button>
                    @if($period !== '30' || $campaignId)
                        <a href="{{ route('statistics.index') }}"
                           class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold rounded-xl transition">
                            Réinitialiser
                        </a>
                    @endif
                </div>
            </form>

            {{-- ── KPI Cards ───────────────────────────────────────── --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                @php
                    $kpis = [
                        ['label' => 'Envoyés',     'value' => number_format($totalSent),     'color' => 'indigo', 'sub' => $deliveryRate.'% taux livraison'],
                        ['label' => 'Ouverts',     'value' => number_format($totalOpened),   'color' => 'sky',    'sub' => $openRate.'% taux ouverture'],
                        ['label' => 'Clics',       'value' => number_format($totalClicked),  'color' => 'purple', 'sub' => $clickRate.'% taux clic'],
                        ['label' => 'Rebonds',     'value' => number_format($totalBounced),  'color' => 'amber',  'sub' => $bounceRate.'% taux rebond'],
                        ['label' => 'Rejetés',     'value' => number_format($totalRejected), 'color' => 'red',    'sub' => ($totalFailed + $totalInvalid).' erreurs/invalides'],
                    ];
                    $colorMap = [
                        'indigo' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'dot' => 'bg-indigo-500'],
                        'sky'    => ['bg' => 'bg-sky-50',    'text' => 'text-sky-700',    'dot' => 'bg-sky-500'],
                        'purple' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'dot' => 'bg-purple-500'],
                        'amber'  => ['bg' => 'bg-amber-50',  'text' => 'text-amber-700',  'dot' => 'bg-amber-500'],
                        'red'    => ['bg' => 'bg-red-50',    'text' => 'text-red-700',    'dot' => 'bg-red-500'],
                    ];
                @endphp
                @foreach($kpis as $kpi)
                    @php $c = $colorMap[$kpi['color']]; @endphp
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-2.5 h-2.5 rounded-full {{ $c['dot'] }}"></span>
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ $kpi['label'] }}</span>
                        </div>
                        <div class="text-3xl font-black {{ $c['text'] }}">{{ $kpi['value'] }}</div>
                        <div class="text-xs text-slate-400 mt-1">{{ $kpi['sub'] }}</div>
                    </div>
                @endforeach
            </div>

            {{-- ── Charts row ──────────────────────────────────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Donut — statut global --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <h3 class="text-base font-bold text-slate-800 mb-4">Répartition des emails</h3>
                    <div class="relative flex items-center justify-center" style="height:200px">
                        <canvas id="donutChart"></canvas>
                        <div class="absolute text-center pointer-events-none">
                            <div class="text-2xl font-black text-slate-800">{{ number_format($totalAll) }}</div>
                            <div class="text-xs text-slate-400 font-semibold">total</div>
                        </div>
                    </div>
                    <div class="mt-4 space-y-1.5">
                        @php
                            $donutItems = [
                                ['label' => 'Envoyés',  'value' => $totalSent,     'color' => '#6366f1'],
                                ['label' => 'Ouverts',  'value' => $totalOpened,   'color' => '#0ea5e9'],
                                ['label' => 'Cliqués',  'value' => $totalClicked,  'color' => '#a855f7'],
                                ['label' => 'Rebondis', 'value' => $totalBounced,  'color' => '#f59e0b'],
                                ['label' => 'Échoués',  'value' => $totalFailed + $totalInvalid, 'color' => '#ef4444'],
                            ];
                        @endphp
                        @foreach($donutItems as $item)
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:{{ $item['color'] }}"></span>
                                    <span class="text-slate-600 font-medium">{{ $item['label'] }}</span>
                                </div>
                                <span class="font-bold text-slate-800">{{ number_format($item['value']) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Line chart — daily trend --}}
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <h3 class="text-base font-bold text-slate-800 mb-4">Évolution quotidienne</h3>
                    @if($dailyEmails->isEmpty())
                        <div class="flex items-center justify-center h-48 text-slate-400 text-sm">
                            Aucune donnée pour la période sélectionnée.
                        </div>
                    @else
                        <div style="height:220px">
                            <canvas id="lineChart"></canvas>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── Prospect funnel ─────────────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <h3 class="text-base font-bold text-slate-800 mb-5">Entonnoir de qualification des prospects</h3>
                @php
                    $funnelSteps = [
                        ['key' => 'Nouveau prospect', 'label' => 'Nouveau',       'color' => 'bg-slate-400'],
                        ['key' => 'Email envoyé',      'label' => 'Email envoyé', 'color' => 'bg-blue-500'],
                        ['key' => 'Email ouvert',      'label' => 'Email ouvert', 'color' => 'bg-indigo-500'],
                        ['key' => 'Intéressé',         'label' => 'Intéressé',    'color' => 'bg-amber-500'],
                        ['key' => 'À relancer',        'label' => 'À relancer',   'color' => 'bg-rose-500'],
                        ['key' => 'Client',            'label' => 'Client',       'color' => 'bg-emerald-500'],
                    ];
                    $funnelTotal = max(1, $prospectFunnel->sum());
                @endphp
                <div class="space-y-3">
                    @foreach($funnelSteps as $step)
                        @php
                            $count = (int) ($prospectFunnel->get($step['key'], 0));
                            $pct   = round(($count / $funnelTotal) * 100);
                        @endphp
                        <div class="flex items-center gap-4">
                            <div class="w-28 text-xs font-semibold text-slate-600 text-right shrink-0">{{ $step['label'] }}</div>
                            <div class="flex-1 bg-slate-100 rounded-full h-5 overflow-hidden">
                                <div class="{{ $step['color'] }} h-full rounded-full transition-all duration-700"
                                     style="width: {{ max(1, $pct) }}%"></div>
                            </div>
                            <div class="w-20 text-xs font-bold text-slate-700">
                                {{ number_format($count) }} <span class="text-slate-400 font-normal">({{ $pct }}%)</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Per-campaign table ──────────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-800">Détail par campagne</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3 text-left">Campagne</th>
                                <th class="px-4 py-3 text-center">Statut</th>
                                <th class="px-4 py-3 text-center">Envoyés</th>
                                <th class="px-4 py-3 text-center">Ouverts</th>
                                <th class="px-4 py-3 text-center">Clics</th>
                                <th class="px-4 py-3 text-center">Rebonds</th>
                                <th class="px-4 py-3 text-center">Échecs</th>
                                <th class="px-4 py-3 text-center">Tx. ouverture</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($perCampaign as $c)
                                @php
                                    $openPct = $c->sent_count > 0 ? round(($c->opened_count / $c->sent_count) * 100, 1) : 0;
                                    $statusMap = [
                                        'brouillon' => ['label' => 'Brouillon', 'class' => 'bg-slate-100 text-slate-600'],
                                        'en_cours'  => ['label' => 'En cours',  'class' => 'bg-blue-100 text-blue-700'],
                                        'envoyee'   => ['label' => 'Envoyée',   'class' => 'bg-emerald-100 text-emerald-700'],
                                        'annulee'   => ['label' => 'Annulée',   'class' => 'bg-red-100 text-red-600'],
                                    ];
                                    $badge = $statusMap[$c->statut] ?? ['label' => $c->statut, 'class' => 'bg-slate-100 text-slate-600'];
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-800">{{ $c->nom }}</div>
                                        <div class="text-xs text-slate-400">{{ $c->created_at->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $badge['class'] }}">
                                            {{ $badge['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center font-bold text-slate-700">{{ number_format($c->sent_count) }}</td>
                                    <td class="px-4 py-4 text-center font-bold text-sky-600">{{ number_format($c->opened_count) }}</td>
                                    <td class="px-4 py-4 text-center font-bold text-purple-600">{{ number_format($c->clicked_count) }}</td>
                                    <td class="px-4 py-4 text-center font-bold text-amber-600">{{ number_format($c->bounced_count) }}</td>
                                    <td class="px-4 py-4 text-center font-bold text-red-500">{{ number_format($c->failed_count) }}</td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <div class="w-16 bg-slate-100 rounded-full h-1.5">
                                                <div class="bg-sky-500 h-full rounded-full" style="width:{{ min(100, $openPct) }}%"></div>
                                            </div>
                                            <span class="font-bold text-slate-700 text-xs">{{ $openPct }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                        Aucune campagne trouvée pour cette période.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($perCampaign->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $perCampaign->links() }}
                    </div>
                @endif
            </div>

            {{-- ── Top bounced domains ─────────────────────────────── --}}
            @if($topBouncedDomains->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <h3 class="text-base font-bold text-slate-800 mb-4">Top 5 domaines avec rebonds</h3>
                @php $maxBounce = $topBouncedDomains->first()->cnt; @endphp
                <div class="space-y-3">
                    @foreach($topBouncedDomains as $domain)
                        @php $pct = round(($domain->cnt / $maxBounce) * 100); @endphp
                        <div class="flex items-center gap-4">
                            <div class="w-44 font-mono text-xs text-slate-600 text-right shrink-0 truncate">{{ $domain->domain }}</div>
                            <div class="flex-1 bg-slate-100 rounded-full h-4 overflow-hidden">
                                <div class="bg-amber-400 h-full rounded-full" style="width:{{ $pct }}%"></div>
                            </div>
                            <div class="text-xs font-bold text-slate-700 w-10">{{ $domain->cnt }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>

    {{-- ── Chart.js ────────────────────────────────────────────────── --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // ── Donut chart ───────────────────────────────────────────────
        const donutCtx = document.getElementById('donutChart');
        if (donutCtx) {
            new Chart(donutCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Envoyés', 'Ouverts', 'Cliqués', 'Rebondis', 'Échoués'],
                    datasets: [{
                        data: [
                            {{ $totalSent }},
                            {{ $totalOpened }},
                            {{ $totalClicked }},
                            {{ $totalBounced }},
                            {{ $totalFailed + $totalInvalid }}
                        ],
                        backgroundColor: ['#6366f1','#0ea5e9','#a855f7','#f59e0b','#ef4444'],
                        borderWidth: 0,
                        hoverOffset: 6,
                    }]
                },
                options: {
                    cutout: '72%',
                    plugins: { legend: { display: false }, tooltip: { callbacks: {
                        label: ctx => ` ${ctx.label}: ${ctx.parsed.toLocaleString()}`
                    }}},
                    animation: { animateRotate: true, duration: 800 }
                }
            });
        }

        // ── Line chart ────────────────────────────────────────────────
        const lineCtx = document.getElementById('lineChart');
        if (lineCtx) {
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
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99,102,241,0.08)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 3,
                        },
                        {
                            label: 'Ouverts',
                            data: opened,
                            borderColor: '#0ea5e9',
                            backgroundColor: 'transparent',
                            borderWidth: 2,
                            tension: 0.4,
                            pointRadius: 3,
                        },
                        {
                            label: 'Rejetés',
                            data: rej,
                            borderColor: '#ef4444',
                            backgroundColor: 'transparent',
                            borderWidth: 2,
                            borderDash: [4, 4],
                            tension: 0.4,
                            pointRadius: 2,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 10, font: { size: 11 }, padding: 14 }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 }, maxTicksLimit: 10 }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: { font: { size: 10 } }
                        }
                    },
                    animation: { duration: 600 }
                }
            });
        }
    </script>
</x-app-layout>
