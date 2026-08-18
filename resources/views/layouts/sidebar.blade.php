@php
    $isAdmin = Auth::user()?->hasRole('admin');

    $mainLinks = [
        ['label' => 'Tableau de bord', 'route' => 'dashboard', 'active' => 'dashboard', 'icon' => 'dashboard'],
        ['label' => 'Contacts', 'route' => 'contacts.index', 'active' => 'contacts.*', 'icon' => 'contacts'],
        ['label' => 'Listes', 'route' => 'categories.index', 'active' => 'categories.*', 'icon' => 'lists'],
        ['label' => 'Campagnes', 'route' => 'campaigns.index', 'active' => 'campaigns.*', 'icon' => 'campaigns'],
        ['label' => 'Pièces jointes', 'route' => 'attachments.index', 'active' => 'attachments.*', 'icon' => 'files'],
    ];

    if ($isAdmin) {
        // Insert Admin-only modules in main navigation
        array_splice($mainLinks, 4, 0, [
            [
                'label' => 'Templates',
                'route' => 'email-templates.index',
                'active' => 'email-templates.*',
                'icon' => 'templates',
            ],
            [
                'label' => 'Suivi des prospects',
                'route' => 'prospects.index',
                'active' => 'prospects.*',
                'icon' => 'prospects',
            ],
        ]);

        $mainLinks[] = [
            'label' => 'Paramètres SMTP',
            'route' => 'smtp-settings.index',
            'active' => 'smtp-settings.*',
            'icon' => 'smtp',
        ];
    }

    $soonLinks = [
        ['label' => 'Envois', 'icon' => 'send'],
        ['label' => 'Statistiques', 'icon' => 'stats'],
    ];

    $settingLinks = [
        ['label' => 'Paramètres', 'route' => 'profile.edit', 'active' => 'profile.edit', 'icon' => 'settings'],
    ];

    if ($isAdmin) {
        $settingLinks[] = [
            'label' => 'Utilisateurs',
            'route' => 'users.index',
            'active' => 'users.index',
            'icon' => 'users',
            'subAction' => [
                'route' => 'users.create',
                'title' => 'Ajouter un utilisateur',
            ],
        ];

        $settingLinks[] = [
            'label' => 'Monitoring Équipe',
            'route' => 'users.monitoring',
            'active' => 'users.monitoring',
            'icon' => 'stats',
        ];
    }
@endphp

<aside {{ ($attributes ?? new \Illuminate\View\ComponentAttributeBag)->merge(['class' => 'flex h-full w-72 flex-col bg-[#101d2f] text-slate-200 shadow-xl shadow-slate-950/20']) }}>
    <div class="flex h-20 items-center gap-3 px-6">
        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-lime-100 text-slate-900 ring-2 ring-white/10">
            <span class="text-xs font-extrabold leading-none">CAEI</span>
        </div>
        <div>
            <div class="text-3xl font-bold leading-none tracking-normal text-white">CAEI</div>
            <div class="mt-1 text-[10px] font-semibold uppercase tracking-normal text-slate-400">
                Codicil - Audit - Formation
            </div>
        </div>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-4 pb-5">
        @foreach($mainLinks as $item)
            @php($isActive = request()->routeIs($item['active']))

            <a href="{{ route($item['route']) }}"
               class="{{ $isActive ? 'bg-blue-600 text-white shadow-lg shadow-blue-950/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} group flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold transition">
                @include('layouts.sidebar-icon', ['name' => $item['icon']])
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach

        @foreach($soonLinks as $item)
            <button type="button"
                    disabled
                    title="Bientôt disponible"
                    class="group flex w-full cursor-not-allowed items-center gap-3 rounded-lg px-4 py-3 text-left text-sm font-semibold text-slate-500 opacity-70">
                @include('layouts.sidebar-icon', ['name' => $item['icon']])
                <span>{{ $item['label'] }}</span>
            </button>
        @endforeach

        <!-- Section Paramètres & Gestion sous Paramètres -->
        <div class="pt-4 mt-4 border-t border-white/10 space-y-1">
            <div class="px-4 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                Paramètres & Accès
            </div>

            @foreach($settingLinks as $item)
                @php($isActive = request()->routeIs($item['active']))

                <div class="flex items-center gap-1">
                    <a href="{{ route($item['route']) }}"
                       class="flex-1 {{ $isActive ? 'bg-blue-600 text-white shadow-lg shadow-blue-950/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} group flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold transition">
                        @include('layouts.sidebar-icon', ['name' => $item['icon']])
                        <span>{{ $item['label'] }}</span>
                    </a>
                    @if(isset($item['subAction']))
                        <a href="{{ route($item['subAction']['route']) }}" 
                           title="{{ $item['subAction']['title'] }}"
                           class="h-10 w-10 flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition"
                           aria-label="{{ $item['subAction']['title'] }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </nav>

    <div class="border-t border-white/10 p-4">
        <div class="mb-3 min-w-0">
            <div class="truncate text-sm font-semibold text-white">
                {{ Auth::user()->name }}
            </div>
            <div class="truncate text-xs text-slate-400">
                {{ Auth::user()->email }}
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full rounded-lg border border-white/10 px-3 py-2 text-left text-sm font-semibold text-slate-300 transition hover:bg-white/10 hover:text-white">
                Déconnexion
            </button>
        </form>
    </div>
</aside>
