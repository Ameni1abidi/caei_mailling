<x-app-layout>
    <div class="p-6 max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3.5 bg-gradient-to-br from-[#03123F] via-[#1E3A8A] to-[#C57A1E] text-white rounded-2xl shadow-md">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-[#03123F] tracking-tight">Modifier la campagne</h1>
                    <p class="text-sm text-slate-500 mt-0.5">{{ $campaign->nom }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('campaigns.preview', $campaign) }}" class="inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold px-4 py-2.5 rounded-xl border border-slate-200 transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span>Prévisualiser</span>
                </a>
                <a href="{{ route('campaigns.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-4 py-2.5 rounded-xl border border-slate-200 transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Retour</span>
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

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition class="flex items-center justify-between gap-3 bg-rose-50 border border-rose-200 text-rose-900 p-4 rounded-xl text-sm font-medium shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-1 bg-rose-100 rounded-lg text-rose-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <span>{{ session('error') }}</span>
                </div>
                <button @click="show = false" class="text-rose-500 hover:text-rose-700 p-1 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
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

        <!-- Destination target Info banner -->
        <div x-data="audienceBanner({{ $nbDestinataires }})"
             @audience-count-changed.window="update($event.detail.count)"
             class="bg-amber-50/70 border border-amber-200/80 text-amber-950 p-4 rounded-2xl flex items-center gap-3.5 shadow-sm transition-all">
            <div class="p-2.5 bg-amber-100 text-[#C57A1E] rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="flex-1">
                <div class="text-xs text-amber-800 font-bold uppercase tracking-wider">Cible d'envoi</div>
                <div class="text-base font-bold text-amber-950 mt-0.5">
                    Cette campagne sera envoyée à
                    <strong class="text-[#C57A1E] font-black tabular-nums" x-text="count.toLocaleString('fr-FR')"></strong>
                    destinataire<span x-show="count !== 1">s</span> ciblé<span x-show="count !== 1">s</span>.
                </div>
            </div>
            <span x-show="loading" class="inline-block w-4 h-4 border-2 border-amber-400 border-t-transparent rounded-full animate-spin shrink-0"></span>
        </div>
        <script>
            function audienceBanner(initial) {
                return { count: initial, loading: false, update(c) { this.count = c; } };
            }
        </script>

        <form action="{{ route('campaigns.update', $campaign) }}" method="POST" id="campaign-edit-form">
            @csrf
            @method('PUT')

            <!-- Section 1 : Configuration & Paramètres (Haut) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Paramètres principaux (2 cols) -->
                <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
                    <!-- Boîte d'envoi automatique -->
                    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200/80 rounded-xl p-3.5 flex items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="font-bold text-emerald-950">Boîte d'envoi :</span>
                                <span class="text-emerald-800 font-medium">
                                    {{ $senderSmtp?->sender_name ?? $campaign->creator?->name ?? auth()->user()->name }}
                                    &lt;{{ $senderSmtp?->sender_email ?? 'contact@caei-afri.com' }}&gt;
                                </span>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 font-semibold text-[11px] text-emerald-700 bg-emerald-100/70 px-2.5 py-1 rounded-full shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Quota sécurisé 180 emails/h
                        </span>
                    </div>

                    <!-- Nom de la campagne -->
                    <div>
                        <label for="nom" class="block text-sm font-bold text-slate-800 mb-1.5">
                            Nom de la campagne <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nom" id="nom" value="{{ old('nom', $campaign->nom) }}" required placeholder="Ex: Séminaire International IA & Fraude" class="w-full text-sm py-2.5 px-3.5 rounded-xl bg-slate-50/50 border @error('nom') border-rose-300 focus:ring-rose-500 focus:border-rose-500 @else border-slate-200 focus:ring-2 focus:ring-[#C57A1E]/30 focus:border-[#C57A1E] @enderror transition-all">
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
                        <label for="objet" class="block text-sm font-bold text-slate-800 mb-1.5 flex items-center justify-between">
                            <span>Objet de l'email <span class="text-rose-500">*</span></span>
                            <span class="text-xs text-slate-400 font-normal">Variables : @{{prenom}}, @{{nom}}</span>
                        </label>
                        <input type="text" name="objet" id="objet" value="{{ old('objet', $campaign->objet) }}" required placeholder="Ex: Invitation officielle au séminaire international CAEI" class="w-full text-sm py-2.5 px-3.5 rounded-xl bg-slate-50/50 border @error('objet') border-rose-300 focus:ring-rose-500 focus:border-rose-500 @else border-slate-200 focus:ring-2 focus:ring-[#C57A1E]/30 focus:border-[#C57A1E] @enderror transition-all">
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
                        <label class="block text-sm font-bold text-slate-800 mb-2">
                            Ciblage de la campagne
                        </label>
                        @php
                            $preSelectedCategoryIds = old('category_ids', $campaign->categoryIds());
                            $preSelectedImportId = old('import_log_id', old('import_batch_id', $campaign->import_log_id));
                            $preAllSelected = (bool) old('all_contacts', ($campaign->categoryIds() === [] && !$campaign->import_log_id) ? '1' : '');
                        @endphp
                        <x-campaign-audience-selector
                            :categories="$categories"
                            :import-logs="$importLogs"
                            :selected-category-ids="$preSelectedCategoryIds"
                            :selected-import-id="$preSelectedImportId"
                            :all-selected="$preAllSelected"
                            :total-contacts="$totalContacts"
                            :recipient-count-url="route('campaigns.recipient-count')"
                        />
                    </div>

                    <!-- Configuration des relances automatiques -->
                    <div class="pt-3 border-t border-slate-100" x-data="{ autoRetry: {{ old('auto_retry', $campaign->auto_retry ?? '1') ? 'true' : 'false' }} }">
                        <label class="block text-sm font-bold text-slate-800 mb-2">
                            Relances automatiques d'envoi
                        </label>
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="auto_retry" value="1" x-model="autoRetry" class="h-4.5 w-4.5 rounded border-slate-300 text-[#C57A1E] focus:ring-[#C57A1E]">
                                <div>
                                    <span class="text-sm font-bold text-slate-800">Activer la relance automatique en cas d'échec</span>
                                    <p class="text-xs text-slate-500">Le système tentera automatiquement de renvoyer les emails échoués en arrière-plan.</p>
                                </div>
                            </label>

                            <div x-show="autoRetry" class="pl-7 pt-2.5 border-t border-slate-200/60 flex flex-wrap items-center gap-3">
                                <label for="max_auto_retries" class="text-xs font-semibold text-slate-700">
                                    Nombre maximal de relances automatiques :
                                </label>
                                <select name="max_auto_retries" id="max_auto_retries" class="text-xs py-1.5 px-3 rounded-lg border-slate-200 bg-white font-bold text-slate-800 focus:ring-[#C57A1E] focus:border-[#C57A1E]">
                                    <option value="1" @selected(old('max_auto_retries', $campaign->max_auto_retries ?? 3) == 1)>1 relance max</option>
                                    <option value="2" @selected(old('max_auto_retries', $campaign->max_auto_retries ?? 3) == 2)>2 relances max</option>
                                    <option value="3" @selected(old('max_auto_retries', $campaign->max_auto_retries ?? 3) == 3)>3 relances max (Recommandé)</option>
                                    <option value="5" @selected(old('max_auto_retries', $campaign->max_auto_retries ?? 3) == 5)>5 relances max</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Colonne droite : Actions & Variables (1 col) -->
                <div class="space-y-5">
                    <!-- Variables dynamiques -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <div class="p-1.5 bg-amber-50 text-[#C57A1E] rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <h3 class="font-bold text-slate-900 text-xs">Variables dynamiques</h3>
                            </div>
                            <span class="text-[10px] text-slate-400 font-semibold">Clic pour copier</span>
                        </div>
                        <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                            @foreach([
                                '@{{nom}}' => 'Nom de famille',
                                '@{{prenom}}' => 'Prénom',
                                '@{{entreprise}}' => 'Entreprise / Organisme',
                                '@{{fonction}}' => 'Poste / Titre',
                                '@{{pays}}' => 'Pays',
                                '@{{nom_seminaire}}' => 'Nom du séminaire',
                                '@{{date}}' => "Date d'envoi",
                                '@{{lien}}' => 'Lien de confirmation'
                            ] as $var => $desc)
                                <div class="bg-slate-50 hover:bg-amber-50 border border-slate-200/60 p-2 rounded-lg cursor-pointer transition flex items-center justify-between text-xs"
                                     onclick="navigator.clipboard.writeText('{{ $var }}'); alert('Variable {{ $var }} copiée !');">
                                    <code class="font-mono text-[#C57A1E] font-bold text-[11px]">{{ $var }}</code>
                                    <span class="text-slate-500 text-[10px]">{{ $desc }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Encart Statut / Lancement -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Statut de la campagne</div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider
                                @if($campaign->statut === 'envoyee') bg-emerald-100 text-emerald-800
                                @elseif($campaign->statut === 'en_cours') bg-blue-100 text-blue-800 animate-pulse
                                @elseif($campaign->statut === 'programmee') bg-purple-100 text-purple-800
                                @elseif($campaign->statut === 'annulee') bg-rose-100 text-rose-800
                                @else bg-amber-100 text-amber-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $campaign->statut)) }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500">
                            Modifiez le contenu ci-dessous, puis cliquez sur « Enregistrer les modifications » en bas de page.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Section 2 : GRAND STUDIO D'ÉMAILING PLEINE PAGE -->
            @php
                $defaultContent = old('contenu', $campaign->contenu ?? '');
            @endphp
            <div x-data="emailBuilder({ initialContent: {{ json_encode($defaultContent) }} })"
                 :class="isFullscreen ? 'fixed inset-0 z-50 bg-slate-950/90 backdrop-blur-md p-4 md:p-6 overflow-hidden flex flex-col' : 'bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6'">

                <!-- Studio Top Header Bar -->
                <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b"
                     :class="isFullscreen ? 'border-slate-800 text-white' : 'border-slate-100 text-slate-900'">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-xl bg-gradient-to-br from-[#03123F] to-[#C57A1E] text-white shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-lg font-black tracking-tight" :class="isFullscreen ? 'text-white' : 'text-[#03123F]'">
                                    Concepteur d'Email Professionnel
                                </h2>
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold"
                                      :class="isFullscreen ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-amber-50 text-[#C57A1E] border border-amber-200'">
                                    <span x-text="blocks.length"></span> bloc(s)
                                </span>
                            </div>
                            <p class="text-xs" :class="isFullscreen ? 'text-slate-400' : 'text-slate-500'">
                                Éditez vos blocs avec prévisualisation fidèle en direct
                            </p>
                        </div>
                    </div>

                    <!-- Contrôles du Studio -->
                    <div class="flex flex-wrap items-center gap-2.5">
                        <!-- Mode Constructeur / HTML -->
                        <div class="p-1 rounded-xl flex items-center gap-1 border"
                             :class="isFullscreen ? 'bg-slate-900 border-slate-700' : 'bg-slate-100 border-slate-200/80'">
                            <button type="button"
                                    @click="mode = 'builder'"
                                    :class="mode === 'builder' ? 'bg-[#03123F] text-white shadow-sm' : (isFullscreen ? 'text-slate-300 hover:text-white' : 'text-slate-600 hover:text-slate-900')"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                <span>Constructeur Visuel</span>
                            </button>
                            <button type="button"
                                    @click="mode = 'html'; htmlValue = buildHtmlFromBlocks(false)"
                                    :class="mode === 'html' ? 'bg-[#03123F] text-white shadow-sm' : (isFullscreen ? 'text-slate-300 hover:text-white' : 'text-slate-600 hover:text-slate-900')"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                <span>Code HTML</span>
                            </button>
                        </div>

                        <!-- Prévisualisation Desktop / Mobile -->
                        <div class="hidden sm:flex p-1 rounded-xl items-center gap-1 border"
                             :class="isFullscreen ? 'bg-slate-900 border-slate-700' : 'bg-slate-100 border-slate-200/80'">
                            <button type="button"
                                    @click="previewDevice = 'desktop'"
                                    :class="previewDevice === 'desktop' ? 'bg-white text-[#03123F] shadow-sm font-bold' : (isFullscreen ? 'text-slate-400 hover:text-white' : 'text-slate-500 hover:text-slate-900')"
                                    class="px-2.5 py-1.5 rounded-lg text-xs transition flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>Bureau</span>
                            </button>
                            <button type="button"
                                    @click="previewDevice = 'mobile'"
                                    :class="previewDevice === 'mobile' ? 'bg-white text-[#03123F] shadow-sm font-bold' : (isFullscreen ? 'text-slate-400 hover:text-white' : 'text-slate-500 hover:text-slate-900')"
                                    class="px-2.5 py-1.5 rounded-lg text-xs transition flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                <span>Mobile</span>
                            </button>
                        </div>

                        <!-- Bouton Mode Plein Écran -->
                        <button type="button"
                                @click="toggleFullscreen()"
                                :class="isFullscreen ? 'bg-amber-500 text-slate-950 font-black' : 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-200/80 shadow-sm'"
                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                            <template x-if="!isFullscreen">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                    <span>Plein écran</span>
                                </span>
                            </template>
                            <template x-if="isFullscreen">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>Quitter plein écran</span>
                                </span>
                            </template>
                        </button>
                    </div>
                </div>

                <!-- Input caché contenant les données à soumettre -->
                <input type="hidden" name="contenu" :value="contentValue">

                <!-- ── MODE CONSTRUCTEUR 3 COLONNES ──────────────────────────────────── -->
                <div x-show="mode === 'builder'"
                     class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start"
                     :class="isFullscreen ? 'flex-1 overflow-hidden mt-4' : ''">

                    <!-- Colonne 1 : Palette d'outils / Blocs (3 cols) -->
                    <div class="lg:col-span-3 space-y-3" :class="isFullscreen ? 'overflow-y-auto h-full pr-2' : ''">
                        <div class="p-4 rounded-2xl border"
                             :class="isFullscreen ? 'bg-slate-900 border-slate-800' : 'bg-slate-50/80 border-slate-200/70'">
                            <div class="text-[11px] font-black uppercase tracking-wider mb-3 flex items-center gap-1.5"
                                 :class="isFullscreen ? 'text-amber-400' : 'text-[#03123F]'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                <span>Ajouter un bloc de contenu</span>
                            </div>

                            <div class="grid grid-cols-1 gap-2">
                                <button type="button" @click="addBlock('text')"
                                        class="flex items-center gap-3 p-3 text-left rounded-xl border bg-white hover:border-[#C57A1E] hover:shadow-sm transition group">
                                    <span class="w-8 h-8 rounded-lg bg-amber-50 text-[#C57A1E] flex items-center justify-center font-bold shrink-0 group-hover:scale-110 transition">✍️</span>
                                    <div>
                                        <div class="text-xs font-bold text-slate-800 group-hover:text-[#C57A1E] transition">Paragraphe Texte</div>
                                        <div class="text-[10px] text-slate-400">Texte principal et salutations</div>
                                    </div>
                                </button>

                                <button type="button" @click="addBlock('heading')"
                                        class="flex items-center gap-3 p-3 text-left rounded-xl border bg-white hover:border-[#C57A1E] hover:shadow-sm transition group">
                                    <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold shrink-0 group-hover:scale-110 transition">🏷️</span>
                                    <div>
                                        <div class="text-xs font-bold text-slate-800 group-hover:text-blue-600 transition">Grand Titre (H1/H2)</div>
                                        <div class="text-[10px] text-slate-400">Titre de section accrocheur</div>
                                    </div>
                                </button>

                                <button type="button" @click="addBlock('button')"
                                        class="flex items-center gap-3 p-3 text-left rounded-xl border bg-white hover:border-[#C57A1E] hover:shadow-sm transition group">
                                    <span class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center font-bold shrink-0 group-hover:scale-110 transition">🔘</span>
                                    <div>
                                        <div class="text-xs font-bold text-slate-800 group-hover:text-purple-600 transition">Bouton d'action (CTA)</div>
                                        <div class="text-[10px] text-slate-400">Lien d'inscription ou confirmation</div>
                                    </div>
                                </button>

                                <button type="button" @click="addBlock('callout')"
                                        class="flex items-center gap-3 p-3 text-left rounded-xl border bg-white hover:border-[#C57A1E] hover:shadow-sm transition group">
                                    <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold shrink-0 group-hover:scale-110 transition">💡</span>
                                    <div>
                                        <div class="text-xs font-bold text-slate-800 group-hover:text-emerald-600 transition">Encadré d'information</div>
                                        <div class="text-[10px] text-slate-400">Dates clés, alertes, informations</div>
                                    </div>
                                </button>

                                <button type="button" @click="addBlock('divider')"
                                        class="flex items-center gap-3 p-3 text-left rounded-xl border bg-white hover:border-[#C57A1E] hover:shadow-sm transition group">
                                    <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center font-bold shrink-0 group-hover:scale-110 transition">➖</span>
                                    <div>
                                        <div class="text-xs font-bold text-slate-800 group-hover:text-slate-600 transition">Ligne de séparation</div>
                                        <div class="text-[10px] text-slate-400">Délimitation propre de sections</div>
                                    </div>
                                </button>

                                <button type="button" @click="addBlock('image')"
                                        class="flex items-center gap-3 p-3 text-left rounded-xl border bg-white hover:border-[#C57A1E] hover:shadow-sm transition group">
                                    <span class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center font-bold shrink-0 group-hover:scale-110 transition">🖼️</span>
                                    <div>
                                        <div class="text-xs font-bold text-slate-800 group-hover:text-sky-600 transition">Bannière / Image</div>
                                        <div class="text-[10px] text-slate-400">Visuel avec lien de redirection</div>
                                    </div>
                                </button>

                                <button type="button" @click="addBlock('logo')"
                                        class="flex items-center gap-3 p-3 text-left rounded-xl border bg-white hover:border-[#C57A1E] hover:shadow-sm transition group">
                                    <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center font-bold shrink-0 group-hover:scale-110 transition">🏷️</span>
                                    <div>
                                        <div class="text-xs font-bold text-slate-800 group-hover:text-amber-700 transition">Logo Officiel CAEI</div>
                                        <div class="text-[10px] text-slate-400">En-tête de marque centré</div>
                                    </div>
                                </button>

                                <button type="button" @click="addBlock('signature')"
                                        class="flex items-center gap-3 p-3 text-left rounded-xl border bg-white hover:border-[#C57A1E] hover:shadow-sm transition group">
                                    <span class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center font-bold shrink-0 group-hover:scale-110 transition">✉️</span>
                                    <div>
                                        <div class="text-xs font-bold text-slate-800 group-hover:text-rose-600 transition">Signature Professionnelle</div>
                                        <div class="text-[10px] text-slate-400">Pied de page institutionnel</div>
                                    </div>
                                </button>

                                <button type="button" @click="addBlock('attachment')"
                                        class="flex items-center gap-3 p-3 text-left rounded-xl border bg-white hover:border-[#C57A1E] hover:shadow-sm transition group">
                                    <span class="w-8 h-8 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center font-bold shrink-0 group-hover:scale-110 transition">📎</span>
                                    <div>
                                        <div class="text-xs font-bold text-slate-800 group-hover:text-teal-600 transition">Pièce Jointe / Plaquette</div>
                                        <div class="text-[10px] text-slate-400">Lien de téléchargement PDF</div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Colonne 2 : Zone de travail spacieuse (5 cols) -->
                    <div class="lg:col-span-5 space-y-4" :class="isFullscreen ? 'overflow-y-auto h-full pr-2' : ''">
                        <template x-if="blocks.length === 0">
                            <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 p-12 text-center">
                                <div class="w-12 h-12 rounded-full bg-amber-50 text-[#C57A1E] flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </div>
                                <h3 class="text-sm font-bold text-slate-800 mb-1">Votre email est vide</h3>
                                <p class="text-xs text-slate-500 mb-4">Cliquez sur un bloc dans la colonne de gauche pour débuter la rédaction.</p>
                                <button type="button" @click="addBlock('text')" class="px-4 py-2 bg-[#03123F] text-white rounded-xl text-xs font-bold hover:bg-[#C57A1E] transition">
                                    Ajouter un premier paragraphe
                                </button>
                            </div>
                        </template>

                        <div class="space-y-4">
                            <template x-for="(block, index) in blocks" :key="block.id">
                                <div class="rounded-2xl border transition-all duration-200 shadow-sm"
                                     :class="activeBlockId === block.id ? 'border-[#C57A1E] ring-2 ring-[#C57A1E]/20 bg-white' : 'border-slate-200 bg-white hover:border-slate-300'"
                                     @click="activeBlockId = block.id">

                                    <!-- Barre d'en-tête du bloc -->
                                    <div class="flex items-center justify-between p-3.5 bg-slate-50/80 rounded-t-2xl border-b border-slate-100">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-[#C57A1E]"></span>
                                            <span class="text-xs font-black text-slate-800 tracking-wide" x-text="blockLabel(block.type)"></span>
                                            <span class="text-[10px] text-slate-400 font-mono">#<span x-text="index + 1"></span></span>
                                        </div>

                                        <!-- Actions rapides de bloc -->
                                        <div class="flex items-center gap-1">
                                            <button type="button" @click.stop="moveBlockUp(index)" :disabled="index === 0"
                                                    title="Monter ce bloc"
                                                    class="p-1 rounded-lg text-slate-500 hover:text-[#03123F] hover:bg-slate-200/60 disabled:opacity-30 disabled:hover:bg-transparent">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                            </button>
                                            <button type="button" @click.stop="moveBlockDown(index)" :disabled="index === blocks.length - 1"
                                                    title="Descendre ce bloc"
                                                    class="p-1 rounded-lg text-slate-500 hover:text-[#03123F] hover:bg-slate-200/60 disabled:opacity-30 disabled:hover:bg-transparent">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                            <button type="button" @click.stop="duplicateBlock(index)"
                                                    title="Dupliquer ce bloc"
                                                    class="p-1 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            </button>
                                            <button type="button" @click.stop="removeBlock(index)"
                                                    title="Supprimer ce bloc"
                                                    class="p-1 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Contenu du bloc -->
                                    <div class="p-4 space-y-3">
                                        <!-- Bloc TEXTE -->
                                        <template x-if="block.type === 'text'">
                                            <div class="space-y-2">
                                                <div class="flex flex-wrap items-center justify-between gap-2 pb-1 border-b border-slate-100 text-xs">
                                                    <!-- Insertion variable rapide -->
                                                    <div class="flex items-center gap-1">
                                                        <span class="text-[10px] font-bold text-slate-400 uppercase">Variables :</span>
                                                        <button type="button" @click="insertVariableInBlock(block, '@{{prenom}}')" class="px-2 py-0.5 rounded bg-amber-50 hover:bg-amber-100 text-[#C57A1E] font-mono text-[11px] font-bold">+ Prénom</button>
                                                        <button type="button" @click="insertVariableInBlock(block, '@{{nom}}')" class="px-2 py-0.5 rounded bg-amber-50 hover:bg-amber-100 text-[#C57A1E] font-mono text-[11px] font-bold">+ Nom</button>
                                                        <button type="button" @click="insertVariableInBlock(block, '@{{entreprise}}')" class="px-2 py-0.5 rounded bg-amber-50 hover:bg-amber-100 text-[#C57A1E] font-mono text-[11px] font-bold">+ Entreprise</button>
                                                    </div>

                                                    <select x-model="block.align" @change="updateHtml()" class="text-xs rounded-lg border-slate-200 py-1 px-2 text-slate-600 font-semibold">
                                                        <option value="left">Aligné à gauche</option>
                                                        <option value="center">Centré</option>
                                                        <option value="right">Aligné à droite</option>
                                                    </select>
                                                </div>

                                                <textarea x-model="block.content"
                                                          @input="updateHtml()"
                                                          rows="6"
                                                          class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm text-slate-800 leading-relaxed focus:bg-white focus:border-[#C57A1E] focus:ring-2 focus:ring-[#C57A1E]/20 transition-all font-sans"
                                                          placeholder="Rédigez votre texte ici..."></textarea>
                                            </div>
                                        </template>

                                        <!-- Bloc TITRE -->
                                        <template x-if="block.type === 'heading'">
                                            <div class="space-y-3">
                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Niveau de titre</label>
                                                        <select x-model="block.level" @change="updateHtml()" class="w-full text-xs rounded-xl border-slate-200 py-2 px-3">
                                                            <option value="h1">Titre principal (H1 - Grand)</option>
                                                            <option value="h2">Sous-titre (H2 - Moyen)</option>
                                                            <option value="h3">Titre de section (H3 - Petit)</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Alignement</label>
                                                        <select x-model="block.align" @change="updateHtml()" class="w-full text-xs rounded-xl border-slate-200 py-2 px-3">
                                                            <option value="left">Gauche</option>
                                                            <option value="center">Centré</option>
                                                            <option value="right">Droite</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <input x-model="block.content" @input="updateHtml()" type="text"
                                                       class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-base font-bold text-slate-900 focus:bg-white focus:border-[#C57A1E]"
                                                       placeholder="Ex: Invitation exclusive aux dirigeants">
                                            </div>
                                        </template>

                                        <!-- Bloc BOUTON CTA -->
                                        <template x-if="block.type === 'button'">
                                            <div class="space-y-3">
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Libellé du bouton</label>
                                                        <input x-model="block.label" @input="updateHtml()" type="text" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-2.5" placeholder="Confirmer ma participation">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Lien de redirection</label>
                                                        <input x-model="block.url" @input="updateHtml()" type="text" class="w-full rounded-xl border-slate-200 text-sm p-2.5 font-mono" placeholder="@{{lien}} ou https://...">
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    <span class="text-xs font-bold text-slate-600">Couleur du bouton :</span>
                                                    <div class="flex items-center gap-2">
                                                        <button type="button" @click="block.color = '#C57A1E'; updateHtml()" class="w-6 h-6 rounded-full bg-[#C57A1E] ring-2 ring-offset-1" :class="block.color === '#C57A1E' ? 'ring-slate-900' : 'ring-transparent'"></button>
                                                        <button type="button" @click="block.color = '#03123F'; updateHtml()" class="w-6 h-6 rounded-full bg-[#03123F] ring-2 ring-offset-1" :class="block.color === '#03123F' ? 'ring-slate-900' : 'ring-transparent'"></button>
                                                        <button type="button" @click="block.color = '#059669'; updateHtml()" class="w-6 h-6 rounded-full bg-[#059669] ring-2 ring-offset-1" :class="block.color === '#059669' ? 'ring-slate-900' : 'ring-transparent'"></button>
                                                        <button type="button" @click="block.color = '#DC2626'; updateHtml()" class="w-6 h-6 rounded-full bg-[#DC2626] ring-2 ring-offset-1" :class="block.color === '#DC2626' ? 'ring-slate-900' : 'ring-transparent'"></button>
                                                        <input x-model="block.color" @input="updateHtml()" type="color" class="w-8 h-8 rounded-lg cursor-pointer border-0 p-0">
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Bloc ENCADRÉ (CALLOUT) -->
                                        <template x-if="block.type === 'callout'">
                                            <div class="space-y-2">
                                                <label class="block text-[11px] font-bold text-slate-500 uppercase">Texte mis en avant</label>
                                                <textarea x-model="block.content" @input="updateHtml()" rows="3" class="w-full rounded-xl border border-slate-200 bg-amber-50/50 p-3 text-sm text-amber-950 font-medium" placeholder="Ex: Remise de 20% accordée pour toute inscription avant le 30 du mois."></textarea>
                                            </div>
                                        </template>

                                        <!-- Bloc SÉPARATEUR -->
                                        <template x-if="block.type === 'divider'">
                                            <div class="py-3 text-center text-xs text-slate-400 font-medium flex items-center justify-center gap-3">
                                                <span class="h-px bg-slate-200 flex-1"></span>
                                                <span>Ligne de séparation horizontale</span>
                                                <span class="h-px bg-slate-200 flex-1"></span>
                                            </div>
                                        </template>

                                        <!-- Bloc LOGO CAEI -->
                                        <template x-if="block.type === 'logo'">
                                            <div class="flex items-center gap-4 bg-slate-50 p-3 rounded-xl border border-slate-200/80">
                                                <img src="/images/logo-caei.jpg" alt="CAEI" class="w-14 h-14 rounded-full object-cover ring-2 ring-amber-500/50">
                                                <div class="flex-1">
                                                    <div class="text-xs font-bold text-slate-800">Logo CAEI Officiel</div>
                                                    <div class="text-[11px] text-slate-500 mt-0.5">Sera centré au format circulaire institutionnel.</div>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Bloc IMAGE -->
                                        <template x-if="block.type === 'image'">
                                            <div class="space-y-3">
                                                <div>
                                                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">URL de l'image (Lien web direct)</label>
                                                    <input x-model="block.src" @input="updateHtml()" type="text" class="w-full rounded-xl border-slate-200 text-sm p-2.5 font-mono" placeholder="https://votresite.com/image.jpg">
                                                </div>
                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Texte alternatif (Alt)</label>
                                                        <input x-model="block.alt" @input="updateHtml()" type="text" class="w-full rounded-xl border-slate-200 text-sm p-2.5" placeholder="Bannière séminaire">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Largeur (px)</label>
                                                        <input x-model="block.width" @input="updateHtml()" type="number" class="w-full rounded-xl border-slate-200 text-sm p-2.5 font-bold" placeholder="240">
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Bloc SIGNATURE -->
                                        <template x-if="block.type === 'signature'">
                                            <div class="space-y-2">
                                                <label class="block text-[11px] font-bold text-slate-500 uppercase">Texte de signature</label>
                                                <textarea x-model="block.content" @input="updateHtml()" rows="3" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-xs italic text-slate-700 font-medium"></textarea>
                                            </div>
                                        </template>

                                        <!-- Bloc PIÈCE JOINTE -->
                                        <template x-if="block.type === 'attachment'">
                                            <div class="space-y-3">
                                                <div>
                                                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Libellé du document</label>
                                                    <input x-model="block.label" @input="updateHtml()" type="text" class="w-full rounded-xl border-slate-200 text-sm p-2.5" placeholder="Plaquette de formation 2026.pdf">
                                                </div>
                                                <div>
                                                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Lien de téléchargement</label>
                                                    <input x-model="block.url" @input="updateHtml()" type="text" class="w-full rounded-xl border-slate-200 text-sm p-2.5 font-mono" placeholder="https://...">
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Colonne 3 : PRÉVISUALISATION EN DIRECT (4 cols) -->
                    <div class="lg:col-span-4" :class="isFullscreen ? 'overflow-y-auto h-full pl-2' : 'sticky top-6'">
                        <div class="rounded-2xl border shadow-sm overflow-hidden"
                             :class="isFullscreen ? 'bg-slate-900 border-slate-800' : 'bg-slate-100/90 border-slate-200/80'">

                            <!-- Barre supérieure du simulateur d'email -->
                            <div class="p-3.5 border-b flex items-center justify-between"
                                 :class="isFullscreen ? 'bg-slate-950 border-slate-800 text-slate-300' : 'bg-white border-slate-200 text-slate-700'">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-rose-400"></span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                                    <span class="text-xs font-bold ml-2">Aperçu Réel</span>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                    En direct
                                </span>
                            </div>

                            <!-- Métadonnées email simulées -->
                            <div class="p-3.5 border-b text-xs space-y-1.5"
                                 :class="isFullscreen ? 'bg-slate-900/50 border-slate-800 text-slate-400' : 'bg-slate-50 border-slate-200/70 text-slate-600'">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-400 w-12">De :</span>
                                    <span class="font-semibold text-slate-700 truncate">{{ $senderSmtp?->sender_email ?? 'Contact@caei-afri.com' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-400 w-12">À :</span>
                                    <span class="font-semibold text-slate-700 truncate">amel.benali@banque-centrale.tn</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-400 w-12">Objet :</span>
                                    <span class="font-bold text-[#03123F] truncate" x-text="document.getElementById('objet')?.value || 'Invitation officielle CAEI'"></span>
                                </div>
                            </div>

                            <!-- Corps de l'email rendu -->
                            <div class="p-4" :class="previewDevice === 'mobile' ? 'flex justify-center bg-slate-200/60' : ''">
                                <div :class="previewDevice === 'mobile' ? 'w-[320px] rounded-3xl border-4 border-slate-800 shadow-2xl overflow-hidden bg-white p-4' : 'bg-white rounded-xl border border-slate-200 p-5 shadow-sm'">
                                    <div class="prose prose-sm max-w-none text-slate-800 font-sans leading-relaxed"
                                         x-html="livePreviewHtml">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── MODE HTML DIRECT ──────────────────────────────────────────────── -->
                <div x-show="mode === 'html'" class="space-y-3">
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#C57A1E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>En mode HTML, votre code est injecté directement dans le corps de l'email. Vous pouvez utiliser les balises HTML standard et les variables <code>@{{prenom}}</code>, <code>@{{nom}}</code>, <code>@{{entreprise}}</code>.</span>
                    </div>
                    <textarea x-model="htmlValue" rows="18"
                              class="w-full rounded-2xl border border-slate-300 bg-slate-900 text-emerald-400 p-5 font-mono text-xs leading-relaxed focus:ring-2 focus:ring-[#C57A1E] focus:outline-none"
                              placeholder="<html>...</html>"></textarea>
                </div>
            </div>

            <!-- Form Action Footer -->
            <div class="flex items-center justify-between gap-4 pt-6 border-t border-slate-200/80 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                <a href="{{ route('campaigns.index') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 hover:text-slate-900 transition">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-[#D9822B] via-[#C57A1E] to-[#B86D18] hover:from-[#E58E26] hover:to-[#C57A1E] text-white font-extrabold text-sm px-8 py-3 rounded-xl shadow-lg shadow-amber-600/25 transition-all duration-200 transform hover:-translate-y-0.5 active:translate-y-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Enregistrer les modifications</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Script Concepteur d'Email Studio -->
    <script>
        function emailBuilder({ initialContent = '' } = {}) {
            return {
                mode: 'builder',
                previewDevice: 'desktop',
                isFullscreen: false,
                blocks: [],
                htmlValue: '',
                activeBlockId: null,
                previewVariables: {
                    prenom: 'Amel',
                    nom: 'Ben Ali',
                    entreprise: 'CAEI Partner / Banque Centrale',
                    fonction: 'Directrice Générale',
                    pays: 'Tunisie',
                    nom_seminaire: 'Séminaire International CAEI 2026',
                    date: new Date().toLocaleDateString('fr-FR'),
                    lien: 'https://caei-afri.com'
                },
                get contentValue() {
                    return this.mode === 'builder'
                        ? JSON.stringify({ version: 1, theme: 'caei', blocks: this.blocks })
                        : this.htmlValue;
                },
                init() {
                    this.blocks = this.parseInitial(initialContent);
                    if (this.blocks.length > 0) {
                        this.activeBlockId = this.blocks[0].id;
                    }
                    this.htmlValue = this.buildHtmlFromBlocks(false);
                },
                toggleFullscreen() {
                    this.isFullscreen = !this.isFullscreen;
                    document.body.style.overflow = this.isFullscreen ? 'hidden' : '';
                },
                insertVariableInBlock(block, variable) {
                    block.content = (block.content || '') + ' ' + variable;
                    this.updateHtml();
                },
                addBlock(type) {
                    const block = { id: this.makeId(), type };
                    if (type === 'text') {
                        block.content = 'Bonjour @{{prenom}} @{{nom}},\n\nNous avons le plaisir de vous convier à notre prochaine session de formation de haut niveau.';
                        block.align = 'left';
                    } else if (type === 'heading') {
                        block.content = 'Invitation officielle au séminaire international CAEI';
                        block.level = 'h2';
                        block.align = 'left';
                    } else if (type === 'button') {
                        block.label = 'Confirmer ma participation';
                        block.url = '@{{lien}}';
                        block.color = '#C57A1E';
                    } else if (type === 'callout') {
                        block.content = 'ℹ️ Note importante : Le nombre de places est strictement limité afin de garantir la qualité des échanges.';
                    } else if (type === 'divider') {
                        // séparateur
                    } else if (type === 'image') {
                        block.src = 'https://mailing.caei-afri.com/images/logo-caei.jpg';
                        block.alt = 'Image CAEI';
                        block.width = 240;
                    } else if (type === 'logo') {
                        block.src = '/images/logo-caei.jpg';
                        block.alt = 'CAEI Company Group';
                        block.width = 115;
                    } else if (type === 'signature') {
                        block.content = "L’équipe CAEI COMPANY GROUP\nCabinet d'Audit, d'Expertise et d'Ingénierie\nwww.caei-afri.com";
                    } else if (type === 'attachment') {
                        block.label = 'Plaquette officielle du programme.pdf';
                        block.url = '#';
                    }

                    this.blocks.push(block);
                    this.activeBlockId = block.id;
                    this.updateHtml();
                },
                moveBlockUp(index) {
                    if (index > 0) {
                        const temp = this.blocks[index];
                        this.blocks[index] = this.blocks[index - 1];
                        this.blocks[index - 1] = temp;
                        this.updateHtml();
                    }
                },
                moveBlockDown(index) {
                    if (index < this.blocks.length - 1) {
                        const temp = this.blocks[index];
                        this.blocks[index] = this.blocks[index + 1];
                        this.blocks[index + 1] = temp;
                        this.updateHtml();
                    }
                },
                duplicateBlock(index) {
                    const original = this.blocks[index];
                    const clone = JSON.parse(JSON.stringify(original));
                    clone.id = this.makeId();
                    this.blocks.splice(index + 1, 0, clone);
                    this.activeBlockId = clone.id;
                    this.updateHtml();
                },
                removeBlock(index) {
                    this.blocks.splice(index, 1);
                    this.updateHtml();
                },
                blockLabel(type) {
                    return {
                        text: 'Paragraphe de Texte',
                        heading: 'Grand Titre',
                        button: 'Bouton d’action (CTA)',
                        callout: 'Encadré d’information',
                        divider: 'Ligne de séparation',
                        image: 'Bannière / Image',
                        logo: 'Logo CAEI',
                        signature: 'Signature institutionnelle',
                        attachment: 'Pièce jointe / Plaquette'
                    }[type] || 'Bloc';
                },
                updateHtml() {
                    this.htmlValue = this.buildHtmlFromBlocks(false);
                },
                get livePreviewHtml() {
                    return this.mode === 'builder'
                        ? this.buildHtmlFromBlocks(true)
                        : this.replaceVariablesForPreview(this.htmlValue);
                },
                replaceVariablesForPreview(text) {
                    if (!text) return '';
                    let res = String(text);
                    for (const [k, v] of Object.entries(this.previewVariables)) {
                        res = res.replaceAll('@{{' + k + '}}', v).replaceAll('{{' + k + '}}', v);
                    }
                    return res;
                },
                buildHtmlFromBlocks(forPreview = false) {
                    let html = '';
                    this.blocks.forEach((block) => {
                        if (!block || !block.type) return;

                        let textVal = block.content || '';
                        let labelVal = block.label || '';
                        let urlVal = block.url || '#';

                        if (forPreview) {
                            textVal = this.replaceVariablesForPreview(textVal);
                            labelVal = this.replaceVariablesForPreview(labelVal);
                            urlVal = this.replaceVariablesForPreview(urlVal);
                        }

                        switch (block.type) {
                            case 'text':
                                html += '<div style="margin:0 0 16px 0; text-align:' + (block.align || 'left') + ';"><p style="margin:0; line-height:1.7; color:#1e293b; font-size:15px;">' + textVal.replace(/\n/g, '<br>') + '</p></div>';
                                break;
                            case 'heading':
                                const level = block.level || 'h2';
                                const size = level === 'h1' ? '24px' : (level === 'h2' ? '20px' : '17px');
                                html += '<div style="margin:18px 0 12px 0; text-align:' + (block.align || 'left') + ';"><h2 style="margin:0; font-size:' + size + '; font-weight:800; color:#03123F; letter-spacing:-0.5px;">' + textVal + '</h2></div>';
                                break;
                            case 'button':
                                html += '<div style="margin:22px 0; text-align:center;"><a href="' + urlVal + '" style="display:inline-block;background:' + (block.color || '#C57A1E') + ';color:#ffffff;text-decoration:none;padding:13px 26px;border-radius:10px;font-weight:700;font-size:14px;letter-spacing:0.3px;box-shadow:0 3px 10px rgba(197,122,30,0.3);">' + (labelVal || 'Confirmer') + '</a></div>';
                                break;
                            case 'callout':
                                html += '<div style="margin:18px 0; padding:14px 18px; background:#fffbeb; border-left:4px solid #C57A1E; border-radius:8px;"><p style="margin:0; line-height:1.6; color:#92400e; font-size:13.5px; font-weight:500;">' + textVal.replace(/\n/g, '<br>') + '</p></div>';
                                break;
                            case 'divider':
                                html += '<div style="margin:20px 0; border-top:1px solid #e2e8f0;"></div>';
                                break;
                            case 'logo':
                                html += '<div style="margin:0 0 20px 0; text-align:center;"><img src="/images/logo-caei.jpg" alt="CAEI" width="' + (block.width || 115) + '" style="max-width:100%; height:auto; display:inline-block; border-radius:50%; box-shadow:0 2px 8px rgba(0,0,0,0.1);" /></div>';
                                break;
                            case 'image':
                                html += '<div style="margin:0 0 20px 0; text-align:center;"><img src="' + (block.src || '/images/logo-caei.jpg') + '" alt="' + (block.alt || 'CAEI') + '" width="' + (block.width || 240) + '" style="max-width:100%; height:auto; display:inline-block; border-radius:10px;" /></div>';
                                break;
                            case 'signature':
                                html += '<div style="margin:24px 0 0 0; padding-top:16px; border-top:1px solid #f1f5f9; font-style:italic; color:#475569; font-size:13px; line-height:1.6;">' + textVal.replace(/\n/g, '<br>') + '</div>';
                                break;
                            case 'attachment':
                                html += '<div style="margin:0 0 16px 0;"><a href="' + urlVal + '" style="display:inline-flex; align-items:center; gap:6px; color:#C57A1E; text-decoration:none; font-weight:700; font-size:13px; padding:8px 14px; background:#fffbeb; border:1px solid #fef3c7; border-radius:8px;">📎 ' + (labelVal || 'Document') + '</a></div>';
                                break;
                        }
                    });
                    return html;
                },
                parseInitial(content) {
                    if (!content || !String(content).trim()) return [];
                    try {
                        const parsed = JSON.parse(String(content));
                        if (parsed && Array.isArray(parsed.blocks)) return parsed.blocks;
                        if (Array.isArray(parsed)) return parsed;
                    } catch (e) {}
                    const text = String(content).replace(/\\n/g, '\n');
                    return [{ id: this.makeId(), type: 'text', content: text, align: 'left' }];
                },
                makeId() {
                    return 'block-' + Math.random().toString(36).slice(2, 10);
                }
            };
        }
    </script>
</x-app-layout>
