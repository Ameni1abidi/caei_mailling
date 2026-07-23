<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight tracking-tight">
            {{ __('Tableau de bord & Statistiques') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Statistiques globales -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- MODULE 8 : Campagnes -->
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-blue-600 to-sky-500 shadow-[0_10px_40px_-10px_rgba(79,70,229,0.5)] transition-all hover:-translate-y-1 duration-300">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-10">
                        <svg class="w-48 h-48 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                        </svg>
                    </div>
                    <div class="p-8 relative z-10 text-white">
                        <div class="flex items-center space-x-3 mb-8">
                            <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <h3 class="text-2xl font-bold tracking-wide">Campagnes</h3>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center bg-white/10 rounded-2xl p-5 backdrop-blur-md border border-white/20 transition-all hover:bg-white/20">
                                <span class="text-blue-50 font-medium text-lg">Envoyées</span>
                                <span class="text-4xl font-extrabold tracking-tight">{{ $campagnesEnvoyees }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-white/10 rounded-2xl p-5 backdrop-blur-md border border-white/20 transition-all hover:bg-white/20">
                                <span class="text-blue-50 font-medium text-lg">Programmées</span>
                                <span class="text-4xl font-extrabold tracking-tight">{{ $campagnesProgrammees }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODULE 8 : Emails -->
                <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden transition-all hover:shadow-2xl duration-300 relative">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-bl-full -z-10 opacity-50"></div>
                    <div class="p-8">
                        <div class="flex items-center space-x-3 mb-8">
                            <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-800 tracking-wide">Performances Emails</h3>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-5">
                            <div class="group flex flex-col p-5 bg-slate-50 rounded-2xl border border-slate-100 transition-all hover:bg-indigo-50 hover:border-indigo-100">
                                <div class="text-sm text-slate-500 font-semibold uppercase tracking-wider mb-2 group-hover:text-indigo-600 transition-colors">Envoyés</div>
                                <div class="text-3xl font-black text-slate-800">{{ $emailsEnvoyes }}</div>
                            </div>
                            <div class="group flex flex-col p-5 bg-slate-50 rounded-2xl border border-slate-100 transition-all hover:bg-emerald-50 hover:border-emerald-100">
                                <div class="text-sm text-slate-500 font-semibold uppercase tracking-wider mb-2 group-hover:text-emerald-600 transition-colors">Délivrés</div>
                                <div class="text-3xl font-black text-emerald-500">{{ $emailsDelivres }}</div>
                            </div>
                            <div class="group flex flex-col p-5 bg-slate-50 rounded-2xl border border-slate-100 transition-all hover:bg-sky-50 hover:border-sky-100">
                                <div class="text-sm text-slate-500 font-semibold uppercase tracking-wider mb-2 group-hover:text-sky-600 transition-colors">Ouverts</div>
                                <div class="text-3xl font-black text-sky-500">{{ $emailsOuverts }}</div>
                            </div>
                            <div class="group flex flex-col p-5 bg-slate-50 rounded-2xl border border-slate-100 transition-all hover:bg-purple-50 hover:border-purple-100">
                                <div class="text-sm text-slate-500 font-semibold uppercase tracking-wider mb-2 group-hover:text-purple-600 transition-colors">Clics</div>
                                <div class="text-3xl font-black text-purple-500">{{ $emailsClics }}</div>
                            </div>
                            <div class="col-span-2 group flex justify-between items-center p-5 bg-red-50/50 rounded-2xl border border-red-100 transition-all hover:bg-red-50">
                                <div class="text-sm text-red-500 font-bold uppercase tracking-wider">Rejetés (Erreurs)</div>
                                <div class="text-3xl font-black text-red-600">{{ $emailsRejetes }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MODULE 9 : Suivi des prospects -->
            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 p-8 relative overflow-hidden">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                    <div class="flex items-center space-x-3">
                        <div class="p-2.5 bg-gradient-to-br from-indigo-500 to-blue-600 text-white rounded-xl shadow-md">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18l-7 8v6l-4 2v-8L3 4z" />
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-100 text-indigo-700">MODULE 9</span>
                                <h3 class="text-2xl font-bold text-slate-800 tracking-wide">Suivi des prospects</h3>
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5">Cycle de qualification des prospects après campagnes</p>
                        </div>
                    </div>

                    <a href="{{ route('prospects.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold text-xs rounded-xl shadow-md transition self-start md:self-auto">
                        <span>Ouvrir le Pipeline</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-500 mb-1">
                            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                            Nouveau prospect
                        </div>
                        <div class="text-2xl font-black text-slate-800">{{ $prospectStats['nouveau'] ?? 0 }}</div>
                    </div>

                    <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-100">
                        <div class="flex items-center gap-1.5 text-xs font-semibold text-blue-700 mb-1">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            Email envoyé
                        </div>
                        <div class="text-2xl font-black text-blue-700">{{ $prospectStats['envoye'] ?? 0 }}</div>
                    </div>

                    <div class="p-4 rounded-2xl bg-indigo-50/60 border border-indigo-100">
                        <div class="flex items-center gap-1.5 text-xs font-semibold text-indigo-700 mb-1">
                            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                            Email ouvert
                        </div>
                        <div class="text-2xl font-black text-indigo-700">{{ $prospectStats['ouvert'] ?? 0 }}</div>
                    </div>

                    <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-100">
                        <div class="flex items-center gap-1.5 text-xs font-semibold text-amber-700 mb-1">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            Intéressé
                        </div>
                        <div class="text-2xl font-black text-amber-700">{{ $prospectStats['interesse'] ?? 0 }}</div>
                    </div>

                    <div class="p-4 rounded-2xl bg-rose-50/60 border border-rose-100">
                        <div class="flex items-center gap-1.5 text-xs font-semibold text-rose-700 mb-1">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            À relancer
                        </div>
                        <div class="text-2xl font-black text-rose-700">{{ $prospectStats['relancer'] ?? 0 }}</div>
                    </div>

                    <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100">
                        <div class="flex items-center gap-1.5 text-xs font-semibold text-emerald-700 mb-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Client
                        </div>
                        <div class="text-2xl font-black text-emerald-700">{{ $prospectStats['client'] ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <!-- Exemple de campagne -->
            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                <div class="p-8">
                    <div class="flex justify-between items-center mb-8">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-800 tracking-wide">Récentes Campagnes</h3>
                        </div>
                        <span class="px-4 py-1.5 bg-indigo-100 text-indigo-700 text-sm font-bold rounded-full shadow-sm">Top 10</span>
                    </div>
                    
                    @if($campaignsWithStats->isEmpty())
                        <div class="flex flex-col items-center justify-center py-16 text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                            <svg class="w-16 h-16 mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <p class="text-lg font-medium">Aucune campagne disponible pour le moment.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($campaignsWithStats as $campaign)
                                @php
                                    $tauxOuverture = $campaign->envoyes_count > 0 ? round(($campaign->ouverts_count / $campaign->envoyes_count) * 100) : 0;
                                    $tauxClic = $campaign->envoyes_count > 0 ? round(($campaign->clics_count / $campaign->envoyes_count) * 100) : 0;
                                @endphp
                                <div class="group relative bg-white border border-slate-100 rounded-2xl p-6 hover:border-indigo-300 hover:shadow-lg transition-all duration-300 overflow-hidden z-10">
                                    <!-- Hover highlight bar -->
                                    <div class="absolute left-0 top-0 h-full w-1.5 bg-gradient-to-b from-indigo-500 to-sky-400 transform scale-y-0 group-hover:scale-y-100 transition-transform origin-bottom duration-300"></div>
                                    
                                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pl-2">
                                        <div class="md:w-1/3">
                                            <h4 class="font-bold text-slate-800 text-lg group-hover:text-indigo-600 transition-colors">{{ $campaign->nom }}</h4>
                                            <div class="flex items-center text-xs text-slate-400 mt-2 space-x-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span>{{ $campaign->updated_at ? $campaign->updated_at->diffForHumans() : 'N/A' }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-3 gap-4 md:w-2/3">
                                            <div class="text-center p-3 rounded-xl bg-slate-50 group-hover:bg-white transition-colors">
                                                <div class="text-[11px] text-slate-400 uppercase tracking-widest mb-1.5 font-bold">Envoyés</div>
                                                <div class="font-black text-slate-800 text-xl">{{ $campaign->envoyes_count }}</div>
                                            </div>
                                            <div class="text-center p-3 rounded-xl bg-emerald-50/50 group-hover:bg-emerald-50 transition-colors">
                                                <div class="text-[11px] text-slate-400 uppercase tracking-widest mb-1.5 font-bold">Délivrés</div>
                                                <div class="font-black text-emerald-600 text-xl">{{ $campaign->delivered_count }}</div>
                                            </div>
                                            <div class="text-center p-3 rounded-xl bg-red-50/50 group-hover:bg-red-50 transition-colors">
                                                <div class="text-[11px] text-slate-400 uppercase tracking-widest mb-1.5 font-bold">Rejetés</div>
                                                <div class="font-black text-red-500 text-xl">{{ $campaign->bounced_count }}</div>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4 md:w-2/3">
                                            <div class="text-center p-3 rounded-xl bg-amber-50/50 group-hover:bg-amber-50 transition-colors">
                                                <div class="text-[11px] text-slate-400 uppercase tracking-widest mb-1.5 font-bold">Invalides</div>
                                                <div class="font-black text-amber-700 text-xl">{{ $campaign->invalid_count }}</div>
                                            </div>
                                            <div class="text-center p-3 rounded-xl bg-sky-50/50 group-hover:bg-sky-50 transition-colors">
                                                <div class="text-[11px] text-slate-400 uppercase tracking-widest mb-1.5 font-bold">Ouverture</div>
                                                <div class="font-black text-sky-600 text-xl">{{ $tauxOuverture }}%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
