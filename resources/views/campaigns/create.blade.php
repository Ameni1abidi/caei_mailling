<x-app-layout>
    <div class="p-6 max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3.5 bg-gradient-to-br from-indigo-500 to-blue-600 text-white rounded-2xl shadow-md shadow-indigo-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Nouvelle campagne</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Planifiez un nouvel envoi d'emails ciblé</p>
                </div>
            </div>
            <a href="{{ route('campaigns.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-4 py-2.5 rounded-xl border border-slate-200 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Retour à la liste</span>
            </a>
        </div>

        @if($template)
            <div class="bg-blue-50 border border-blue-200 text-blue-900 p-4 rounded-xl text-sm font-medium shadow-sm flex items-center gap-3">
                <div class="p-1.5 bg-blue-100 rounded-lg text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    Modèle utilisé : <strong class="text-blue-950">{{ $template->nom }}</strong> (Le sujet et le contenu ont été pré-remplis)
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-900 p-4 rounded-xl text-sm font-medium shadow-sm flex items-start gap-3">
                <div class="p-1 bg-rose-100 rounded-lg text-rose-600 shrink-0 mt-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-rose-900 mb-1">Veuillez corriger les erreurs suivantes :</div>
                    <ul class="list-disc list-inside space-y-1 text-rose-800 text-xs">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('campaigns.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Form Card (2 cols) -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
                        <!-- Nom de la campagne -->
                        <div>
                            <label for="nom" class="block text-sm font-bold text-slate-800 mb-1.5">
                                Nom de la campagne <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="nom" id="nom" value="{{ old('nom', $template?->nom) }}" required placeholder="Ex: Séminaire International IA & Fraude" class="w-full text-sm py-2.5 px-3.5 rounded-xl bg-slate-50/50 border @error('nom') border-rose-300 focus:ring-rose-500 focus:border-rose-500 @else border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @enderror transition-all">
                            @error('nom')
                                <p class="text-xs text-rose-600 mt-1.5 font-semibold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <!-- Objet de l'email -->
                        <div>
                            <label for="objet" class="block text-sm font-bold text-slate-800 mb-1.5">
                                Objet de l'email <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="objet" id="objet" value="{{ old('objet', $template?->sujet) }}" required placeholder="Ex: Invitation officielle au séminaire international CAEI" class="w-full text-sm py-2.5 px-3.5 rounded-xl bg-slate-50/50 border @error('objet') border-rose-300 focus:ring-rose-500 focus:border-rose-500 @else border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @enderror transition-all">
                            @error('objet')
                                <p class="text-xs text-rose-600 mt-1.5 font-semibold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <!-- Cible -->
                        <div>
                            <label for="category_id" class="block text-sm font-bold text-slate-800 mb-1.5">
                                Liste ciblée (Catégorie de contacts)
                            </label>
                            <select name="category_id" id="category_id" class="w-full text-sm py-2.5 px-3.5 rounded-xl bg-slate-50/50 border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-medium text-slate-700">
                                <option value="">-- Tous les contacts --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Contenu du message -->
                        <div class="pt-3 border-t border-slate-100">
                            <label for="contenu" class="block text-sm font-bold text-slate-800 mb-1.5">
                                Contenu de l'email (HTML supporté) <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="contenu" id="contenu" rows="13" required placeholder="Rédigez le texte de votre email..." class="w-full text-sm font-mono bg-slate-50/40 p-4 leading-relaxed rounded-xl border @error('contenu') border-rose-300 focus:ring-rose-500 focus:border-rose-500 @else border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @enderror transition-all">@if(old('contenu')){{ old('contenu') }}@elseif($template){{ $template->contenu }}@else Bonjour @{{prenom}} @{{nom}},

Nous avons le plaisir de vous inviter...

Cordialement,
L'équipe CAEI @endif</textarea>
                            @error('contenu')
                                <p class="text-xs text-rose-600 mt-1.5 font-semibold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Variable reference Sidebar (1 col) -->
                <div class="space-y-4">
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-4 sticky top-6">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <h2 class="font-bold text-slate-900 text-sm">Variables dynamiques</h2>
                            </div>
                        </div>

                        <p class="text-xs text-slate-500 leading-relaxed">
                            Vous pouvez insérer les variables ci-dessous dans l'objet ou le contenu de votre message. Elles seront automatiquement remplacées par les informations du destinataire lors de l'envoi :
                        </p>

                        <div class="space-y-2 max-h-[460px] overflow-y-auto pr-1">
                            @php
                                $vars = [
                                    '@{{nom}}' => 'Nom de famille du contact',
                                    '@{{prenom}}' => 'Prénom du contact',
                                    '@{{entreprise}}' => 'Entreprise / Organisme du contact',
                                    '@{{fonction}}' => 'Poste / Fonction du contact',
                                    '@{{pays}}' => 'Pays du contact',
                                    '@{{nom_seminaire}}' => 'Nom de cette campagne / séminaire',
                                    '@{{date}}' => "Date d'envoi de la campagne",
                                    '@{{lien}}' => 'Lien de désinscription ou URL de base'
                                ];
                            @endphp
                            @foreach($vars as $var => $description)
                                <div class="w-full bg-slate-50 border border-slate-200/70 p-3 rounded-xl">
                                    <div class="flex items-center justify-between gap-2">
                                        <code class="font-mono text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-100 font-bold text-xs">
                                            {{ $var }}
                                        </code>
                                    </div>
                                    <div class="text-slate-500 text-xs mt-1.5 font-medium">
                                        {{ $description }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Action Buttons Footer -->
            <div class="flex items-center justify-end gap-3 pt-6 mt-6 border-t border-slate-200/80 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                <a href="{{ route('campaigns.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-900 transition">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow-md shadow-indigo-100 transition duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Créer la campagne</span>
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
