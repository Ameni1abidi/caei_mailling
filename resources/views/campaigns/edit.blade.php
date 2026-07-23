<x-app-layout>
    <div class="p-6 max-w-7xl mx-auto space-y-6">
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

        <!-- Destination target Info banner -->
        <div class="bg-indigo-50 border border-indigo-100 text-indigo-900 p-4 rounded-2xl flex items-center gap-3.5 shadow-sm">
            <div class="p-2.5 bg-indigo-100 text-indigo-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-xs text-indigo-700 font-semibold uppercase tracking-wider">Cible d'envoi</div>
                <div class="text-base font-bold text-indigo-950 mt-0.5">Cette campagne sera envoyée à <strong class="text-indigo-600 font-extrabold">{{ $nbDestinataires }}</strong> destinataires ciblés.</div>
            </div>
        </div>

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
                        <label class="block text-sm font-bold text-slate-800 mb-1.5">
                            Ciblage de la campagne
                        </label>

                        <label class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700">
                            <input type="checkbox" name="all_contacts" value="1" {{ old('all_contacts', $campaign->categoryIds() === [] ? '1' : '') ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span>Tous les contacts</span>
                        </label>

                        <div class="mt-3">
                            <label for="category_ids" class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">
                                Ou choisir plusieurs listes ciblées
                            </label>
                            <select name="category_ids[]" id="category_ids" multiple class="w-full min-h-[120px] text-sm py-2.5 px-3.5 rounded-xl bg-slate-50/50 border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-medium text-slate-700">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ in_array($cat->id, old('category_ids', $campaign->categoryIds()), true) ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-xs text-slate-500">Maintenez la touche Ctrl/Cmd pour sélectionner plusieurs listes.</p>
                        </div>
                    </div>

                    <!-- Contenu du message -->
                    @php
                        $defaultContent = old('contenu', $campaign->contenu ?? '');
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

                    @if($campaign->statut === 'brouillon')
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
                    @else
                        <div class="bg-slate-50 border border-slate-200 text-slate-500 p-4 rounded-xl space-y-2 text-center">
                            <svg class="w-8 h-8 text-slate-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-700 mt-1">Campagne déjà lancée</div>
                            <div class="text-[11px] leading-relaxed font-medium">
                                Le statut actuel est <strong class="text-indigo-600">@if($campaign->statut === 'envoyee') Envoyée @else En cours @endif</strong>. Il est impossible de renvoyer ou modifier une campagne lancée.
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
