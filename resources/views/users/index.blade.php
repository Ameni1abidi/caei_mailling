<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Gestion des utilisateurs</h1>
                <p class="mt-1 text-sm text-gray-500">Administrez les comptes et leurs roles d'acces.</p>
            </div>
            <a href="{{ route('users.create') }}"
               class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium shadow-sm transition-all duration-200 hover:shadow-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouvel utilisateur
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg"
                 x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 4000)">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg"
                 x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 6000)">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <form method="GET" action="{{ route('users.index') }}" class="mb-6 bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-[1fr_220px_auto] gap-3">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Rechercher par nom ou email"
                       class="w-full rounded-lg border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <select name="role" class="rounded-lg border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Tous les roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role }}" @selected(request('role') === $role)>{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                    Filtrer
                </button>
            </div>
        </form>

        <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] border border-gray-50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Utilisateur</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Roles</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cree le</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-50">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-700">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        @forelse($user->roles as $role)
                                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ ucfirst($role->name) }}</span>
                                        @empty
                                            <span class="text-xs italic text-gray-400">Aucun role</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $user->created_at?->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('users.edit', $user) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg text-amber-700 bg-amber-50 hover:bg-amber-100 transition-colors">
                                            Modifier
                                        </a>
                                        @if(!auth()->user()->is($user))
                                            <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                  onsubmit="return confirm('Supprimer l utilisateur {{ $user->name }} ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition-colors">
                                                    Supprimer
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">Aucun utilisateur trouve.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
