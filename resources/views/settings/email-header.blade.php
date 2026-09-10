<x-app-layout>
    <div class="p-6 max-w-7xl mx-auto space-y-6" x-data="emailHeaderSettings({
        company_name: @js($settings['company_name']),
        siege_social: @js($settings['siege_social']),
        telephone: @js($settings['telephone']),
        email: @js($settings['email']),
        site_web: @js($settings['site_web']),
        matricule_fiscal: @js($settings['matricule_fiscal']),
        show_header: @js($settings['show_header']),
        show_footer_logo: @js($settings['show_footer_logo']),
        logo_url: @js($settings['logo_url']),
        footer_logo_url: @js($settings['footer_logo_url'])
    })">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3.5 bg-gradient-to-br from-amber-500 to-orange-600 text-white rounded-2xl shadow-md shadow-amber-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">En-tête & Logo des Emails</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Personnalisez les coordonnées officielles et le logo affichés en haut de toutes vos campagnes</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <form action="{{ route('settings.email-header.reset') }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment réinitialiser l\'en-tête et le logo aux valeurs d\'origine de CAEI ?')">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-4 py-2.5 rounded-xl border border-slate-200 transition">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Réinitialiser par défaut</span>
                    </button>
                </form>
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
            <!-- Formulaire d'édition (7 cols) -->
            <div class="lg:col-span-6 space-y-6">
                <form action="{{ route('settings.email-header.update') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
                    @csrf

                    <!-- Activer / Désactiver -->
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200/70">
                        <div>
                            <div class="text-sm font-bold text-slate-800">Afficher l'en-tête officiel</div>
                            <div class="text-xs text-slate-500">Activer le bandeau d'en-tête (coordonnées + logo) sur tous les emails envoyés</div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="show_header" value="1" x-model="show_header" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                        </label>
                    </div>

                    <!-- Nom Entreprise -->
                    <div>
                        <label for="company_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nom de l'entreprise
                        </label>
                        <input type="text"
                               id="company_name"
                               name="company_name"
                               x-model="company_name"
                               class="w-full text-sm border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-sm"
                               placeholder="Ex: CAEI COMPANY GROUP">
                    </div>

                    <!-- Siège Social -->
                    <div>
                        <label for="siege_social" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Siège social / Adresse
                        </label>
                        <input type="text"
                               id="siege_social"
                               name="siege_social"
                               x-model="siege_social"
                               class="w-full text-sm border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-sm"
                               placeholder="Ex: SIS 8 Rue Claude Bernard 1002 Belvédère-Tunis Tunisie">
                    </div>

                    <!-- Téléphone & E-mail -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="telephone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Téléphone
                            </label>
                            <input type="text"
                                   id="telephone"
                                   name="telephone"
                                   x-model="telephone"
                                   class="w-full text-sm border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-sm"
                                   placeholder="Ex: +216 58 332 143">
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                E-mail
                            </label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   x-model="email"
                                   class="w-full text-sm border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-sm"
                                   placeholder="Ex: Training@caei-afri.com">
                        </div>
                    </div>

                    <!-- Site Web & Matricule Fiscal -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="site_web" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Site Web
                            </label>
                            <input type="text"
                                   id="site_web"
                                   name="site_web"
                                   x-model="site_web"
                                   class="w-full text-sm border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-sm"
                                   placeholder="Ex: www.caei-afri.com">
                        </div>

                        <div>
                            <label for="matricule_fiscal" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Matricule Fiscal (MF)
                            </label>
                            <input type="text"
                                   id="matricule_fiscal"
                                   name="matricule_fiscal"
                                   x-model="matricule_fiscal"
                                   class="w-full text-sm border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-sm"
                                   placeholder="Ex: 1821205 MAM 000">
                        </div>
                    </div>

                    <!-- Logo Upload En-tête -->
                    <div class="pt-4 border-t border-slate-100">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            1. Logo d'En-tête (Haut du mail)
                        </label>
                        
                        <div class="flex items-center gap-5 p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                            <!-- Aperçu Logo Actuel -->
                            <div class="w-20 h-20 bg-white rounded-xl border border-slate-200 p-2 flex items-center justify-center shrink-0 shadow-sm overflow-hidden">
                                <img :src="logo_preview || logo_url" alt="Logo En-tête" class="max-w-full max-h-full object-contain">
                            </div>

                            <div class="flex-1 space-y-1.5">
                                <label for="logo-input" class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold cursor-pointer transition shadow-sm">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    <span>Changer le logo d'en-tête...</span>
                                </label>
                                <input type="file"
                                       id="logo-input"
                                       name="logo"
                                       accept="image/png,image/jpeg,image/svg+xml,image/webp"
                                       class="hidden"
                                       @change="handleLogoChange($event)">
                                <p class="text-[11px] text-slate-400">Emblème officiel rond ou carré. PNG transparent ou JPEG. Max 5 Mo.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Logo Upload Pied de page (Footer) -->
                    <div class="pt-4 border-t border-slate-100 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                2. Logo du Pied de page (Footer)
                            </label>
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-600 cursor-pointer">
                                <input type="checkbox" name="show_footer_logo" value="1" x-model="show_footer_logo" class="rounded text-amber-500 focus:ring-amber-400">
                                <span>Activer dans le footer</span>
                            </label>
                        </div>
                        
                        <div class="flex items-center gap-5 p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                            <!-- Aperçu Logo Footer Actuel -->
                            <div class="w-24 h-16 bg-white rounded-xl border border-slate-200 p-2 flex items-center justify-center shrink-0 shadow-sm overflow-hidden">
                                <img :src="footer_logo_preview || footer_logo_url" alt="Logo Footer" class="max-w-full max-h-full object-contain">
                            </div>

                            <div class="flex-1 space-y-1.5">
                                <label for="footer-logo-input" class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold cursor-pointer transition shadow-sm">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    <span>Changer le logo de footer...</span>
                                </label>
                                <input type="file"
                                       id="footer-logo-input"
                                       name="footer_logo"
                                       accept="image/png,image/jpeg,image/svg+xml,image/webp"
                                       class="hidden"
                                       @change="handleFooterLogoChange($event)">
                                <p class="text-[11px] text-slate-400">Bannière CAEI horizontale (avec texte du comité). PNG ou JPEG. Max 5 Mo.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Bouton Enregistrer -->
                    <div class="pt-3">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white text-sm font-bold px-6 py-3 rounded-xl shadow-md shadow-amber-200 transition transform active:scale-[0.99]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Enregistrer les modifications</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Aperçu en Direct (Live Preview) (6 cols) -->
            <div class="lg:col-span-6 space-y-4 lg:sticky lg:top-6">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Aperçu en temps réel de l'email
                    </span>
                    <span class="text-[11px] text-slate-400">Rendu identique à l'email final</span>
                </div>

                <!-- Simulateur d'Email Client -->
                <div class="bg-slate-100 p-4 sm:p-6 rounded-2xl border border-slate-200 shadow-inner">
                    <!-- Carte Email Blanche -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-lg overflow-hidden max-w-[580px] mx-auto transition-all">
                        
                        <!-- EN-TÊTE : exactement comme la capture demandée -->
                        <div x-show="show_header" class="p-5 sm:p-6 border-b border-slate-200 bg-white">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <!-- Coordonnées à gauche -->
                                    <td valign="middle" style="text-align: left; vertical-align: middle; padding-right: 16px; font-family: Arial, Helvetica, sans-serif;">
                                        <div class="text-[14px] font-bold italic text-slate-900 mb-1" x-text="company_name || 'CAEI COMPANY GROUP'"></div>
                                        <div class="text-[11px] leading-[1.55] text-slate-700 italic space-y-0.5">
                                            <div x-show="siege_social">
                                                <strong>Siège social :</strong> <span x-text="siege_social"></span>
                                            </div>
                                            <div x-show="telephone">
                                                <strong>Téléphone :</strong> <span x-text="telephone"></span>
                                            </div>
                                            <div x-show="email">
                                                <strong>E-mail :</strong> <span class="text-slate-900" x-text="email"></span>
                                            </div>
                                            <div x-show="site_web">
                                                <strong>Site :</strong> <span class="text-slate-900" x-text="site_web"></span>
                                            </div>
                                            <div x-show="matricule_fiscal">
                                                <span class="underline"><strong>MF:</strong></span> <span x-text="matricule_fiscal"></span>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- Logo à droite -->
                                    <td valign="middle" align="right" style="width: 115px; min-width: 95px; text-align: right; vertical-align: middle;">
                                        <img :src="logo_preview || logo_url"
                                             alt="CAEI"
                                             class="w-[105px] h-auto object-contain ml-auto"
                                             style="display: block; max-width: 115px; height: auto;" />
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Si désactivé -->
                        <div x-show="!show_header" class="p-6 text-center text-xs text-slate-400 italic bg-slate-50 border-b border-slate-200">
                            En-tête désactivé — le message débutera directement sans coordonnées.
                        </div>

                        <!-- Corps d'exemple du message -->
                        <div class="p-6 text-sm text-slate-700 leading-relaxed space-y-3 bg-white">
                            <p class="font-semibold text-slate-900">Bonjour M. Ben Ali,</p>
                            <p class="text-xs sm:text-sm text-slate-600">
                                Nous avons le plaisir de vous convier à notre prochain séminaire international d'expertise et de formation professionnelle.
                            </p>
                            <div class="p-3 bg-amber-50 rounded-xl border border-amber-100 text-xs text-amber-900">
                                📅 <strong>Thématique :</strong> Audit, Gouvernance & Management de la Performance
                            </div>
                            <p class="text-xs text-slate-500 pt-2">
                                Cordialement,<br>
                                <strong class="text-slate-700" x-text="company_name || 'L\'équipe CAEI'"></strong>
                            </p>
                        </div>

                        <!-- Pied de page officiel avec Logo CAEI -->
                        <div class="p-5 bg-slate-50 border-t border-slate-100 text-center text-[10px] text-slate-500 space-y-2">
                            <div x-show="show_footer_logo" class="pb-1">
                                <img :src="footer_logo_preview || footer_logo_url"
                                     alt="Logo Footer CAEI"
                                     class="max-w-[210px] w-auto h-auto mx-auto object-contain" />
                            </div>
                            <div class="font-bold text-slate-800 text-xs" x-text="company_name || 'CAEI COMPANY GROUP'"></div>
                            <div class="text-[10px] text-slate-500">Cabinet International d'Audit, d'Expertise et d'Ingénierie de Formation</div>
                            <div class="text-[10px] text-slate-400">
                                <span x-text="site_web || 'www.caei-afri.com'"></span> &bull; <span x-text="email || 'Training@caei-afri.com'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info note -->
                <div class="p-4 rounded-xl bg-amber-50/80 border border-amber-200/80 text-xs text-amber-900 flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="space-y-1 leading-relaxed">
                        <span class="font-bold">Application universelle :</span>
                        Toutes vos campagnes d'emailing actuelles et futures utiliseront automatiquement cet en-tête officiel ainsi que le logo configuré ici.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function emailHeaderSettings(initialData) {
        return {
            company_name: initialData.company_name,
            siege_social: initialData.siege_social,
            telephone: initialData.telephone,
            email: initialData.email,
            site_web: initialData.site_web,
            matricule_fiscal: initialData.matricule_fiscal,
            show_header: initialData.show_header,
            show_footer_logo: initialData.show_footer_logo,
            logo_url: initialData.logo_url,
            logo_preview: null,
            footer_logo_url: initialData.footer_logo_url,
            footer_logo_preview: null,

            handleLogoChange(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.logo_preview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },

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

