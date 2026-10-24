<x-app-layout>
    <div class="p-4 sm:p-6 max-w-[1550px] mx-auto space-y-6">
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

        <form action="{{ route('campaigns.store') }}" method="POST" id="campaign-form">
            @csrf

            <!-- Section 1 : Configuration & Ciblage de la campagne -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5 mb-6">
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
                                {{ $senderSmtp?->sender_name ?? auth()->user()->name }}
                                &lt;{{ $senderSmtp?->sender_email ?? 'contact@caei-afri.com' }}&gt;
                            </span>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1 font-semibold text-[11px] text-emerald-700 bg-emerald-100/70 px-2.5 py-1 rounded-full shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Quota sécurisé 180 emails/h
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Nom de la campagne -->
                    <div>
                        <label for="nom" class="block text-sm font-bold text-slate-800 mb-1.5">
                            Nom de la campagne <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nom" id="nom" value="{{ old('nom', $template?->nom) }}" required placeholder="Ex: Séminaire International IA & Fraude" class="w-full text-sm py-2.5 px-3.5 rounded-xl bg-slate-50/50 border @error('nom') border-rose-300 focus:ring-rose-500 focus:border-rose-500 @else border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @enderror transition-all">
                        @error('nom')
                            <p class="text-xs text-rose-600 mt-1.5 font-semibold flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Configuration des relances automatiques -->
                    <div x-data="{ autoRetry: {{ old('auto_retry', '1') ? 'true' : 'false' }} }">
                        <label class="block text-sm font-bold text-slate-800 mb-1.5">
                            Relances automatiques d'envoi
                        </label>
                        <div class="p-2.5 rounded-xl border border-slate-200 bg-slate-50/60 flex items-center justify-between gap-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="auto_retry" value="1" x-model="autoRetry" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs font-semibold text-slate-700">Relance auto en cas d'échec</span>
                            </label>
                            <div x-show="autoRetry" class="flex items-center gap-1.5">
                                <select name="max_auto_retries" id="max_auto_retries" class="text-xs py-1 px-2.5 rounded-lg border-slate-200 bg-white font-bold text-slate-800">
                                    <option value="1" @selected(old('max_auto_retries', 3) == 1)>1 max</option>
                                    <option value="2" @selected(old('max_auto_retries', 3) == 2)>2 max</option>
                                    <option value="3" @selected(old('max_auto_retries', 3) == 3)>3 max (Recommandé)</option>
                                    <option value="5" @selected(old('max_auto_retries', 3) == 5)>5 max</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cible -->
                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-2">
                        Ciblage de la campagne
                    </label>
                    <x-campaign-audience-selector
                        :categories="$categories"
                        :import-logs="$importLogs"
                        :selected-category-ids="old('category_ids', [])"
                        :selected-import-id="old('import_log_id', old('import_batch_id'))"
                        :all-selected="(bool) old('all_contacts')"
                        :total-contacts="$totalContacts"
                        :recipient-count-url="route('campaigns.recipient-count')"
                    />
                </div>
            </div>

            <!-- Section 2 : GRAND ÉDITEUR DE CAMPAGNE (Conforme à la maquette) -->
            @php
                $defaultContent = old('contenu', $template?->contenu ?? '');
            @endphp
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/90 shadow-sm space-y-6"
                 x-data="emailBuilder({ initialContent: {{ json_encode($defaultContent) }} })">

                <!-- Header de l'Éditeur -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight flex items-center gap-1">
                            <span>Éditeur de campagne</span>
                            <span class="text-rose-500">*</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-1">
                            Composez votre e-mail bloc par bloc, puis vérifiez l'aperçu avant l'envoi.
                        </p>
                    </div>

                    <!-- Boutons Constructeur / Modifier HTML -->
                    <div class="flex items-center gap-2">
                        <button type="button"
                                @click="mode = 'builder'"
                                :class="mode === 'builder' ? 'bg-[#4338CA] text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                            <span>Constructeur</span>
                        </button>
                        <button type="button"
                                @click="mode = 'html'; htmlValue = buildHtmlFromBlocks()"
                                :class="mode === 'html' ? 'bg-[#4338CA] text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                            <span>Modifier HTML</span>
                        </button>
                    </div>
                </div>

                <!-- Ligne de saisie : Objet de l'e-mail & Texte d'aperçu (préheader) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Objet de l'e-mail -->
                    <div>
                        <label for="objet" class="block text-xs font-bold text-slate-800 mb-1.5">
                            Objet de l'e-mail <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               name="objet"
                               id="objet"
                               value="{{ old('objet', $template?->sujet) }}"
                               required
                               placeholder="Invitation : séminaire CAEI du 12 décembre"
                               class="w-full text-xs py-2.5 px-3.5 rounded-xl bg-white border @error('objet') border-rose-300 focus:ring-rose-500 @else border-slate-200 focus:ring-2 focus:ring-indigo-500 @enderror transition-all">
                        @error('objet')
                            <p class="text-[11px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Texte d'aperçu (préheader) -->
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

                <!-- Input caché du contenu JSON/HTML transmis lors du submit -->
                <input type="hidden" name="contenu" :value="contentValue">

                <!-- ── MODE CONSTRUCTEUR : 3 COLONNES CONFORMES À LA MAQUETTE ── -->
                <div x-show="mode === 'builder'"
                     style="display: flex; gap: 20px; align-items: flex-start; width: 100%; box-sizing: border-box;"
                     class="flex-col xl:flex-row">

                    <!-- COLONNE 1 : AJOUTER UN BLOC (Palette gauche ~210px) -->
                    <div style="width: 210px; min-width: 210px; max-width: 210px; flex-shrink: 0; box-sizing: border-box;"
                         class="bg-slate-50/70 p-3.5 rounded-2xl border border-slate-200/80 space-y-3">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 px-1">
                            AJOUTER UN BLOC
                        </div>

                        <div class="grid grid-cols-1 gap-2">
                            <!-- Bloc Texte -->
                            <button type="button" @click="addBlock('text')"
                                    class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-200/80 bg-white hover:border-indigo-400 hover:bg-indigo-50/50 hover:shadow-sm transition text-left group">
                                <span class="w-7 h-7 rounded-lg bg-indigo-50 text-[#4338CA] flex items-center justify-center font-bold text-xs shrink-0 group-hover:scale-110 transition">T</span>
                                <span class="text-xs font-bold text-slate-800 group-hover:text-[#4338CA] transition">Texte</span>
                            </button>

                            <!-- Bloc Image -->
                            <button type="button" @click="addBlock('image')"
                                    class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-200/80 bg-white hover:border-indigo-400 hover:bg-indigo-50/50 hover:shadow-sm transition text-left group">
                                <span class="w-7 h-7 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center text-xs shrink-0 group-hover:scale-110 transition">🖼️</span>
                                <span class="text-xs font-bold text-slate-800 group-hover:text-sky-600 transition">Image</span>
                            </button>

                            <!-- Bloc Logo CAEI -->
                            <button type="button" @click="addBlock('logo')"
                                    class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-200/80 bg-white hover:border-indigo-400 hover:bg-indigo-50/50 hover:shadow-sm transition text-left group">
                                <span class="w-7 h-7 rounded-lg bg-indigo-50 text-[#4338CA] flex items-center justify-center text-xs shrink-0 group-hover:scale-110 transition">🏷️</span>
                                <span class="text-xs font-bold text-slate-800 group-hover:text-[#4338CA] transition">Logo CAEI</span>
                            </button>

                            <!-- Bloc Bouton -->
                            <button type="button" @click="addBlock('button')"
                                    class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-200/80 bg-white hover:border-indigo-400 hover:bg-indigo-50/50 hover:shadow-sm transition text-left group">
                                <span class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-xs shrink-0 group-hover:scale-110 transition">🔘</span>
                                <span class="text-xs font-bold text-slate-800 group-hover:text-purple-600 transition">Bouton</span>
                            </button>

                            <!-- Bloc Lien -->
                            <button type="button" @click="addBlock('link')"
                                    class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-200/80 bg-white hover:border-indigo-400 hover:bg-indigo-50/50 hover:shadow-sm transition text-left group">
                                <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs shrink-0 group-hover:scale-110 transition">🔗</span>
                                <span class="text-xs font-bold text-slate-800 group-hover:text-blue-600 transition">Lien</span>
                            </button>

                            <!-- Bloc Signature -->
                            <button type="button" @click="addBlock('signature')"
                                    class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-200/80 bg-white hover:border-indigo-400 hover:bg-indigo-50/50 hover:shadow-sm transition text-left group">
                                <span class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-xs shrink-0 group-hover:scale-110 transition">✍️</span>
                                <span class="text-xs font-bold text-slate-800 group-hover:text-rose-600 transition">Signature</span>
                            </button>

                            <!-- Bloc Pièce jointe -->
                            <button type="button" @click="addBlock('attachment')"
                                    class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-200/80 bg-white hover:border-indigo-400 hover:bg-indigo-50/50 hover:shadow-sm transition text-left group">
                                <span class="w-7 h-7 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center text-xs shrink-0 group-hover:scale-110 transition">📎</span>
                                <span class="text-xs font-bold text-slate-800 group-hover:text-teal-600 transition">Pièce jointe</span>
                            </button>
                        </div>

                        <p class="text-[11px] text-slate-400 leading-tight px-1 pt-2">
                            Glissez un bloc dans l'aperçu ou cliquez pour l'ajouter à la fin.
                        </p>
                    </div>

                    <!-- COLONNE 2 : CANEVAS / APERÇU DE L'E-MAIL (Centre interactif) -->
                    <div style="flex: 1 1 0%; min-width: 0; box-sizing: border-box;"
                         class="bg-slate-100/80 rounded-2xl border border-slate-200/80 p-5 space-y-4">

                        <!-- Header Canevas : Aperçu de l'e-mail + commutateur Ordinateur / Mobile -->
                        <div class="flex items-center justify-between pb-2">
                            <span class="text-sm font-bold text-slate-800">Aperçu de l'e-mail</span>
                            <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-slate-200/80 shadow-2xs">
                                <button type="button"
                                        @click="previewDevice = 'desktop'"
                                        :class="previewDevice === 'desktop' ? 'bg-[#EEF2FF] text-[#4338CA] font-bold' : 'text-slate-600 hover:text-slate-900'"
                                        class="px-3 py-1 rounded-lg text-xs transition">
                                    Ordinateur
                                </button>
                                <button type="button"
                                        @click="previewDevice = 'mobile'"
                                        :class="previewDevice === 'mobile' ? 'bg-[#EEF2FF] text-[#4338CA] font-bold' : 'text-slate-600 hover:text-slate-900'"
                                        class="px-3 py-1 rounded-lg text-xs transition">
                                    Mobile
                                </button>
                            </div>
                        </div>

                        <!-- Feuille de l'e-mail rendu (Sheet) -->
                        <div class="flex justify-center">
                            <div :class="previewDevice === 'mobile' ? 'w-[340px] rounded-3xl border-4 border-slate-800 shadow-xl' : 'w-full max-w-[620px] rounded-2xl shadow-sm border border-slate-200/70'"
                                 class="bg-white p-6 space-y-4 transition-all duration-200 min-h-[460px]">

                                <!-- Entête fixe logo si présent ou placeholder -->
                                <div class="flex items-center gap-3 pb-2 border-b border-slate-50">
                                    <div class="w-10 h-10 rounded-xl bg-[#2563EB] text-white flex items-center justify-center font-black text-xs shadow-xs">
                                        CAEI
                                    </div>
                                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">[LOGO CAEI]</span>
                                </div>

                                <!-- Liste des blocs éditables en place -->
                                <div class="space-y-3">
                                    <template x-for="(block, index) in blocks" :key="block.id">
                                        <div>
                                            <!-- Bloc TEXTE -->
                                            <template x-if="block.type === 'text'">
                                                <div class="relative transition-all"
                                                     :class="activeBlockId === block.id ? 'rounded-2xl border-2 border-[#6366F1] bg-white p-3.5 shadow-sm' : 'rounded-xl border border-transparent hover:border-slate-200 p-2 cursor-pointer'"
                                                     @click="activeBlockId = block.id">

                                                    <!-- Badge flottant du bloc actif -->
                                                    <div x-show="activeBlockId === block.id"
                                                         class="absolute -top-3.5 left-4 z-10 flex items-center gap-1 bg-[#4338CA] text-white px-2.5 py-0.5 rounded-lg text-[10px] font-bold shadow-xs">
                                                        <span>Bloc texte</span>
                                                        <button type="button" @click.stop="moveBlockUp(index)" :disabled="index === 0" class="hover:text-indigo-200 px-0.5 disabled:opacity-30">▲</button>
                                                        <button type="button" @click.stop="moveBlockDown(index)" :disabled="index === blocks.length - 1" class="hover:text-indigo-200 px-0.5 disabled:opacity-30">▼</button>
                                                        <button type="button" @click.stop="duplicateBlock(index)" class="hover:text-indigo-200 px-0.5" title="Dupliquer">⎘</button>
                                                    </div>

                                                    <!-- Barre d'outils de formatage dans le bloc actif -->
                                                    <div x-show="activeBlockId === block.id" class="flex items-center gap-2 pb-2 mb-2 border-b border-slate-100 text-xs">
                                                        <button type="button" @click.stop="applyFormatToBlock(block, 'bold')" class="w-6 h-6 rounded bg-slate-50 hover:bg-slate-100 font-bold text-slate-700 flex items-center justify-center">B</button>
                                                        <button type="button" @click.stop="applyFormatToBlock(block, 'italic')" class="w-6 h-6 rounded bg-slate-50 hover:bg-slate-100 italic text-slate-700 font-serif flex items-center justify-center">I</button>
                                                        <button type="button" @click.stop="applyFormatToBlock(block, 'underline')" class="w-6 h-6 rounded bg-slate-50 hover:bg-slate-100 underline text-slate-700 flex items-center justify-center">U</button>

                                                        <!-- Menu déroulant Insérer une variable -->
                                                        <div class="relative" x-data="{ openVars: false }">
                                                            <button type="button" @click.stop="openVars = !openVars"
                                                                    class="px-2.5 py-1 rounded-md bg-[#EEF2FF] text-[#4338CA] font-bold text-[11px] flex items-center gap-1 hover:bg-indigo-100 transition">
                                                                <span>Insérer une variable</span>
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                            </button>
                                                            <div x-show="openVars" @click.away="openVars = false"
                                                                 class="absolute left-0 top-full mt-1 w-44 bg-white rounded-xl shadow-lg border border-slate-200 p-1.5 z-30 space-y-1">
                                                                <button type="button" @click.stop="insertVariableInBlock(block, '@{{prenom}}'); openVars = false" class="w-full text-left px-2 py-1 rounded text-xs hover:bg-indigo-50 text-indigo-900 font-mono">@{{prenom}}</button>
                                                                <button type="button" @click.stop="insertVariableInBlock(block, '@{{nom}}'); openVars = false" class="w-full text-left px-2 py-1 rounded text-xs hover:bg-indigo-50 text-indigo-900 font-mono">@{{nom}}</button>
                                                                <button type="button" @click.stop="insertVariableInBlock(block, '@{{entreprise}}'); openVars = false" class="w-full text-left px-2 py-1 rounded text-xs hover:bg-indigo-50 text-indigo-900 font-mono">@{{entreprise}}</button>
                                                                <button type="button" @click.stop="insertVariableInBlock(block, '@{{lien}}'); openVars = false" class="w-full text-left px-2 py-1 rounded text-xs hover:bg-indigo-50 text-indigo-900 font-mono">@{{lien}}</button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Zone d'édition du texte -->
                                                    <textarea x-model="block.content"
                                                              @input="updateHtml()"
                                                              :style="'text-align:' + (block.align || 'left') + '; font-size:' + (block.fontSize || '15px') + '; color:' + (block.color || '#1E1B4B') + ';'"
                                                              rows="4"
                                                              class="w-full border-0 focus:ring-0 p-1 leading-relaxed resize-none outline-none bg-transparent font-sans"
                                                              placeholder="Bonjour @{{prenom}} @{{nom}}, ..."></textarea>
                                                </div>
                                            </template>

                                            <!-- Bloc IMAGE / BANNIÈRE -->
                                            <template x-if="block.type === 'image'">
                                                <div class="relative transition-all"
                                                     :class="activeBlockId === block.id ? 'rounded-2xl border-2 border-[#6366F1] p-3' : 'rounded-xl border border-transparent hover:border-slate-200 p-1 cursor-pointer'"
                                                     @click="activeBlockId = block.id">
                                                    <!-- Badge flottant -->
                                                    <div x-show="activeBlockId === block.id"
                                                         class="absolute -top-3.5 left-4 z-10 flex items-center gap-1 bg-[#4338CA] text-white px-2.5 py-0.5 rounded-lg text-[10px] font-bold shadow-xs">
                                                        <span>Bloc image</span>
                                                        <button type="button" @click.stop="moveBlockUp(index)" :disabled="index === 0" class="hover:text-indigo-200 px-0.5 disabled:opacity-30">▲</button>
                                                        <button type="button" @click.stop="moveBlockDown(index)" :disabled="index === blocks.length - 1" class="hover:text-indigo-200 px-0.5 disabled:opacity-30">▼</button>
                                                        <button type="button" @click.stop="duplicateBlock(index)" class="hover:text-indigo-200 px-0.5">⎘</button>
                                                    </div>

                                                    <template x-if="block.src && block.src !== '/logo-caei.svg'">
                                                        <img :src="block.src" :alt="block.alt || 'Bannière'" class="w-full rounded-xl object-cover max-h-48">
                                                    </template>
                                                    <template x-if="!block.src || block.src === '/logo-caei.svg'">
                                                        <div class="w-full h-28 rounded-xl bg-slate-100 border border-dashed border-slate-300 flex items-center justify-center text-xs font-semibold text-slate-500 tracking-wider">
                                                            [BANNIÈRE · 600 × 200]
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>

                                            <!-- Bloc LOGO CAEI -->
                                            <template x-if="block.type === 'logo'">
                                                <div class="relative transition-all"
                                                     :class="activeBlockId === block.id ? 'rounded-2xl border-2 border-[#6366F1] p-3' : 'rounded-xl border border-transparent hover:border-slate-200 p-1 cursor-pointer'"
                                                     @click="activeBlockId = block.id">
                                                    <div x-show="activeBlockId === block.id"
                                                         class="absolute -top-3.5 left-4 z-10 flex items-center gap-1 bg-[#4338CA] text-white px-2.5 py-0.5 rounded-lg text-[10px] font-bold shadow-xs">
                                                        <span>Logo CAEI</span>
                                                    </div>
                                                    <div class="text-center py-2">
                                                        <img :src="block.src || '/images/logo-caei.jpg'" :alt="block.alt || 'Logo CAEI'" :width="block.width || 115" class="inline-block rounded-full shadow-xs">
                                                    </div>
                                                </div>
                                            </template>

                                            <!-- Bloc BOUTON -->
                                            <template x-if="block.type === 'button'">
                                                <div class="relative transition-all py-2"
                                                     :class="activeBlockId === block.id ? 'rounded-2xl border-2 border-[#6366F1] p-3' : 'rounded-xl border border-transparent hover:border-slate-200 p-1 cursor-pointer'"
                                                     @click="activeBlockId = block.id">
                                                    <div x-show="activeBlockId === block.id"
                                                         class="absolute -top-3.5 left-4 z-10 flex items-center gap-1 bg-[#4338CA] text-white px-2.5 py-0.5 rounded-lg text-[10px] font-bold shadow-xs">
                                                        <span>Bloc bouton</span>
                                                        <button type="button" @click.stop="moveBlockUp(index)" :disabled="index === 0" class="hover:text-indigo-200 px-0.5 disabled:opacity-30">▲</button>
                                                        <button type="button" @click.stop="moveBlockDown(index)" :disabled="index === blocks.length - 1" class="hover:text-indigo-200 px-0.5 disabled:opacity-30">▼</button>
                                                        <button type="button" @click.stop="duplicateBlock(index)" class="hover:text-indigo-200 px-0.5">⎘</button>
                                                    </div>
                                                    <div class="text-center">
                                                        <span :style="'background:' + (block.color || '#4338CA') + ';'"
                                                              class="inline-block px-7 py-3 rounded-xl text-white font-bold text-xs shadow-md tracking-wide"
                                                              x-text="block.label || 'Confirmer ma présence'"></span>
                                                    </div>
                                                </div>
                                            </template>

                                            <!-- Bloc LIEN -->
                                            <template x-if="block.type === 'link'">
                                                <div class="relative transition-all py-1"
                                                     :class="activeBlockId === block.id ? 'rounded-2xl border-2 border-[#6366F1] p-3' : 'rounded-xl border border-transparent hover:border-slate-200 p-1 cursor-pointer'"
                                                     @click="activeBlockId = block.id">
                                                    <div x-show="activeBlockId === block.id"
                                                         class="absolute -top-3.5 left-4 z-10 flex items-center gap-1 bg-[#4338CA] text-white px-2.5 py-0.5 rounded-lg text-[10px] font-bold shadow-xs">
                                                        <span>Bloc lien</span>
                                                    </div>
                                                    <a href="javascript:void(0)" class="text-xs font-semibold text-[#4338CA] underline" x-text="block.label || 'En savoir plus'"></a>
                                                </div>
                                            </template>

                                            <!-- Bloc SIGNATURE -->
                                            <template x-if="block.type === 'signature'">
                                                <div class="relative transition-all pt-2"
                                                     :class="activeBlockId === block.id ? 'rounded-2xl border-2 border-[#6366F1] p-3' : 'rounded-xl border border-transparent hover:border-slate-200 p-1 cursor-pointer'"
                                                     @click="activeBlockId = block.id">
                                                    <div x-show="activeBlockId === block.id"
                                                         class="absolute -top-3.5 left-4 z-10 flex items-center gap-1 bg-[#4338CA] text-white px-2.5 py-0.5 rounded-lg text-[10px] font-bold shadow-xs">
                                                        <span>Bloc signature</span>
                                                        <button type="button" @click.stop="moveBlockUp(index)" :disabled="index === 0" class="hover:text-indigo-200 px-0.5 disabled:opacity-30">▲</button>
                                                        <button type="button" @click.stop="moveBlockDown(index)" :disabled="index === blocks.length - 1" class="hover:text-indigo-200 px-0.5 disabled:opacity-30">▼</button>
                                                        <button type="button" @click.stop="duplicateBlock(index)" class="hover:text-indigo-200 px-0.5">⎘</button>
                                                    </div>
                                                    <div class="text-slate-800 text-xs leading-relaxed">
                                                        <div class="font-bold">[Prénom Nom]</div>
                                                        <div class="text-slate-500">[Fonction] · CAEI</div>
                                                    </div>
                                                </div>
                                            </template>

                                            <!-- Bloc PIÈCE JOINTE -->
                                            <template x-if="block.type === 'attachment'">
                                                <div class="relative transition-all"
                                                     :class="activeBlockId === block.id ? 'rounded-2xl border-2 border-[#6366F1] p-3' : 'rounded-xl border border-transparent hover:border-slate-200 p-1 cursor-pointer'"
                                                     @click="activeBlockId = block.id">
                                                    <div x-show="activeBlockId === block.id"
                                                         class="absolute -top-3.5 left-4 z-10 flex items-center gap-1 bg-[#4338CA] text-white px-2.5 py-0.5 rounded-lg text-[10px] font-bold shadow-xs">
                                                        <span>Pièce jointe</span>
                                                    </div>
                                                    <div class="p-3 rounded-xl border border-slate-200/90 bg-white flex items-center gap-2 text-xs text-slate-700 shadow-2xs">
                                                        <span class="text-slate-400">📎</span>
                                                        <span class="font-semibold" x-text="block.label || '[programme.pdf] · [taille]'"></span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>

                                <!-- Déposer un bloc ici (Bouton zone de fin) -->
                                <button type="button"
                                        @click="addBlock('text')"
                                        class="w-full py-3 rounded-xl border-2 border-dashed border-slate-200 hover:border-indigo-400 hover:bg-indigo-50/40 text-slate-400 hover:text-[#4338CA] text-xs font-semibold text-center transition flex items-center justify-center gap-1">
                                    <span>+ Déposer un bloc ici</span>
                                </button>

                                <!-- Pied de page de l'email conforme à la maquette -->
                                <div class="text-center text-[10px] text-slate-400 pt-4 border-t border-slate-100 leading-relaxed">
                                    Vous recevez cet e-mail car vous figurez dans notre liste de contacts. <a href="javascript:void(0)" class="underline text-slate-600">Se désabonner</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- COLONNE 3 : PROPRIÉTÉS DU BLOC ACTIF (Inspecteur droite ~260px) -->
                    <div style="width: 260px; min-width: 260px; max-width: 260px; flex-shrink: 0; box-sizing: border-box;"
                         class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">

                        <template x-if="getActiveBlock()">
                            <div class="space-y-4">
                                <!-- En-tête de l'inspecteur : Titre + Supprimer -->
                                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                    <span class="text-sm font-bold text-slate-800" x-text="blockLabel(getActiveBlock().type)"></span>
                                    <button type="button"
                                            @click="removeActiveBlock()"
                                            class="text-xs font-bold text-rose-600 hover:text-rose-700 transition">
                                        Supprimer
                                    </button>
                                </div>

                                <!-- Propriétés pour bloc TEXTE -->
                                <template x-if="getActiveBlock().type === 'text'">
                                    <div class="space-y-4">
                                        <!-- Alignement -->
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Alignement</label>
                                            <div class="grid grid-cols-4 gap-1 p-1 bg-slate-100 rounded-xl">
                                                <button type="button" @click="getActiveBlock().align = 'left'; updateHtml()"
                                                        :class="(getActiveBlock().align || 'left') === 'left' ? 'bg-[#EEF2FF] text-[#4338CA] font-bold shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
                                                        class="p-1.5 rounded-lg text-xs flex justify-center transition">
                                                    ⫷
                                                </button>
                                                <button type="button" @click="getActiveBlock().align = 'center'; updateHtml()"
                                                        :class="getActiveBlock().align === 'center' ? 'bg-[#EEF2FF] text-[#4338CA] font-bold shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
                                                        class="p-1.5 rounded-lg text-xs flex justify-center transition">
                                                    ≡
                                                </button>
                                                <button type="button" @click="getActiveBlock().align = 'right'; updateHtml()"
                                                        :class="getActiveBlock().align === 'right' ? 'bg-[#EEF2FF] text-[#4338CA] font-bold shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
                                                        class="p-1.5 rounded-lg text-xs flex justify-center transition">
                                                    ⫸
                                                </button>
                                                <button type="button" @click="getActiveBlock().align = 'justify'; updateHtml()"
                                                        :class="getActiveBlock().align === 'justify' ? 'bg-[#EEF2FF] text-[#4338CA] font-bold shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
                                                        class="p-1.5 rounded-lg text-xs flex justify-center transition">
                                                    ☰
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Taille du texte -->
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Taille du texte</label>
                                            <select x-model="getActiveBlock().fontSize"
                                                    @change="updateHtml()"
                                                    class="w-full text-xs rounded-xl border-slate-200 py-2 px-3 text-slate-800 font-medium">
                                                <option value="13px">Petit (13 px)</option>
                                                <option value="15px">Normal (15 px)</option>
                                                <option value="18px">Grand (18 px)</option>
                                                <option value="22px">Titre (22 px)</option>
                                            </select>
                                        </div>

                                        <!-- Couleur du texte -->
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Couleur du texte</label>
                                            <div class="flex items-center gap-2">
                                                <button type="button" @click="getActiveBlock().color = '#1E1B4B'; updateHtml()" class="w-6 h-6 rounded-full bg-[#1E1B4B] ring-2 ring-offset-1" :class="getActiveBlock().color === '#1E1B4B' ? 'ring-[#4338CA]' : 'ring-transparent'"></button>
                                                <button type="button" @click="getActiveBlock().color = '#475569'; updateHtml()" class="w-6 h-6 rounded-full bg-[#475569] ring-2 ring-offset-1" :class="getActiveBlock().color === '#475569' ? 'ring-[#4338CA]' : 'ring-transparent'"></button>
                                                <button type="button" @click="getActiveBlock().color = '#4338CA'; updateHtml()" class="w-6 h-6 rounded-full bg-[#4338CA] ring-2 ring-offset-1" :class="getActiveBlock().color === '#4338CA' ? 'ring-[#4338CA]' : 'ring-transparent'"></button>
                                                <button type="button" @click="getActiveBlock().color = '#DC2626'; updateHtml()" class="w-6 h-6 rounded-full bg-[#DC2626] ring-2 ring-offset-1" :class="getActiveBlock().color === '#DC2626' ? 'ring-[#4338CA]' : 'ring-transparent'"></button>
                                                <input x-model="getActiveBlock().color" @input="updateHtml()" type="color" class="w-6 h-6 rounded cursor-pointer border-0 p-0">
                                            </div>
                                        </div>

                                        <!-- Variables disponibles -->
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Variables disponibles</label>
                                            <div class="flex flex-wrap gap-1.5">
                                                <button type="button" @click="insertVariableInActive('@{{prenom}}')" class="px-2.5 py-1 rounded-lg bg-[#EEF2FF] hover:bg-indigo-100 text-[#4338CA] font-mono text-[11px] font-bold transition">@{{prenom}}</button>
                                                <button type="button" @click="insertVariableInActive('@{{nom}}')" class="px-2.5 py-1 rounded-lg bg-[#EEF2FF] hover:bg-indigo-100 text-[#4338CA] font-mono text-[11px] font-bold transition">@{{nom}}</button>
                                                <button type="button" @click="insertVariableInActive('@{{entreprise}}')" class="px-2.5 py-1 rounded-lg bg-[#EEF2FF] hover:bg-indigo-100 text-[#4338CA] font-mono text-[11px] font-bold transition">@{{entreprise}}</button>
                                                <button type="button" @click="insertVariableInActive('@{{lien}}')" class="px-2.5 py-1 rounded-lg bg-[#EEF2FF] hover:bg-indigo-100 text-[#4338CA] font-mono text-[11px] font-bold transition">@{{lien}}</button>
                                            </div>
                                        </div>

                                        <!-- Bouton Dupliquer -->
                                        <button type="button" @click="duplicateActiveBlock()" class="w-full py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                                            Dupliquer le bloc
                                        </button>
                                    </div>
                                </template>

                                <!-- Propriétés pour bloc BOUTON -->
                                <template x-if="getActiveBlock().type === 'button'">
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Libellé du bouton</label>
                                            <input x-model="getActiveBlock().label" @input="updateHtml()" type="text" class="w-full text-xs rounded-xl border-slate-200 p-2 font-semibold">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Lien de redirection</label>
                                            <input x-model="getActiveBlock().url" @input="updateHtml()" type="text" class="w-full text-xs rounded-xl border-slate-200 p-2 font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Couleur</label>
                                            <div class="flex items-center gap-2">
                                                <input x-model="getActiveBlock().color" @input="updateHtml()" type="color" class="w-8 h-8 rounded cursor-pointer border-0 p-0">
                                                <span class="text-xs font-mono text-slate-600" x-text="getActiveBlock().color"></span>
                                            </div>
                                        </div>
                                        <button type="button" @click="duplicateActiveBlock()" class="w-full py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                                            Dupliquer le bloc
                                        </button>
                                    </div>
                                </template>

                                <!-- Propriétés pour bloc IMAGE -->
                                <template x-if="getActiveBlock().type === 'image'">
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">URL de l'image</label>
                                            <input x-model="getActiveBlock().src" @input="updateHtml()" type="text" class="w-full text-xs rounded-xl border-slate-200 p-2" placeholder="/images/banniere.jpg">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Texte alternatif</label>
                                            <input x-model="getActiveBlock().alt" @input="updateHtml()" type="text" class="w-full text-xs rounded-xl border-slate-200 p-2" placeholder="Bannière">
                                        </div>
                                        <button type="button" @click="duplicateActiveBlock()" class="w-full py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                                            Dupliquer le bloc
                                        </button>
                                    </div>
                                </template>

                                <!-- Propriétés pour bloc SIGNATURE -->
                                <template x-if="getActiveBlock().type === 'signature'">
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Texte de signature</label>
                                            <textarea x-model="getActiveBlock().content" @input="updateHtml()" rows="3" class="w-full text-xs rounded-xl border-slate-200 p-2"></textarea>
                                        </div>
                                        <button type="button" @click="duplicateActiveBlock()" class="w-full py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                                            Dupliquer le bloc
                                        </button>
                                    </div>
                                </template>

                                <!-- Propriétés pour bloc PIÈCE JOINTE -->
                                <template x-if="getActiveBlock().type === 'attachment'">
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Nom du document</label>
                                            <input x-model="getActiveBlock().label" @input="updateHtml()" type="text" class="w-full text-xs rounded-xl border-slate-200 p-2">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Lien de téléchargement</label>
                                            <input x-model="getActiveBlock().url" @input="updateHtml()" type="text" class="w-full text-xs rounded-xl border-slate-200 p-2 font-mono">
                                        </div>
                                        <button type="button" @click="duplicateActiveBlock()" class="w-full py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                                            Dupliquer le bloc
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="!getActiveBlock()">
                            <div class="text-center py-10 text-xs text-slate-400 space-y-2">
                                <svg class="w-8 h-8 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
                                <p>Cliquez sur un bloc dans l'aperçu pour modifier ses options.</p>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- ── MODE HTML DIRECT ── -->
                <div x-show="mode === 'html'" class="space-y-3">
                    <div class="p-3.5 rounded-xl bg-indigo-50 border border-indigo-100 text-xs text-indigo-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#4338CA] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>En mode HTML, votre code est injecté directement dans l'email. Les variables <code>@{{prenom}}</code>, <code>@{{nom}}</code>, <code>@{{entreprise}}</code> et <code>@{{lien}}</code> sont supportées.</span>
                    </div>
                    <textarea x-model="htmlValue" rows="14"
                              class="w-full rounded-2xl border border-slate-300 bg-slate-900 text-emerald-400 p-4 font-mono text-xs leading-relaxed focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                              placeholder="<html>...</html>"></textarea>
                </div>

                <!-- Astuce conforme à la maquette -->
                <div class="rounded-xl border border-indigo-100 bg-[#EEF2FF] p-3 text-xs text-[#4338CA] font-medium">
                    Astuce : vous pouvez utiliser les variables <span class="font-bold">@{{prenom}}</span>, <span class="font-bold">@{{entreprise}}</span> et <span class="font-bold">@{{lien}}</span> dans vos blocs.
                </div>
            </div>

            <!-- Boutons de soumission du formulaire -->
            <div class="flex items-center justify-end gap-3 pt-6 mt-6 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                <a href="{{ route('campaigns.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-900 transition">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center gap-2 bg-[#4338CA] hover:bg-[#3730A3] active:bg-[#312E81] text-white font-bold text-sm px-7 py-3 rounded-xl shadow-md shadow-indigo-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Créer et programmer la campagne</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Script du Concepteur AlpineJS Conforme à la Maquette -->
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
                        // Défaut fidèle à la maquette
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
    </div>
</x-app-layout>
