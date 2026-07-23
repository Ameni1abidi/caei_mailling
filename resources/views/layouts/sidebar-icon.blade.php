@props(['name'])

<svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    @switch($name)
        @case('dashboard')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 11.5 12 4l9 7.5M5 10.5V20h5v-5h4v5h5v-9.5" />
            @break
        @case('contacts')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 19a4 4 0 0 0-8 0M12 13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm6 5a3 3 0 0 0-2.2-2.9M8.2 15.1A3 3 0 0 0 6 18" />
            @break
        @case('lists')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 6h14M5 12h14M5 18h14M3 6h.01M3 12h.01M3 18h.01" />
            @break
        @case('campaigns')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7.5h16v10H4zM4 8l8 5 8-5" />
            @break
        @case('prospects')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4h18l-7 8v6l-4 2v-8L3 4z" />
            @break
        @case('templates')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 3h8l4 4v14H7zM15 3v5h5M10 12h6M10 16h6" />
            @break
        @case('send')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m4 12 16-8-5 16-3-7-8-1Zm8 1 4-4" />
            @break
        @case('stats')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 19V9m7 10V5m7 14v-6M3 19h18" />
            @break
        @case('automation')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 2m5-2a8 8 0 1 1-2.3-5.6M20 4v5h-5" />
            @break
        @case('smtp')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 8h16v10H4zM4 9l8 5 8-5" />
            @break
        @case('users')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm-5 8a5 5 0 0 1 10 0m3-8a3 3 0 1 0 0-6m0 14a4.5 4.5 0 0 0-2.5-4" />
            @break
        @case('files')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 3h7l5 5v13H7zM14 3v6h5M10 14h6M10 17h4" />
            @break
        @case('settings')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm0-12v2m0 13v2m8.5-8.5h-2m-13 0h-2m14.5-6.5-1.4 1.4M7.4 16.6 6 18m12 0-1.4-1.4M7.4 7.4 6 6" />
            @break
        @case('journal')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 4h10a2 2 0 0 1 2 2v14H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Zm2 4h6M9 12h6M9 16h4" />
            @break
    @endswitch
</svg>
