@if($errors->any())
    <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-900 p-4 rounded-xl text-sm font-medium shadow-sm flex items-start gap-3">
        <div class="p-1 bg-rose-100 rounded-lg text-rose-600 shrink-0 mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <div class="font-bold text-rose-900 mb-1">Veuillez corriger les erreurs ci-dessous :</div>
            <ul class="list-disc list-inside space-y-1 text-rose-800 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

@php
    $selectedCategories = old('categories', isset($contact) ? $contact->categories->pluck('id')->toArray() : []);
@endphp

<div class="space-y-6">
    <!-- Section 1: Informations Personnelles & Contact -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
        <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
            <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <h2 class="font-bold text-slate-900 text-base">Identité & Coordonnées</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="nom" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nom <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nom" id="nom" value="{{ old('nom', $contact->nom ?? '') }}" required placeholder="Ex: Ben Ali" class="w-full text-sm py-2.5 px-3.5 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50">
            </div>

            <div>
                <label for="prenom" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Prénom <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="prenom" id="prenom" value="{{ old('prenom', $contact->prenom ?? '') }}" required placeholder="Ex: Amel" class="w-full text-sm py-2.5 px-3.5 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50">
            </div>

            <div class="md:col-span-2">
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Adresse Email <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <input type="email" name="email" id="email" value="{{ old('email', $contact->email ?? '') }}" required placeholder="exemple@entreprise.com" class="w-full text-sm py-2.5 pl-10 pr-3.5 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50">
                </div>
            </div>

            <div>
                <label for="telephone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Téléphone
                </label>
                <input type="text" name="telephone" id="telephone" value="{{ old('telephone', $contact->telephone ?? '') }}" placeholder="+216 20 000 000" class="w-full text-sm py-2.5 px-3.5 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50">
            </div>

            <div>
                <label for="whatsapp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Numéro WhatsApp
                </label>
                <input type="text" name="whatsapp" id="whatsapp" value="{{ old('whatsapp', $contact->whatsapp ?? '') }}" placeholder="+216 20 000 000" class="w-full text-sm py-2.5 px-3.5 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50">
            </div>
        </div>
    </div>

    <!-- Section 2: Organisation & Poste -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
        <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h5m-5 0V10m0 0V5"/>
                </svg>
            </div>
            <h2 class="font-bold text-slate-900 text-base">Organisation & Profession</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="entreprise" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Entreprise / Organisme
                </label>
                <input type="text" name="entreprise" id="entreprise" value="{{ old('entreprise', $contact->entreprise ?? '') }}" placeholder="Ex: CAEI Partner" class="w-full text-sm py-2.5 px-3.5 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50">
            </div>

            <div>
                <label for="fonction" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Fonction / Poste
                </label>
                <input type="text" name="fonction" id="fonction" value="{{ old('fonction', $contact->fonction ?? '') }}" placeholder="Ex: Directrice Formation" class="w-full text-sm py-2.5 px-3.5 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50">
            </div>

            <div>
                <label for="secteur_activite" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Secteur d'activité
                </label>
                <input type="text" name="secteur_activite" id="secteur_activite" value="{{ old('secteur_activite', $contact->secteur_activite ?? '') }}" placeholder="Ex: Finance & Banque" class="w-full text-sm py-2.5 px-3.5 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50">
            </div>
        </div>
    </div>

    <!-- Section 3: Localisation & Origine -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
        <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
            <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h2 class="font-bold text-slate-900 text-base">Localisation & Source</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="pays" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Pays
                </label>
                <input type="text" name="pays" id="pays" value="{{ old('pays', $contact->pays ?? '') }}" placeholder="Ex: Tunisie" class="w-full text-sm py-2.5 px-3.5 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50">
            </div>

            <div>
                <label for="ville" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Ville
                </label>
                <input type="text" name="ville" id="ville" value="{{ old('ville', $contact->ville ?? '') }}" placeholder="Ex: Tunis" class="w-full text-sm py-2.5 px-3.5 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50">
            </div>

            <div>
                <label for="source" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Source du contact
                </label>
                <input type="text" name="source" id="source" value="{{ old('source', $contact->source ?? '') }}" placeholder="Ex: Site web, Séminaire, LinkedIn" class="w-full text-sm py-2.5 px-3.5 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50">
            </div>
        </div>
    </div>

    <!-- Section 4: Qualification & Statut Prospect (Module 9) -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
        <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
            <div class="p-2 bg-amber-50 text-amber-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-slate-900 text-base">Suivi & Qualification du Prospect</h2>
                <p class="text-xs text-slate-400">Statut du cycle de prospect (Module 9)</p>
            </div>
        </div>

        <div>
            <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                Statut du contact
            </label>
            @php
                $availableStatuses = \App\Models\Contact::getProspectStatuses();
                $currentStatus = old('status', $contact->status ?? \App\Models\Contact::STATUS_NOUVEAU);
            @endphp
            <select name="status" id="status" class="w-full text-sm py-2.5 px-3.5 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50 font-medium">
                @foreach($availableStatuses as $stKey => $stMeta)
                    <option value="{{ $stKey }}" {{ $currentStatus === $stKey ? 'selected' : '' }}>
                        {{ $stMeta['label'] }} — {{ $stMeta['description'] }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Section 4: Catégories & Listes -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 11h10M7 15h10"/>
                    </svg>
                </div>
                <h2 class="font-bold text-slate-900 text-base">Listes & Catégories de diffusion</h2>
            </div>
        </div>

        @if(isset($categories) && count($categories) > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($categories as $category)
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200/70 hover:border-indigo-200 bg-slate-50/40 hover:bg-indigo-50/40 transition cursor-pointer select-none">
                        <input type="checkbox" name="categories[]" value="{{ $category->id }}" {{ in_array($category->id, $selectedCategories) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded">
                        <span class="text-sm font-semibold text-slate-700">{{ $category->name }}</span>
                    </label>
                @endforeach
            </div>
        @else
            <p class="text-xs text-slate-400 italic">Aucune catégorie disponible. Vous pouvez en créer dans la section Catégories.</p>
        @endif
    </div>

    <!-- Section 5: Notes & remarques -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
        <label for="notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
            Notes & Remarques
        </label>
        <textarea name="notes" id="notes" rows="4" placeholder="Saisissez vos remarques particulières concernant ce contact..." class="w-full text-sm border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50 p-3.5">{{ old('notes', $contact->notes ?? '') }}</textarea>
    </div>
</div>
