<div class="space-y-8" x-data="{
    showAddForm: true,
    password: '',
    passwordConfirmation: '',
    showPassword: false,
    generatePassword() {
        const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*';
        let res = '';
        for (let i = 0; i < 12; i++) {
            res += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        this.password = res;
        this.passwordConfirmation = res;
    }
}">
    <!-- Section Header & Overview -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-gradient-to-br from-indigo-500 to-blue-600 text-white rounded-xl shadow-md shadow-indigo-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 uppercase tracking-wider">Administration</span>
                        <h2 class="text-xl font-bold text-slate-900">Gestion des utilisateurs</h2>
                    </div>
                    <p class="text-sm text-slate-500 mt-0.5">Créez des accès avec email et mot de passe, et gérez les comptes existants.</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('users.monitoring') }}" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Monitoring d'activité
                </a>
                <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg border border-slate-200/80 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    Tous les comptes
                </a>
            </div>
        </div>

        <!-- Formulaire d'ajout rapide d'un utilisateur -->
        <div class="mt-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700">+</span>
                    Ajouter un nouvel utilisateur
                </h3>
                <span class="text-xs text-slate-400">Remplissez les informations de connexion ci-dessous</span>
            </div>

            <form action="{{ route('users.store') }}" method="POST" class="bg-slate-50/70 border border-slate-200/80 rounded-xl p-5 sm:p-6 space-y-5">
                @csrf
                <input type="hidden" name="source" value="settings">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Nom -->
                    <div>
                        <label for="new_user_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nom complet <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="new_user_name" 
                               value="{{ old('name') }}" 
                               required
                               placeholder="ex: Jean Dupont"
                               class="w-full text-sm py-2.5 px-3.5 border-slate-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition">
                        @error('name')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="new_user_email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Adresse Email <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" 
                               name="email" 
                               id="new_user_email" 
                               value="{{ old('email') }}" 
                               required
                               placeholder="ex: jean.dupont@caei-afri.com"
                               class="w-full text-sm py-2.5 px-3.5 border-slate-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition">
                        @error('email')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mot de passe -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="new_user_password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Mot de passe <span class="text-rose-500">*</span>
                            </label>
                            <button type="button" 
                                    @click="generatePassword()" 
                                    class="text-[11px] font-semibold text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Générer un mot de passe
                            </button>
                        </div>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" 
                                   name="password" 
                                   id="new_user_password" 
                                   x-model="password"
                                   required
                                   placeholder="Minimum 8 caractères"
                                   class="w-full text-sm py-2.5 px-3.5 pr-10 border-slate-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition">
                            <button type="button" 
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirmation du mot de passe -->
                    <div>
                        <label for="new_user_password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Confirmation du mot de passe <span class="text-rose-500">*</span>
                        </label>
                        <input :type="showPassword ? 'text' : 'password'" 
                               name="password_confirmation" 
                               id="new_user_password_confirmation" 
                               x-model="passwordConfirmation"
                               required
                               placeholder="Répétez le mot de passe"
                               class="w-full text-sm py-2.5 px-3.5 border-slate-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition">
                    </div>
                </div>

                <!-- Attribution des Rôles -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Rôles & Permissions
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5">
                        @foreach($roles as $role)
                            <label class="flex items-center gap-2.5 p-3 rounded-lg border border-slate-200 bg-white hover:border-indigo-300 hover:bg-indigo-50/30 transition cursor-pointer select-none">
                                <input type="checkbox" 
                                       name="roles[]" 
                                       value="{{ $role }}"
                                       @checked(old('roles') && in_array($role, old('roles')))
                                       class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded">
                                <span class="text-xs font-bold text-slate-700">{{ ucfirst($role) }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('roles')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bouton d'action -->
                <div class="flex items-center justify-end pt-3 border-t border-slate-200">
                    <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold text-sm px-6 py-2.5 rounded-lg shadow-md shadow-indigo-200 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        Créer l'utilisateur
                    </button>
                </div>
            </form>
        </div>

        <!-- Liste des utilisateurs existants -->
        <div class="mt-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Utilisateurs enregistrés ({{ $users->count() }})
                </h3>
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200 shadow-sm">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Utilisateur</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Rôles</th>
                            <th class="px-5 py-3.5 text-center text-xs font-bold text-slate-600 uppercase tracking-wider">Emails / Campagnes</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Date de création</th>
                            <th class="px-5 py-3.5 text-right text-xs font-bold text-slate-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @forelse($users as $u)
                            @php($st = $u->stats)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                                {{ $u->name }}
                                                @if(auth()->id() === $u->id)
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700">Vous</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-slate-500">{{ $u->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex flex-wrap gap-1.5">
                                        @forelse($u->roles as $role)
                                            <span class="inline-flex rounded-full bg-slate-100 text-slate-700 px-2.5 py-0.5 text-xs font-semibold">
                                                {{ ucfirst($role->name) }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-slate-400 italic">Aucun rôle</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-center text-xs">
                                    <div class="font-bold text-slate-900">{{ $st['emails_sent'] }} emails</div>
                                    <div class="text-[10px] text-slate-400">{{ $st['total_campaigns'] }} campagne(s)</div>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-xs text-slate-600">
                                    {{ $u->created_at?->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-right text-xs">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('users.show', $u) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-bold rounded-lg text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition-colors">
                                            Suivi
                                        </a>
                                        <a href="{{ route('users.edit', $u) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium rounded-lg text-amber-700 bg-amber-50 hover:bg-amber-100 transition-colors">
                                            Modifier
                                        </a>
                                        @if(auth()->id() !== $u->id)
                                            <form action="{{ route('users.destroy', $u) }}" method="POST" onsubmit="return confirm('Confirmez-vous la suppression de {{ $u->name }} ?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="source" value="settings">
                                                <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium rounded-lg text-rose-700 bg-rose-50 hover:bg-rose-100 transition-colors">
                                                    Supprimer
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-sm text-slate-500">
                                    Aucun utilisateur trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
