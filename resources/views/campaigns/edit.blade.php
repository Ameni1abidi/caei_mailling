<x-app-layout>
    <div class="p-4 sm:p-6 max-w-[1550px] mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3.5 bg-gradient-to-br from-amber-500 to-orange-600 text-white rounded-2xl shadow-md shadow-amber-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Modifier la campagne</h1>
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

        <!-- Destination target Info banner (dynamic via audienceBanner) -->
        <div
            x-data="audienceBanner({{ $nbDestinataires }})"
            @audience-count-changed.window="update($event.detail.count)"
            class="bg-indigo-50 border border-indigo-100 text-indigo-900 p-4 rounded-2xl flex items-center gap-3.5 shadow-sm transition-all"
        >
            <div class="p-2.5 bg-indigo-100 text-indigo-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="flex-1">
                <div class="text-xs text-indigo-700 font-semibold uppercase tracking-wider">Cible d'envoi</div>
                <div class="text-base font-bold text-indigo-950 mt-0.5">
                    Cette campagne sera envoyée à
                    <strong class="text-indigo-600 font-extrabold tabular-nums" x-text="count.toLocaleString('fr-FR')"></strong>
                    destinataire<span x-show="count !== 1">s</span> ciblé<span x-show="count !== 1">s</span>.
                </div>
            </div>
            <span x-show="loading" class="inline-block w-4 h-4 border-2 border-indigo-400 border-t-transparent rounded-full animate-spin shrink-0"></span>
        </div>
        <script>
            function audienceBanner(initial) {
                return { count: initial, loading: false,
                    update(c) { this.count = c; } };
            }
        </script>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Side Campaign Edit Form (2 cols) -->
            <div class="lg:col-span-2 space-y-6">
                <form action="{{ route('campaigns.update', $campaign) }}" method="POST" class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
                    @csrf
                    @method('PUT')

                    <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Configuration de la campagne
                    </h2>

                    <!-- Boîte d'envoi automatique -->
                    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200/80 rounded-xl p-3.5 flex items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="font-bold text-emerald-950">Boîte d'envoi OVH :</span>
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
                        <input type="text" name="nom" id="nom" value="{{ old('nom', $campaign->nom) }}" required placeholder="Ex: Séminaire International IA & Fraude" class="w-full text-sm py-2.5 px-3.5 rounded-xl bg-slate-50/50 border @error('nom') border-rose-300 focus:ring-rose-500 focus:border-rose-500 @else border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @enderror transition-all">
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
                        <input type="text" name="objet" id="objet" value="{{ old('objet', $campaign->objet) }}" required placeholder="Ex: Invitation officielle au séminaire international CAEI" class="w-full text-sm py-2.5 px-3.5 rounded-xl bg-slate-50/50 border @error('objet') border-rose-300 focus:ring-rose-500 focus:border-rose-500 @else border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @enderror transition-all">
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
                            broadcast-changes
                        />
                    </div>

                    <!-- Configuration des relances automatiques -->
                    <div class="pt-3 border-t border-slate-100" x-data="{ autoRetry: {{ old('auto_retry', $campaign->auto_retry) ? 'true' : 'false' }} }">
                        <label class="block text-sm font-bold text-slate-800 mb-2">
                            Relances automatiques d'envoi
                        </label>
                        
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="auto_retry" value="1" x-model="autoRetry" class="h-4.5 w-4.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <span class="text-sm font-bold text-slate-800">Activer la relance automatique en cas d'échec</span>
                                    <p class="text-xs text-slate-500">Le système tentera automatiquement de renvoyer les emails échoués en arrière-plan.</p>
                                </div>
                            </label>

                            <div x-show="autoRetry" class="pl-7 pt-2.5 border-t border-slate-200/60 flex flex-wrap items-center gap-3">
                                <label for="max_auto_retries" class="text-xs font-semibold text-slate-700">
                                    Nombre maximal de relances automatiques :
                                </label>
                                <select name="max_auto_retries" id="max_auto_retries" class="text-xs py-1.5 px-3 rounded-lg border-slate-200 bg-white font-bold text-slate-800 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="1" @selected(old('max_auto_retries', $campaign->max_auto_retries ?? 3) == 1)>1 relance max</option>
                                    <option value="2" @selected(old('max_auto_retries', $campaign->max_auto_retries ?? 3) == 2)>2 relances max</option>
                                    <option value="3" @selected(old('max_auto_retries', $campaign->max_auto_retries ?? 3) == 3)>3 relances max (Recommandé)</option>
                                    <option value="5" @selected(old('max_auto_retries', $campaign->max_auto_retries ?? 3) == 5)>5 relances max</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Contenu du message : ÉDITEUR CONFORME À LA MAQUETTE -->
                    @php
                        $defaultContent = old('contenu', $campaign->contenu ?? '');
                    @endphp
                    <div class="pt-4 border-t border-slate-100 space-y-5"
                         x-data="emailBuilder({ initialContent: {{ json_encode($defaultContent) }} })">

                        <!-- Header de l'Éditeur -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100">
                            <div>
                                <h2 class="text-lg font-extrabold text-slate-900 tracking-tight flex items-center gap-1">
                                    <span>Éditeur de campagne</span>
                                    <span class="text-rose-500">*</span>
                                </h2>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Composez votre e-mail bloc par bloc, puis vérifiez l'aperçu avant l'envoi.
                                </p>
                            </div>

                            <!-- Boutons Constructeur / Modifier HTML -->
                            <div class="flex items-center gap-2">
                                <button type="button"
                                        @click="mode = 'builder'"
                                        :class="mode === 'builder' ? 'bg-[#4338CA] text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                                    <span>Constructeur</span>
                                </button>
                                <button type="button"
                                        @click="mode = 'html'; htmlValue = buildHtmlFromBlocks()"
                                        :class="mode === 'html' ? 'bg-[#4338CA] text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                                    <span>Modifier HTML</span>
                                </button>
                            </div>
                        </div>

                        <!-- Ligne : Objet de l'e-mail & Texte d'aperçu (préheader) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="objet" class="block text-xs font-bold text-slate-800 mb-1.5">
                                    Objet de l'e-mail <span class="text-rose-500">*</span>
                                </label>
                                <input type="text"
                                       name="objet"
                                       id="objet"
                                       value="{{ old('objet', $campaign->objet) }}"
                                       required
                                       placeholder="Invitation : séminaire CAEI du 12 décembre"
                                       class="w-full text-xs py-2.5 px-3.5 rounded-xl bg-white border @error('objet') border-rose-300 focus:ring-rose-500 @else border-slate-200 focus:ring-2 focus:ring-indigo-500 @enderror transition-all">
                                @error('objet')
                                    <p class="text-[11px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="preheader" class="block text-xs font-bold text-slate-800 mb-1.5">
                                    Texte d'aperçu (préheader)
                                </label>
                                <input type="text"
                                       name="preheader"
                                       id="preheader"
                                       x-model="preheaderText"
                                       placeholder="Réservez votre place dès maintenant"
                                       class="w-full text-xs py-2.5 px-3.5 rounded-xl bg-white border border-slate-200 focus:ring-2 focus:ring-indigo-500 transition-all">
                            </div>
                        </div>

                        <input type="hidden" name="contenu" :value="contentValue">

                        <!-- ── MODE CONSTRUCTEUR : 3 COLONNES CONFORMES À LA MAQUETTE ── -->
                        <div x-show="mode === 'builder'"
                             style="display: flex; gap: 16px; align-items: flex-start; width: 100%; box-sizing: border-box;"
                             class="flex-col xl:flex-row">

                            <!-- COLONNE 1 : AJOUTER UN BLOC (190px) -->
                            <div style="width: 190px; min-width: 190px; max-width: 190px; flex-shrink: 0; box-sizing: border-box;"
                                 class="bg-slate-50/70 p-3 rounded-2xl border border-slate-200/80 space-y-2.5">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-1">
                                    AJOUTER UN BLOC
                                </div>

                                <div class="grid grid-cols-1 gap-1.5">
                                    <button type="button" @click="addBlock('text')"
                                            class="flex items-center gap-2.5 p-2 rounded-xl border border-slate-200/80 bg-white hover:border-indigo-400 hover:bg-indigo-50/50 transition text-left group">
                                        <span class="w-6 h-6 rounded-lg bg-indigo-50 text-[#4338CA] flex items-center justify-center font-bold text-xs shrink-0">T</span>
                                        <span class="text-xs font-bold text-slate-800 group-hover:text-[#4338CA]">Texte</span>
                                    </button>

                                    <button type="button" @click="addBlock('image')"
                                            class="flex items-center gap-2.5 p-2 rounded-xl border border-slate-200/80 bg-white hover:border-indigo-400 hover:bg-indigo-50/50 transition text-left group">
                                        <span class="w-6 h-6 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center text-xs shrink-0">🖼️</span>
                                        <span class="text-xs font-bold text-slate-800 group-hover:text-sky-600">Image</span>
                                    </button>

                                    <button type="button" @click="addBlock('logo')"
                                            class="flex items-center gap-2.5 p-2 rounded-xl border border-slate-200/80 bg-white hover:border-indigo-400 hover:bg-indigo-50/50 transition text-left group">
                                        <span class="w-6 h-6 rounded-lg bg-indigo-50 text-[#4338CA] flex items-center justify-center text-xs shrink-0">🏷️</span>
                                        <span class="text-xs font-bold text-slate-800 group-hover:text-[#4338CA]">Logo CAEI</span>
                                    </button>

                                    <button type="button" @click="addBlock('button')"
                                            class="flex items-center gap-2.5 p-2 rounded-xl border border-slate-200/80 bg-white hover:border-indigo-400 hover:bg-indigo-50/50 transition text-left group">
                                        <span class="w-6 h-6 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-xs shrink-0">🔘</span>
                                        <span class="text-xs font-bold text-slate-800 group-hover:text-purple-600">Bouton</span>
                                    </button>

                                    <button type="button" @click="addBlock('link')"
                                            class="flex items-center gap-2.5 p-2 rounded-xl border border-slate-200/80 bg-white hover:border-indigo-400 hover:bg-indigo-50/50 transition text-left group">
                                        <span class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs shrink-0">🔗</span>
                                        <span class="text-xs font-bold text-slate-800 group-hover:text-blue-600">Lien</span>
                                    </button>

                                    <button type="button" @click="addBlock('signature')"
                                            class="flex items-center gap-2.5 p-2 rounded-xl border border-slate-200/80 bg-white hover:border-indigo-400 hover:bg-indigo-50/50 transition text-left group">
                                        <span class="w-6 h-6 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-xs shrink-0">✍️</span>
                                        <span class="text-xs font-bold text-slate-800 group-hover:text-rose-600">Signature</span>
                                    </button>

                                    <button type="button" @click="addBlock('attachment')"
                                            class="flex items-center gap-2.5 p-2 rounded-xl border border-slate-200/80 bg-white hover:border-indigo-400 hover:bg-indigo-50/50 transition text-left group">
                                        <span class="w-6 h-6 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center text-xs shrink-0">📎</span>
                                        <span class="text-xs font-bold text-slate-800 group-hover:text-teal-600">Pièce jointe</span>
                                    </button>
                                </div>

                                <p class="text-[10px] text-slate-400 leading-tight px-1 pt-1">
                                    Cliquez sur un bloc pour l'ajouter à l'e-mail.
                                </p>
                            </div>

                            <!-- COLONNE 2 : CANEVAS / APERÇU DE L'E-MAIL -->
                            <div style="flex: 1 1 0%; min-width: 0; box-sizing: border-box;"
                                 class="bg-slate-100/80 rounded-2xl border border-slate-200/80 p-4 space-y-3">

                                <div class="flex items-center justify-between pb-1">
                                    <span class="text-xs font-bold text-slate-800">Aperçu de l'e-mail</span>
                                    <div class="flex items-center gap-1 bg-white p-0.5 rounded-lg border border-slate-200/80">
                                        <button type="button"
                                                @click="previewDevice = 'desktop'"
                                                :class="previewDevice === 'desktop' ? 'bg-[#EEF2FF] text-[#4338CA] font-bold' : 'text-slate-600 hover:text-slate-900'"
                                                class="px-2.5 py-1 rounded-md text-[11px] transition">
                                            Ordinateur
                                        </button>
                                        <button type="button"
                                                @click="previewDevice = 'mobile'"
                                                :class="previewDevice === 'mobile' ? 'bg-[#EEF2FF] text-[#4338CA] font-bold' : 'text-slate-600 hover:text-slate-900'"
                                                class="px-2.5 py-1 rounded-md text-[11px] transition">
                                            Mobile
                                        </button>
                                    </div>
                                </div>

                                <div class="flex justify-center">
                                    <div :class="previewDevice === 'mobile' ? 'w-[320px] rounded-3xl border-4 border-slate-800 shadow-xl' : 'w-full rounded-2xl shadow-sm border border-slate-200/70'"
                                         class="bg-white p-5 space-y-3 transition-all duration-200 min-h-[420px]">

                                        <!-- Entête Logo -->
                                        <div class="flex items-center gap-2.5 pb-2 border-b border-slate-50">
                                            <div class="w-8 h-8 rounded-lg bg-[#2563EB] text-white flex items-center justify-center font-black text-[11px]">
                                                CAEI
                                            </div>
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">[LOGO CAEI]</span>
                                        </div>

                                        <!-- Liste des blocs éditables -->
                                        <div class="space-y-2.5">
                                            <template x-for="(block, index) in blocks" :key="block.id">
                                                <div>
                                                    <!-- Bloc TEXTE -->
                                                    <template x-if="block.type === 'text'">
                                                        <div class="relative transition-all"
                                                             :class="activeBlockId === block.id ? 'rounded-2xl border-2 border-[#6366F1] bg-white p-3 shadow-xs' : 'rounded-xl border border-transparent hover:border-slate-200 p-2 cursor-pointer'"
                                                             @click="activeBlockId = block.id">

                                                            <!-- Badge flottant -->
                                                            <div x-show="activeBlockId === block.id"
                                                                 class="absolute -top-3 left-3 z-10 flex items-center gap-1 bg-[#4338CA] text-white px-2 py-0.5 rounded-md text-[10px] font-bold shadow-xs">
                                                                <span>Bloc texte</span>
                                                                <button type="button" @click.stop="moveBlockUp(index)" :disabled="index === 0" class="hover:text-indigo-200 px-0.5 disabled:opacity-30">▲</button>
                                                                <button type="button" @click.stop="moveBlockDown(index)" :disabled="index === blocks.length - 1" class="hover:text-indigo-200 px-0.5 disabled:opacity-30">▼</button>
                                                                <button type="button" @click.stop="duplicateBlock(index)" class="hover:text-indigo-200 px-0.5" title="Dupliquer">⎘</button>
                                                            </div>

                                                            <!-- Outils formatage -->
                                                            <div x-show="activeBlockId === block.id" class="flex items-center gap-1.5 pb-2 mb-2 border-b border-slate-100 text-xs">
                                                                <button type="button" @click.stop="applyFormatToBlock(block, 'bold')" class="w-5 h-5 rounded hover:bg-slate-100 font-bold text-slate-700 flex items-center justify-center">B</button>
                                                                <button type="button" @click.stop="applyFormatToBlock(block, 'italic')" class="w-5 h-5 rounded hover:bg-slate-100 italic text-slate-700 font-serif flex items-center justify-center">I</button>
                                                                <button type="button" @click.stop="applyFormatToBlock(block, 'underline')" class="w-5 h-5 rounded hover:bg-slate-100 underline text-slate-700 flex items-center justify-center">U</button>

                                                                <div class="relative" x-data="{ openVars: false }">
                                                                    <button type="button" @click.stop="openVars = !openVars"
                                                                            class="px-2 py-0.5 rounded bg-[#EEF2FF] text-[#4338CA] font-bold text-[10px] flex items-center gap-1">
                                                                        <span>Insérer variable</span>
                                                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                                    </button>
                                                                    <div x-show="openVars" @click.away="openVars = false"
                                                                         class="absolute left-0 top-full mt-1 w-40 bg-white rounded-xl shadow-lg border border-slate-200 p-1 z-30 space-y-0.5">
                                                                        <button type="button" @click.stop="insertVariableInBlock(block, '@{{prenom}}'); openVars = false" class="w-full text-left px-2 py-1 rounded text-[11px] hover:bg-indigo-50 text-indigo-900 font-mono">@{{prenom}}</button>
                                                                        <button type="button" @click.stop="insertVariableInBlock(block, '@{{nom}}'); openVars = false" class="w-full text-left px-2 py-1 rounded text-[11px] hover:bg-indigo-50 text-indigo-900 font-mono">@{{nom}}</button>
                                                                        <button type="button" @click.stop="insertVariableInBlock(block, '@{{entreprise}}'); openVars = false" class="w-full text-left px-2 py-1 rounded text-[11px] hover:bg-indigo-50 text-indigo-900 font-mono">@{{entreprise}}</button>
                                                                        <button type="button" @click.stop="insertVariableInBlock(block, '@{{lien}}'); openVars = false" class="w-full text-left px-2 py-1 rounded text-[11px] hover:bg-indigo-50 text-indigo-900 font-mono">@{{lien}}</button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <textarea x-model="block.content"
                                                                      @input="updateHtml()"
                                                                      :style="'text-align:' + (block.align || 'left') + '; font-size:' + (block.fontSize || '15px') + '; color:' + (block.color || '#1E1B4B') + ';'"
                                                                      rows="4"
                                                                      class="w-full border-0 focus:ring-0 p-1 leading-relaxed resize-none outline-none bg-transparent font-sans text-xs"
                                                                      placeholder="Bonjour {{prenom}} {{nom}}, ..."></textarea>
                                                        </div>
                                                    </template>

                                                    <!-- Bloc IMAGE -->
                                                    <template x-if="block.type === 'image'">
                                                        <div class="relative transition-all"
                                                             :class="activeBlockId === block.id ? 'rounded-2xl border-2 border-[#6366F1] p-2.5' : 'rounded-xl border border-transparent hover:border-slate-200 p-1 cursor-pointer'"
                                                             @click="activeBlockId = block.id">
                                                            <div x-show="activeBlockId === block.id"
                                                                 class="absolute -top-3 left-3 z-10 flex items-center gap-1 bg-[#4338CA] text-white px-2 py-0.5 rounded-md text-[10px] font-bold shadow-xs">
                                                                <span>Bloc image</span>
                                                                <button type="button" @click.stop="moveBlockUp(index)" :disabled="index === 0" class="hover:text-indigo-200 px-0.5 disabled:opacity-30">▲</button>
                                                                <button type="button" @click.stop="moveBlockDown(index)" :disabled="index === blocks.length - 1" class="hover:text-indigo-200 px-0.5 disabled:opacity-30">▼</button>
                                                                <button type="button" @click.stop="duplicateBlock(index)" class="hover:text-indigo-200 px-0.5">⎘</button>
                                                            </div>
                                                            <template x-if="block.src && block.src !== '/logo-caei.svg'">
                                                                <img :src="block.src" :alt="block.alt || 'Image'" class="w-full rounded-xl object-cover max-h-40">
                                                            </template>
                                                            <template x-if="!block.src || block.src === '/logo-caei.svg'">
                                                                <div class="w-full h-24 rounded-xl bg-slate-100 border border-dashed border-slate-300 flex items-center justify-center text-[11px] font-semibold text-slate-500">
                                                                    [BANNIÈRE · 600 × 200]
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>

                                                    <!-- Bloc LOGO CAEI -->
                                                    <template x-if="block.type === 'logo'">
                                                        <div class="relative transition-all"
                                                             :class="activeBlockId === block.id ? 'rounded-2xl border-2 border-[#6366F1] p-2.5' : 'rounded-xl border border-transparent hover:border-slate-200 p-1 cursor-pointer'"
                                                             @click="activeBlockId = block.id">
                                                            <div x-show="activeBlockId === block.id"
                                                                 class="absolute -top-3 left-3 z-10 flex items-center gap-1 bg-[#4338CA] text-white px-2 py-0.5 rounded-md text-[10px] font-bold shadow-xs">
                                                                <span>Logo CAEI</span>
                                                            </div>
                                                            <div class="text-center py-1">
                                                                <img :src="block.src || '/images/logo-caei.jpg'" :alt="block.alt || 'Logo CAEI'" :width="block.width || 115" class="inline-block rounded-full shadow-xs">
                                                            </div>
                                                        </div>
                                                    </template>

                                                    <!-- Bloc BOUTON -->
                                                    <template x-if="block.type === 'button'">
                                                        <div class="relative transition-all py-1.5"
                                                             :class="activeBlockId === block.id ? 'rounded-2xl border-2 border-[#6366F1] p-2.5' : 'rounded-xl border border-transparent hover:border-slate-200 p-1 cursor-pointer'"
                                                             @click="activeBlockId = block.id">
                                                            <div x-show="activeBlockId === block.id"
                                                                 class="absolute -top-3 left-3 z-10 flex items-center gap-1 bg-[#4338CA] text-white px-2 py-0.5 rounded-md text-[10px] font-bold shadow-xs">
                                                                <span>Bloc bouton</span>
                                                                <button type="button" @click.stop="moveBlockUp(index)" :disabled="index === 0" class="hover:text-indigo-200 px-0.5 disabled:opacity-30">▲</button>
                                                                <button type="button" @click.stop="moveBlockDown(index)" :disabled="index === blocks.length - 1" class="hover:text-indigo-200 px-0.5 disabled:opacity-30">▼</button>
                                                                <button type="button" @click.stop="duplicateBlock(index)" class="hover:text-indigo-200 px-0.5">⎘</button>
                                                            </div>
                                                            <div class="text-center">
                                                                <span :style="'background:' + (block.color || '#4338CA') + ';'"
                                                                      class="inline-block px-6 py-2.5 rounded-xl text-white font-bold text-xs shadow-xs"
                                                                      x-text="block.label || 'Confirmer ma présence'"></span>
                                                            </div>
                                                        </div>
                                                    </template>

                                                    <!-- Bloc LIEN -->
                                                    <template x-if="block.type === 'link'">
                                                        <div class="relative transition-all py-1"
                                                             :class="activeBlockId === block.id ? 'rounded-2xl border-2 border-[#6366F1] p-2.5' : 'rounded-xl border border-transparent hover:border-slate-200 p-1 cursor-pointer'"
                                                             @click="activeBlockId = block.id">
                                                            <div x-show="activeBlockId === block.id"
                                                                 class="absolute -top-3 left-3 z-10 flex items-center gap-1 bg-[#4338CA] text-white px-2 py-0.5 rounded-md text-[10px] font-bold shadow-xs">
                                                                <span>Bloc lien</span>
                                                            </div>
                                                            <a href="javascript:void(0)" class="text-xs font-semibold text-[#4338CA] underline" x-text="block.label || 'En savoir plus'"></a>
                                                        </div>
                                                    </template>

                                                    <!-- Bloc SIGNATURE -->
                                                    <template x-if="block.type === 'signature'">
                                                        <div class="relative transition-all pt-1.5"
                                                             :class="activeBlockId === block.id ? 'rounded-2xl border-2 border-[#6366F1] p-2.5' : 'rounded-xl border border-transparent hover:border-slate-200 p-1 cursor-pointer'"
                                                             @click="activeBlockId = block.id">
                                                            <div x-show="activeBlockId === block.id"
                                                                 class="absolute -top-3 left-3 z-10 flex items-center gap-1 bg-[#4338CA] text-white px-2 py-0.5 rounded-md text-[10px] font-bold shadow-xs">
                                                                <span>Bloc signature</span>
                                                                <button type="button" @click.stop="moveBlockUp(index)" :disabled="index === 0" class="hover:text-indigo-200 px-0.5 disabled:opacity-30">▲</button>
                                                                <button type="button" @click.stop="moveBlockDown(index)" :disabled="index === blocks.length - 1" class="hover:text-indigo-200 px-0.5 disabled:opacity-30">▼</button>
                                                                <button type="button" @click.stop="duplicateBlock(index)" class="hover:text-indigo-200 px-0.5">⎘</button>
                                                            </div>
                                                            <div class="text-slate-800 text-xs leading-relaxed">
                                                                <div class="font-bold">[Prénom Nom]</div>
                                                                <div class="text-slate-500 text-[11px]">[Fonction] · CAEI</div>
                                                            </div>
                                                        </div>
                                                    </template>

                                                    <!-- Bloc PIÈCE JOINTE -->
                                                    <template x-if="block.type === 'attachment'">
                                                        <div class="relative transition-all"
                                                             :class="activeBlockId === block.id ? 'rounded-2xl border-2 border-[#6366F1] p-2.5' : 'rounded-xl border border-transparent hover:border-slate-200 p-1 cursor-pointer'"
                                                             @click="activeBlockId = block.id">
                                                            <div x-show="activeBlockId === block.id"
                                                                 class="absolute -top-3 left-3 z-10 flex items-center gap-1 bg-[#4338CA] text-white px-2 py-0.5 rounded-md text-[10px] font-bold shadow-xs">
                                                                <span>Pièce jointe</span>
                                                            </div>
                                                            <div class="p-2.5 rounded-xl border border-slate-200 bg-white flex items-center gap-2 text-xs text-slate-700">
                                                                <span class="text-slate-400">📎</span>
                                                                <span class="font-semibold" x-text="block.label || '[programme.pdf] · [taille]'"></span>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>

                                        <button type="button"
                                                @click="addBlock('text')"
                                                class="w-full py-2.5 rounded-xl border-2 border-dashed border-slate-200 hover:border-indigo-400 hover:bg-indigo-50/40 text-slate-400 hover:text-[#4338CA] text-xs font-semibold text-center transition flex items-center justify-center gap-1">
                                            <span>+ Déposer un bloc ici</span>
                                        </button>

                                        <div class="text-center text-[10px] text-slate-400 pt-3 border-t border-slate-100 leading-relaxed">
                                            Vous recevez cet e-mail car vous figurez dans notre liste de contacts. <a href="javascript:void(0)" class="underline text-slate-600">Se désabonner</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- COLONNE 3 : PROPRIÉTÉS DU BLOC ACTIF (250px) -->
                            <div style="width: 240px; min-width: 240px; max-width: 240px; flex-shrink: 0; box-sizing: border-box;"
                                 class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">

                                <template x-if="getActiveBlock()">
                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                                            <span class="text-xs font-bold text-slate-800" x-text="blockLabel(getActiveBlock().type)"></span>
                                            <button type="button"
                                                    @click="removeActiveBlock()"
                                                    class="text-[11px] font-bold text-rose-600 hover:text-rose-700">
                                                Supprimer
                                            </button>
                                        </div>

                                        <!-- Bloc TEXTE -->
                                        <template x-if="getActiveBlock().type === 'text'">
                                            <div class="space-y-3.5">
                                                <div>
                                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Alignement</label>
                                                    <div class="grid grid-cols-4 gap-1 p-1 bg-slate-100 rounded-xl">
                                                        <button type="button" @click="getActiveBlock().align = 'left'; updateHtml()"
                                                                :class="(getActiveBlock().align || 'left') === 'left' ? 'bg-[#EEF2FF] text-[#4338CA] font-bold' : 'text-slate-600 hover:text-slate-900'"
                                                                class="p-1 rounded-lg text-xs flex justify-center">⫷</button>
                                                        <button type="button" @click="getActiveBlock().align = 'center'; updateHtml()"
                                                                :class="getActiveBlock().align === 'center' ? 'bg-[#EEF2FF] text-[#4338CA] font-bold' : 'text-slate-600 hover:text-slate-900'"
                                                                class="p-1 rounded-lg text-xs flex justify-center">≡</button>
                                                        <button type="button" @click="getActiveBlock().align = 'right'; updateHtml()"
                                                                :class="getActiveBlock().align === 'right' ? 'bg-[#EEF2FF] text-[#4338CA] font-bold' : 'text-slate-600 hover:text-slate-900'"
                                                                class="p-1 rounded-lg text-xs flex justify-center">⫸</button>
                                                        <button type="button" @click="getActiveBlock().align = 'justify'; updateHtml()"
                                                                :class="getActiveBlock().align === 'justify' ? 'bg-[#EEF2FF] text-[#4338CA] font-bold' : 'text-slate-600 hover:text-slate-900'"
                                                                class="p-1 rounded-lg text-xs flex justify-center">☰</button>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Taille du texte</label>
                                                    <select x-model="getActiveBlock().fontSize"
                                                            @change="updateHtml()"
                                                            class="w-full text-xs rounded-xl border-slate-200 py-1.5 px-2.5 text-slate-800 font-medium">
                                                        <option value="13px">Petit (13 px)</option>
                                                        <option value="15px">Normal (15 px)</option>
                                                        <option value="18px">Grand (18 px)</option>
                                                        <option value="22px">Titre (22 px)</option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Couleur du texte</label>
                                                    <div class="flex items-center gap-1.5">
                                                        <button type="button" @click="getActiveBlock().color = '#1E1B4B'; updateHtml()" class="w-5 h-5 rounded-full bg-[#1E1B4B] ring-2 ring-offset-1" :class="getActiveBlock().color === '#1E1B4B' ? 'ring-[#4338CA]' : 'ring-transparent'"></button>
                                                        <button type="button" @click="getActiveBlock().color = '#475569'; updateHtml()" class="w-5 h-5 rounded-full bg-[#475569] ring-2 ring-offset-1" :class="getActiveBlock().color === '#475569' ? 'ring-[#4338CA]' : 'ring-transparent'"></button>
                                                        <button type="button" @click="getActiveBlock().color = '#4338CA'; updateHtml()" class="w-5 h-5 rounded-full bg-[#4338CA] ring-2 ring-offset-1" :class="getActiveBlock().color === '#4338CA' ? 'ring-[#4338CA]' : 'ring-transparent'"></button>
                                                        <button type="button" @click="getActiveBlock().color = '#DC2626'; updateHtml()" class="w-5 h-5 rounded-full bg-[#DC2626] ring-2 ring-offset-1" :class="getActiveBlock().color === '#DC2626' ? 'ring-[#4338CA]' : 'ring-transparent'"></button>
                                                        <input x-model="getActiveBlock().color" @input="updateHtml()" type="color" class="w-5 h-5 rounded cursor-pointer border-0 p-0">
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Variables disponibles</label>
                                                    <div class="flex flex-wrap gap-1">
                                                        <button type="button" @click="insertVariableInActive('@{{prenom}}')" class="px-2 py-0.5 rounded-lg bg-[#EEF2FF] hover:bg-indigo-100 text-[#4338CA] font-mono text-[10px] font-bold">@{{prenom}}</button>
                                                        <button type="button" @click="insertVariableInActive('@{{nom}}')" class="px-2 py-0.5 rounded-lg bg-[#EEF2FF] hover:bg-indigo-100 text-[#4338CA] font-mono text-[10px] font-bold">@{{nom}}</button>
                                                        <button type="button" @click="insertVariableInActive('@{{entreprise}}')" class="px-2 py-0.5 rounded-lg bg-[#EEF2FF] hover:bg-indigo-100 text-[#4338CA] font-mono text-[10px] font-bold">@{{entreprise}}</button>
                                                        <button type="button" @click="insertVariableInActive('@{{lien}}')" class="px-2 py-0.5 rounded-lg bg-[#EEF2FF] hover:bg-indigo-100 text-[#4338CA] font-mono text-[10px] font-bold">@{{lien}}</button>
                                                    </div>
                                                </div>

                                                <button type="button" @click="duplicateActiveBlock()" class="w-full py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                                                    Dupliquer le bloc
                                                </button>
                                            </div>
                                        </template>

                                        <!-- Bloc BOUTON -->
                                        <template x-if="getActiveBlock().type === 'button'">
                                            <div class="space-y-2.5">
                                                <div>
                                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Libellé</label>
                                                    <input x-model="getActiveBlock().label" @input="updateHtml()" type="text" class="w-full text-xs rounded-xl border-slate-200 p-1.5 font-semibold">
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Lien</label>
                                                    <input x-model="getActiveBlock().url" @input="updateHtml()" type="text" class="w-full text-xs rounded-xl border-slate-200 p-1.5 font-mono">
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Couleur</label>
                                                    <input x-model="getActiveBlock().color" @input="updateHtml()" type="color" class="w-7 h-7 rounded cursor-pointer border-0 p-0">
                                                </div>
                                                <button type="button" @click="duplicateActiveBlock()" class="w-full py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                                                    Dupliquer le bloc
                                                </button>
                                            </div>
                                        </template>

                                        <!-- Bloc IMAGE -->
                                        <template x-if="getActiveBlock().type === 'image'">
                                            <div class="space-y-2.5">
                                                <div>
                                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">URL image</label>
                                                    <input x-model="getActiveBlock().src" @input="updateHtml()" type="text" class="w-full text-xs rounded-xl border-slate-200 p-1.5">
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Texte Alt</label>
                                                    <input x-model="getActiveBlock().alt" @input="updateHtml()" type="text" class="w-full text-xs rounded-xl border-slate-200 p-1.5">
                                                </div>
                                                <button type="button" @click="duplicateActiveBlock()" class="w-full py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                                                    Dupliquer le bloc
                                                </button>
                                            </div>
                                        </template>

                                        <!-- Bloc SIGNATURE -->
                                        <template x-if="getActiveBlock().type === 'signature'">
                                            <div class="space-y-2.5">
                                                <div>
                                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Texte signature</label>
                                                    <textarea x-model="getActiveBlock().content" @input="updateHtml()" rows="3" class="w-full text-xs rounded-xl border-slate-200 p-1.5"></textarea>
                                                </div>
                                                <button type="button" @click="duplicateActiveBlock()" class="w-full py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                                                    Dupliquer le bloc
                                                </button>
                                            </div>
                                        </template>

                                        <!-- Bloc PIÈCE JOINTE -->
                                        <template x-if="getActiveBlock().type === 'attachment'">
                                            <div class="space-y-2.5">
                                                <div>
                                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Nom</label>
                                                    <input x-model="getActiveBlock().label" @input="updateHtml()" type="text" class="w-full text-xs rounded-xl border-slate-200 p-1.5">
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Lien</label>
                                                    <input x-model="getActiveBlock().url" @input="updateHtml()" type="text" class="w-full text-xs rounded-xl border-slate-200 p-1.5 font-mono">
                                                </div>
                                                <button type="button" @click="duplicateActiveBlock()" class="w-full py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                                                    Dupliquer le bloc
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <template x-if="!getActiveBlock()">
                                    <div class="text-center py-8 text-[11px] text-slate-400">
                                        Cliquez sur un bloc pour modifier ses options.
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- ── MODE HTML DIRECT ── -->
                        <div x-show="mode === 'html'" class="space-y-3">
                            <div class="p-3 rounded-xl bg-indigo-50 border border-indigo-100 text-xs text-indigo-900">
                                En mode HTML, votre code est injecté directement dans l'email.
                            </div>
                            <textarea x-model="htmlValue" rows="12"
                                      class="w-full rounded-2xl border border-slate-300 bg-slate-900 text-emerald-400 p-4 font-mono text-xs leading-relaxed focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                      placeholder="<html>...</html>"></textarea>
                        </div>

                        <!-- Astuce conforme à la maquette -->
                        <div class="rounded-xl border border-indigo-100 bg-[#EEF2FF] p-3 text-xs text-[#4338CA] font-medium">
                            Astuce : vous pouvez utiliser les variables <span class="font-bold">@{{prenom}}</span>, <span class="font-bold">@{{entreprise}}</span> et <span class="font-bold">@{{lien}}</span> dans vos blocs.
                        </div>
                    </div>

                    <script>
                        function emailBuilder({ initialContent = '' } = {}) {
                            return {
                                mode: 'builder',
                                previewDevice: 'desktop',
                                preheaderText: '',
                                blocks: [],
                                activeBlockId: null,
                                htmlValue: '',
                                get contentValue() {
                                    return this.mode === 'builder' ? JSON.stringify({ version: 1, theme: 'caei', preheader: this.preheaderText, blocks: this.blocks }) : this.htmlValue;
                                },
                                init() {
                                    this.blocks = this.parseInitial(initialContent);
                                    if (this.blocks.length > 0) {
                                        const textBlock = this.blocks.find(b => b.type === 'text');
                                        this.activeBlockId = textBlock ? textBlock.id : this.blocks[0].id;
                                    }
                                    this.htmlValue = this.buildHtmlFromBlocks();
                                },
                                parseInitial(content) {
                                    if (!content || !String(content).trim()) {
                                        return [
                                            { id: this.makeId(), type: 'image', src: '', alt: 'BANNIÈRE · 600 × 200' },
                                            { id: this.makeId(), type: 'text', content: "Bonjour @{{prenom}} @{{nom}} ,\n\nNous avons le plaisir de vous inviter à notre prochain séminaire, réservé aux dirigeants de @{{entreprise}} et de ses partenaires.\n\nCordialement,", align: 'left', fontSize: '15px', color: '#1E1B4B' },
                                            { id: this.makeId(), type: 'button', label: 'Confirmer ma présence', url: '@{{lien}}', color: '#4338CA' },
                                            { id: this.makeId(), type: 'signature', content: "[Prénom Nom]\n[Fonction] · CAEI" },
                                            { id: this.makeId(), type: 'attachment', label: '[programme.pdf] · [taille]', url: '@{{lien}}' }
                                        ];
                                    }

                                    try {
                                        const parsed = JSON.parse(String(content));
                                        if (parsed && Array.isArray(parsed.blocks)) {
                                            if (parsed.preheader) {
                                                this.preheaderText = parsed.preheader;
                                            }
                                            return parsed.blocks;
                                        }
                                        if (Array.isArray(parsed)) {
                                            return parsed;
                                        }
                                    } catch (e) {}

                                    const textContent = String(content).replace(/\\n/g, '\n');
                                    return [
                                        { id: this.makeId(), type: 'text', content: textContent, align: 'left', fontSize: '15px', color: '#1E1B4B' }
                                    ];
                                },
                                getActiveBlock() {
                                    return this.blocks.find(b => b.id === this.activeBlockId) || null;
                                },
                                addBlock(type) {
                                    const block = { id: this.makeId(), type };
                                    if (type === 'text') {
                                        block.content = 'Bonjour @{{prenom}},\n\nVotre nouveau paragraphe ici.';
                                        block.align = 'left';
                                        block.fontSize = '15px';
                                        block.color = '#1E1B4B';
                                    } else if (type === 'image') {
                                        block.src = '';
                                        block.alt = 'Bannière 600 × 200';
                                    } else if (type === 'logo') {
                                        block.src = '/images/logo-caei.jpg';
                                        block.alt = 'Logo CAEI';
                                        block.width = 115;
                                    } else if (type === 'button') {
                                        block.label = 'Confirmer ma présence';
                                        block.url = '@{{lien}}';
                                        block.color = '#4338CA';
                                    } else if (type === 'link') {
                                        block.label = 'En savoir plus';
                                        block.url = '@{{lien}}';
                                    } else if (type === 'signature') {
                                        block.content = "[Prénom Nom]\n[Fonction] · CAEI";
                                    } else if (type === 'attachment') {
                                        block.label = 'Plaquette de présentation.pdf';
                                        block.url = '@{{lien}}';
                                    }
                                    this.blocks.push(block);
                                    this.activeBlockId = block.id;
                                    this.updateHtml();
                                },
                                removeActiveBlock() {
                                    const idx = this.blocks.findIndex(b => b.id === this.activeBlockId);
                                    if (idx !== -1) {
                                        this.blocks.splice(idx, 1);
                                        this.activeBlockId = this.blocks.length > 0 ? this.blocks[Math.max(0, idx - 1)].id : null;
                                        this.updateHtml();
                                    }
                                },
                                duplicateActiveBlock() {
                                    const active = this.getActiveBlock();
                                    if (active) {
                                        const idx = this.blocks.findIndex(b => b.id === active.id);
                                        this.duplicateBlock(idx);
                                    }
                                },
                                duplicateBlock(index) {
                                    const original = this.blocks[index];
                                    if (!original) return;
                                    const cloned = JSON.parse(JSON.stringify(original));
                                    cloned.id = this.makeId();
                                    this.blocks.splice(index + 1, 0, cloned);
                                    this.activeBlockId = cloned.id;
                                    this.updateHtml();
                                },
                                moveBlockUp(index) {
                                    if (index > 0) {
                                        const item = this.blocks.splice(index, 1)[0];
                                        this.blocks.splice(index - 1, 0, item);
                                        this.updateHtml();
                                    }
                                },
                                moveBlockDown(index) {
                                    if (index < this.blocks.length - 1) {
                                        const item = this.blocks.splice(index, 1)[0];
                                        this.blocks.splice(index + 1, 0, item);
                                        this.updateHtml();
                                    }
                                },
                                insertVariableInActive(variable) {
                                    const active = this.getActiveBlock();
                                    if (active && active.type === 'text') {
                                        this.insertVariableInBlock(active, variable);
                                    }
                                },
                                insertVariableInBlock(block, variable) {
                                    block.content = (block.content || '') + ' ' + variable + ' ';
                                    this.updateHtml();
                                },
                                applyFormatToBlock(block, format) {
                                    if (!block.content) return;
                                    if (format === 'bold') {
                                        block.content = '**' + block.content + '**';
                                    } else if (format === 'italic') {
                                        block.content = '*' + block.content + '*';
                                    } else if (format === 'underline') {
                                        block.content = '__' + block.content + '__';
                                    }
                                    this.updateHtml();
                                },
                                updateHtml() {
                                    this.htmlValue = this.buildHtmlFromBlocks();
                                },
                                blockLabel(type) {
                                    return {
                                        text: 'Bloc texte',
                                        image: 'Bloc image',
                                        logo: 'Logo CAEI',
                                        button: 'Bloc bouton',
                                        link: 'Bloc lien',
                                        signature: 'Bloc signature',
                                        attachment: 'Pièce jointe'
                                    }[type] || 'Bloc';
                                },
                                buildHtmlFromBlocks() {
                                    let html = '';
                                    this.blocks.forEach((block) => {
                                        if (!block || !block.type) return;
                                        switch (block.type) {
                                            case 'text':
                                                html += '<div style="margin:0 0 16px 0; text-align:' + (block.align || 'left') + '; font-size:' + (block.fontSize || '15px') + '; color:' + (block.color || '#1e1b4b') + ';"><p style="margin:0; line-height:1.7;">' + (block.content || '').replace(/\n/g, '<br>') + '</p></div>';
                                                break;
                                            case 'image':
                                                if (block.src && block.src !== '/logo-caei.svg') {
                                                    html += '<div style="margin:0 0 16px 0; text-align:center;"><img src="' + block.src + '" alt="' + (block.alt || 'Bannière') + '" style="max-width:100%; height:auto; display:inline-block; border-radius:12px;" /></div>';
                                                }
                                                break;
                                            case 'logo':
                                                html += '<div style="margin:0 0 16px 0; text-align:center;"><img src="' + (block.src || '/images/logo-caei.jpg') + '" alt="CAEI" width="' + (block.width || 115) + '" style="max-width:100%; height:auto; display:inline-block; border-radius:50%;" /></div>';
                                                break;
                                            case 'button':
                                                html += '<div style="margin:20px 0; text-align:center;"><a href="' + (block.url || '#') + '" style="display:inline-block;background:' + (block.color || '#4338CA') + ';color:#ffffff;text-decoration:none;padding:12px 28px;border-radius:10px;font-weight:700;font-size:14px;">' + (block.label || 'Confirmer ma présence') + '</a></div>';
                                                break;
                                            case 'link':
                                                html += '<div style="margin:0 0 16px 0;"><a href="' + (block.url || '#') + '" style="color:#4338CA; text-decoration:underline; font-weight:600;">' + (block.label || 'En savoir plus') + '</a></div>';
                                                break;
                                            case 'signature':
                                                html += '<div style="margin:20px 0 0 0; padding-top:14px; border-top:1px solid #f1f5f9; color:#334155; line-height:1.6;">' + (block.content || '').replace(/\n/g, '<br>') + '</div>';
                                                break;
                                            case 'attachment':
                                                html += '<div style="margin:0 0 16px 0;"><a href="' + (block.url || '#') + '" style="display:inline-flex; align-items:center; gap:6px; color:#4338CA; text-decoration:none; font-weight:600; padding:8px 14px; background:#EEF2FF; border:1px solid #E0E7FF; border-radius:8px;">📎 ' + (block.label || 'Pièce jointe') + '</a></div>';
                                                break;
                                        }
                                    });
                                    return html;
                                },
                                makeId() {
                                    return 'b-' + Math.random().toString(36).slice(2, 9);
                                }
                            };
                        }
                    </script>

                    <!-- Action buttons footer inside form card -->
                    <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-100">
                        <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold text-sm px-5 py-2.5 rounded-xl shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Enregistrer les modifications</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Side Sidebar (Attachments and Launch Control) (1 col) -->
            <div class="space-y-6">
                <!-- Attachments Card -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                    <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        Pièces jointes ({{ $campaign->attachments->count() }})
                    </h2>

                    @if($campaign->attachments->isNotEmpty())
                        <div class="space-y-2 max-h-[300px] overflow-y-auto pr-1">
                            @foreach($campaign->attachments as $attachment)
                                <div class="bg-slate-50 border border-slate-200/60 p-3 rounded-xl flex items-center justify-between gap-3 group hover:border-slate-300 transition duration-150">
                                    <div class="min-w-0">
                                        <div class="text-xs font-bold text-slate-800 truncate" title="{{ $attachment->file_name }}">
                                            {{ $attachment->file_name }}
                                        </div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[10px] font-semibold bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded border border-indigo-100">
                                                {{ $attachment->typeLabel() }}
                                            </span>
                                            <span class="text-[10px] font-mono text-slate-400">
                                                {{ $attachment->file_size ? number_format($attachment->file_size / 1024, 1) . ' KB' : 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1 shrink-0">
                                        <a href="{{ route('attachments.download', $attachment) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Télécharger">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </a>
                                        <form action="{{ route('attachments.destroy', $attachment) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette pièce jointe ?')">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="redirect_to_campaign" value="1">
                                            <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Supprimer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-slate-50 p-4 text-center rounded-xl border border-slate-200/50">
                            <p class="text-xs text-slate-400 font-medium">Aucune pièce jointe associée pour le moment.</p>
                        </div>
                    @endif

                    <!-- Quick Add Attachment Form -->
                    <form action="{{ route('attachments.store') }}" method="POST" enctype="multipart/form-data" class="bg-slate-50/70 p-4 border border-slate-200/80 rounded-xl space-y-3 mt-4">
                        @csrf
                        <input type="hidden" name="campaign_id" value="{{ $campaign->id }}">
                        <input type="hidden" name="redirect_to_campaign" value="1">

                        <h3 class="text-xs font-extrabold text-slate-700 uppercase tracking-wider">Ajouter des fichiers</h3>
                        
                        <div class="space-y-2">
                            <div>
                                <label for="attachment_type" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Type (optionnel)</label>
                                <select name="attachment_type" id="attachment_type" class="border border-slate-200 w-full p-2 text-xs rounded-lg bg-white text-slate-700 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Non spécifié --</option>
                                    @foreach(\App\Models\CampaignAttachment::types() as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="files" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Sélectionner les fichiers *</label>
                                <input type="file" name="files[]" id="files" multiple required class="border border-slate-200 w-full p-2 text-xs bg-white rounded-lg file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[10px] file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold py-2 rounded-lg border border-indigo-200/50 transition">
                            Joindre le(s) fichier(s)
                        </button>
                    </form>
                </div>

                <!-- Launch Control Panel Card -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                    <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Lancement de l'envoi
                    </h2>

                    @if(($failedCount ?? 0) > 0)
                        <div class="bg-rose-50 border border-rose-200 text-rose-950 p-4 rounded-xl space-y-3">
                            <div class="flex items-center gap-2 text-xs font-bold text-rose-800 uppercase tracking-wider">
                                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ $failedCount }} email(s) en échec</span>
                            </div>
                            <p class="text-xs text-rose-700 leading-relaxed font-medium">
                                Des erreurs sont survenues lors du précédent envoi. Vous pouvez consulter les détails ci-dessous et relancer les destinataires.
                            </p>

                            @if(isset($failedLogs) && $failedLogs->isNotEmpty())
                                <div class="mt-2 space-y-2 max-h-48 overflow-y-auto border-t border-rose-200 pt-2 text-xs">
                                    <span class="font-bold text-rose-900 block mb-1">Détail des erreurs d'envoi :</span>
                                    @foreach($failedLogs as $log)
                                        <div class="bg-white/90 p-2 rounded-lg border border-rose-200 text-slate-800">
                                            <div class="font-bold text-rose-900 flex justify-between">
                                                <span>{{ $log->contact?->email ?? 'Contact inconnu' }}</span>
                                                <span class="text-[10px] uppercase font-semibold text-rose-600">{{ $log->status }}</span>
                                            </div>
                                            <div class="text-[11px] font-mono text-rose-700 break-words mt-1">
                                                 {{ !empty($log->error_message) ? $log->error_message : 'Échec de connexion SMTP / Rejet du serveur ou domaine destinataire inatteignable' }}
                                             </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <form action="{{ route('campaigns.retry-failed', $campaign) }}" method="POST"
                                  onsubmit="return confirm('Relancer l\'envoi pour les {{ $failedCount }} email(s) en échec ?')">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center gap-2 w-full bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-bold py-2.5 px-4 rounded-xl shadow-sm transition duration-150">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    <span>Relancer les {{ $failedCount }} échec(s)</span>
                                </button>
                            </form>
                        </div>
                    @endif

                    @if($campaign->statut === 'annulee')
                        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl space-y-2 text-center">
                            <svg class="w-8 h-8 text-rose-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                            <div class="text-xs font-bold uppercase tracking-wider text-rose-900 mt-1">Campagne Annulée</div>
                            <div class="text-[11px] leading-relaxed font-medium text-rose-700">
                                L'envoi de cette campagne a été annulé par l'utilisateur.
                            </div>
                        </div>
                    @elseif($campaign->statut === 'programmee')
                        {{-- Campagne déjà programmée --}}
                        <div class="bg-purple-50 border border-purple-200 text-purple-950 p-4 rounded-xl space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-xs font-bold text-purple-800 uppercase tracking-wider">
                                    <svg class="w-4 h-4 text-purple-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Envoi programmé</span>
                                </div>
                                @if($campaign->date_envoi && $campaign->date_envoi <= now())
                                    <span class="px-2 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-800 rounded-full animate-pulse">
                                        Prête à partir
                                    </span>
                                @endif
                            </div>
                            <div class="text-sm font-bold text-purple-900">
                                📅 {{ $campaign->date_envoi?->format('d/m/Y à H:i') ?? 'Date non définie' }}
                            </div>
                            <p class="text-xs text-purple-700 leading-relaxed">
                                @if($campaign->date_envoi && $campaign->date_envoi <= now())
                                    L'heure programmée est atteinte. La campagne partira automatiquement au prochain passage du cron, ou vous pouvez la lancer immédiatement ci-dessous.
                                @else
                                    La campagne sera automatiquement envoyée à cette date. Le cron vérifie chaque minute.
                                @endif
                            </p>

                            {{-- Bouton pour forcer l'envoi immédiat maintenant --}}
                            <form action="{{ route('campaigns.send', $campaign) }}" method="POST"
                                  onsubmit="return confirm('Lancer l\'envoi immédiat de cette campagne maintenant aux {{ $nbDestinataires }} contact(s) ?')">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center gap-2 w-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold py-2.5 px-4 rounded-xl shadow-md shadow-emerald-100 transition duration-150">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                    <span>🚀 Lancer l'envoi maintenant</span>
                                </button>
                            </form>

                            {{-- Modifier la date --}}
                            <form action="{{ route('campaigns.schedule-send', $campaign) }}" method="POST" class="space-y-2 border-t border-purple-200/80 pt-3">
                                @csrf
                                <label class="block text-xs font-bold text-purple-800">Modifier la date d'envoi :</label>
                                <input type="datetime-local" name="date_envoi"
                                       value="{{ $campaign->date_envoi?->format('Y-m-d\TH:i') }}"
                                       min="{{ now()->addMinutes(1)->format('Y-m-d\TH:i') }}"
                                       class="w-full text-sm py-2 px-3 rounded-lg border border-purple-200 bg-white focus:ring-2 focus:ring-purple-400 focus:outline-none" required>
                                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold py-2 px-4 rounded-lg transition">
                                    Modifier la programmation
                                </button>
                            </form>

                            {{-- Annuler la programmation --}}
                            <form action="{{ route('campaigns.unschedule', $campaign) }}" method="POST"
                                  onsubmit="return confirm('Annuler la programmation ? La campagne repassera en brouillon.')">
                                @csrf
                                <button type="submit" class="w-full border border-purple-300 text-purple-700 hover:bg-purple-100 text-xs font-semibold py-2 px-4 rounded-lg transition">
                                    Annuler la programmation (retour brouillon)
                                </button>
                            </form>
                        </div>
                    @elseif($campaign->statut === 'brouillon')
                        <div class="bg-emerald-50 border border-emerald-100 text-emerald-950 p-4 rounded-xl space-y-3.5">
                            <div class="text-xs leading-relaxed font-semibold text-emerald-800">
                                <span class="font-extrabold text-emerald-950">Attention :</span> cette action enverra immédiatement des emails personnalisés aux <span class="underline">{{ $nbDestinataires }}</span> contacts ciblés. Cette action est définitive.
                            </div>
                            <form action="{{ route('campaigns.send', $campaign) }}" method="POST"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir lancer l\'envoi immédiat de cette campagne à {{ $nbDestinataires }} contact(s) ?')">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center gap-2 w-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-emerald-100 transition duration-150 transform hover:-translate-y-0.5">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                    <span>Lancer la campagne</span>
                                </button>
                            </form>
                        </div>

                        {{-- Panneau de programmation --}}
                        <div class="bg-white border border-purple-200 rounded-2xl p-5 space-y-3 mt-2" x-data="{ open: false }">
                            <button type="button" @click="open = !open"
                                    class="flex items-center justify-between w-full text-left">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-sm font-bold text-slate-800">Programmer l'envoi</span>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="open" x-transition class="space-y-3 border-t border-purple-100 pt-3">
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Choisissez une date et heure d'envoi. La campagne sera déclenchée automatiquement par le cron chaque minute.
                                </p>
                                <form action="{{ route('campaigns.schedule-send', $campaign) }}" method="POST" class="space-y-3">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Date et heure d'envoi <span class="text-rose-500">*</span></label>
                                        <input type="datetime-local" name="date_envoi"
                                               min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}"
                                               class="w-full text-sm py-2.5 px-3 rounded-xl border border-purple-200 bg-purple-50/40 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 focus:outline-none"
                                               required>
                                        @error('date_envoi')
                                            <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <button type="submit"
                                            class="inline-flex items-center justify-center gap-2 w-full bg-purple-600 hover:bg-purple-700 active:bg-purple-800 text-white font-bold py-2.5 px-4 rounded-xl shadow-sm transition duration-150">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>Confirmer la programmation</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="bg-slate-50 border border-slate-200 text-slate-500 p-4 rounded-xl space-y-3 text-center">
                            <svg class="w-8 h-8 text-slate-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-700 mt-1">Campagne déjà lancée</div>
                            <div class="text-[11px] leading-relaxed font-medium">
                                Le statut actuel est <strong class="text-indigo-600">@if($campaign->statut === 'envoyee') Envoyée @else En cours @endif</strong>.
                            </div>

                            @if($campaign->statut === 'en_cours')
                                <form action="{{ route('campaigns.cancel', $campaign) }}" method="POST"
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette campagne en cours ? L\'envoi des emails restants sera arrêté.')">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center justify-center gap-2 w-full bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-bold py-2 px-3 rounded-lg text-xs transition">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                        <span>Annuler l'envoi de la campagne</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
