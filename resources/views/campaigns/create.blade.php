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
                            <label class="block text-sm font-bold text-slate-800 mb-1.5">
                                Ciblage de la campagne
                            </label>

                            <label class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700">
                                <input type="checkbox" name="all_contacts" value="1" {{ old('all_contacts') ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span>Tous les contacts</span>
                            </label>

                            <div class="mt-3">
                                <label for="category_ids" class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">
                                    Ou choisir plusieurs listes ciblées
                                </label>
                                <select name="category_ids[]" id="category_ids" multiple class="w-full min-h-[120px] text-sm py-2.5 px-3.5 rounded-xl bg-slate-50/50 border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-medium text-slate-700">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ in_array((string) $cat->id, old('category_ids', []), true) || (old('category_id') == $cat->id) ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-2 text-xs text-slate-500">Maintenez la touche Ctrl/Cmd pour sélectionner plusieurs listes.</p>
                            </div>
                        </div>

                        <!-- Contenu du message -->
                        @php
                            $defaultContent = old('contenu', $template?->contenu ?? 'Bonjour {{prenom}} {{nom}},\n\nNous avons le plaisir de vous inviter...\n\nCordialement,\nL\'équipe CAEI');
                        @endphp
                        <div class="pt-3 border-t border-slate-100" x-data="emailBuilder({ initialContent: {{ json_encode($defaultContent) }} })">
                            <div class="flex items-center justify-between gap-3 mb-3">
                                <label class="block text-sm font-bold text-slate-800">
                                    Éditeur de campagne <span class="text-rose-500">*</span>
                                </label>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="mode = 'builder'" :class="mode === 'builder' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700'" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition">Constructeur</button>
                                    <button type="button" @click="mode = 'html'; htmlValue = buildHtmlFromBlocks()" :class="mode === 'html' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700'" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition">Modifier HTML</button>
                                </div>
                            </div>

                            <input type="hidden" name="contenu" :value="contentValue">

                            <div x-show="mode === 'builder'" class="grid grid-cols-1 xl:grid-cols-[220px_minmax(0,1fr)] gap-4">
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 space-y-2">
                                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Ajouter un bloc</p>
                                    <div class="grid grid-cols-1 gap-2">
                                        <button type="button" @click="addBlock('text')" class="text-left rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:border-indigo-300 hover:bg-indigo-50">✍️ Texte</button>
                                        <button type="button" @click="addBlock('image')" class="text-left rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:border-indigo-300 hover:bg-indigo-50">🖼️ Image</button>
                                        <button type="button" @click="addBlock('logo')" class="text-left rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:border-indigo-300 hover:bg-indigo-50">🏷️ Logo CAEI</button>
                                        <button type="button" @click="addBlock('button')" class="text-left rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:border-indigo-300 hover:bg-indigo-50">🔘 Bouton</button>
                                        <button type="button" @click="addBlock('link')" class="text-left rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:border-indigo-300 hover:bg-indigo-50">🔗 Lien</button>
                                        <button type="button" @click="addBlock('signature')" class="text-left rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:border-indigo-300 hover:bg-indigo-50">✉️ Signature</button>
                                        <button type="button" @click="addBlock('attachment')" class="text-left rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:border-indigo-300 hover:bg-indigo-50">📎 Pièce jointe</button>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-white p-3 space-y-3 min-h-[260px]">
                                    <template x-if="blocks.length === 0">
                                        <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50 p-6 text-center text-sm text-slate-500">
                                            Ajoutez un premier bloc pour commencer votre email.
                                        </div>
                                    </template>

                                    <template x-for="(block, index) in blocks" :key="block.id">
                                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 space-y-3">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-semibold text-slate-800" x-text="blockLabel(block.type)"></span>
                                                <button type="button" @click="removeBlock(index)" class="text-xs font-semibold text-rose-600 hover:text-rose-700">Supprimer</button>
                                            </div>

                                            <template x-if="block.type === 'text'">
                                                <div class="space-y-2">
                                                    <textarea x-model="block.content" rows="4" class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm" placeholder="Votre texte..."></textarea>
                                                    <select x-model="block.align" class="w-full rounded-xl border border-slate-200 bg-white p-2 text-sm">
                                                        <option value="left">Aligner à gauche</option>
                                                        <option value="center">Centrer</option>
                                                        <option value="right">Aligner à droite</option>
                                                    </select>
                                                </div>
                                            </template>

                                            <template x-if="block.type === 'image'">
                                                <div class="space-y-2">
                                                    <input x-model="block.src" type="text" class="w-full rounded-xl border border-slate-200 bg-white p-2 text-sm" placeholder="/storage/logo-caei.png">
                                                    <input x-model="block.alt" type="text" class="w-full rounded-xl border border-slate-200 bg-white p-2 text-sm" placeholder="Alt texte">
                                                    <input x-model="block.width" type="number" class="w-full rounded-xl border border-slate-200 bg-white p-2 text-sm" placeholder="220">
                                                </div>
                                            </template>

                                            <template x-if="block.type === 'logo'">
                                                <div class="space-y-2">
                                                    <input x-model="block.src" type="text" class="w-full rounded-xl border border-slate-200 bg-white p-2 text-sm" placeholder="/logo-caei.svg">
                                                    <input x-model="block.alt" type="text" class="w-full rounded-xl border border-slate-200 bg-white p-2 text-sm" placeholder="Logo CAEI">
                                                </div>
                                            </template>

                                            <template x-if="block.type === 'button'">
                                                <div class="space-y-2">
                                                    <input x-model="block.label" type="text" class="w-full rounded-xl border border-slate-200 bg-white p-2 text-sm" placeholder="Libellé du bouton">
                                                    <input x-model="block.url" type="text" class="w-full rounded-xl border border-slate-200 bg-white p-2 text-sm" placeholder="@{{lien}}">
                                                    <input x-model="block.color" type="color" class="h-10 w-full rounded-xl border border-slate-200 bg-white p-1">
                                                </div>
                                            </template>

                                            <template x-if="block.type === 'link'">
                                                <div class="space-y-2">
                                                    <input x-model="block.label" type="text" class="w-full rounded-xl border border-slate-200 bg-white p-2 text-sm" placeholder="Nom du lien">
                                                    <input x-model="block.url" type="text" class="w-full rounded-xl border border-slate-200 bg-white p-2 text-sm" placeholder="@{{lien}}">
                                                </div>
                                            </template>

                                            <template x-if="block.type === 'signature'">
                                                <div class="space-y-2">
                                                    <textarea x-model="block.content" rows="3" class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm" placeholder="L’équipe CAEI"></textarea>
                                                </div>
                                            </template>

                                            <template x-if="block.type === 'attachment'">
                                                <div class="space-y-2">
                                                    <input x-model="block.label" type="text" class="w-full rounded-xl border border-slate-200 bg-white p-2 text-sm" placeholder="Nom de la pièce jointe">
                                                    <input x-model="block.url" type="text" class="w-full rounded-xl border border-slate-200 bg-white p-2 text-sm" placeholder="/attachments/mon-fichier.pdf">
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div x-show="mode === 'html'" class="space-y-2">
                                <textarea x-model="htmlValue" rows="12" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 p-4 font-mono text-sm" placeholder="Collez ou éditez du HTML ici..."></textarea>
                                <p class="text-xs text-slate-500">Le contenu HTML sera utilisé tel quel pour l’envoi et la prévisualisation.</p>
                            </div>

                            <div class="mt-3 rounded-xl border border-indigo-100 bg-indigo-50/70 p-3 text-xs text-indigo-700">
                                Astuce : vous pouvez utiliser les variables <span class="font-semibold">@{{prenom}}</span>, <span class="font-semibold">@{{entreprise}}</span> et <span class="font-semibold">@{{lien}}</span> dans vos blocs.
                            </div>

                            @error('contenu')
                                <p class="text-xs text-rose-600 mt-1.5 font-semibold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <script>
                            function emailBuilder({ initialContent = '' } = {}) {
                                return {
                                    mode: 'builder',
                                    blocks: [],
                                    htmlValue: '',
                                    get contentValue() {
                                        return this.mode === 'builder' ? JSON.stringify({ version: 1, theme: 'caei', blocks: this.blocks }) : this.htmlValue;
                                    },
                                    init() {
                                        this.blocks = this.parseInitial(initialContent);
                                        this.htmlValue = this.buildHtmlFromBlocks();
                                    },
                                    parseInitial(content) {
                                        if (!content || !String(content).trim()) {
                                            return [];
                                        }

                                        try {
                                            const parsed = JSON.parse(String(content));
                                            if (parsed && Array.isArray(parsed.blocks)) {
                                                return parsed.blocks;
                                            }
                                            if (Array.isArray(parsed)) {
                                                return parsed;
                                            }
                                        } catch (e) {}

                                        return [{ id: this.makeId(), type: 'text', content: String(content), align: 'left' }];
                                    },
                                    addBlock(type) {
                                        const block = { id: this.makeId(), type };
                                        if (type === 'text') {
                                            block.content = 'Bonjour @{{prenom}},\n\nVotre message ici.';
                                            block.align = 'left';
                                        } else if (type === 'image') {
                                            block.src = '/logo-caei.svg';
                                            block.alt = 'Logo CAEI';
                                            block.width = 220;
                                        } else if (type === 'logo') {
                                            block.src = '/logo-caei.svg';
                                            block.alt = 'Logo CAEI';
                                        } else if (type === 'button') {
                                            block.label = 'Découvrir le programme';
                                            block.url = '@{{lien}}';
                                            block.color = '#2563eb';
                                        } else if (type === 'link') {
                                            block.label = 'En savoir plus';
                                            block.url = '@{{lien}}';
                                        } else if (type === 'signature') {
                                            block.content = 'L’équipe CAEI';
                                        } else if (type === 'attachment') {
                                            block.label = 'Pièce jointe';
                                            block.url = '/attachments/mon-fichier.pdf';
                                        }
                                        this.blocks.push(block);
                                        this.htmlValue = this.buildHtmlFromBlocks();
                                    },
                                    removeBlock(index) {
                                        this.blocks.splice(index, 1);
                                        this.htmlValue = this.buildHtmlFromBlocks();
                                    },
                                    blockLabel(type) {
                                        return {
                                            text: 'Bloc texte',
                                            image: 'Bloc image',
                                            logo: 'Bloc logo',
                                            button: 'Bloc bouton',
                                            link: 'Bloc lien',
                                            signature: 'Bloc signature',
                                            attachment: 'Bloc pièce jointe'
                                        }[type] || 'Bloc';
                                    },
                                    buildHtmlFromBlocks() {
                                        let html = '';
                                        this.blocks.forEach((block) => {
                                            if (!block || !block.type) {
                                                return;
                                            }

                                            switch (block.type) {
                                                case 'text':
                                                    html += '<div style="margin:0 0 16px 0; text-align:' + (block.align || 'left') + ';"><p style="margin:0; line-height:1.7; color:#0f172a;">' + (block.content || '').replace(/\n/g, '<br>') + '</p></div>';
                                                    break;
                                                case 'image':
                                                case 'logo':
                                                    html += '<div style="margin:0 0 16px 0;"><img src="' + (block.src || '/logo-caei.svg') + '" alt="' + (block.alt || 'Logo CAEI') + '" width="' + (block.width || 220) + '" style="max-width:100%; height:auto; display:block;" /></div>';
                                                    break;
                                                case 'button':
                                                    html += '<div style="margin:0 0 16px 0;"><a href="' + (block.url || '#') + '" style="display:inline-block;background:' + (block.color || '#2563eb') + ';color:#ffffff;text-decoration:none;padding:12px 20px;border-radius:999px;font-weight:600;">' + (block.label || 'Bouton') + '</a></div>';
                                                    break;
                                                case 'link':
                                                    html += '<div style="margin:0 0 16px 0;"><a href="' + (block.url || '#') + '" style="color:#2563eb; text-decoration:underline;">' + (block.label || 'En savoir plus') + '</a></div>';
                                                    break;
                                                case 'signature':
                                                    html += '<div style="margin:0 0 16px 0; font-style:italic; color:#334155;">' + (block.content || '') + '</div>';
                                                    break;
                                                case 'attachment':
                                                    html += '<div style="margin:0 0 16px 0;"><a href="' + (block.url || '#') + '" style="color:#2563eb; text-decoration:underline;">' + (block.label || 'Pièce jointe') + '</a></div>';
                                                    break;
                                            }
                                        });
                                        return html;
                                    },
                                    makeId() {
                                        return 'block-' + Math.random().toString(36).slice(2, 10);
                                    }
                                };
                            }
                        </script>
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
