<x-app-layout>
<div class="p-6 space-y-6">

    {{-- Header / Stepper --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('contacts.index') }}" class="p-2 hover:bg-slate-100 rounded-lg text-slate-500 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div class="p-3 bg-gradient-to-br from-indigo-500 to-blue-600 text-white rounded-2xl shadow-md">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Importer des contacts</h1>
                <p class="text-sm text-slate-500">Importez vos contacts depuis un fichier CSV ou Excel</p>
            </div>
        </div>

        {{-- Stepper --}}
        <div class="flex items-center gap-0">
            @foreach([['①','Fichier',true],['②','Mapping',false],['③','Aperçu',false],['④','Import',false],['⑤','Résultat',false]] as [$num,$label,$active])
            <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
                <div class="flex flex-col items-center">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold {{ $active ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'bg-slate-100 text-slate-400' }}">{{ $num }}</div>
                    <span class="text-xs mt-1 font-semibold {{ $active ? 'text-indigo-600' : 'text-slate-400' }}">{{ $label }}</span>
                </div>
                @if(!$loop->last)<div class="flex-1 h-0.5 mb-4 {{ $active ? 'bg-slate-200' : 'bg-slate-100' }} mx-2"></div>@endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Erreurs de validation --}}
    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl text-sm font-medium">
            <div class="flex items-center gap-2 mb-2 font-bold">
                <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856C18.448 19 19 18.552 19 18V6c0-.552-.448-1-1-1H6c-.552 0-1 .448-1 1v12c0 .552.448 1 1 1z"/></svg>
                Erreur lors de l'upload
            </div>
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Zone d'upload --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-8">
        <form action="{{ route('contacts.import.handle-upload') }}" method="POST" enctype="multipart/form-data"
              x-data="{ dragging: false, fileName: '', fileSize: '' }"
              @dragover.prevent="dragging = true"
              @dragleave.prevent="dragging = false"
              @drop.prevent="dragging = false; let f = $event.dataTransfer.files[0]; if(f){ fileName = f.name; fileSize = (f.size/1024/1024).toFixed(2)+' Mo'; $refs.fileInput.files = $event.dataTransfer.files; }">
            @csrf

            {{-- Drop zone --}}
            <div :class="dragging ? 'border-indigo-400 bg-indigo-50' : 'border-slate-200 bg-slate-50/50'"
                 class="border-2 border-dashed rounded-2xl p-12 text-center transition-all cursor-pointer"
                 @click="$refs.fileInput.click()">

                <input type="file" name="file" id="file-input" x-ref="fileInput" accept=".csv,.xlsx,.xls,.txt" class="hidden"
                       @change="let f = $event.target.files[0]; if(f){ fileName = f.name; fileSize = (f.size/1024/1024).toFixed(2)+' Mo'; }">

                <div x-show="!fileName">
                    <div class="w-16 h-16 bg-indigo-50 text-indigo-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    </div>
                    <p class="text-lg font-bold text-slate-700">Glissez votre fichier ici</p>
                    <p class="text-sm text-slate-500 mt-1">ou <span class="text-indigo-600 font-semibold underline">cliquez pour parcourir</span></p>
                    <p class="text-xs text-slate-400 mt-3">Formats acceptés : .csv, .xlsx, .xls — Taille max : 50 Mo</p>
                </div>

                <div x-show="fileName" class="flex items-center justify-center gap-4">
                    <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="text-left">
                        <p class="font-bold text-slate-800" x-text="fileName"></p>
                        <p class="text-sm text-slate-500" x-text="fileSize"></p>
                    </div>
                    <button type="button" @click.stop="fileName=''; fileSize=''; $refs.fileInput.value='';" class="p-1.5 text-slate-400 hover:text-rose-500 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- Boutons --}}
            <div class="flex items-center justify-between mt-6">
                <a href="{{ route('contacts.import.template') }}"
                   class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Télécharger le template CSV
                </a>
                <button type="submit" x-show="fileName"
                        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-xl shadow-sm transition">
                    Analyser le fichier
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </form>
    </div>

    {{-- Infos pratiques --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach([
            ['Formats supportés', 'CSV (séparateur , ou ;), Excel .xlsx et .xls', 'from-blue-500 to-indigo-600', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['Taille maximale', 'Jusqu\'à 50 Mo — 100 000+ contacts traités en file d\'attente', 'from-violet-500 to-purple-600', 'M13 10V3L4 14h7v7l9-11h-7z'],
            ['Champ obligatoire', 'Seul l\'Email est requis. Tous les autres champs sont optionnels.', 'from-emerald-500 to-teal-600', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ] as [$title, $desc, $gradient, $icon])
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex gap-4">
            <div class="p-3 bg-gradient-to-br {{ $gradient }} text-white rounded-xl shrink-0 self-start">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
            </div>
            <div>
                <div class="font-bold text-slate-800 text-sm">{{ $title }}</div>
                <div class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ $desc }}</div>
            </div>
        </div>
        @endforeach
    </div>

</div>
</x-app-layout>

