<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto" x-data="{ addModalOpen: false }">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('categories.index') }}" class="hover:text-indigo-600 transition-colors">Listes</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 font-medium">{{ $category->name }}</span>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-6 flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg"
                 x-data="{ show: true }" x-show="show" x-transition
                 x-init="setTimeout(() => show = false, 4000)">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg"
                 x-data="{ show: true }" x-show="show" x-transition
                 x-init="setTimeout(() => show = false, 6000)">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Header Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="h-2" style="background-color: {{ $category->couleur ?? '#6366f1' }}"></div>
            <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background-color: {{ $category->couleur ?? '#6366f1' }}20">
                        @php
                            $icone = $category->icone ?? 'list';
                            $icons = [
                                'list' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>',
                                'building' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
                                'briefcase' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
                                'users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>',
                                'globe' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                                'shield' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
                                'academic-cap' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>',
                                'link' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>',
                                'star' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>',
                            ];
                        @endphp
                        <svg class="w-7 h-7" fill="none" stroke="{{ $category->couleur ?? '#6366f1' }}" viewBox="0 0 24 24">
                            {!! $icons[$icone] ?? $icons['list'] !!}
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $category->name }}</h1>
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold"
                                  style="background-color: {{ $category->couleur ?? '#6366f1' }}15; color: {{ $category->couleur ?? '#6366f1' }}">
                                {{ $contacts->total() }} contact(s)
                            </span>
                        </div>
                        @if($category->description)
                            <p class="mt-1 text-sm text-gray-500">{{ $category->description }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" @click="addModalOpen = true"
                            class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Ajouter des contacts
                    </button>
                    <a href="{{ route('categories.edit', $category) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier
                    </a>
                </div>
            </div>
        </div>

        {{-- Filters & Search Bar --}}
        <form action="{{ route('categories.show', $category) }}" method="GET" class="bg-white rounded-lg shadow-sm p-4 mb-6 border border-gray-100">
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[220px]">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="search" name="search" value="{{ request('search') }}"
                           class="block w-full pl-9 pr-3 py-2 border border-gray-200 rounded-md text-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Rechercher un contact...">
                </div>

                @if($pays->isNotEmpty())
                    <select name="pays" onchange="this.form.submit()" class="px-3 py-2 text-sm border border-gray-200 rounded-md bg-white text-gray-700">
                        <option value="">Tous les pays</option>
                        @foreach($pays as $p)
                            <option value="{{ $p }}" @selected(request('pays') === $p)>{{ $p }}</option>
                        @endforeach
                    </select>
                @endif

                @if($entreprises->isNotEmpty())
                    <select name="entreprise" onchange="this.form.submit()" class="px-3 py-2 text-sm border border-gray-200 rounded-md bg-white text-gray-700">
                        <option value="">Toutes les entreprises</option>
                        @foreach($entreprises as $e)
                            <option value="{{ $e }}" @selected(request('entreprise') === $e)>{{ $e }}</option>
                        @endforeach
                    </select>
                @endif

                @if($secteurs->isNotEmpty())
                    <select name="secteur_activite" onchange="this.form.submit()" class="px-3 py-2 text-sm border border-gray-200 rounded-md bg-white text-gray-700">
                        <option value="">Tous les secteurs</option>
                        @foreach($secteurs as $s)
                            <option value="{{ $s }}" @selected(request('secteur_activite') === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                @endif

                <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Filtrer
                </button>

                @if(request()->hasAny(['search', 'pays', 'entreprise', 'fonction', 'secteur_activite']))
                    <a href="{{ route('categories.show', $category) }}" class="inline-flex items-center px-3 py-2 border border-gray-200 rounded-md text-sm font-medium text-gray-500 bg-white hover:bg-gray-50">
                        Réinitialiser
                    </a>
                @endif
            </div>
        </form>

        {{-- Contacts Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nom & Prénom</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Entreprise</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pays</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Secteur</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-50">
                        @forelse($contacts as $contact)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $contact->nom }} {{ $contact->prenom }}</div>
                                    @if($contact->fonction)
                                        <div class="text-xs text-gray-500">{{ $contact->fonction }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-700">{{ $contact->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-700">{{ $contact->entreprise ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-700">{{ $contact->pays ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-700">{{ $contact->secteur_activite ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <form action="{{ route('categories.removeContact', [$category, $contact]) }}" method="POST"
                                          onsubmit="return confirm('Retirer « {{ $contact->nom }} {{ $contact->prenom }} » de cette liste ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors" title="Retirer de la liste">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Retirer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <p class="text-base font-medium text-gray-900">Aucun contact dans cette liste</p>
                                        <p class="text-sm text-gray-500 mt-1">Ajoutez des contacts existants pour peupler votre liste.</p>
                                        <button type="button" @click="addModalOpen = true"
                                                class="mt-4 inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Ajouter des contacts
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($contacts->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $contacts->links() }}
                </div>
            @endif
        </div>

        {{-- Modal : Ajouter des contacts --}}
        <div x-show="addModalOpen" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="addModalOpen" x-transition.opacity
                     @click="addModalOpen = false"
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="addModalOpen" x-transition
                     class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form action="{{ route('categories.addContacts', $category) }}" method="POST">
                        @csrf
                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                                <h3 class="text-lg font-bold text-gray-900" id="modal-title">
                                    Ajouter des contacts à « {{ $category->name }} »
                                </h3>
                                <button type="button" @click="addModalOpen = false" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            @if($availableContacts->isEmpty())
                                <div class="py-8 text-center text-gray-500">
                                    <p class="text-sm">Tous les contacts enregistrés appartiennent déjà à cette liste.</p>
                                </div>
                            @else
                                <div class="mt-4 max-h-96 overflow-y-auto space-y-2 pr-1" x-data="{ search: '' }">
                                    <div class="mb-3">
                                        <input type="text" x-model="search" placeholder="Filtrer la liste..."
                                               class="w-full text-sm rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    @foreach($availableContacts as $available)
                                        <label x-show="!search || '{{ strtolower(addslashes($available->nom . ' ' . $available->prenom . ' ' . $available->email . ' ' . $available->entreprise)) }}'.includes(search.toLowerCase())"
                                               class="flex items-center justify-between p-3 rounded-lg border border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors">
                                            <div class="flex items-center gap-3">
                                                <input type="checkbox" name="contacts[]" value="{{ $available->id }}"
                                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-800">{{ $available->nom }} {{ $available->prenom }}</div>
                                                    <div class="text-xs text-gray-500">{{ $available->email }} {{ $available->entreprise ? '· ' . $available->entreprise : '' }}</div>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-gray-100">
                            <button type="button" @click="addModalOpen = false"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                Annuler
                            </button>
                            @if($availableContacts->isNotEmpty())
                                <button type="submit"
                                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm">
                                    Ajouter la sélection
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
