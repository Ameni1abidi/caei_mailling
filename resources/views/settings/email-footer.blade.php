<x-app-layout>
    <div class="p-6 max-w-7xl mx-auto space-y-6" x-data="emailFooterSettings({
        show_footer: @js($settings['show_footer'] ?? true),
        show_footer_logo: @js($settings['show_footer_logo'] ?? true),
        footer_logo_url: @js($settings['footer_logo_url'] ?? ''),
        footer_title: @js($settings['footer_title'] ?? $settings['company_name']),
        footer_subtitle: @js($settings['footer_subtitle'] ?? "Cabinet International d'Audit, d'Expertise et d'Ingénierie de Formation"),
        footer_disclaimer: @js($settings['footer_disclaimer'] ?? "Vous recevez cette communication professionnelle car vous êtes inscrit dans le réseau de contacts CAEI."),
        footer_unsubscribe_text: @js($settings['footer_unsubscribe_text'] ?? "Se désinscrire de cette liste"),
        footer_extra_text: @js($settings['footer_extra_text'] ?? ''),
        site_web: @js($settings['site_web'] ?? 'www.caei-afri.com'),
        email: @js($settings['email'] ?? 'Training@caei-afri.com'),
        telephone: @js($settings['telephone'] ?? '+216 58 332 143')
    })">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3.5 bg-gradient-to-br from-indigo-500 to-blue-600 text-white rounded-2xl shadow-md shadow-indigo-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5Zm0 10h16" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pied de page (Footer) des Emails</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Personnalisez les mentions légales, la signature officielle et le logo affichés au bas de toutes vos campagnes</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <form action="{{ route('settings.email-footer.reset') }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment réinitialiser le pied de page aux valeurs d\'origine de CAEI ?')">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-4 py-2.5 rounded-xl border border-slate-200 transition">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Réinitialiser le footer</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Navigation Tabs (En-tête / Pied de page) -->
        <div class="flex items-center gap-2 border-b border-slate-200 pb-1">
            <a href="{{ route('settings.email-header') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>En-tête & Logo Haut</span>
            </a>
            <a href="{{ route('settings.email-footer') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold bg-indigo-600 text-white shadow-md shadow-indigo-100 transition">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5Zm0 10h16" />
                </svg>
                <span>Pied de page (Footer)</span>
            </a>
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

        @if(isset($errors) && $errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl text-sm font-medium">
                <div class="font-bold mb-1">Veuillez corriger les erreurs suivantes :</div>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Formulaire d'édition (6 cols) -->
            <div class="lg:col-span-6 space-y-6">
                <form action="{{ route('settings.email-footer.update') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
                    @csrf

                    <!-- Activer / Désactiver le pied de page -->
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200/70">
                        <div>
                            <div class="text-sm font-bold text-slate-800">Afficher le pied de page officiel</div>
                            <div class="text-xs text-slate-500">Activer le bloc de pied de page (signature, mentions et désinscription) sur tous les emails</div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="show_footer" value="1" x-model="show_footer" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>

                    <!-- Logo / Bannière du Footer -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs font-bold text-slate-700 uppercase tracking-wider">Logo / Bannière du Footer</div>
                                <div class="text-xs text-slate-400">Emblème ou bannière affiché en haut du pied de page</div>
                            </div>
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-600 cursor-pointer">
                                <input type="checkbox" name="show_footer_logo" value="1" x-model="show_footer_logo" class="rounded text-indigo-600 focus:ring-indigo-500">
                                <span>Activer le logo</span>
                            </label>
                        </div>

                        <div class="flex items-center gap-5">
                            <div class="w-28 h-16 bg-white rounded-xl border border-slate-200 p-2 flex items-center justify-center shrink-0 shadow-sm overflow-hidden">
                                <img :src="footer_logo_preview || footer_logo_url" alt="Logo Footer" class="max-w-full max-h-full object-contain">
                            </div>

                            <div class="flex-1 space-y-1.5">
                                <label for="footer-logo-input" class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold cursor-pointer transition shadow-sm">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    <span>Changer la bannière...</span>
                                </label>
                                <input type="file"
                                       id="footer-logo-input"
                                       name="footer_logo"
                                       accept="image/png,image/jpeg,image/svg+xml,image/webp"
                                       class="hidden"
                                       @change="handleFooterLogoChange($event)">
                                <p class="text-[11px] text-slate-400">Format horizontal recommandé. PNG transparent ou JPEG. Max 5 Mo.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Titre du Footer -->
                    <div>
                        <label for="footer_title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Titre / Nom d'organisation
                        </label>
                        <input type="text"
                               id="footer_title"
                               name="footer_title"
                               x-model="footer_title"
                               class="w-full text-sm border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm"
                               placeholder="Ex: CAEI COMPANY GROUP">
                    </div>

                    <!-- Sous-titre / Description de l'activité -->
                    <div>
                        <label for="footer_subtitle" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Sous-titre / Description de l'activité
                        </label>
                        <input type="text"
                               id="footer_subtitle"
                               name="footer_subtitle"
                               x-model="footer_subtitle"
                               class="w-full text-sm border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm"
                               placeholder="Ex: Cabinet International d'Audit, d'Expertise et d'Ingénierie de Formation">
                    </div>

                    <!-- Notice d'information / Disclaimer -->
                    <div>
                        <label for="footer_disclaimer" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Texte de conformité / Disclaimer
                        </label>
                        <textarea id="footer_disclaimer"
                                  name="footer_disclaimer"
                                  rows="2"
                                  x-model="footer_disclaimer"
                                  class="w-full text-sm border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm"
                                  placeholder="Ex: Vous recevez cette communication professionnelle car vous êtes inscrit dans le réseau de contacts CAEI."></textarea>
                        <p class="mt-1 text-xs text-slate-400">Explique au destinataire pourquoi il reçoit ce message (Requis anti-spam/RGPD).</p>
                    </div>

                    <!-- Texte du lien de désinscription -->
                    <div>
                        <label for="footer_unsubscribe_text" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Texte du lien de désinscription
                        </label>
                        <input type="text"
                               id="footer_unsubscribe_text"
                               name="footer_unsubscribe_text"
                               x-model="footer_unsubscribe_text"
                               class="w-full text-sm border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm"
                               placeholder="Ex: Se désinscrire de cette liste">
                    </div>

                    <!-- Mentions légales / Texte supplémentaire libre -->
                    <div>
                        <label for="footer_extra_text" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Mentions légales additionnelles (Optionnel)
                        </label>
                        <textarea id="footer_extra_text"
                                  name="footer_extra_text"
                                  rows="2"
                                  x-model="footer_extra_text"
                                  class="w-full text-sm border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm"
                                  placeholder="Ex: Tous droits réservés &copy; 2026 CAEI. Conforme à la législation en vigueur."></textarea>
                    </div>

                    <!-- Bouton Enregistrer -->
                    <div class="pt-3">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white text-sm font-bold px-6 py-3 rounded-xl shadow-md shadow-indigo-100 transition transform active:scale-[0.99]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Enregistrer les modifications du pied de page</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Aperçu en Direct (Live Preview) (6 cols) -->
            <div class="lg:col-span-6 space-y-4 lg:sticky lg:top-6">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Aperçu en direct du pied de page
                    </span>
                    <span class="text-[11px] text-slate-400">Rendu identique aux emails reçus</span>
                </div>

                <!-- Simulateur d'Email Client -->
                <div class="bg-slate-100 p-4 sm:p-6 rounded-2xl border border-slate-200 shadow-inner">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200/90 overflow-hidden">
                        <!-- Simulateur d'un contenu fictif de mail -->
                        <div class="p-6 bg-slate-50/50 border-b border-slate-100 text-xs text-slate-400 italic space-y-2">
                            <div class="h-3 bg-slate-200/70 rounded w-3/4"></div>
                            <div class="h-3 bg-slate-200/60 rounded w-full"></div>
                            <div class="h-3 bg-slate-200/60 rounded w-5/6"></div>
                            <div class="text-[11px] text-slate-400 text-center pt-2">... [Corps de l'email de votre campagne] ...</div>
                        </div>

                        <!-- Rendu du Pied de page en temps réel -->
                        <template x-if="show_footer">
                            <div style="background-color: #f8fafc; padding: 26px 28px; border-top: 1px solid #e2e8f0; text-align: center; color: #64748b; font-size: 11px; line-height: 1.6;">
                                <!-- Logo Footer -->
                                <template x-if="show_footer_logo && (footer_logo_preview || footer_logo_url)">
                                    <div style="margin: 0 auto 16px auto; text-align: center;">
                                        <img :src="footer_logo_preview || footer_logo_url" 
                                             alt="Logo Footer" 
                                             width="260" 
                                             style="display: block; width: 260px; max-width: 85%; height: auto; border: 0; margin: 0 auto;" />
                                    </div>
                                </template>

                                <!-- Titre & Sous-titre -->
                                <p style="margin: 0 0 6px 0; font-weight: 700; color: #334155; font-size: 12px;" x-text="footer_title || 'CAEI COMPANY GROUP'"></p>
                                <p style="margin: 0 0 8px 0; color: #64748b;" x-show="footer_subtitle" x-text="footer_subtitle"></p>

                                <!-- Coordonnées -->
                                <p style="margin: 0 0 12px 0;">
                                    <span style="color: #b45309; font-weight: 600;" x-text="site_web"></span>
                                    &nbsp;&bull;&nbsp;
                                    <span style="color: #64748b;" x-text="email"></span>
                                </p>

                                <!-- Disclaimer -->
                                <p style="margin: 0 0 12px 0; color: #94a3b8; font-size: 10px;" x-show="footer_disclaimer" x-text="footer_disclaimer"></p>

                                <!-- Texte extra optionnel -->
                                <p style="margin: 0 0 12px 0; color: #94a3b8; font-size: 10px;" x-show="footer_extra_text" x-text="footer_extra_text"></p>

                                <!-- Lien de désinscription -->
                                <p style="margin: 0; padding-top: 8px; border-top: 1px dashed #e2e8f0;">
                                    <span style="color: #94a3b8; text-decoration: underline; font-size: 10px; cursor: pointer;" x-text="footer_unsubscribe_text || 'Se désinscrire de cette liste'"></span>
                                </p>
                            </div>
                        </template>

                        <template x-if="!show_footer">
                            <div class="p-8 text-center text-xs text-slate-400 bg-slate-50 border-t border-dashed border-slate-200">
                                <em>Le pied de page est actuellement désactivé. Les emails n'afficheront aucun bloc de footer.</em>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function emailFooterSettings(initial) {
            return {
                show_footer: initial.show_footer,
                show_footer_logo: initial.show_footer_logo,
                footer_logo_url: initial.footer_logo_url,
                footer_logo_preview: null,
                footer_title: initial.footer_title,
                footer_subtitle: initial.footer_subtitle,
                footer_disclaimer: initial.footer_disclaimer,
                footer_unsubscribe_text: initial.footer_unsubscribe_text,
                footer_extra_text: initial.footer_extra_text,
                site_web: initial.site_web,
                email: initial.email,
                telephone: initial.telephone,

                handleFooterLogoChange(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.footer_logo_preview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                }
            };
        }
    </script>
</x-app-layout>
