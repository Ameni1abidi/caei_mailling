<x-app-layout>
    <div class="p-6 space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3.5 bg-gradient-to-br from-indigo-500 to-blue-600 text-white rounded-2xl shadow-md shadow-indigo-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Campagnes</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Pilotez, planifiez et suivez l'envoi de vos emails de masse</p>
                </div>
            </div>
            <a href="{{ route('campaigns.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-sm transition-all duration-150 transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Nouvelle campagne</span>
            </a>
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

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition class="flex items-center justify-between gap-3 bg-rose-50 border border-rose-200 text-rose-900 p-4 rounded-xl text-sm font-medium shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-1 bg-rose-100 rounded-lg text-rose-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
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

        <!-- Quick Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm flex items-center gap-3.5">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-slate-900">{{ $stats['total'] ?? 0 }}</div>
                    <div class="text-xs font-medium text-slate-500">Total campagnes</div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm flex items-center gap-3.5">
                <div class="p-3 bg-slate-100 text-slate-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-slate-900">{{ $stats['brouillon'] ?? 0 }}</div>
                    <div class="text-xs font-medium text-slate-500">Brouillons</div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm flex items-center gap-3.5">
                <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-slate-900">{{ $stats['en_cours'] ?? 0 }}</div>
                    <div class="text-xs font-medium text-slate-500">En cours</div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm flex items-center gap-3.5">
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-slate-900">{{ $stats['envoyee'] ?? 0 }}</div>
                    <div class="text-xs font-medium text-slate-500">Envoyées</div>
                </div>
            </div>
        </div>

        <!-- Table View -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50/80 text-xs uppercase font-bold text-slate-500 border-b border-slate-200/80">
                        <tr>
                            <th class="px-6 py-4">Campagne</th>
                            <th class="px-6 py-4">Objet</th>
                            <th class="px-6 py-4">Cible</th>
                            <th class="px-6 py-4">Statut</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($campaigns as $campaign)
                            <tr class="hover:bg-slate-50/50 transition duration-150">
                                <td class="px-6 py-4 font-bold text-slate-900">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <span class="truncate max-w-[200px]" title="{{ $campaign->nom }}">{{ $campaign->nom }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-600 truncate max-w-xs" title="{{ $campaign->objet }}">
                                    {{ $campaign->objet }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        @php
                                            $targetLabels = [];
                                            foreach ($campaign->categoryIds() as $categoryId) {
                                                $category = \App\Models\Category::find($categoryId);
                                                if ($category) {
                                                    $targetLabels[] = $category->name;
                                                }
                                            }
                                        @endphp
                                        {{ $targetLabels !== [] ? implode(', ', $targetLabels) : 'Tous les contacts' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border transition
                                        @if($campaign->statut === 'brouillon') bg-slate-50 text-slate-700 border-slate-200
                                        @elseif($campaign->statut === 'envoyee') bg-emerald-50 text-emerald-700 border-emerald-200
                                        @else bg-amber-50 text-amber-700 border-amber-200 @endif">
                                        <span class="w-1.5 h-1.5 rounded-full 
                                            @if($campaign->statut === 'brouillon') bg-slate-400
                                            @elseif($campaign->statut === 'envoyee') bg-emerald-500
                                            @else bg-amber-500 animate-pulse @endif"></span>
                                        <span>
                                            @if($campaign->statut === 'brouillon') Brouillon
                                            @elseif($campaign->statut === 'envoyee') Envoyée
                                            @else En cours @endif
                                        </span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('campaigns.preview', $campaign) }}" class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Aperçu du rendu">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>

                                        <a href="{{ route('campaigns.edit', $campaign) }}" class="p-2 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Modifier la campagne">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        @if($campaign->statut === 'brouillon')
                                            <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette campagne ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Supprimer la campagne">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <div class="p-2 text-slate-300 cursor-not-allowed" title="Suppression impossible (déjà envoyée ou en cours)">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    <div class="w-16 h-16 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-3 border border-slate-200">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-bold text-slate-800">Aucune campagne trouvée</h3>
                                    <p class="text-xs text-slate-500 max-w-xs mx-auto mt-1">Créez votre première campagne d'emails en cliquant sur le bouton ci-dessus.</p>
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