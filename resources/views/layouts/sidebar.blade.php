@php
    $links = [
        ['label' => 'Tableau de bord', 'route' => 'dashboard', 'active' => 'dashboard', 'icon' => 'dashboard'],
        ['label' => 'Contacts', 'route' => 'contacts.index', 'active' => 'contacts.*', 'icon' => 'contacts'],
        ['label' => 'Listes', 'route' => 'categories.index', 'active' => 'categories.*', 'icon' => 'lists'],
        ['label' => 'Campagnes', 'route' => 'campaigns.index', 'active' => 'campaigns.*', 'icon' => 'campaigns'],
        ['label' => 'Pièces jointes', 'route' => 'attachments.index', 'active' => 'attachments.*', 'icon' => 'files'],
        ['label' => 'Parametres SMTP', 'route' => 'smtp-settings.index', 'active' => 'smtp-settings.*', 'icon' => 'smtp'],
    ];

    if (Auth::user()?->hasRole('admin')) {
        array_splice($links, 4, 0, [[
            'label' => 'Templates',
            'route' => 'email-templates.index',
            'active' => 'email-templates.*',
            'icon' => 'templates',
        ], [
            'label' => 'Utilisateurs',
            'route' => 'users.index',
            'active' => 'users.*',
            'icon' => 'users',
        ]]);
    }

    $soonLinks = [
        ['label' => 'Envois', 'icon' => 'send'],
        ['label' => 'Statistiques', 'icon' => 'stats'],
        ['label' => 'Parametres', 'route' => 'profile.edit', 'active' => 'profile.edit', 'icon' => 'settings'],
    ];
@endphp

<aside {{ $attributes->merge(['class' => 'flex h-full w-72 flex-col bg-[#101d2f] text-slate-200 shadow-xl shadow-slate-950/20']) }}>
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
        @foreach($links as $item)
            @php($isActive = request()->routeIs($item['active']))

            <a href="{{ route($item['route']) }}"
               class="{{ $isActive ? 'bg-blue-600 text-white shadow-lg shadow-blue-950/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} group flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold transition">
                @include('layouts.sidebar-icon', ['name' => $item['icon']])
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach

        @foreach($soonLinks as $item)
            @if(isset($item['route']))
                @php($isActive = request()->routeIs($item['active']))

                <a href="{{ route($item['route']) }}"
                   class="{{ $isActive ? 'bg-blue-600 text-white shadow-lg shadow-blue-950/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} group flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold transition">
                    @include('layouts.sidebar-icon', ['name' => $item['icon']])
                    <span>{{ $item['label'] }}</span>
                </a>
            @else
                <button type="button"
                        disabled
                        title="Bientôt disponible"
                        class="group flex w-full cursor-not-allowed items-center gap-3 rounded-lg px-4 py-3 text-left text-sm font-semibold text-slate-500 opacity-70">
                    @include('layouts.sidebar-icon', ['name' => $item['icon']])
                    <span>{{ $item['label'] }}</span>
                </button>
            @endif
        @endforeach
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
