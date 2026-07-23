<x-app-layout>
    <div class="p-6 space-y-6" x-data="{ viewMode: localStorage.getItem('email_template_view') || 'grid' }">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3.5 bg-gradient-to-br from-indigo-500 to-blue-600 text-white rounded-2xl shadow-md shadow-indigo-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Modèles d'emails</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Créez, personnalisez et réutilisez vos templates pour vos campagnes de communication</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('email-templates.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-sm transition-all duration-150 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Nouveau template</span>
                </a>
                <form action="{{ route('email-templates.install-defaults') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold px-4 py-2.5 rounded-xl border border-slate-200/80 transition duration-150">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Exemples par défaut</span>
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

        <!-- Quick Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm flex items-center gap-3.5">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-slate-900">{{ $stats['total'] ?? 0 }}</div>
                    <div class="text-xs font-medium text-slate-500">Total templates</div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm flex items-center gap-3.5">
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-slate-900">{{ $stats['active'] ?? 0 }}</div>
                    <div class="text-xs font-medium text-slate-500">Templates actifs</div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm flex items-center gap-3.5">
                <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-slate-900">{{ $stats['newsletter'] ?? 0 }}</div>
                    <div class="text-xs font-medium text-slate-500">Newsletters</div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm flex items-center gap-3.5">
                <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-slate-900">{{ $stats['promotion'] ?? 0 }}</div>
                    <div class="text-xs font-medium text-slate-500">Promotions / Invit.</div>
                </div>
            </div>
        </div>

        <!-- Filters & Toolbar -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
            <form method="GET" action="{{ route('email-templates.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <!-- Search input -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par nom, objet, contenu..." class="w-full text-sm pl-9 pr-4 py-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50">
                    </div>

                    <!-- Type filter -->
                    <div>
                        <select name="type" onchange="this.form.submit()" class="w-full text-sm py-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50 text-slate-700">
                            <option value="">Tous les types</option>
                            @foreach($types as $value => $label)
                                <option value="{{ $value }}" {{ request('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status filter -->
                    <div>
                        <select name="status" onchange="this.form.submit()" class="w-full text-sm py-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50 text-slate-700">
                            <option value="">Tous les statuts</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actifs uniquement</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactifs uniquement</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between lg:justify-end gap-3 border-t lg:border-t-0 pt-3 lg:pt-0 border-slate-100">
                    <div class="flex items-center gap-2">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition shadow-sm">
                            Filtrer
                        </button>
                        @if(request()->hasAny(['search', 'type', 'status']))
                            <a href="{{ route('email-templates.index') }}" class="px-3 py-2 text-slate-500 hover:text-slate-700 text-sm font-semibold flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Réinitialiser
                            </a>
                        @endif
                    </div>

                    <!-- Layout Toggle (Grid vs Table) -->
                    <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200/70">
                        <button type="button" @click="viewMode = 'grid'; localStorage.setItem('email_template_view', 'grid')" :class="viewMode === 'grid' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="p-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            <span class="hidden sm:inline">Grille</span>
                        </button>
                        <button type="button" @click="viewMode = 'table'; localStorage.setItem('email_template_view', 'table')" :class="viewMode === 'table' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="p-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                            <span class="hidden sm:inline">Tableau</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- GRID VIEW -->
        <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($templates as $template)
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition duration-200 overflow-hidden flex flex-col justify-between group">
                    <div>
                        <!-- Card Header Badge & Actions -->
                        <div class="p-5 border-b border-slate-100 flex items-center justify-between gap-2 bg-slate-50/50">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 11h10M7 15h10"/>
                                    </svg>
                                    {{ $template->type_label }}
                                </span>
                                @if($template->gjs_data)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200" title="Modèle créé avec l'éditeur visuel">
                                        <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                        </svg>
                                        Visuel
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-2">
                                <form action="{{ route('email-templates.toggle', $template) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" title="Cliquez pour changer le statut" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold transition cursor-pointer {{ $template->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $template->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                                        <span>{{ $template->is_active ? 'Actif' : 'Inactif' }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 space-y-3">
                            <div>
                                <h3 class="text-base font-bold text-slate-900 group-hover:text-indigo-600 transition truncate" title="{{ $template->nom }}">
                                    {{ $template->nom }}
                                </h3>
                                @if($template->sujet)
                                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-1 truncate" title="{{ $template->sujet }}">
                                        <span class="font-semibold text-slate-700 shrink-0">Objet :</span>
                                        <span class="truncate">{{ $template->sujet }}</span>
                                    </p>
                                @endif
                            </div>

                            <!-- Content Preview Box -->
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/60 text-xs text-slate-600 font-sans line-clamp-3 min-h-[64px] leading-relaxed">
                                {{ \Illuminate\Support\Str::limit(strip_tags($template->contenu), 120) }}
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer Actions -->
                    <div class="p-4 bg-slate-50/70 border-t border-slate-100 flex items-center justify-between gap-2">
                        <div>
                            @if($template->is_active)
                                <a href="{{ route('campaigns.create', ['template_id' => $template->id]) }}" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg shadow-sm transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                    Utiliser
                                </a>
                            @else
                                <span class="text-xs text-slate-400 font-medium">Inactif</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-1">
                            <a href="{{ route('email-templates.preview', $template) }}" class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Aperçu du rendu">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>

                            <!-- Visual Builder Link -->
                            <a href="{{ route('email-templates.builder', $template) }}" class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Éditeur Visuel (Drag & Drop)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                            </a>

                            <a href="{{ route('email-templates.edit', $template) }}" class="p-2 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Modifier le template">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>

                            <form action="{{ route('email-templates.duplicate', $template) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-2 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Dupliquer ce template">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            </form>

                            <form action="{{ route('email-templates.destroy', $template) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce template ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white p-12 text-center rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                    <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Aucun modèle d'email trouvé</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">Créez votre premier modèle d'email ou installez les exemples fournis par défaut pour débuter rapidement.</p>
                    <div class="pt-2 flex justify-center gap-3">
                        <a href="{{ route('email-templates.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-4 py-2 rounded-xl transition">
                            Nouveau template
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- TABLE VIEW -->
        <div x-show="viewMode === 'table'" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50/80 text-xs uppercase font-bold text-slate-500 border-b border-slate-200/80">
                        <tr>
                            <th class="px-6 py-4">Template</th>
                            <th class="px-6 py-4">Type</th>
                            <th class="px-6 py-4">Objet</th>
                            <th class="px-6 py-4">Statut</th>
                            <th class="px-6 py-4">Aperçu</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($templates as $template)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-6 py-4 font-bold text-slate-900">
                                    <div class="flex items-center gap-2.5">
                                        <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <span>{{ $template->nom }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            {{ $template->type_label }}
                                        </span>
                                        @if($template->gjs_data)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200" title="Modèle créé avec l'éditeur visuel">
                                                Visuel
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-700 truncate max-w-xs" title="{{ $template->sujet }}">
                                    {{ $template->sujet ?: '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('email-templates.toggle', $template) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold transition cursor-pointer {{ $template->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $template->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                                            <span>{{ $template->is_active ? 'Actif' : 'Inactif' }}</span>
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-slate-500 truncate max-w-xs">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($template->contenu), 60) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        @if($template->is_active)
                                            <a href="{{ route('campaigns.create', ['template_id' => $template->id]) }}" class="text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1.5 rounded-lg transition" title="Utiliser pour une campagne">
                                                Utiliser
                                            </a>
                                        @endif
                                        <a href="{{ route('email-templates.preview', $template) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Aperçu">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <!-- Visual Builder Link -->
                                        <a href="{{ route('email-templates.builder', $template) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Éditeur Visuel (Drag & Drop)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('email-templates.edit', $template) }}" class="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Modifier">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form action="{{ route('email-templates.duplicate', $template) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Dupliquer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                            </button>
                                        </form>
                                        <form action="{{ route('email-templates.destroy', $template) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce template ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Supprimer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    <p class="font-medium text-slate-600">Aucun template trouvé</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($templates->hasPages())
            <div class="pt-2">
                {{ $templates->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
