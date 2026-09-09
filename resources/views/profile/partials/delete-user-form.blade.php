<section class="space-y-6">
    <header class="pb-4 border-b border-slate-100">
        <div class="flex items-center gap-2.5">
            <div class="p-2 bg-rose-50 text-rose-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-900">
                    Suppression du compte
                </h2>
                <p class="text-xs text-slate-500">
                    Une fois votre compte supprimé, toutes ses ressources et données seront définitivement effacées.
                </p>
            </div>
        </div>
    </header>

    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white text-xs font-bold rounded-lg shadow-sm transition"
    >
        Supprimer mon compte
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-slate-900">
                Êtes-vous sûr de vouloir supprimer votre compte ?
            </h2>

            <p class="mt-2 text-xs text-slate-500">
                Cette action est irréversible. Veuillez saisir votre mot de passe pour confirmer la suppression définitive de votre compte.
            </p>

            <div class="mt-4">
                <label for="delete_account_password" class="sr-only">Mot de passe</label>
                <input
                    id="delete_account_password"
                    name="password"
                    type="password"
                    class="w-full text-sm py-2.5 px-3.5 border-slate-300 rounded-lg bg-white focus:ring-2 focus:ring-rose-500 focus:border-rose-500 shadow-sm"
                    placeholder="Saisissez votre mot de passe actuel"
                />
                @if($errors->userDeletion->get('password'))
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $errors->userDeletion->first('password') }}</p>
                @endif
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                    Annuler
                </button>

                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-lg shadow-sm transition">
                    Confirmer la suppression
                </button>
            </div>
        </form>
    </x-modal>
</section>
