<x-app-layout>
    <div class="p-6 max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3.5 bg-gradient-to-br from-indigo-500 to-blue-600 text-white rounded-2xl shadow-md shadow-indigo-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Aperçu du message</h1>
                    <p class="text-sm text-slate-500 mt-0.5">{{ $campaign->nom }}</p>
                </div>
            </div>
            <a href="{{ route('campaigns.edit', $campaign) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-4 py-2.5 rounded-xl border border-slate-200 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Retour à l'édition</span>
            </a>
        </div>

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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Simulated Email Client Box (2 cols) -->
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-md overflow-hidden flex flex-col">
                    <!-- Window Chrome Top Bar -->
                    <div class="bg-slate-100 px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-rose-400 inline-block"></span>
                            <span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span>
                            <span class="w-3 h-3 rounded-full bg-emerald-400 inline-block"></span>
                        </div>
                        <span class="text-xs font-semibold text-slate-500 font-mono">Simulateur de Messagerie</span>
                        <div class="w-12"></div>
                    </div>

                    <!-- Email Header Metadata -->
                    <div class="bg-slate-50/80 px-6 py-4 border-b border-slate-200 space-y-2.5 text-xs">
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-slate-500 w-20 uppercase tracking-wider">Expéditeur :</span>
                            <span class="text-slate-800 font-semibold bg-slate-200/60 px-2.5 py-1 rounded">CAEI &lt;contact@caei.org&gt;</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-slate-500 w-20 uppercase tracking-wider">Destinataire :</span>
                            <span class="text-slate-800 font-semibold bg-slate-200/60 px-2.5 py-1 rounded">
                                {{ $contact->prenom }} {{ $contact->nom }} &lt;{{ $contact->email }}&gt;
                            </span>
                        </div>
                        <div class="flex items-center gap-3 pt-2.5 border-t border-slate-200/60">
                            <span class="font-bold text-slate-900 w-20 uppercase tracking-wider text-sm">Objet :</span>
                            <span class="font-extrabold text-slate-900 text-sm">{{ $objetPersonnalise }}</span>
                        </div>
                    </div>

                    <!-- Email Body Render -->
                    <div class="p-6 md:p-8 bg-white min-h-[300px] text-sm text-slate-800 leading-relaxed overflow-y-auto">
                        {!! nl2br(\App\Models\EmailTemplate::sanitizeContent($contenuPersonnalise)) !!}
                    </div>

                    <!-- Attachments Footer -->
                    @if($campaign->attachments->isNotEmpty())
                        <div class="p-4 bg-slate-50 border-t border-slate-200/80">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">📎 Pièces jointes ({{ $campaign->attachments->count() }}) :</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($campaign->attachments as $att)
                                    <div class="flex items-center justify-between gap-3 bg-white p-3 rounded-xl border border-slate-200/60 shadow-sm">
                                        <div class="min-w-0">
                                            <div class="text-xs font-bold text-slate-800 truncate" title="{{ $att->file_name }}">
                                                {{ $att->file_name }}
                                            </div>
                                            <div class="text-[10px] text-slate-400 mt-0.5">
                                                {{ $att->typeLabel() }} - {{ $att->file_size ? number_format($att->file_size / 1024, 1) . ' KB' : 'N/A' }}
                                            </div>
                                        </div>
                                        <a href="{{ route('attachments.download', $att) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Télécharger">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sandbox Control Sidebar (1 col) -->
            <div class="space-y-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-4 sticky top-6">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <h2 class="font-bold text-slate-900 text-sm">Destinataire test</h2>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('campaigns.preview', $campaign) }}" class="space-y-4">
                        <div>
                            <label for="contact_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Sélectionner un contact :</label>
                            <select name="contact_id" id="contact_id" onchange="this.form.submit()" class="w-full text-xs py-2.5 px-3 border border-slate-200 rounded-xl bg-slate-50/50 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-700 font-medium">
                                @foreach($contactsDisponibles as $cd)
                                    <option value="{{ $cd->id }}" {{ $contact->id == $cd->id ? 'selected' : '' }}>
                                        {{ $cd->nom }} {{ $cd->prenom }} ({{ $cd->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    <!-- Contact Metadata details card -->
                    <div class="bg-slate-50/80 p-4 border border-slate-200/50 rounded-xl space-y-2.5 text-xs">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200/60 pb-1.5 mb-2">
                            Détails de test injectés
                        </div>
                        <div class="flex justify-between">
                            <span class="font-bold text-slate-500">Nom complet :</span>
                            <span class="text-slate-800 font-semibold">{{ $contact->prenom }} {{ $contact->nom }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-bold text-slate-500">Email :</span>
                            <span class="text-slate-800 font-semibold truncate max-w-[150px]" title="{{ $contact->email }}">{{ $contact->email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-bold text-slate-500">Entreprise :</span>
                            <span class="text-slate-800 font-semibold">{{ $contact->entreprise ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-bold text-slate-500">Poste :</span>
                            <span class="text-slate-800 font-semibold">{{ $contact->fonction ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-bold text-slate-500">Pays :</span>
                            <span class="text-slate-800 font-semibold">{{ $contact->pays ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="text-[10px] text-slate-400 leading-relaxed pt-2 border-t border-slate-100 flex items-center gap-1.5 justify-center">
                        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ $contactsDisponibles->count() }} destinataire(s) cible(s) au total</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
