<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Email Builder - {{ $emailTemplate->nom }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS (via CDN for builder page wrapper) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>

    <!-- GrapesJS Styles -->
    <link rel="stylesheet" href="https://unpkg.com/grapesjs/dist/css/grapes.min.css">
    <link rel="stylesheet" href="https://unpkg.com/grapesjs-preset-newsletter/dist/grapesjs-preset-newsletter.min.css">

    <style>
        /* Modern styling overrides for GrapesJS UI */
        .gjs-one-bg {
            background-color: #ffffff;
        }
        .gjs-two-color {
            color: #475569;
        }
        .gjs-three-bg {
            background-color: #4f46e5 !important;
            color: #ffffff !important;
        }
        .gjs-four-color, .gjs-four-color-h:hover {
            color: #4f46e5;
        }
        .gjs-pn-views-container {
            height: 100%;
            padding: 0;
            border-left: 1px solid #e2e8f0;
            box-shadow: none;
        }
        .gjs-cv-canvas {
            width: 100%;
            height: 100%;
            top: 0;
        }
        .gjs-block {
            width: 100% !important;
            min-height: auto !important;
            padding: 12px !important;
            margin: 0 0 10px 0 !important;
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            color: #334155 !important;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: 12px !important;
            text-align: left !important;
        }
        .gjs-block:hover {
            border-color: #6366f1 !important;
            background-color: #f5f3ff !important;
            color: #4f46e5 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
        }
        .gjs-block-label {
            font-size: 13px !important;
            font-weight: 600 !important;
        }
        .gjs-block svg, .gjs-block i {
            width: 24px;
            font-size: 18px;
            color: #64748b;
            flex-shrink: 0;
            text-align: center;
        }
        .gjs-block:hover svg, .gjs-block:hover i {
            color: #4f46e5;
        }
        /* Custom layout structures */
        .editor-container {
            height: calc(100vh - 64px);
        }
        .gjs-mdl-dialog {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }
    </style>
</head>
<body class="h-full overflow-hidden flex flex-col font-sans antialiased text-slate-800">

    <!-- HEADER -->
    <header class="h-16 bg-[#101d2f] text-white px-6 flex items-center justify-between border-b border-slate-800 shrink-0 z-30 shadow-md">
        <div class="flex items-center gap-4">
            <a href="{{ route('email-templates.index') }}" class="p-2 hover:bg-slate-800 rounded-xl transition duration-150 text-slate-400 hover:text-white" title="Retour aux templates">
                <i class="fa-solid fa-arrow-left text-lg"></i>
            </a>
            <div class="h-8 w-px bg-slate-800"></div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase bg-lime-400 text-slate-900 rounded-md tracking-wider">Visual Builder</span>
                    <h1 class="text-base font-bold text-white tracking-tight">{{ $emailTemplate->nom }}</h1>
                </div>
                <p class="text-[11px] text-slate-400">Type: <span class="capitalize font-semibold text-slate-300">{{ $emailTemplate->type }}</span></p>
            </div>
        </div>

        <!-- RESPONSIVE DEVICE SWITCHER -->
        <div class="hidden md:flex items-center bg-slate-800/80 p-1 rounded-xl border border-slate-700/50">
            <button id="btn-device-desktop" class="px-3.5 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition bg-indigo-600 text-white shadow-sm" onclick="setDevice('desktop')">
                <i class="fa-solid fa-desktop"></i>
                <span>Bureau</span>
            </button>
            <button id="btn-device-tablet" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 text-slate-400 hover:text-slate-200 transition" onclick="setDevice('tablet')">
                <i class="fa-solid fa-tablet-screen-button"></i>
                <span>Tablette</span>
            </button>
            <button id="btn-device-mobile" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 text-slate-400 hover:text-slate-200 transition" onclick="setDevice('mobile')">
                <i class="fa-solid fa-mobile-screen-button"></i>
                <span>Mobile</span>
            </button>
        </div>

        <!-- ACTIONS & AUTOSAVE -->
        <div class="flex items-center gap-3">
            <!-- Autosave Indicator -->
            <div id="autosave-status" class="flex items-center gap-2 text-xs font-medium text-slate-400 pr-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 shadow shadow-emerald-400 animate-pulse"></span>
                <span>Modifications enregistrées</span>
            </div>

            <!-- Undo/Redo -->
            <div class="flex items-center bg-slate-800/60 p-1 rounded-xl border border-slate-700/50 text-slate-400">
                <button onclick="undo()" class="p-1.5 hover:text-white hover:bg-slate-700 rounded-lg transition" title="Annuler (Ctrl+Z)">
                    <i class="fa-solid fa-rotate-left"></i>
                </button>
                <button onclick="redo()" class="p-1.5 hover:text-white hover:bg-slate-700 rounded-lg transition" title="Rétablir (Ctrl+Y)">
                    <i class="fa-solid fa-rotate-right"></i>
                </button>
            </div>

            <button onclick="openPreviewModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700/80 text-xs font-bold text-slate-200 hover:text-white rounded-xl transition flex items-center gap-1.5">
                <i class="fa-solid fa-eye"></i>
                <span>Prévisualiser</span>
            </button>

            <button onclick="exportHtml()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700/80 text-xs font-bold text-slate-200 hover:text-white rounded-xl transition flex items-center gap-1.5">
                <i class="fa-solid fa-code"></i>
                <span>Code HTML</span>
            </button>

            <button onclick="saveTemplate(true)" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-xs font-bold text-white rounded-xl shadow-md shadow-indigo-900/30 transition flex items-center gap-1.5">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Enregistrer</span>
            </button>
        </div>
    </header>

    <!-- WRAPPER -->
    <div class="flex editor-container overflow-hidden">

        <!-- LEFT SIDEBAR: BLOCKS -->
        <aside class="w-80 border-r border-slate-200 bg-white flex flex-col shrink-0 z-20">
            <!-- Tabs Header -->
            <div class="flex border-b border-slate-100 bg-slate-50/50 p-2 shrink-0">
                <button id="tab-blocks" class="flex-1 text-center py-2 text-xs font-bold text-indigo-600 border-b-2 border-indigo-600 transition">
                    <i class="fa-solid fa-cubes mr-1.5"></i> Blocs
                </button>
                <button id="tab-variables" class="flex-1 text-center py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition" onclick="switchLeftTab('variables')">
                    <i class="fa-solid fa-bolt mr-1.5"></i> Variables
                </button>
            </div>

            <!-- TAB CONTENT: BLOCKS LIST -->
            <div id="left-tab-content-blocks" class="flex-grow overflow-y-auto p-4 space-y-4">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" id="block-search" oninput="filterBlocks()" placeholder="Rechercher un bloc..." class="w-full text-xs pl-8 pr-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Custom Blocks Category -->
                <div>
                    <h3 class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-lime-500"></span>
                        Composants Spécifiques CAEI
                    </h3>
                    <div id="caei-blocks-list" class="space-y-2">
                        <!-- Custom CAEI Blocks dynamically loaded -->
                    </div>
                </div>

                <!-- Basic Blocks Category -->
                <div class="pt-2">
                    <h3 class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                        Éléments de Base
                    </h3>
                    <div id="gjs-blocks" class="space-y-2">
                        <!-- Default GrapesJS blocks will be placed here -->
                    </div>
                </div>
            </div>

            <!-- TAB CONTENT: VARIABLES REFERENCE -->
            <div id="left-tab-content-variables" class="hidden flex-grow overflow-y-auto p-4 space-y-3">
                <p class="text-xs text-slate-500 leading-relaxed">
                    Cliquez sur une variable ci-dessous pour la copier dans votre presse-papier et collez-la dans n'importe quel bloc texte de l'éditeur :
                </p>
                <div class="space-y-2.5 max-h-[500px] overflow-y-auto pr-1">
                    @foreach($variables as $variable => $description)
                        <div class="bg-slate-50 border border-slate-200 hover:border-indigo-300 p-3 rounded-xl transition duration-150 flex flex-col gap-1.5 group cursor-pointer" onclick="copyToClipboard('{{ $variable }}', this)">
                            <div class="flex items-center justify-between">
                                <code class="font-mono text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-100 font-bold text-[11px] group-hover:bg-indigo-600 group-hover:text-white transition">
                                    {{ $variable }}
                                </code>
                                <span class="text-[10px] text-slate-400 group-hover:text-indigo-600 transition font-semibold flex items-center gap-1">
                                    <i class="fa-regular fa-copy"></i>
                                    <span>Copier</span>
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-500 font-medium">
                                {{ $description }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>

        <!-- CENTER: CANVAS -->
        <main class="flex-1 bg-slate-100 flex flex-col overflow-hidden relative">
            <div id="canvas-wrapper" class="flex-grow p-6 flex justify-center items-center overflow-auto transition-all duration-300">
                <div id="gjs-container" class="w-full h-full bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-200/60 overflow-hidden transition-all duration-300">
                    <div id="gjs" class="h-full"></div>
                </div>
            </div>
        </main>

        <!-- RIGHT SIDEBAR: PROPERTIES / STYLES -->
        <aside class="w-80 border-l border-slate-200 bg-white flex flex-col shrink-0 z-20 overflow-hidden">
            <!-- Right Tabs Header -->
            <div class="flex border-b border-slate-100 bg-slate-50/50 p-2 shrink-0 text-xs font-bold text-slate-500">
                <button id="btn-tab-styles" class="flex-1 text-center py-2 text-indigo-600 border-b-2 border-indigo-600" onclick="switchRightTab('styles')">
                    <i class="fa-solid fa-paintbrush mr-1"></i> Style
                </button>
                <button id="btn-tab-traits" class="flex-1 text-center py-2 hover:text-slate-800" onclick="switchRightTab('traits')">
                    <i class="fa-solid fa-sliders mr-1"></i> Propriétés
                </button>
                <button id="btn-tab-layers" class="flex-1 text-center py-2 hover:text-slate-800" onclick="switchRightTab('layers')">
                    <i class="fa-solid fa-layer-group mr-1"></i> Calques
                </button>
            </div>

            <!-- Style Manager Panel -->
            <div id="right-panel-styles" class="flex-grow overflow-y-auto p-4">
                <div class="styles-container"></div>
            </div>

            <!-- Trait Manager (Properties) Panel -->
            <div id="right-panel-traits" class="hidden flex-grow overflow-y-auto p-4">
                <div class="traits-container"></div>
            </div>

            <!-- Layer Manager Panel -->
            <div id="right-panel-layers" class="hidden flex-grow overflow-y-auto p-4">
                <div class="layers-container"></div>
            </div>
        </aside>
    </div>

    <!-- PREVIEW MODAL -->
    <div id="preview-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-4xl h-[85vh] rounded-2xl shadow-2xl flex flex-col overflow-hidden">
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <h3 class="font-bold text-sm">Aperçu Réel de l'Email</h3>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Responsive Simulation in Modal -->
                    <div class="flex items-center bg-slate-800 p-0.5 rounded-lg border border-slate-700">
                        <button class="px-3 py-1 bg-indigo-600 text-white text-xs font-bold rounded-md" onclick="setModalPreviewDevice('desktop', this)">
                            <i class="fa-solid fa-desktop mr-1"></i> Desktop
                        </button>
                        <button class="px-3 py-1 text-slate-400 hover:text-white text-xs font-semibold rounded-md" onclick="setModalPreviewDevice('tablet', this)">
                            <i class="fa-solid fa-tablet-screen-button mr-1"></i> Tablette
                        </button>
                        <button class="px-3 py-1 text-slate-400 hover:text-white text-xs font-semibold rounded-md" onclick="setModalPreviewDevice('mobile', this)">
                            <i class="fa-solid fa-mobile-screen-button mr-1"></i> Mobile
                        </button>
                    </div>
                    <button onclick="closePreviewModal()" class="text-slate-400 hover:text-white transition">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
            </div>
            <!-- Modal Body (Iframe) -->
            <div class="flex-grow bg-slate-100 p-6 flex justify-center overflow-auto">
                <div id="modal-preview-frame-wrapper" class="w-full h-full max-w-full transition-all duration-300">
                    <iframe id="modal-preview-iframe" class="w-full h-full bg-white rounded-xl shadow-lg border border-slate-200"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- CODE EDITOR DRAWER -->
    <div id="code-drawer" class="fixed inset-y-0 right-0 w-[600px] bg-slate-900 text-slate-300 z-40 hidden flex flex-col shadow-2xl transition-all duration-300 border-l border-slate-800">
        <div class="px-6 h-16 border-b border-slate-800 flex items-center justify-between shrink-0">
            <h3 class="font-bold text-sm text-white flex items-center gap-2">
                <i class="fa-solid fa-code text-indigo-400"></i>
                Éditeur de Code HTML
            </h3>
            <button onclick="closeCodeDrawer()" class="text-slate-400 hover:text-white transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <div class="flex-grow p-6 flex flex-col gap-3">
            <p class="text-xs text-slate-400 leading-relaxed">
                Modifiez directement le code HTML généré ci-dessous. Cliquez sur "Appliquer" pour synchroniser vos modifications avec l'éditeur visuel.
            </p>
            <textarea id="html-code-editor" class="flex-grow w-full rounded-xl bg-slate-950 border border-slate-800 text-emerald-400 font-mono text-xs p-5 focus:outline-none focus:ring-1 focus:ring-indigo-500 leading-relaxed overflow-auto resize-none"></textarea>
        </div>
        <div class="px-6 py-4 bg-slate-950 border-t border-slate-800 flex items-center justify-end gap-3 shrink-0">
            <button onclick="closeCodeDrawer()" class="px-4 py-2 text-slate-400 hover:text-white text-xs font-semibold">Annuler</button>
            <button onclick="applyHtmlCode()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-950/40 transition">
                Appliquer les modifications
            </button>
        </div>
    </div>

    <!-- Toast Toast Notification -->
    <div id="toast-message" class="fixed bottom-6 right-6 bg-slate-900 text-white text-xs font-bold px-4 py-3 rounded-2xl shadow-xl flex items-center gap-2.5 z-50 transform translate-y-20 opacity-0 transition duration-300">
        <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>
        <span id="toast-text">Message</span>
    </div>

    <!-- GrapesJS Scripts -->
    <script src="https://unpkg.com/grapesjs"></script>
    <script src="https://unpkg.com/grapesjs-preset-newsletter"></script>

    <script>
        // Init variables and GrapesJS
        let editor;
        const initialHtml = {!! json_encode($emailTemplate->contenu, JSON_UNESCAPED_UNICODE) !!};
        const initialGjsData = {!! json_encode($emailTemplate->gjs_data, JSON_UNESCAPED_UNICODE) !!};
        const templateId = "{{ $emailTemplate->id }}";
        const saveUrl = "{{ route('email-templates.builder.save', $emailTemplate) }}";

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Editor
            editor = grapesjs.init({
                container: '#gjs',
                fromElement: false,
                height: '100%',
                storageManager: false,
                assetManager: {
                    upload: '{{ route("email-templates.upload-image") }}',
                    params: {
                        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    uploadName: 'files',
                    autoAdd: true,
                },
                deviceManager: {
                    devices: [
                        { name: 'desktop', label: 'Ordinateur', width: '' },
                        { name: 'tablet', label: 'Tablette', width: '768px', widthMedia: '768px' },
                        { name: 'mobile', label: 'Mobile', width: '320px', widthMedia: '480px' },
                    ]
                },
                plugins: ['gjs-preset-newsletter'],
                pluginsOpts: {
                    'gjs-preset-newsletter': {
                        inlineCss: true,
                        cellStyle: {
                            'font-family': 'Helvetica, Arial, sans-serif',
                            'color': '#333333',
                        }
                    }
                }
            });

            // Set content
            if (initialGjsData && Object.keys(initialGjsData).length > 0) {
                editor.loadProjectData(initialGjsData);
            } else if (initialHtml && initialHtml.trim() !== '') {
                editor.setComponents(initialHtml);
            } else {
                // Default empty newsletter template structure
                editor.setComponents(`
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f1f5f9; padding: 30px 10px;">
                        <tr>
                            <td align="center">
                                <table width="600" border="0" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                                    <tr>
                                        <td align="center" style="padding: 30px; border-bottom: 1px solid #f1f5f9;">
                                            <!-- CAEI LOGO HEADER -->
                                            <table border="0" cellspacing="0" cellpadding="0" style="background-color: #101d2f; padding: 12px 24px; border-radius: 12px;">
                                                <tr>
                                                    <td style="background-color: #d9f99d; color: #0f172a; border-radius: 50%; width: 36px; height: 36px; text-align: center; font-family: Arial, sans-serif; font-weight: 900; font-size: 13px; line-height: 36px; vertical-align: middle;">CAEI</td>
                                                    <td style="padding-left: 12px; font-family: Arial, sans-serif; text-align: left;">
                                                        <div style="font-weight: bold; font-size: 18px; color: #ffffff; line-height: 1.1;">CAEI</div>
                                                        <div style="font-size: 9px; font-weight: bold; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Codicil - Audit - Formation</div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 40px 30px; font-family: Helvetica, Arial, sans-serif; font-size: 14px; color: #334155; line-height: 1.6;">
                                            <h2 style="font-size: 20px; font-weight: bold; color: #0f172a; margin-top: 0; margin-bottom: 20px;">Bonjour @{{prenom}} @{{nom}},</h2>
                                            <p style="margin-bottom: 20px;">Bienvenue sur notre plateforme de communication visuelle. Vous pouvez commencer à glisser-déposer des blocs et configurer ce modèle d'email selon vos besoins.</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                `);
            }

            // Custom Layout Settings GrapesJS
            const styleManager = editor.StyleManager;
            const styleContainer = document.querySelector('.styles-container');
            styleContainer.appendChild(styleManager.render());

            const traitManager = editor.TraitManager;
            const traitContainer = document.querySelector('.traits-container');
            traitContainer.appendChild(traitManager.render());

            const layerManager = editor.LayerManager;
            const layerContainer = document.querySelector('.layers-container');
            layerContainer.appendChild(layerManager.render());

            // Build GrapesJS Blocks & categories
            const blockManager = editor.BlockManager;
            
            // Render basic blocks into specific container
            const basicBlocksContainer = document.getElementById('gjs-blocks');
            basicBlocksContainer.appendChild(blockManager.render(
                blockManager.getAll().filter(b => !b.get('id').startsWith('caei-'))
            ));

            // Custom CAEI Specific Blocks
            registerCaeiBlocks(blockManager);

            // Hide GrapesJS default panels that are replaced by our custom UI
            const pn = editor.Panels;
            pn.getPanel('views').set('visible', false);
            pn.getPanel('options').set('visible', false);

            // Handle Autosave debounced
            let autosaveTimer = null;
            editor.on('component:update', () => triggerAutosave());
            editor.on('style:update', () => triggerAutosave());

            function triggerAutosave() {
                setAutosaveStatus('saving');
                clearTimeout(autosaveTimer);
                autosaveTimer = setTimeout(() => {
                    saveTemplate(false);
                }, 4000); // Autosave after 4 seconds of idle
            }
        });

        // Custom CAEI Blocks
        function registerCaeiBlocks(bm) {
            const listContainer = document.getElementById('caei-blocks-list');

            // 1. Logo CAEI Block
            bm.add('caei-logo', {
                label: 'Logo CAEI',
                category: 'Composants CAEI',
                content: `
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin: 15px 0;">
                        <tr>
                            <td align="center">
                                <table border="0" cellspacing="0" cellpadding="0" style="background-color: #101d2f; padding: 12px 24px; border-radius: 12px;">
                                    <tr>
                                        <td style="background-color: #d9f99d; color: #0f172a; border-radius: 50%; width: 36px; height: 36px; text-align: center; font-family: Arial, sans-serif; font-weight: 900; font-size: 13px; line-height: 36px; vertical-align: middle;">CAEI</td>
                                        <td style="padding-left: 12px; font-family: Arial, sans-serif; text-align: left;">
                                            <div style="font-weight: bold; font-size: 18px; color: #ffffff; line-height: 1.1;">CAEI</div>
                                            <div style="font-size: 9px; font-weight: bold; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Codicil - Audit - Formation</div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                `,
                attributes: { class: 'gjs-block' }
            });

            // 2. CTA Button Block
            bm.add('caei-button', {
                label: 'Bouton CTA',
                category: 'Composants CAEI',
                content: `
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin: 15px 0;">
                        <tr>
                            <td align="center">
                                <table border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td align="center" style="border-radius: 8px;" bgcolor="#4f46e5">
                                            <a href="https://caei.org" target="_blank" style="font-size: 14px; font-family: Arial, sans-serif; color: #ffffff; text-decoration: none; border-radius: 8px; padding: 12px 24px; border: 1px solid #4f46e5; display: inline-block; font-weight: bold;">Cliquez ici</a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                `,
                attributes: { class: 'gjs-block' }
            });

            // 3. Social Links Block
            bm.add('caei-socials', {
                label: 'Liens Sociaux',
                category: 'Composants CAEI',
                content: `
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin: 15px 0; text-align: center;">
                        <tr>
                            <td align="center">
                                <table border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="padding: 0 10px;">
                                            <a href="https://linkedin.com" target="_blank" style="text-decoration: none; color: #4f46e5; font-family: Arial, sans-serif; font-size: 12px; font-weight: bold;">LinkedIn</a>
                                        </td>
                                        <td style="padding: 0 10px; color: #cbd5e1;">|</td>
                                        <td style="padding: 0 10px;">
                                            <a href="https://caei.org" target="_blank" style="text-decoration: none; color: #4f46e5; font-family: Arial, sans-serif; font-size: 12px; font-weight: bold;">Site CAEI</a>
                                        </td>
                                        <td style="padding: 0 10px; color: #cbd5e1;">|</td>
                                        <td style="padding: 0 10px;">
                                            <a href="mailto:contact@caei.org" style="text-decoration: none; color: #4f46e5; font-family: Arial, sans-serif; font-size: 12px; font-weight: bold;">Contact</a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                `,
                attributes: { class: 'gjs-block' }
            });

            // 4. Signature Block
            bm.add('caei-signature', {
                label: 'Signature',
                category: 'Composants CAEI',
                content: `
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin: 20px 0; border-top: 1px solid #e2e8f0; padding-top: 15px; text-align: left;">
                        <tr>
                            <td valign="top" width="50" style="padding-right: 15px;">
                                <table border="0" cellspacing="0" cellpadding="0" style="background-color: #101d2f; border-radius: 50%; width: 44px; height: 44px; text-align: center;">
                                    <tr>
                                        <td style="color: #d9f99d; font-family: Arial, sans-serif; font-weight: 900; font-size: 12px; line-height: 44px;">CAEI</td>
                                    </tr>
                                </table>
                            </td>
                            <td valign="top" style="font-family: Arial, sans-serif; font-size: 13px; color: #475569; line-height: 1.4;">
                                <div style="font-weight: bold; font-size: 14px; color: #0f172a;">Amel Ben Ali</div>
                                <div style="font-style: italic; color: #64748b; margin-bottom: 4px;">Directrice Formation - CAEI</div>
                                <div style="font-size: 12px;">Email: <a href="mailto:a.benali@caei.org" style="color: #4f46e5; text-decoration: none;">a.benali@caei.org</a></div>
                                <div style="font-size: 12px;">Tél: +216 71 000 000</div>
                            </td>
                        </tr>
                    </table>
                `,
                attributes: { class: 'gjs-block' }
            });

            // 5. Attachments Block
            bm.add('caei-attachment', {
                label: 'Document/Fichier',
                category: 'Composants CAEI',
                content: `
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin: 15px 0;">
                        <tr>
                            <td align="center">
                                <table width="90%" border="0" cellspacing="0" cellpadding="0" style="border: 2px dashed #cbd5e1; border-radius: 12px; background-color: #f8fafc; padding: 15px;">
                                    <tr>
                                        <td width="40" align="center" style="font-size: 24px; color: #64748b;">📄</td>
                                        <td style="padding-left: 10px; text-align: left; font-family: Arial, sans-serif;">
                                            <div style="font-weight: bold; font-size: 13px; color: #334155;">Brochure_CAEI_2026.pdf</div>
                                            <div style="font-size: 11px; color: #64748b;">Document PDF - 2.4 Mo</div>
                                        </td>
                                        <td align="right">
                                            <a href="https://caei.org" target="_blank" style="display: inline-block; font-size: 11px; font-family: Arial, sans-serif; color: #ffffff; background-color: #0f172a; text-decoration: none; padding: 8px 16px; border-radius: 6px; font-weight: bold;">Télécharger</a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                `,
                attributes: { class: 'gjs-block' }
            });

            // Add icons or logos in sidebar representation for these blocks
            setTimeout(() => {
                const elements = [
                    { id: 'caei-logo', icon: 'fa-solid fa-ribbon' },
                    { id: 'caei-button', icon: 'fa-solid fa-rectangle-ad' },
                    { id: 'caei-socials', icon: 'fa-solid fa-share-nodes' },
                    { id: 'caei-signature', icon: 'fa-solid fa-signature' },
                    { id: 'caei-attachment', icon: 'fa-solid fa-file-arrow-down' }
                ];
                elements.forEach(item => {
                    const blockObj = bm.get(item.id);
                    if (blockObj) {
                        const el = document.getElementById(item.id) || document.querySelector(`[data-id="${item.id}"]`);
                        if (el) {
                            el.innerHTML = `<i class="${item.icon} text-indigo-500"></i><span class="gjs-block-label">${blockObj.get('label')}</span>`;
                        }
                    }
                });

                // Move them to custom CAEI category container in DOM
                const list = bm.getAll().filter(b => b.get('id').startsWith('caei-'));
                const caeiContainer = document.getElementById('caei-blocks-list');
                list.forEach(b => {
                    const blockEl = document.querySelector(`[data-id="${b.get('id')}"]`);
                    if (blockEl) {
                        caeiContainer.appendChild(blockEl);
                    }
                });
            }, 100);
        }

        // Layout actions
        function setDevice(device) {
            const wrapper = document.getElementById('canvas-wrapper');
            const gjsContainer = document.getElementById('gjs-container');
            const buttons = ['desktop', 'tablet', 'mobile'];
            
            buttons.forEach(d => {
                const btn = document.getElementById(`btn-device-${d}`);
                if (d === device) {
                    btn.classList.add('bg-indigo-600', 'text-white', 'shadow-sm');
                    btn.classList.remove('text-slate-400', 'hover:text-slate-200');
                } else {
                    btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-sm');
                    btn.classList.add('text-slate-400', 'hover:text-slate-200');
                }
            });

            // Change GrapesJS Device
            editor.setDevice(device);

            // Change CSS wrapper dimensions to simulate the devices
            if (device === 'mobile') {
                gjsContainer.style.maxWidth = '375px';
            } else if (device === 'tablet') {
                gjsContainer.style.maxWidth = '768px';
            } else {
                gjsContainer.style.maxWidth = '100%';
            }
        }

        // Undo/Redo
        function undo() {
            editor.runCommand('core:undo');
        }
        function redo() {
            editor.runCommand('core:redo');
        }

        // Left sidebar switching
        function switchLeftTab(tab) {
            const bBtn = document.getElementById('tab-blocks');
            const vBtn = document.getElementById('tab-variables');
            const bCont = document.getElementById('left-tab-content-blocks');
            const vCont = document.getElementById('left-tab-content-variables');

            if (tab === 'blocks') {
                bBtn.className = "flex-grow text-center py-2 text-xs font-bold text-indigo-600 border-b-2 border-indigo-600 transition";
                vBtn.className = "flex-grow text-center py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition";
                bCont.classList.remove('hidden');
                vCont.classList.add('hidden');
            } else {
                bBtn.className = "flex-grow text-center py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition";
                vBtn.className = "flex-grow text-center py-2 text-xs font-bold text-indigo-600 border-b-2 border-indigo-600 transition";
                bCont.classList.add('hidden');
                vCont.classList.remove('hidden');
            }
        }
        document.getElementById('tab-blocks').addEventListener('click', () => switchLeftTab('blocks'));

        // Right sidebar tabs
        function switchRightTab(tab) {
            const tabs = ['styles', 'traits', 'layers'];
            tabs.forEach(t => {
                const btn = document.getElementById(`btn-tab-${t}`);
                const panel = document.getElementById(`right-panel-${t}`);
                if (t === tab) {
                    btn.className = "flex-1 text-center py-2 text-indigo-600 border-b-2 border-indigo-600";
                    panel.classList.remove('hidden');
                } else {
                    btn.className = "flex-1 text-center py-2 hover:text-slate-800";
                    panel.classList.add('hidden');
                }
            });
        }

        // Filter Blocks search
        function filterBlocks() {
            const q = document.getElementById('block-search').value.toLowerCase();
            const blocks = document.querySelectorAll('.gjs-block');
            blocks.forEach(b => {
                const txt = b.textContent.toLowerCase();
                if (txt.includes(q)) {
                    b.style.display = 'flex';
                } else {
                    b.style.display = 'none';
                }
            });
        }

        // Save Template Ajax
        function saveTemplate(showNotification = false) {
            if (showNotification) {
                setAutosaveStatus('saving');
            }
            
            const html = editor.runCommand('gjs-get-inlined-html') || editor.getHtml();
            const gjs_data = editor.getProjectData();

            fetch(saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    gjs_data: JSON.stringify(gjs_data),
                    html: html
                })
            })
            .then(res => {
                if (!res.ok) throw new Error('Erreur serveur lors de la sauvegarde.');
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    setAutosaveStatus('saved');
                    if (showNotification) {
                        showToast("Template enregistré avec succès !");
                    }
                } else {
                    setAutosaveStatus('error');
                    if (showNotification) {
                        showToast("Erreur lors de la sauvegarde.", true);
                    }
                }
            })
            .catch(err => {
                console.error(err);
                setAutosaveStatus('error');
                if (showNotification) {
                    showToast("Erreur réseau.", true);
                }
            });
        }

        // Autosave status manager
        function setAutosaveStatus(status) {
            const dot = document.querySelector('#autosave-status span');
            const text = document.querySelector('#autosave-status span:nth-child(2)');
            
            if (status === 'saving') {
                dot.className = "w-2 h-2 rounded-full bg-amber-500 shadow shadow-amber-400 animate-pulse";
                text.textContent = "Sauvegarde en cours...";
            } else if (status === 'saved') {
                dot.className = "w-2 h-2 rounded-full bg-emerald-500 shadow shadow-emerald-400";
                text.textContent = "Modifications enregistrées";
            } else if (status === 'error') {
                dot.className = "w-2 h-2 rounded-full bg-rose-500 shadow shadow-rose-400";
                text.textContent = "Erreur de sauvegarde";
            }
        }

        // HTML direct editing Drawer
        function exportHtml() {
            const html = editor.runCommand('gjs-get-inlined-html') || editor.getHtml();
            document.getElementById('html-code-editor').value = html;
            document.getElementById('code-drawer').classList.remove('hidden');
        }
        function closeCodeDrawer() {
            document.getElementById('code-drawer').classList.add('hidden');
        }
        function applyHtmlCode() {
            const html = document.getElementById('html-code-editor').value;
            editor.setComponents(html);
            closeCodeDrawer();
            showToast("Modifications HTML appliquées avec succès !");
            saveTemplate(false);
        }

        // Preview Modal Manager
        function openPreviewModal() {
            const modal = document.getElementById('preview-modal');
            const iframe = document.getElementById('modal-preview-iframe');
            
            // Get inline HTML
            let html = editor.runCommand('gjs-get-inlined-html') || editor.getHtml();
            
            // Inject Test Values
            const renderedPreview = html
                .replace(/\{\{\s*nom\s*\}\}/g, 'Ben Ali')
                .replace(/\{\{\s*prenom\s*\}\}/g, 'Amel')
                .replace(/\{\{\s*entreprise\s*\}\}/g, 'CAEI Partner')
                .replace(/\{\{\s*fonction\s*\}\}/g, 'Directrice Formation')
                .replace(/\{\{\s*pays\s*\}\}/g, 'Tunisie')
                .replace(/\{\{\s*nom_seminaire\s*\}\}/g, 'Séminaire Audit & Gouvernance')
                .replace(/\{\{\s*date\s*\}\}/g, '30/07/2026')
                .replace(/\{\{\s*lien\s*\}\}/g, 'https://caei.org');

            modal.classList.remove('hidden');
            
            // Write to iframe document
            const doc = iframe.contentDocument || iframe.contentWindow.document;
            doc.open();
            doc.write(renderedPreview);
            doc.close();
        }

        function closePreviewModal() {
            document.getElementById('preview-modal').classList.add('hidden');
        }

        function setModalPreviewDevice(device, button) {
            const wrapper = document.getElementById('modal-preview-frame-wrapper');
            const buttons = button.parentNode.querySelectorAll('button');
            
            buttons.forEach(btn => {
                btn.className = "px-3 py-1 text-slate-400 hover:text-white text-xs font-semibold rounded-md";
            });
            button.className = "px-3 py-1 bg-indigo-600 text-white text-xs font-bold rounded-md";

            if (device === 'mobile') {
                wrapper.style.maxWidth = '375px';
            } else if (device === 'tablet') {
                wrapper.style.maxWidth = '768px';
            } else {
                wrapper.style.maxWidth = '100%';
            }
        }

        // Clipboard Copy Helper
        function copyToClipboard(text, element) {
            navigator.clipboard.writeText(text).then(() => {
                const label = element.querySelector('span span');
                const orig = label.textContent;
                label.textContent = "Copié !";
                element.classList.add('border-emerald-400', 'bg-emerald-50/50');
                
                setTimeout(() => {
                    label.textContent = orig;
                    element.classList.remove('border-emerald-400', 'bg-emerald-50/50');
                }, 1500);
            });
        }

        // Notification Toast Manager
        function showToast(msg, isError = false) {
            const toast = document.getElementById('toast-message');
            const text = document.getElementById('toast-text');
            
            text.textContent = msg;
            
            if (isError) {
                toast.querySelector('i').className = "fa-solid fa-circle-xmark text-rose-400 text-sm";
            } else {
                toast.querySelector('i').className = "fa-solid fa-circle-check text-emerald-400 text-sm";
            }
            
            toast.classList.remove('translate-y-20', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
            
            setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 3000);
        }
    </script>
</body>
</html>
