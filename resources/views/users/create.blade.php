<x-app-layout>
    <div class="p-6 max-w-7xl mx-auto space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-xl border border-slate-200/80 shadow-sm">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Ajouter un utilisateur</h1>
                <p class="text-sm text-slate-500 mt-0.5">Creez un compte et attribuez ses roles.</p>
            </div>
            <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-4 py-2.5 rounded-lg border border-slate-200 transition">
                Retour a la liste
            </a>
        </div>

        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            @include('users._form')

            <div class="flex items-center justify-end gap-3 pt-6 mt-6 border-t border-slate-200/80 bg-white p-6 rounded-xl border border-slate-200/80 shadow-sm">
                <a href="{{ route('users.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-900 transition">Annuler</a>
                <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold text-sm px-6 py-2.5 rounded-lg shadow-md shadow-indigo-100 transition duration-150">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
