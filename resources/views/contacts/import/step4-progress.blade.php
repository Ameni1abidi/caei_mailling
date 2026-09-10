<x-app-layout>
<div class="p-6 space-y-6" x-data="importProgress({{ $importLog->id }})" x-init="startPolling()">

    {{-- Header / Stepper --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div class="flex items-center gap-4 mb-6">
            <div class="p-3 bg-gradient-to-br from-amber-400 to-orange-500 text-white rounded-2xl shadow-md">
                <svg class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!isDone">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="isDone" style="display:none">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-slate-900" x-text="isDone ? 'Import terminé !' : 'Import en cours...'">Import en cours...</h1>
                <p class="text-sm text-slate-500">{{ $importLog->filename }}</p>
            </div>
        </div>
        <div class="flex items-center gap-0">
            @foreach([['①','Fichier',false,true],['②','Mapping',false,true],['③','Aperçu',false,true],['④','Import',true,false],['⑤','Résultat',false,false]] as [$num,$label,$active,$done])
            <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
                <div class="flex flex-col items-center">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold {{ $active ? 'bg-amber-500 text-white shadow-md' : ($done ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400') }}">
                        @if($done) ✓ @else {{ $num }} @endif
                    </div>
                    <span class="text-xs mt-1 font-semibold {{ $active ? 'text-amber-600' : 'text-slate-400' }}">{{ $label }}</span>
                </div>
                @if(!$loop->last)<div class="flex-1 h-0.5 mb-4 bg-slate-200 mx-2"></div>@endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Barre de progression --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-8">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-semibold text-slate-700" x-text="statusLabel">Initialisation...</span>
            <span class="text-sm font-bold text-slate-900" x-text="progress + '%'">0%</span>
        </div>

        <div class="w-full bg-slate-100 rounded-full h-4 overflow-hidden">
            <div class="h-4 rounded-full transition-all duration-500 bg-gradient-to-r from-indigo-500 to-blue-500"
                 :class="isDone && !isFailed ? 'bg-gradient-to-r from-emerald-500 to-teal-500' : (isFailed ? 'bg-rose-500' : 'bg-gradient-to-r from-indigo-500 to-blue-500')"
                 :style="'width: ' + progress + '%'">
            </div>
        </div>

        {{-- Compteurs temps réel --}}
        <div class="grid grid-cols-3 gap-4 mt-8">
            <div class="text-center p-4 bg-emerald-50 rounded-xl">
                <div class="text-2xl font-extrabold text-emerald-700" x-text="imported">0</div>
                <div class="text-xs text-emerald-600 font-semibold mt-1">Importés</div>
            </div>
            <div class="text-center p-4 bg-amber-50 rounded-xl">
                <div class="text-2xl font-extrabold text-amber-700" x-text="duplicates">0</div>
                <div class="text-xs text-amber-600 font-semibold mt-1">Doublons</div>
            </div>
            <div class="text-center p-4 bg-rose-50 rounded-xl">
                <div class="text-2xl font-extrabold text-rose-700" x-text="errors">0</div>
                <div class="text-xs text-rose-600 font-semibold mt-1">Erreurs</div>
            </div>
        </div>

        {{-- Message d'attente --}}
        <div x-show="!isDone" class="mt-6 text-center text-sm text-slate-400">
            <span class="animate-pulse">⏳ Traitement en cours, veuillez patienter...</span>
            <p class="text-xs mt-1">Vous pouvez quitter cette page, l'import continuera en arrière-plan.</p>
        </div>

        {{-- Bouton résultat --}}
        <div x-show="isDone" class="mt-6 text-center" style="display:none">
            <a :href="resultUrl"
               class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-8 py-3 rounded-xl shadow-sm transition">
                Voir le rapport final
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</div>

<script>
function importProgress(importLogId) {
    return {
        progress: 0,
        imported: 0,
        duplicates: 0,
        errors: 0,
        isDone: false,
        isFailed: false,
        resultUrl: '',
        statusLabel: 'Initialisation...',
        pollingInterval: null,

        startPolling() {
            this.pollingInterval = setInterval(() => {
                this.checkStatus();
            }, 2000);
            // Premier check immédiat
            this.checkStatus();
        },

        async checkStatus() {
            try {
                const res = await fetch(`/contacts/import/${importLogId}/status`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                this.progress   = data.progress;
                this.imported   = data.imported;
                this.duplicates = data.duplicates;
                this.errors     = data.errors;

                const labels = {
                    'pending':    'En attente de traitement...',
                    'processing': 'Importation en cours...',
                    'done':       'Import terminé !',
                    'failed':     'Import échoué',
                };
                this.statusLabel = labels[data.status] || data.status;

                if (data.is_done) {
                    clearInterval(this.pollingInterval);
                    this.isDone   = true;
                    this.isFailed = data.status === 'failed';
                    this.resultUrl = data.result_url;
                    this.progress = 100;

                    // Redirection automatique après 1.5s
                    setTimeout(() => { window.location.href = data.result_url; }, 1500);
                }
            } catch (e) {
                console.error('Polling error:', e);
            }
        }
    };
}
</script>
</x-app-layout>

