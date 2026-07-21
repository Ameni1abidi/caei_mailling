<x-app-layout>
    <div class="p-8 bg-[#f8f9fa] min-h-screen">
        <!-- Header -->
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Contacts</h1>
                <nav class="flex text-sm text-gray-500 mt-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-2">
                        <li class="inline-flex items-center">
                            <a href="#" class="hover:text-gray-700 transition-colors">Accueil</a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <span class="mx-2 text-gray-400">/</span>
                                <span class="text-gray-700 font-medium">Contacts</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
            
            <div class="flex items-center space-x-4">
                <form action="{{ route('contacts.import') }}" method="POST" enctype="multipart/form-data" class="inline-flex" x-data>
                    @csrf
                    <input type="file" name="file" id="file-upload" class="hidden" x-on:change="$el.closest('form').submit()">
                    <label for="file-upload" class="cursor-pointer px-5 py-2.5 border border-gray-200 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-sm">
                        Importer
                    </label>
                </form>

                <a href="{{ route('contacts.create') }}" class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#1d4ed8] hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Ajouter un contact
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded-md shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filters Bar -->
        <form action="{{ route('contacts.index') }}" method="GET" class="bg-white rounded-lg shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] p-3 mb-6 border border-gray-100">
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative flex-1 min-w-[220px] md:max-w-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        class="block w-full pl-9 pr-3 py-2 border border-gray-200 rounded-md leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors hover:border-gray-300"
                        placeholder="Rechercher..."
                    >
                </div>

                <select name="category_id" onchange="this.form.submit()" class="w-36 px-3 py-2 text-sm font-medium text-gray-600 border border-gray-200 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-md bg-white hover:border-gray-300 transition-colors cursor-pointer">
                    <option value="">Liste</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>

                <select name="pays" onchange="this.form.submit()" class="w-36 px-3 py-2 text-sm font-medium text-gray-600 border border-gray-200 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-md bg-white hover:border-gray-300 transition-colors cursor-pointer">
                    <option value="">Pays</option>
                    @foreach($paysOptions as $pays)
                        <option value="{{ $pays }}" @selected(request('pays') === $pays)>{{ $pays }}</option>
                    @endforeach
                </select>

                <select name="secteur_activite" onchange="this.form.submit()" class="w-40 px-3 py-2 text-sm font-medium text-gray-600 border border-gray-200 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-md bg-white hover:border-gray-300 transition-colors cursor-pointer">
                    <option value="">Secteur</option>
                    @foreach($secteurOptions as $secteur)
                        <option value="{{ $secteur }}" @selected(request('secteur_activite') === $secteur)>{{ $secteur }}</option>
                    @endforeach
                </select>

                @if($hasStatusColumn)
                    <select name="status" onchange="this.form.submit()" class="w-36 px-3 py-2 text-sm font-medium text-gray-600 border border-gray-200 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-md bg-white hover:border-gray-300 transition-colors cursor-pointer">
                        <option value="">Statut</option>
                        @foreach($statusOptions as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                @endif

                <button type="submit" class="inline-flex h-10 w-10 items-center justify-center border border-gray-200 rounded-md text-gray-500 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all" title="Filtrer">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                </button>

                @if(request()->hasAny(['search', 'category_id', 'pays', 'secteur_activite', 'status']))
                    <a href="{{ route('contacts.index') }}" class="inline-flex h-10 px-3 items-center justify-center border border-gray-200 rounded-md text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 transition-all">
                        Réinitialiser
                    </a>
                @endif
            </div>
        </form>

        <!-- Table Container -->
        <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] border border-gray-50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-white">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 tracking-wider w-12">
                                <div class="flex items-center">
                                    <input type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cursor-pointer">
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-900 tracking-wider">Nom</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-900 tracking-wider">Entreprise</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-900 tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-900 tracking-wider">Pays</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-900 tracking-wider">Liste</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-900 tracking-wider">Date d'ajout</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-900 tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-50">
                        @forelse($contacts as $contact)
                        <tr class="hover:bg-gray-50/70 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <input type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cursor-pointer">
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-800">{{ $contact->nom }} {{ $contact->prenom }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600">{{ $contact->entreprise ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600">{{ $contact->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600">{{ $contact->pays ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600">{{ $contact->categories->pluck('name')->join(', ') ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600">{{ $contact->created_at ? $contact->created_at->format('d M Y') : '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="relative flex justify-start" x-data="{ open: false }">
                                    <button @click="open = !open" @click.away="open = false" type="button" class="text-gray-400 hover:text-gray-800 focus:outline-none rounded-full p-1.5 hover:bg-gray-100 transition-colors">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                        </svg>
                                    </button>
                                    <div x-show="open" 
                                         x-transition:enter="transition ease-out duration-150" 
                                         x-transition:enter-start="transform opacity-0 scale-95" 
                                         x-transition:enter-end="transform opacity-100 scale-100" 
                                         x-transition:leave="transition ease-in duration-100" 
                                         x-transition:leave-start="transform opacity-100 scale-100" 
                                         x-transition:leave-end="transform opacity-0 scale-95" 
                                         class="origin-top-right absolute right-8 top-1 mt-0 w-48 rounded-lg shadow-xl bg-white border border-gray-100 divide-y divide-gray-50 z-50" 
                                         style="display: none;">
                                        <div class="py-1">
                                            <a href="{{ route('contacts.edit', $contact) }}" class="group flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600 transition-colors">
                                                <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                                Modifier
                                            </a>
                                        </div>
                                        <div class="py-1">
                                            <form action="{{ route('contacts.destroy', $contact) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce contact ?')" class="group flex w-full items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors">
                                                    <svg class="mr-3 h-4 w-4 text-red-400 group-hover:text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 whitespace-nowrap text-sm text-gray-500 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-50 rounded-full p-4 mb-4">
                                        <svg class="h-8 w-8 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <p class="text-base font-medium text-gray-900 mb-1">Aucun contact trouvé</p>
                                    <p class="text-gray-500 mb-4">Commencez par ajouter votre premier contact ou importez une liste.</p>
                                    <a href="{{ route('contacts.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-blue-600 bg-blue-50 hover:bg-blue-100">
                                        Ajouter un contact
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Footer -->
            @if($contacts->hasPages() || (method_exists($contacts, 'total') && $contacts->total() > 0))
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-white">
                <div class="text-sm text-gray-500 font-medium">
                    Total : <span class="text-gray-900">{{ method_exists($contacts, 'total') ? $contacts->total() : $contacts->count() }}</span> contacts
                </div>
                <div class="pagination-wrapper">
                    @if(method_exists($contacts, 'links'))
                        {{ $contacts->links() }}
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
