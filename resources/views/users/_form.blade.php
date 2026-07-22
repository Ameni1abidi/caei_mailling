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

<div class="space-y-6">
    <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-sm space-y-5">
        <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
            <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <h2 class="font-bold text-slate-900 text-base">Informations du compte</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nom <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}" required
                       class="w-full text-sm py-2.5 px-3.5 border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50">
            </div>

            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email <span class="text-rose-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" required
                       class="w-full text-sm py-2.5 px-3.5 border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Mot de passe @if(!isset($user)) <span class="text-rose-500">*</span> @endif
                </label>
                <input type="password" name="password" id="password" @if(!isset($user)) required @endif
                       class="w-full text-sm py-2.5 px-3.5 border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50">
                @isset($user)
                    <p class="mt-1 text-xs text-slate-400">Laissez vide pour conserver le mot de passe actuel.</p>
                @endisset
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Confirmation</label>
                <input type="password" name="password_confirmation" id="password_confirmation" @if(!isset($user)) required @endif
                       class="w-full text-sm py-2.5 px-3.5 border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50">
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h2 class="font-bold text-slate-900 text-base">Roles</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($roles as $role)
                <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200/70 hover:border-indigo-200 bg-slate-50/40 hover:bg-indigo-50/40 transition cursor-pointer select-none">
                    <input type="checkbox" name="roles[]" value="{{ $role }}" @checked(in_array($role, $selectedRoles))
                           class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded">
                    <span class="text-sm font-semibold text-slate-700">{{ ucfirst($role) }}</span>
                </label>
            @endforeach
        </div>
    </div>
</div>
