@if($errors->any())
    <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-900 p-4 rounded-xl text-sm font-medium shadow-sm">
        <div class="font-bold mb-1">Veuillez corriger les erreurs ci-dessous :</div>
        <ul class="list-disc list-inside space-y-1 text-rose-800 text-xs">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $selectedRoles = old('roles', isset($user) ? $user->roles->pluck('name')->toArray() : []);
@endphp

<div class="space-y-6" x-data="{
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
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-sm space-y-6">
        <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100">
            <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-slate-900 text-base">Identifiants et profil</h2>
                <p class="text-xs text-slate-400">Email et mot de passe de connexion</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nom complet <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}" required placeholder="ex: Jean Dupont"
                       class="w-full text-sm py-2.5 px-3.5 border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-sm">
            </div>

            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Adresse Email <span class="text-rose-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" required placeholder="ex: utilisateur@caei-afri.com"
                       class="w-full text-sm py-2.5 px-3.5 border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-sm">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Mot de passe @if(!isset($user)) <span class="text-rose-500">*</span> @endif
                    </label>
                    <button type="button" 
                            @click="generatePassword()" 
                            class="text-[11px] font-semibold text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Générer
                    </button>
                </div>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" 
                           name="password" 
                           id="password" 
                           x-model="password"
                           @if(!isset($user)) required @endif
                           placeholder="@isset($user) Laisser vide pour conserver @else Minimum 8 caractères @endisset"
                           class="w-full text-sm py-2.5 px-3.5 pr-10 border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-sm">
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
                @isset($user)
                    <p class="mt-1 text-xs text-slate-400">Laissez vide pour conserver le mot de passe actuel.</p>
                @endisset
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Confirmation mot de passe</label>
                <input :type="showPassword ? 'text' : 'password'" 
                       name="password_confirmation" 
                       id="password_confirmation" 
                       x-model="passwordConfirmation"
                       @if(!isset($user)) required @endif
                       placeholder="Répétez le mot de passe"
                       class="w-full text-sm py-2.5 px-3.5 border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-sm">
            </div>
        </div>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100">
            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-slate-900 text-base">Rôles & Permissions</h2>
                <p class="text-xs text-slate-400">Sélectionnez les accès attribués à cet utilisateur</p>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            @foreach($roles as $role)
                <label class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-indigo-300 bg-slate-50/50 hover:bg-indigo-50/30 transition cursor-pointer select-none">
                    <input type="checkbox" name="roles[]" value="{{ $role }}" @checked(in_array($role, $selectedRoles))
                           class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded">
                    <span class="text-xs font-bold text-slate-700">{{ ucfirst($role) }}</span>
                </label>
            @endforeach
        </div>
    </div>
</div>
