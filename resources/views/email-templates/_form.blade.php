@php
    $emailTemplate = $emailTemplate ?? null;
@endphp

@if($errors->any())
    <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-900 p-4 rounded-xl text-sm font-medium shadow-sm flex items-start gap-3">
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

<div x-data="templateForm" class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Main Form Controls (2 cols) -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
            <!-- Nom du template -->
            <div>
                <label for="nom" class="block text-sm font-bold text-slate-800 mb-1.5">
                    Nom du template <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nom" id="nom" value="{{ old('nom', $emailTemplate?->nom ?? '') }}" required placeholder="Ex: Invitation au Séminaire CAEI 2026" class="w-full text-sm py-2.5 px-3.5 rounded-xl bg-slate-50/50 border @error('nom') border-rose-300 focus:ring-rose-500 focus:border-rose-500 @else border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @enderror transition-all">
                @error('nom')
                    <p class="text-xs text-rose-600 mt-1.5 font-semibold flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <!-- Objet par défaut -->
            <div>
                <label for="sujet" class="block text-sm font-bold text-slate-800 mb-1.5">
                    Objet de l'email
                </label>
                <input type="text" name="sujet" id="sujet" value="{{ old('sujet', $emailTemplate?->sujet ?? '') }}" placeholder="Ex: Invitation : @{{nom_seminaire}} pour @{{entreprise}}" class="w-full text-sm py-2.5 px-3.5 rounded-xl bg-slate-50/50 border @error('sujet') border-rose-300 focus:ring-rose-500 focus:border-rose-500 @else border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @enderror transition-all">
                <p class="text-xs text-slate-400 mt-1">L'objet prend en charge les variables dynamiques (ex: <code>@{{nom_seminaire}}</code>)</p>
                @error('sujet')
                    <p class="text-xs text-rose-600 mt-1.5 font-semibold flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                <div>
                    <label for="type" class="block text-sm font-bold text-slate-800 mb-1.5">
                        Type de template <span class="text-rose-500">*</span>
                    </label>
                    <select name="type" id="type" required class="w-full text-sm py-2.5 px-3.5 rounded-xl bg-slate-50/50 font-medium text-slate-700 border @error('type') border-rose-300 focus:ring-rose-500 focus:border-rose-500 @else border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @enderror transition-all">
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}" {{ old('type', $emailTemplate?->type ?? 'newsletter') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="text-xs text-rose-600 mt-1.5 font-semibold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <div class="flex items-center sm:pt-6">
                    <label class="relative inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $emailTemplate?->is_active ?? true) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        <span class="ml-3 text-sm font-bold text-slate-800">Template actif</span>
                    </label>
                </div>
            </div>

            <!-- Editor Header Tabs -->
            <div class="pt-3 border-t border-slate-100 space-y-3">
                <div class="flex items-center justify-between">
                    <label for="contenu" class="block text-sm font-bold text-slate-800">
                        Contenu du message <span class="text-rose-500">*</span>
                    </label>

                    <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200/80 text-xs">
                        <button type="button" @click="activeTab = 'edit'" :class="activeTab === 'edit' ? 'bg-white text-indigo-600 font-bold shadow-sm' : 'text-slate-500 font-medium hover:text-slate-800'" class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span>Éditeur</span>
                        </button>
                        <button type="button" @click="activeTab = 'preview'" :class="activeTab === 'preview' ? 'bg-white text-indigo-600 font-bold shadow-sm' : 'text-slate-500 font-medium hover:text-slate-800'" class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span>Aperçu direct</span>
                        </button>
                    </div>
                </div>

                <!-- Textarea Editor -->
                <div x-show="activeTab === 'edit'" class="space-y-2">
                    <textarea name="contenu" id="contenu" x-model="content" rows="13" required placeholder="Rédigez le texte ou le HTML de votre modèle d'email..." class="w-full text-sm font-mono bg-slate-50/40 p-4 leading-relaxed rounded-xl border @error('contenu') border-rose-300 focus:ring-rose-500 focus:border-rose-500 @else border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @enderror transition-all"></textarea>
                    <div class="flex items-center justify-between text-xs text-slate-400">
                        <span>Formats supportés : Texte brut, HTML basique</span>
                        <span x-text="content.length + ' caractères'"></span>
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

                <!-- Live Preview Tab -->
                <div x-show="activeTab === 'preview'" class="bg-white rounded-xl border border-slate-200 p-5 space-y-3 min-h-[300px]" style="display: none;">
                    <div class="text-xs text-slate-400 font-bold uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center justify-between">
                        <span>Rendu visuel simulé</span>
                        <span class="text-emerald-600 font-normal">Valeurs de test injectées</span>
                    </div>
                    <div class="prose max-w-none text-slate-800 text-sm leading-relaxed" x-html="renderedPreview"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Variables Reference (1 col) -->
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
                Cliquez sur une variable ci-dessous pour l'insérer directement dans votre message au niveau du curseur :
            </p>

            <!-- Toast Notification -->
            <div x-show="copiedToast" x-transition class="bg-indigo-600 text-white text-xs font-semibold px-3 py-2 rounded-xl shadow-md flex items-center justify-between gap-2" style="display: none;">
                <span x-text="toastMessage"></span>
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <div class="space-y-2 max-h-[460px] overflow-y-auto pr-1">
                @foreach($variables as $variable => $description)
                    <button type="button" @click="insertVariable('{{ $variable }}')" class="w-full text-left bg-slate-50 hover:bg-indigo-50/70 border border-slate-200/70 hover:border-indigo-200 p-3 rounded-xl transition duration-150 group">
                        <div class="flex items-center justify-between gap-2">
                            <code class="font-mono text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-100 font-bold text-xs group-hover:bg-indigo-600 group-hover:text-white transition">
                                {{ $variable }}
                            </code>
                            <span class="text-slate-400 group-hover:text-indigo-600 text-xs font-semibold flex items-center gap-1">
                                Insérer
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </span>
                        </div>
                        <div class="text-slate-500 text-xs mt-1.5 group-hover:text-slate-700 font-medium">
                            {{ $description }}
                        </div>
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    function initTemplateForm() {
        if (typeof Alpine !== 'undefined' && !Alpine.data('templateForm')) {
            Alpine.data('templateForm', () => ({
                activeTab: 'edit',
                content: {!! json_encode(old('contenu', $emailTemplate?->contenu ?? ''), JSON_UNESCAPED_UNICODE) !!},
                copiedToast: false,
                toastMessage: '',
                insertVariable(varTag) {
                    const textarea = document.getElementById('contenu');
                    if (textarea) {
                        const start = textarea.selectionStart || 0;
                        const end = textarea.selectionEnd || 0;
                        const text = this.content;
                        this.content = text.substring(0, start) + varTag + text.substring(end);
                        this.$nextTick(() => {
                            textarea.focus();
                            textarea.setSelectionRange(start + varTag.length, start + varTag.length);
                        });
                    } else {
                        this.content += ' ' + varTag;
                    }
                    this.showToast('Variable ' + varTag + ' insérée !');
                },
                showToast(msg) {
                    this.toastMessage = msg;
                    this.copiedToast = true;
                    setTimeout(() => { this.copiedToast = false; }, 2500);
                },
                get renderedPreview() {
                    if (!this.content) return '<span class="text-slate-400 italic">Aucun contenu à afficher</span>';
                    let rendered = this.content
                        .replace(/\{\{\s*nom\s*\}\}/g, 'Ben Ali')
                        .replace(/\{\{\s*prenom\s*\}\}/g, 'Amel')
                        .replace(/\{\{\s*entreprise\s*\}\}/g, 'CAEI Partner')
                        .replace(/\{\{\s*fonction\s*\}\}/g, 'Directrice Formation')
                        .replace(/\{\{\s*pays\s*\}\}/g, 'Tunisie')
                        .replace(/\{\{\s*nom_seminaire\s*\}\}/g, 'Séminaire Audit & Gouvernance')
                        .replace(/\{\{\s*date\s*\}\}/g, '30/07/2026')
                        .replace(/\{\{\s*lien\s*\}\}/g, 'https://caei.org');

                    return rendered.replace(/\n/g, '<br>');
                }
            }));
        }
    }

    if (typeof Alpine !== 'undefined') {
        initTemplateForm();
    } else {
        document.addEventListener('alpine:init', initTemplateForm);
    }
</script>
