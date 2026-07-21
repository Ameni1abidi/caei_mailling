<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('categories.index') }}" class="hover:text-indigo-600 transition-colors">Listes</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 font-medium">Nouvelle liste</span>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h1 class="text-xl font-bold text-gray-900">Créer une nouvelle liste</h1>
                <p class="mt-1 text-sm text-gray-500">Définissez un nom, une couleur et une icône pour identifier votre liste</p>
            </div>

            <form action="{{ route('categories.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom de la liste <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors"
                           placeholder="Ex: Banques Côte d'Ivoire">
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors"
                              placeholder="Décrivez l'objectif de cette liste...">{{ old('description') }}</textarea>
                </div>

                {{-- Color & Icon Row --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6" x-data="{ 
                    couleur: '{{ old('couleur', '#6366f1') }}',
                    icone: '{{ old('icone', 'list') }}',
                    colors: [
                        { value: '#6366f1', name: 'Indigo' },
                        { value: '#10b981', name: 'Émeraude' },
                        { value: '#f59e0b', name: 'Ambre' },
                        { value: '#ec4899', name: 'Rose' },
                        { value: '#0ea5e9', name: 'Ciel' },
                        { value: '#8b5cf6', name: 'Violet' },
                        { value: '#ef4444', name: 'Rouge' },
                        { value: '#14b8a6', name: 'Sarcelle' },
                        { value: '#f97316', name: 'Orange' },
                        { value: '#64748b', name: 'Ardoise' },
                    ],
                    icons: [
                        { value: 'list', name: 'Liste' },
                        { value: 'building', name: 'Bâtiment' },
                        { value: 'briefcase', name: 'Business' },
                        { value: 'users', name: 'Utilisateurs' },
                        { value: 'globe', name: 'Globe' },
                        { value: 'shield', name: 'Bouclier' },
                        { value: 'academic-cap', name: 'Formation' },
                        { value: 'link', name: 'Lien' },
                        { value: 'star', name: 'Étoile' },
                    ]
                }">
                    {{-- Color Picker --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Couleur</label>
                        <input type="hidden" name="couleur" :value="couleur">
                        <div class="flex flex-wrap gap-2">
                            <template x-for="color in colors" :key="color.value">
                                <button type="button"
                                        @click="couleur = color.value"
                                        :title="color.name"
                                        class="w-8 h-8 rounded-full border-2 transition-all duration-200 hover:scale-110"
                                        :class="couleur === color.value ? 'border-gray-900 ring-2 ring-offset-2 scale-110' : 'border-transparent'"
                                        :style="'background-color: ' + color.value">
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Icon Selector --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Icône</label>
                        <input type="hidden" name="icone" :value="icone">
                        <select @change="icone = $event.target.value" 
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <template x-for="icon in icons" :key="icon.value">
                                <option :value="icon.value" :selected="icone === icon.value" x-text="icon.name"></option>
                            </template>
                        </select>
                    </div>
                </div>

                {{-- Preview --}}
                <div class="bg-gray-50 rounded-lg p-4" x-data x-effect="
                    $el.querySelector('.preview-bar').style.backgroundColor = couleur;
                    $el.querySelector('.preview-icon-bg').style.backgroundColor = couleur + '20';
                    $el.querySelector('.preview-badge').style.backgroundColor = couleur + '15';
                    $el.querySelector('.preview-badge').style.color = couleur;
                ">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-3">Aperçu</p>
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                        <div class="preview-bar h-2 bg-indigo-500"></div>
                        <div class="p-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="preview-icon-bg w-10 h-10 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                    </svg>
                                </div>
                                <span class="font-semibold text-gray-900" x-text="$refs?.nameInput?.value || 'Nom de la liste'">Nom de la liste</span>
                            </div>
                            <span class="preview-badge inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium">
                                0 contacts
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-medium shadow-sm transition-all duration-200 hover:shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Créer la liste
                    </button>
                    <a href="{{ route('categories.index') }}"
                       class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-800 px-4 py-2.5 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
