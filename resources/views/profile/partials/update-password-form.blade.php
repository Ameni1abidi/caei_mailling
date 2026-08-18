<section>
    <header class="pb-4 border-b border-slate-100">
        <div class="flex items-center gap-2.5">
            <div class="p-2 bg-amber-50 text-amber-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-900">
                    Modifier votre mot de passe
                </h2>
                <p class="text-xs text-slate-500">
                    Assurez-vous que votre compte utilise un mot de passe robuste pour garantir sa sécurité.
                </p>
            </div>
        </div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Mot de passe actuel</label>
            <input id="update_password_current_password" name="current_password" type="password" class="w-full text-sm py-2.5 px-3.5 border-slate-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition" autocomplete="current-password" />
            @if($errors->updatePassword->get('current_password'))
                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $errors->updatePassword->first('current_password') }}</p>
            @endif
        </div>

        <div>
            <label for="update_password_password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nouveau mot de passe</label>
            <input id="update_password_password" name="password" type="password" class="w-full text-sm py-2.5 px-3.5 border-slate-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition" autocomplete="new-password" />
            @if($errors->updatePassword->get('password'))
                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $errors->updatePassword->first('password') }}</p>
            @endif
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Confirmer le mot de passe</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="w-full text-sm py-2.5 px-3.5 border-slate-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition" autocomplete="new-password" />
            @if($errors->updatePassword->get('password_confirmation'))
                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $errors->updatePassword->first('password_confirmation') }}</p>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 active:bg-slate-950 text-white font-bold text-sm px-5 py-2.5 rounded-lg shadow-sm transition">
                Mettre à jour le mot de passe
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-xs font-semibold text-emerald-600"
                >Mot de passe mis à jour avec succès.</p>
            @endif
        </div>
    </form>
</section>
