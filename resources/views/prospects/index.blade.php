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
            <!-- STATISTICS DASHBOARD VIEW -->
            <div class="space-y-6">
                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <!-- Pie / Doughnut Chart: Répartition par statut -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2.5 bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-xl shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-extrabold text-slate-900">Répartition par Statut</h3>
                                <p class="text-xs text-slate-400">Distribution des prospects dans le pipeline</p>
                            </div>
                        </div>
                        <div class="relative" style="height: 320px;">
                            <canvas id="statusPieChart"></canvas>
                        </div>
                        <div id="pieChartMessage" class="mt-4 text-center text-sm font-semibold text-indigo-600 h-6 transition-all duration-300 opacity-0 transform translate-y-2">
                            <!-- Message will be injected here via JS -->
                        </div>
                    </div>

                    <!-- Bar Chart: Volume par statut -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2.5 bg-gradient-to-br from-sky-500 to-blue-600 text-white rounded-xl shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-extrabold text-slate-900">Volume par Étape</h3>
                                <p class="text-xs text-slate-400">Nombre de prospects à chaque étape du funnel</p>
                            </div>
                        </div>
                        <div class="relative" style="height: 320px;">
                            <canvas id="statusBarChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Summary Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Total -->
                    <div class="bg-gradient-to-br from-slate-800 to-slate-900 text-white p-5 rounded-2xl shadow-lg">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-300">Total Prospects</span>
                            <div class="p-2 bg-white/10 rounded-lg backdrop-blur-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-4xl font-black tracking-tight">{{ $totalProspects }}</div>
                    </div>

                    <!-- Taux de conversion (Email envoyé → Ouvert) -->
                    @php
                        $envoyeCount = $stats[\App\Models\Contact::STATUS_EMAIL_ENVOYE] ?? 0;
                        $ouvertCount = $stats[\App\Models\Contact::STATUS_EMAIL_OUVERT] ?? 0;
                        $interesseCount = $stats[\App\Models\Contact::STATUS_INTERESSE] ?? 0;
                        $clientCount = $stats[\App\Models\Contact::STATUS_CLIENT] ?? 0;
                        $tauxOuverture = $envoyeCount > 0 ? round(($ouvertCount / ($envoyeCount + $ouvertCount + $interesseCount + $clientCount)) * 100, 1) : 0;
                        $tauxConversion = $totalProspects > 0 ? round(($clientCount / $totalProspects) * 100, 1) : 0;
                        $tauxEngagement = $totalProspects > 0 ? round((($ouvertCount + $interesseCount + $clientCount) / $totalProspects) * 100, 1) : 0;
                    @endphp
                    <div class="bg-gradient-to-br from-sky-500 to-blue-600 text-white p-5 rounded-2xl shadow-lg">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-sky-100">Taux d'engagement</span>
                            <div class="p-2 bg-white/10 rounded-lg backdrop-blur-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-4xl font-black tracking-tight">{{ $tauxEngagement }}%</div>
                        <p class="text-xs text-sky-200 mt-1">Ouvert + Intéressé + Client</p>
                    </div>

                    <!-- Taux de conversion client -->
                    <div class="bg-gradient-to-br from-emerald-500 to-teal-600 text-white p-5 rounded-2xl shadow-lg">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-100">Taux de conversion</span>
                            <div class="p-2 bg-white/10 rounded-lg backdrop-blur-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-4xl font-black tracking-tight">{{ $tauxConversion }}%</div>
                        <p class="text-xs text-emerald-200 mt-1">Prospects convertis en client</p>
                    </div>

                    <!-- À relancer -->
                    <div class="bg-gradient-to-br from-rose-500 to-pink-600 text-white p-5 rounded-2xl shadow-lg">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-rose-100">À relancer</span>
                            <div class="p-2 bg-white/10 rounded-lg backdrop-blur-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-4xl font-black tracking-tight">{{ $stats[\App\Models\Contact::STATUS_A_RELANCER] ?? 0 }}</div>
                        <p class="text-xs text-rose-200 mt-1">Prospects nécessitant une relance</p>
                    </div>
                </div>

                <!-- Funnel Visualization -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2.5 bg-gradient-to-br from-amber-500 to-orange-600 text-white rounded-xl shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18l-7 8v6l-4 2v-8L3 4z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-900">Tunnel de Conversion</h3>
                            <p class="text-xs text-slate-400">Progression visuelle des prospects dans le pipeline</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @php
                            $funnelSteps = [
                                ['key' => \App\Models\Contact::STATUS_NOUVEAU, 'color' => 'bg-slate-400', 'barBg' => 'bg-slate-100'],
                                ['key' => \App\Models\Contact::STATUS_EMAIL_ENVOYE, 'color' => 'bg-blue-500', 'barBg' => 'bg-blue-50'],
                                ['key' => \App\Models\Contact::STATUS_EMAIL_OUVERT, 'color' => 'bg-indigo-500', 'barBg' => 'bg-indigo-50'],
                                ['key' => \App\Models\Contact::STATUS_INTERESSE, 'color' => 'bg-amber-500', 'barBg' => 'bg-amber-50'],
                                ['key' => \App\Models\Contact::STATUS_A_RELANCER, 'color' => 'bg-rose-500', 'barBg' => 'bg-rose-50'],
                                ['key' => \App\Models\Contact::STATUS_CLIENT, 'color' => 'bg-emerald-500', 'barBg' => 'bg-emerald-50'],
                            ];
                            $maxCount = max(1, max(array_values($stats) ?: [1]));
                        @endphp
                        @foreach($funnelSteps as $step)
                            @php
                                $stepCount = $stats[$step['key']] ?? 0;
                                $stepPercent = $totalProspects > 0 ? round(($stepCount / $totalProspects) * 100, 1) : 0;
                                $barWidth = $maxCount > 0 ? round(($stepCount / $maxCount) * 100) : 0;
                            @endphp
                            <div class="flex items-center gap-4">
                                <div class="w-40 shrink-0 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $step['color'] }} shrink-0"></span>
                                    <span class="text-sm font-bold text-slate-700 truncate">{{ $statuses[$step['key']]['label'] }}</span>
                                </div>
                                <div class="flex-1 {{ $step['barBg'] }} rounded-full h-8 overflow-hidden relative">
                                    <div class="{{ $step['color'] }} h-full rounded-full transition-all duration-700 ease-out flex items-center justify-end pr-3"
                                         style="width: {{ max($barWidth, 2) }}%; min-width: {{ $stepCount > 0 ? '40px' : '0' }}">
                                        @if($stepCount > 0)
                                            <span class="text-xs font-black text-white drop-shadow">{{ $stepCount }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="w-14 text-right">
                                    <span class="text-sm font-bold text-slate-500">{{ $stepPercent }}%</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Chart.js -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const labels = {!! json_encode(array_values(array_map(fn($s) => $s['label'], $statuses))) !!};
                const data = {!! json_encode(array_values(array_map(fn($key) => $stats[$key] ?? 0, array_keys($statuses)))) !!};
                const colors = ['#94a3b8', '#3b82f6', '#6366f1', '#f59e0b', '#f43f5e', '#10b981'];

                // Doughnut Chart
                new Chart(document.getElementById('statusPieChart'), {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: colors,
                            borderColor: '#ffffff',
                            borderWidth: 3,
                            hoverBorderWidth: 0,
                            hoverOffset: 12
                        }]
                    },
                    options: {
                        onClick: (e, activeEls) => {
                            const msgDiv = document.getElementById('pieChartMessage');
                            if (activeEls.length > 0) {
                                const index = activeEls[0].index;
                                const label = labels[index];
                                msgDiv.textContent = 'Statut sélectionné : ' + label;
                                msgDiv.classList.remove('opacity-0', 'translate-y-2');
                            } else {
                                msgDiv.classList.add('opacity-0', 'translate-y-2');
                            }
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 16,
                                    usePointStyle: true,
                                    pointStyleWidth: 12,
                                    font: { size: 12, weight: '600', family: "'Inter', 'Figtree', sans-serif" },
                                    color: '#475569'
                                }
                            },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                titleFont: { size: 13, weight: '700' },
                                bodyFont: { size: 12 },
                                padding: 12,
                                cornerRadius: 10,
                                callbacks: {
                                    label: function(ctx) {
                                        const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                        const pct = total > 0 ? ((ctx.raw / total) * 100).toFixed(1) : 0;
                                        return ` ${ctx.label}: ${ctx.raw} (${pct}%)`;
                                    }
                                }
                            }
                        }
                    }
                });

                // Bar Chart
                new Chart(document.getElementById('statusBarChart'), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Prospects',
                            data: data,
                            backgroundColor: colors.map(c => c + 'cc'),
                            borderColor: colors,
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                titleFont: { size: 13, weight: '700' },
                                bodyFont: { size: 12 },
                                padding: 12,
                                cornerRadius: 10,
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    font: { size: 11, weight: '600' },
                                    color: '#94a3b8'
                                },
                                grid: { color: '#f1f5f9' }
                            },
                            x: {
                                ticks: {
                                    font: { size: 10, weight: '700' },
                                    color: '#64748b',
                                    maxRotation: 45
                                },
                                grid: { display: false }
                            }
                        }
                    }
                });
            });
            </script>
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
                                    $meta = $statuses[$contact->prospect_status] ?? [
                                        'label' => $contact->prospect_status ?? 'Inconnu',
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
                                                    <option value="{{ $stKey }}" {{ $contact->prospect_status === $stKey ? 'selected' : '' }}>
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
                                            <button @click="openStatusModal({{ $contact->id }}, '{{ addslashes($contact->prenom . ' ' . $contact->nom) }}', '{{ $contact->prospect_status }}')"
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
