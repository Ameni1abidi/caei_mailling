<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8">
        <!-- En-tête -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-gradient-to-br from-indigo-500 to-blue-600 text-white rounded-2xl shadow-lg shadow-indigo-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-100 text-indigo-700 uppercase tracking-wider">Espace Admin</span>
                        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Monitoring d'activité des utilisateurs</h1>
                    </div>
                    <p class="mt-0.5 text-xs text-slate-500">Suivez en temps réel les actions, campagnes et emails envoyés par chaque utilisateur de l'équipe.</p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('users.index') }}"
                   class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-xl text-xs font-bold border border-slate-200/80 shadow-sm transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Gestion des comptes
                </a>
                <a href="{{ route('users.create') }}"
                   class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white px-4 py-2.5 rounded-xl text-xs font-bold shadow-md shadow-indigo-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajouter un utilisateur
                </a>
            </div>
        </div>

        <!-- Statistiques Globales de l'Équipe -->
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Utilisateurs</span>
                    <span class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-slate-900">{{ $globalStats['total_users'] }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Comptes enregistrés</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Campagnes</span>
                    <span class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-blue-600">{{ $globalStats['total_campaigns'] }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Créées par l'équipe</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Emails Envoyés</span>
                    <span class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-emerald-600">{{ $globalStats['total_emails_sent'] }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Total volume envoyé</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Taux Ouverture</span>
                    <span class="p-2 bg-sky-50 text-sky-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-sky-600">{{ $globalStats['global_open_rate'] }}%</div>
                <div class="text-[11px] text-slate-400 mt-1">{{ $globalStats['total_emails_opened'] }} ouvertures</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm col-span-2 sm:col-span-1">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Taux de Clics</span>
                    <span class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                        </svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-purple-600">{{ $globalStats['global_click_rate'] }}%</div>
                <div class="text-[11px] text-slate-400 mt-1">{{ $globalStats['total_emails_clicked'] }} clics</div>
            </div>
        </div>

        <!-- Filtre de recherche -->
        <form method="GET" action="{{ route('users.monitoring') }}" class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-[1fr_220px_auto] gap-3">
                <input type="search" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Rechercher par nom ou email d'un utilisateur..."
                       class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                <select name="role" class="rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    <option value="">Tous les rôles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role }}" @selected(request('role') === $role)>{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-2.5 text-xs font-bold text-white hover:bg-slate-800 transition">
                    Filtrer
                </button>
            </div>
        </form>

        <!-- Tableau détaillé de suivi par utilisateur -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Activité individuelle des utilisateurs</h2>
                    <p class="text-xs text-slate-400">Détail des emails, campagnes et taux d'engagement par utilisateur</p>
                </div>
                <span class="px-3 py-1 bg-slate-100 text-slate-700 text-xs font-bold rounded-full">
                    {{ count($userStatsList) }} compte(s)
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/70">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Utilisateur</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-600 uppercase tracking-wider">Campagnes</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-600 uppercase tracking-wider">Emails Envoyés</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-600 uppercase tracking-wider">Taux Ouverture</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-600 uppercase tracking-wider">Taux Clics</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-600 uppercase tracking-wider">Contacts Importés</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Dernière Activité</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($userStatsList as $item)
                            @php
                                $u = $item['user'];
                                $st = $item['stats'];
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- Utilisateur -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-indigo-100 to-blue-100 text-sm font-bold text-indigo-700 border border-indigo-200">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                                {{ $u->name }}
                                                @if(auth()->id() === $u->id)
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-100 text-indigo-700">Vous</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-slate-500">{{ $u->email }}</div>
                                            <div class="mt-1 flex flex-wrap gap-1">
                                                @foreach($u->roles as $role)
                                                    <span class="inline-flex rounded-full bg-slate-100 text-slate-700 px-2 py-0.5 text-[10px] font-semibold">
                                                        {{ ucfirst($role->name) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Campagnes -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-base font-black text-slate-900">{{ $st['total_campaigns'] }}</div>
                                    <div class="text-[11px] text-slate-400 space-x-1">
                                        <span class="text-emerald-600 font-semibold">{{ $st['sent_campaigns'] }} env.</span>
                                        <span>•</span>
                                        <span class="text-amber-600 font-semibold">{{ $st['draft_campaigns'] }} br.</span>
                                    </div>
                                </td>

                                <!-- Emails Envoyés -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-base font-black text-slate-900">{{ $st['emails_sent'] }}</div>
                                    @if($st['emails_failed'] > 0)
                                        <div class="text-[10px] text-rose-500 font-semibold">{{ $st['emails_failed'] }} erreur(s)</div>
                                    @else
                                        <div class="text-[10px] text-emerald-600 font-semibold">0 erreur</div>
                                    @endif
                                </td>

                                <!-- Taux Ouverture -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        <span class="text-sm font-black text-sky-600">{{ $st['open_rate'] }}%</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400">{{ $st['emails_opened'] }} ouverts</div>
                                </td>

                                <!-- Taux Clics -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        <span class="text-sm font-black text-purple-600">{{ $st['click_rate'] }}%</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400">{{ $st['emails_clicked'] }} clics</div>
                                </td>

                                <!-- Contacts Importés -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm font-black text-slate-800">{{ $st['contacts_imported'] }}</div>
                                    <div class="text-[10px] text-slate-400">contacts</div>
                                </td>

                                <!-- Dernière Activité -->
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">
                                    @if($st['last_activity'])
                                        <div>{{ \Carbon\Carbon::parse($st['last_activity'])->diffForHumans() }}</div>
                                        <div class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($st['last_activity'])->format('d/m/Y H:i') }}</div>
                                    @else
                                        <span class="text-slate-400 italic">Aucune</span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-xs">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('users.show', $u) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Détail de l'activité
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-sm text-slate-500">
                                    Aucun utilisateur trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
