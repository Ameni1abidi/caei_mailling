<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8">
        <!-- En-tête Utilisateur -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white text-2xl font-black shadow-lg shadow-indigo-100">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $user->name }}</h1>
                            @if(auth()->id() === $user->id)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-indigo-100 text-indigo-700">Vous</span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-500 mt-0.5">{{ $user->email }}</p>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            @forelse($user->roles as $role)
                                <span class="inline-flex rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100 px-3 py-0.5 text-xs font-bold uppercase tracking-wider">
                                    {{ ucfirst($role->name) }}
                                </span>
                            @empty
                                <span class="text-xs text-slate-400 italic">Aucun rôle attribué</span>
                            @endforelse
                            <span class="text-xs text-slate-400">• Inscrit le {{ $user->created_at?->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <a href="{{ route('users.monitoring') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour au monitoring
                    </a>
                    <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Modifier le compte
                    </a>
                </div>
            </div>

            <!-- KPI Cards Utilisateur -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mt-6">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Campagnes créées</div>
                    <div class="text-2xl font-black text-slate-900">{{ $stats['total_campaigns'] }}</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">{{ $stats['sent_campaigns'] }} envoyée(s)</div>
                </div>

                <div class="p-4 rounded-xl bg-blue-50/60 border border-blue-100">
                    <div class="text-xs font-bold text-blue-700 uppercase tracking-wider mb-1">Emails Envoyés</div>
                    <div class="text-2xl font-black text-blue-700">{{ $stats['emails_sent'] }}</div>
                    <div class="text-[10px] text-blue-500 mt-0.5">{{ $stats['emails_delivered'] }} délivrés</div>
                </div>

                <div class="p-4 rounded-xl bg-sky-50/60 border border-sky-100">
                    <div class="text-xs font-bold text-sky-700 uppercase tracking-wider mb-1">Taux Ouverture</div>
                    <div class="text-2xl font-black text-sky-700">{{ $stats['open_rate'] }}%</div>
                    <div class="text-[10px] text-sky-500 mt-0.5">{{ $stats['emails_opened'] }} ouvertures</div>
                </div>

                <div class="p-4 rounded-xl bg-purple-50/60 border border-purple-100">
                    <div class="text-xs font-bold text-purple-700 uppercase tracking-wider mb-1">Taux Clics</div>
                    <div class="text-2xl font-black text-purple-700">{{ $stats['click_rate'] }}%</div>
                    <div class="text-[10px] text-purple-500 mt-0.5">{{ $stats['emails_clicked'] }} clics</div>
                </div>

                <div class="p-4 rounded-xl bg-amber-50/60 border border-amber-100">
                    <div class="text-xs font-bold text-amber-700 uppercase tracking-wider mb-1">Contacts Importés</div>
                    <div class="text-2xl font-black text-amber-700">{{ $stats['contacts_imported'] }}</div>
                    <div class="text-[10px] text-amber-500 mt-0.5">{{ $imports->count() }} fichier(s)</div>
                </div>

                <div class="p-4 rounded-xl bg-rose-50/60 border border-rose-100">
                    <div class="text-xs font-bold text-rose-700 uppercase tracking-wider mb-1">Erreurs / Rejets</div>
                    <div class="text-2xl font-black text-rose-700">{{ $stats['emails_failed'] }}</div>
                    <div class="text-[10px] text-rose-500 mt-0.5">Échecs d'envoi</div>
                </div>
            </div>
        </div>

        <!-- Historique des Campagnes de cet Utilisateur -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Campagnes gérées par {{ $user->name }}</h2>
                    <p class="text-xs text-slate-400">Performances détaillées de chaque campagne créée par cet utilisateur</p>
                </div>
                <span class="px-3 py-1 bg-slate-100 text-slate-700 text-xs font-bold rounded-full">
                    {{ $campaigns->total() }} campagne(s)
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/70">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Campagne</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-600 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-600 uppercase tracking-wider">Destinataires</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-600 uppercase tracking-wider">Délivrés</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-600 uppercase tracking-wider">Ouvertures</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-600 uppercase tracking-wider">Clics</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-600 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($campaigns as $camp)
                            @php
                                $campOpenRate = $camp->envoyes_count > 0 ? round(($camp->ouverts_count / $camp->envoyes_count) * 100, 1) : 0;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-slate-900">{{ $camp->nom }}</div>
                                    <div class="text-xs text-slate-400 truncate max-w-xs">{{ $camp->objet }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($camp->statut === 'envoyee')
                                        <span class="inline-flex rounded-full bg-emerald-100 text-emerald-800 px-2.5 py-0.5 text-xs font-bold">Envoyée</span>
                                    @elseif($camp->statut === 'en_cours')
                                        <span class="inline-flex rounded-full bg-blue-100 text-blue-800 px-2.5 py-0.5 text-xs font-bold">En cours</span>
                                    @elseif($camp->statut === 'annulee')
                                        <span class="inline-flex rounded-full bg-rose-100 text-rose-800 px-2.5 py-0.5 text-xs font-bold">Annulée</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-slate-100 text-slate-700 px-2.5 py-0.5 text-xs font-bold">Brouillon</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-slate-800 text-sm">
                                    {{ $camp->envoyes_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-emerald-600 text-sm">
                                    {{ $camp->delivered_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                    <div class="font-bold text-sky-600">{{ $camp->ouverts_count }}</div>
                                    <div class="text-[10px] text-slate-400">({{ $campOpenRate }}%)</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-purple-600 text-sm">
                                    {{ $camp->clics_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">
                                    {{ $camp->created_at?->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-xs">
                                    <a href="{{ route('campaigns.preview', $camp) }}" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 font-bold">
                                        Voir
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-sm text-slate-500">
                                    Cet utilisateur n'a créé aucune campagne pour le moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($campaigns->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $campaigns->links() }}
                </div>
            @endif
        </div>

        <!-- Historique des Imports de Contacts -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Imports de fichiers & contacts réalisés</h2>
            @if($imports->isEmpty())
                <p class="text-sm text-slate-400 italic">Aucun import réalisé par cet utilisateur.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($imports as $imp)
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-800 truncate">{{ $imp->filename ?? 'Import' }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">{{ $imp->imported }} contacts</span>
                            </div>
                            <div class="text-[11px] text-slate-400">
                                {{ $imp->created_at?->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
