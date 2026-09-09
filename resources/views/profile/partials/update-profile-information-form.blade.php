<section>
    <header class="pb-4 border-b border-slate-100">
        <div class="flex items-center gap-2.5">
            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-900">
                    Informations du profil
                </h2>
                <p class="text-xs text-slate-500">
                    Mettez à jour les informations de votre compte et votre adresse email.
                </p>
            </div>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nom complet</label>
            <input id="name" name="name" type="text" class="w-full text-sm py-2.5 px-3.5 border-slate-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            @error('name')
                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Adresse Email</label>
            <input id="email" name="email" type="email" class="w-full text-sm py-2.5 px-3.5 border-slate-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition" :value="old('email', $user->email)" required autocomplete="username" />
            @error('email')
                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-amber-50 rounded-lg border border-amber-200">
                    <p class="text-xs text-amber-800">
                        Votre adresse email n'est pas encore vérifiée.

                        <button form="send-verification" class="underline font-semibold hover:text-amber-900">
                            Cliquez ici pour renvoyer le lien de vérification.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-1.5 font-medium text-xs text-emerald-600">
                            Un nouveau lien de vérification a été envoyé à votre adresse email.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 active:bg-slate-950 text-white font-bold text-sm px-5 py-2.5 rounded-lg shadow-sm transition">
                Enregistrer les modifications
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-xs font-semibold text-emerald-600"
                >Modifications enregistrées.</p>
            @endif
        </div>
    </form>
</section>
