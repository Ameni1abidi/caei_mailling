<x-app-layout>
    <div class="p-6 space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3.5 bg-gradient-to-br from-indigo-500 to-blue-600 text-white rounded-2xl shadow-md shadow-indigo-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Gestion des contacts</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Organisez et segmentez votre base de destinataires pour vos campagnes de communication</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <form action="{{ route('contacts.import') }}" method="POST" enctype="multipart/form-data" class="inline-flex" x-data="{ loading: false }">
                    @csrf
                    <input type="file" name="file" id="file-upload" accept=".csv,.xlsx,.txt" class="hidden" x-on:change="loading = true; $el.closest('form').submit()">
                    <label for="file-upload" class="cursor-pointer inline-flex items-center gap-2 px-4 py-2.5 border border-slate-200/80 rounded-xl text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 transition shadow-sm">
                        <svg x-show="!loading" class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <svg x-show="loading" class="w-4 h-4 text-indigo-600 animate-spin" fill="none" viewBox="0 0 24 24" style="display: none;">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="loading ? 'Importation...' : 'Importer CSV/Excel'">Importer CSV/Excel</span>
                    </label>
                </form>

                <a href="{{ route('contacts.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-sm transition duration-150 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Nouveau contact</span>
                </a>
            </div>
        </div>

        <!-- Flash messages -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition class="flex items-center justify-between gap-3 bg-emerald-50 border border-emerald-200 text-emerald-900 p-4 rounded-xl text-sm font-medium shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-1 bg-emerald-100 rounded-lg text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span>{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 p-1 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        <!-- Stats Overview -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm flex items-center gap-3.5">
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-slate-900">{{ method_exists($contacts, 'total') ? $contacts->total() : $contacts->count() }}</div>
                    <div class="text-xs font-medium text-slate-500">Total contacts</div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm flex items-center gap-3.5">
                <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 11h10M7 15h10"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-slate-900">{{ $categories->count() }}</div>
                    <div class="text-xs font-medium text-slate-500">Listes & Catégories</div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm flex items-center gap-3.5">
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h1.5a2.5 2.5 0 002.5-2.5V7a2 2 0 00-2-2h-2c0-1.1.9-2 2-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-slate-900">{{ $paysOptions->count() }}</div>
                    <div class="text-xs font-medium text-slate-500">Pays distincts</div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm flex items-center gap-3.5">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h5m-5 0V10m0 0V5"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-slate-900">{{ $secteurOptions->count() }}</div>
                    <div class="text-xs font-medium text-slate-500">Secteurs d'activité</div>
                </div>
            </div>
        </div>

        <!-- Filters Toolbar -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
            <form action="{{ route('contacts.index') }}" method="GET" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Search input -->
                    <div class="relative sm:col-span-2 lg:col-span-2">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="search" name="search" value="{{ request('search') }}" placeholder="Rechercher par nom, prénom, email, entreprise..." class="w-full text-sm pl-10 pr-4 py-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50">
                    </div>

                    <!-- Category filter -->
                    <div>
                        <select name="category_id" onchange="this.form.submit()" class="w-full text-sm py-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50 text-slate-700">
                            <option value="">Toutes les listes</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }} ({{ $category->contacts_count }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pays filter -->
                    <div>
                        <select name="pays" onchange="this.form.submit()" class="w-full text-sm py-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50 text-slate-700">
                            <option value="">Tous les pays</option>
                            @foreach($paysOptions as $pays)
                                <option value="{{ $pays }}" @selected(request('pays') === $pays)>{{ $pays }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Secteur filter -->
                    <div>
                        <select name="secteur_activite" onchange="this.form.submit()" class="w-full text-sm py-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50 text-slate-700">
                            <option value="">Tous les secteurs</option>
                            @foreach($secteurOptions as $secteur)
                                <option value="{{ $secteur }}" @selected(request('secteur_activite') === $secteur)>{{ $secteur }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1 border-t border-slate-100">
                    <div class="flex items-center gap-2">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-4 py-2 rounded-xl transition shadow-sm">
                            Filtrer
                        </button>
                        @if(request()->hasAny(['search', 'category_id', 'pays', 'secteur_activite', 'status']))
                            <a href="{{ route('contacts.index') }}" class="px-3 py-2 text-slate-500 hover:text-slate-700 text-xs font-semibold flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Réinitialiser les filtres
                            </a>
                        @endif
                    </div>
                    <div class="text-xs text-slate-400 font-medium">
                        Affichage de {{ $contacts->firstItem() ?? 0 }} à {{ $contacts->lastItem() ?? 0 }} sur {{ $contacts->total() ?? 0 }} contacts
                    </div>
                </div>
            </form>
        </div>

        <!-- Contacts Data Table Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50/80 text-xs uppercase font-bold text-slate-500 border-b border-slate-200/80">
                        <tr>
                            <th class="px-6 py-4">Contact</th>
                            <th class="px-6 py-4">Entreprise & Poste</th>
                            <th class="px-6 py-4">Coordonnées</th>
                            <th class="px-6 py-4">Localisation</th>
                            <th class="px-6 py-4">Listes / Catégories</th>
                            <th class="px-6 py-4">Ajouté le</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($contacts as $contact)
                            @php
                                $initials = strtoupper(substr($contact->prenom ?? '', 0, 1) . substr($contact->nom ?? '', 0, 1));
                                if (!$initials) $initials = 'CT';
                                $bgGradient = ['from-blue-500 to-indigo-600', 'from-purple-500 to-pink-600', 'from-emerald-500 to-teal-600', 'from-amber-500 to-orange-600'][abs(crc32($contact->email)) % 4];
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition group">
                                <!-- Contact Name & Avatar -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $bgGradient }} text-white font-extrabold text-xs flex items-center justify-center shadow-sm shrink-0">
                                            {{ $initials }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 group-hover:text-indigo-600 transition">
                                                {{ $contact->prenom }} {{ $contact->nom }}
                                            </div>
                                            @if($contact->source)
                                                <div class="text-xs text-slate-400 mt-0.5">Source: {{ $contact->source }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Company & Role -->
                                <td class="px-6 py-4">
                                    @if($contact->entreprise || $contact->fonction)
                                        <div class="font-semibold text-slate-800">{{ $contact->entreprise ?: '-' }}</div>
                                        <div class="text-xs text-slate-500">{{ $contact->fonction ?: ($contact->secteur_activite ?: '') }}</div>
                                    @else
                                        <span class="text-slate-400 italic">-</span>
                                    @endif
                                </td>

                                <!-- Coordinates (Email / Phone / WhatsApp) -->
                                <td class="px-6 py-4 space-y-1">
                                    <div class="flex items-center gap-1.5 text-slate-700">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <a href="mailto:{{ $contact->email }}" class="hover:text-indigo-600 font-medium truncate max-w-[200px]" title="{{ $contact->email }}">
                                            {{ $contact->email }}
                                        </a>
                                    </div>
                                    @if($contact->whatsapp || $contact->telephone)
                                        <div class="flex items-center gap-2 text-xs text-slate-500">
                                            @if($contact->whatsapp)
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact->whatsapp) }}" target="_blank" class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-800 font-semibold" title="Envoyer WhatsApp">
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-1.156 4.221 4.239-1.111z"/>
                                                    </svg>
                                                    {{ $contact->whatsapp }}
                                                </a>
                                            @elseif($contact->telephone)
                                                <span>{{ $contact->telephone }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                <!-- Location -->
                                <td class="px-6 py-4 text-xs font-semibold text-slate-700">
                                    @if($contact->pays || $contact->ville)
                                        <div>{{ $contact->pays ?: '-' }}</div>
                                        <div class="text-slate-400 font-normal">{{ $contact->ville ?: '' }}</div>
                                    @else
                                        <span class="text-slate-400 font-normal italic">-</span>
                                    @endif
                                </td>

                                <!-- Categories Badges -->
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1 max-w-xs">
                                        @forelse($contact->categories as $cat)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-100">
                                                {{ $cat->name }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-slate-400 italic">Aucune liste</span>
                                        @endforelse
                                    </div>
                                </td>

                                <!-- Created At -->
                                <td class="px-6 py-4 text-xs text-slate-500 font-medium">
                                    {{ $contact->created_at ? $contact->created_at->format('d/m/Y') : '-' }}
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('contacts.edit', $contact) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Modifier">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        <form action="{{ route('contacts.destroy', $contact) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce contact ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Supprimer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                    <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-bold text-slate-800">Aucun contact trouvé</h3>
                                    <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">Commencez par ajouter votre premier contact ou importez un fichier CSV/Excel.</p>
                                    <div class="pt-4 flex justify-center gap-3">
                                        <a href="{{ route('contacts.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-4 py-2 rounded-xl transition shadow-sm">
                                            Ajouter un contact
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($contacts->hasPages())
                <div class="px-6 py-4 border-t border-slate-200/80 bg-slate-50/50">
                    {{ $contacts->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
