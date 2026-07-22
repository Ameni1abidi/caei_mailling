<x-app-layout>
    <div class="p-6 max-w-7xl mx-auto space-y-6" x-data="{
        device: 'desktop',
        copiedHtml: false,
        vars: {
            @foreach($variables as $name => $value)
                '{{ $name }}': `{{ addslashes($value) }}`,
            @endforeach
        },
        rawSubject: `{{ addslashes($emailTemplate->sujet ?: $emailTemplate->nom) }}`,
        rawContent: `{{ addslashes($emailTemplate->contenu) }}`,
        get renderedSubject() {
            let res = this.rawSubject;
            for (const [key, val] of Object.entries(this.vars)) {
                const reg = new RegExp('\\{\\{\\s*' + key + '\\s*\\}\\}', 'g');
                res = res.replace(reg, val || '');
            }
            return res;
        },
        get renderedBody() {
            let res = this.rawContent;
            for (const [key, val] of Object.entries(this.vars)) {
                const reg = new RegExp('\\{\\{\\s*' + key + '\\s*\\}\\}', 'g');
                res = res.replace(reg, val || '');
            }
            return res.replace(/\n/g, '<br>');
        },
        copyToClipboard() {
            navigator.clipboard.writeText(this.rawContent).then(() => {
                this.copiedHtml = true;
                setTimeout(() => { this.copiedHtml = false; }, 2500);
            });
        }
    }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3.5 bg-gradient-to-br from-indigo-500 to-blue-600 text-white rounded-2xl shadow-md shadow-indigo-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Prévisualisation : {{ $emailTemplate->nom }}</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Rendu visuel interactif du template avec injection dynamique de données</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if($emailTemplate->is_active)
                    <a href="{{ route('campaigns.create', ['template_id' => $emailTemplate->id]) }}" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <span>Utiliser dans une campagne</span>
                    </a>
                @endif
                <a href="{{ route('email-templates.edit', $emailTemplate) }}" class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span>Modifier</span>
                </a>
                <a href="{{ route('email-templates.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-4 py-2.5 rounded-xl border border-slate-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Retour</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Email Render Window (2 cols) -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Device Viewport Switcher & Bar -->
                <div class="flex items-center justify-between bg-white p-3 rounded-2xl border border-slate-200/80 shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider pl-2">Affichage :</span>
                        <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200/70">
                            <button type="button" @click="device = 'desktop'" :class="device === 'desktop' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="px-3 py-1 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span>Bureau</span>
                            </button>
                            <button type="button" @click="device = 'tablet'" :class="device === 'tablet' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="px-3 py-1 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <span>Tablette</span>
                            </button>
                            <button type="button" @click="device = 'mobile'" :class="device === 'mobile' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="px-3 py-1 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <span>Mobile</span>
                            </button>
                        </div>
                    </div>

                    <button type="button" @click="copyToClipboard()" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-indigo-600 bg-slate-100 hover:bg-indigo-50 px-3 py-1.5 rounded-xl border border-slate-200 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <span x-text="copiedHtml ? 'Copié !' : 'Copier le HTML'"></span>
                    </button>
                </div>

                <!-- Simulated Email Client Box -->
                <div class="transition-all duration-300 mx-auto" :class="{
                    'w-full': device === 'desktop',
                    'max-w-[640px]': device === 'tablet',
                    'max-w-[375px]': device === 'mobile'
                }">
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-md overflow-hidden flex flex-col">
                        <!-- Window Chrome Top Bar -->
                        <div class="bg-slate-100 px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                            <div class="flex items-center gap-1.5">
                                <span class="w-3 h-3 rounded-full bg-rose-400 inline-block"></span>
                                <span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-400 inline-block"></span>
                            </div>
                            <span class="text-xs font-semibold text-slate-500 font-mono">Aperçu du courriel</span>
                            <div class="w-12"></div>
                        </div>

                        <!-- Email Header Metadata -->
                        <div class="bg-slate-50/80 px-6 py-4 border-b border-slate-200 space-y-2 text-xs">
                            <div class="flex items-center gap-3">
                                <span class="font-bold text-slate-600 w-20">Expéditeur:</span>
                                <span class="text-slate-800 font-semibold bg-slate-200/60 px-2 py-0.5 rounded">CAEI &lt;contact@caei.org&gt;</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="font-bold text-slate-600 w-20">Destinataire:</span>
                                <span class="text-slate-800 font-semibold bg-slate-200/60 px-2 py-0.5 rounded" x-text="vars['email'] || (vars['prenom'] ? vars['prenom'].toLowerCase() + '.' + (vars['nom'] || '').toLowerCase() + '@exemple.com' : 'exemple@client.com')"></span>
                            </div>
                            <div class="flex items-center gap-3 pt-2 border-t border-slate-200/80">
                                <span class="font-extrabold text-slate-900 w-20 text-sm">Objet:</span>
                                <span class="font-bold text-slate-900 text-sm" x-text="renderedSubject"></span>
                            </div>
                        </div>

                        <!-- Email Body Render -->
                        <div class="p-6 md:p-8 prose max-w-none text-slate-800 bg-white min-h-[320px] text-sm leading-relaxed" x-html="renderedBody">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dynamic Variables Live Tester (1 col) -->
            <div class="space-y-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-4 sticky top-6">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                </svg>
                            </div>
                            <h2 class="font-bold text-slate-900 text-sm">Test des variables</h2>
                        </div>
                    </div>

                    <p class="text-xs text-slate-500 leading-relaxed">
                        Modifiez les valeurs ci-dessous pour voir la mise à jour instantanée du modèle d'email :
                    </p>

                    <div class="space-y-3 max-h-[500px] overflow-y-auto pr-1">
                        @foreach($variables as $name => $value)
                            <div>
                                <label for="var_{{ $name }}" class="block text-xs font-bold text-slate-700 mb-1 flex items-center justify-between">
                                    <span>{{ $name }}</span>
                                    <code class="font-mono text-indigo-600 text-[10px]">@{{ @{{ $name }} }}</code>
                                </label>
                                <input type="text" id="var_{{ $name }}" x-model="vars['{{ $name }}']" class="w-full text-xs py-2 px-3 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
