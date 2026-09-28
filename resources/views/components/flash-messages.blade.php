{{-- ═══════════════════════════════════════════════════════════
     Flash Messages — Toast notifications (success / error / warning / info)
     Usage: @include('components.flash-messages') ou <x-flash-messages />
     ═══════════════════════════════════════════════════════════ --}}

@if (session('success') || session('error') || session('warning') || session('info') || $errors->any())
<div
    id="flash-container"
    aria-live="polite"
    class="fixed top-5 right-5 z-[9999] flex flex-col gap-3 w-full max-w-sm"
    x-data="{ messages: [] }"
    x-init="
        @if(session('success'))
            messages.push({ id: 1, type: 'success', text: @js(session('success')) });
        @endif
        @if(session('error'))
            messages.push({ id: 2, type: 'error', text: @js(session('error')) });
        @endif
        @if(session('warning'))
            messages.push({ id: 3, type: 'warning', text: @js(session('warning')) });
        @endif
        @if(session('info'))
            messages.push({ id: 4, type: 'info', text: @js(session('info')) });
        @endif
        @if($errors->any())
            messages.push({ id: 5, type: 'error', text: @js($errors->first()) });
        @endif

        messages.forEach((msg, i) => {
            setTimeout(() => {
                messages = messages.filter(m => m.id !== msg.id);
            }, 6000 + i * 500);
        });
    "
>
    <template x-for="msg in messages" :key="msg.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-8 scale-95"
            x-transition:enter-end="opacity-100 translate-x-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0 scale-100"
            x-transition:leave-end="opacity-0 translate-x-8 scale-95"
            :class="{
                'border-emerald-500 bg-emerald-950 text-emerald-100': msg.type === 'success',
                'border-red-500 bg-red-950 text-red-100':             msg.type === 'error',
                'border-amber-500 bg-amber-950 text-amber-100':       msg.type === 'warning',
                'border-blue-500 bg-blue-950 text-blue-100':          msg.type === 'info',
            }"
            class="flex items-start gap-3 rounded-xl border px-4 py-3 shadow-2xl backdrop-blur-sm"
            role="alert"
        >
            {{-- Icon --}}
            <span class="mt-0.5 shrink-0 text-lg" x-text="{
                success: '✅',
                error:   '❌',
                warning: '⚠️',
                info:    'ℹ️',
            }[msg.type]"></span>

            {{-- Message --}}
            <p class="flex-1 text-sm font-medium leading-snug" x-text="msg.text"></p>

            {{-- Close button --}}
            <button
                @click="messages = messages.filter(m => m.id !== msg.id)"
                class="ml-1 shrink-0 rounded-md p-1 opacity-60 transition hover:opacity-100 focus:outline-none"
                aria-label="Fermer"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- Progress bar (auto-dismiss) --}}
            <span
                :class="{
                    'bg-emerald-400': msg.type === 'success',
                    'bg-red-400':     msg.type === 'error',
                    'bg-amber-400':   msg.type === 'warning',
                    'bg-blue-400':    msg.type === 'info',
                }"
                class="absolute bottom-0 left-0 h-0.5 rounded-b-xl animate-[shrink_6s_linear_forwards]"
                style="width: 100%"
            ></span>
        </div>
    </template>
</div>

<style>
@keyframes shrink {
    from { width: 100%; }
    to   { width: 0%;   }
}
</style>
@endif
