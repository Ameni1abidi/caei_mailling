<x-app-layout>
    <div class="p-6 space-y-6" x-data="{
        openNoteModal: false,
        openFollowupModal: false,
    }">
        <!-- Header Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-amber-500 rounded-l-2xl"></div>

            <div class="flex items-center gap-4 pl-2">
                <a href="{{ route('prospects.index') }}" class="p-2.5 text-slate-500 hover:text-[#03123F] hover:bg-slate-100 rounded-xl transition" title="Retour aux prospects">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                @php
                    $initials = strtoupper(substr($contact->prenom ?? 'P', 0, 1) . substr($contact->nom ?? 'R', 0, 1));
                @endphp
                <div class="w-12 h-12 rounded-2xl bg-[#03123F] text-amber-400 font-black flex items-center justify-center text-sm shrink-0 shadow-sm border border-[#03123F]/20">
                    {{ $initials }}
                </div>
                <div>
                    <h1 class="text-2xl font-black text-[#03123F] tracking-tight">{{ $contact->prenom }} {{ $contact->nom }}</h1>
                    <p class="text-xs font-semibold text-slate-500 mt-0.5">{{ $contact->entreprise ?? 'Sans entreprise' }} • {{ $contact->fonction ?? 'Fonction non spécifiée' }}</p>
                </div>
            </div>
            
            <div class="shrink-0">
                <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold border shadow-2xs {{ $statuses[$contact->prospect_status]['badge'] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                    {{ $statuses[$contact->prospect_status]['label'] ?? $contact->prospect_status }}
                </span>
            </div>
        </div>

        <!-- Flash messages -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition class="flex items-center justify-between gap-3 bg-emerald-50 border border-emerald-200 text-emerald-900 p-4 rounded-xl text-sm font-medium shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-1.5 bg-emerald-500 text-white rounded-lg shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Contact Info Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                        <div class="w-1.5 h-4 bg-amber-500 rounded-full"></div>
                        <h2 class="text-base font-black text-[#03123F]">Informations de contact</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-slate-50/70 p-3.5 rounded-xl border border-slate-100">
                            <label class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Email</label>
                            <p class="text-sm font-extrabold text-[#03123F] truncate">{{ $contact->email }}</p>
                        </div>
                        <div class="bg-slate-50/70 p-3.5 rounded-xl border border-slate-100">
                            <label class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Téléphone</label>
                            <p class="text-sm font-bold text-slate-800">{{ $contact->telephone ?? '—' }}</p>
                        </div>
                        <div class="bg-slate-50/70 p-3.5 rounded-xl border border-slate-100">
                            <label class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Pays</label>
                            <p class="text-sm font-bold text-slate-800">{{ $contact->pays ?? '—' }}</p>
                        </div>
                        <div class="bg-slate-50/70 p-3.5 rounded-xl border border-slate-100">
                            <label class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Secteur d'activité</label>
                            <p class="text-sm font-bold text-slate-800">{{ $contact->secteur_activite ?? '—' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Interaction History -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                    <div class="flex items-center gap-2 mb-6 pb-3 border-b border-slate-100">
                        <div class="w-1.5 h-4 bg-amber-500 rounded-full"></div>
                        <h2 class="text-base font-black text-[#03123F]">Historique des interactions</h2>
                    </div>
                    
                    @if($contact->interactions->isNotEmpty())
                        <div class="space-y-4">
                            @foreach($contact->interactions as $interaction)
                                @php
                                    $typeData = $interactionTypes[$interaction->type] ?? [];
                                @endphp
                                <div class="flex gap-4 pb-4 border-b border-slate-100 last:border-b-0">
                                    <div class="shrink-0">
                                        <div class="w-10 h-10 rounded-xl bg-[#03123F]/5 text-[#03123F] border border-[#03123F]/10 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-[#03123F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-1">
                                            <h3 class="font-extrabold text-sm text-[#03123F]">{{ $typeData['label'] ?? $interaction->type }}</h3>
                                            <span class="text-[11px] font-medium text-slate-400">{{ $interaction->created_at->diffForHumans() }}</span>
                                        </div>
                                        
                                        @if($interaction->description)
                                            <p class="text-xs text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100/80 font-medium mt-1">{{ $interaction->description }}</p>
                                        @endif
                                        
                                        @if($interaction->campaign)
                                            <div class="text-[11px] text-indigo-700 font-semibold mt-2 p-2 bg-indigo-50/60 rounded-lg border border-indigo-100 flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                                Campagne : <strong>{{ $interaction->campaign->nom }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10">
                            <div class="w-12 h-12 bg-slate-50 text-slate-400 rounded-xl flex items-center justify-center mx-auto mb-3 border border-slate-200/80">
                                <svg class="w-6 h-6 text-[#03123F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <p class="text-xs font-bold text-slate-500">Aucune interaction enregistrée</p>
                        </div>
                    @endif
                </div>

                <!-- Notes Section -->
                @if($contact->notes)
                    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                            <div class="w-1.5 h-4 bg-amber-500 rounded-full"></div>
                            <h2 class="text-base font-black text-[#03123F]">Notes & Commentaires</h2>
                        </div>
                        <div class="bg-amber-50/50 p-4 rounded-xl border border-amber-100 text-xs font-medium text-slate-700 whitespace-pre-wrap leading-relaxed">{{ $contact->notes }}</div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                    <h3 class="font-black text-base text-[#03123F] mb-4 pb-2 border-b border-slate-100">Changer de Statut</h3>
                    
                    <form action="{{ route('prospects.update-status', $contact) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        
                        <select name="status" class="w-full px-3 py-2 text-xs font-extrabold border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#03123F] bg-slate-50/50 text-slate-800" required>
                            @foreach($statuses as $key => $meta)
                                <option value="{{ $key }}" {{ $contact->prospect_status === $key ? 'selected' : '' }}>
                                    {{ $meta['label'] }}
                                </option>
                            @endforeach
                        </select>
                        
                        <textarea name="note" placeholder="Note de qualification (optionnel)" class="w-full px-3 py-2.5 text-xs font-medium border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#03123F] bg-slate-50/50 text-slate-800" rows="3"></textarea>
                        
                        <button type="submit" 
                                class="w-full px-4 py-2.5 text-xs font-extrabold text-white rounded-xl shadow-md transition hover:scale-[1.02] active:scale-[0.98]"
                                style="background: linear-gradient(135deg, #C57A1E 0%, #E5983B 100%);">
                            Mettre à jour le statut
                        </button>
                    </form>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                    <h3 class="font-black text-base text-[#03123F] mb-4 pb-2 border-b border-slate-100">Actions rapides</h3>
                    
                    <div class="space-y-3">
                        <!-- Add Note Button -->
                        <button @click="openNoteModal = true" class="w-full px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-[#03123F] font-bold text-xs rounded-xl transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Ajouter une note
                        </button>

                        <!-- Schedule Follow-up Button -->
                        <button @click="openFollowupModal = true" class="w-full px-4 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-900 font-bold text-xs rounded-xl transition flex items-center justify-center gap-2 border border-indigo-100">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Planifier une relance
                        </button>
                    </div>
                </div>

                <!-- Last Campaign Card -->
                @if($contact->lastCampaign)
                    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                        <h3 class="font-black text-base text-[#03123F] mb-3 pb-2 border-b border-slate-100">Dernière campagne reçue</h3>
                        <div class="space-y-2.5 text-xs">
                            <div>
                                <p class="text-[11px] text-slate-400 uppercase font-extrabold">Campagne</p>
                                <p class="text-slate-900 font-extrabold mt-0.5">{{ $contact->lastCampaign->nom }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-400 uppercase font-extrabold">Objet</p>
                                <p class="text-slate-600 font-medium mt-0.5 truncate">{{ $contact->lastCampaign->objet }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Dates Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                    <h3 class="font-black text-base text-[#03123F] mb-4 pb-2 border-b border-slate-100">Dates clés</h3>
                    
                    <div class="space-y-3 text-xs">
                        <div class="bg-slate-50 p-3 rounded-xl">
                            <p class="text-[11px] text-slate-400 uppercase font-extrabold">Dernière interaction</p>
                            <p class="text-slate-900 font-extrabold mt-0.5">{{ $contact->last_interaction?->format('d/m/Y à H:i') ?? '—' }}</p>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-xl">
                            <p class="text-[11px] text-slate-400 uppercase font-extrabold">Prochaine relance</p>
                            @if($contact->next_followup_date)
                                <p class="text-[#03123F] font-extrabold mt-0.5">{{ $contact->next_followup_date->format('d/m/Y à H:i') }}</p>
                                @if($contact->next_followup_date < now())
                                    <span class="inline-block mt-1.5 px-2.5 py-0.5 bg-rose-50 text-rose-700 text-[10px] font-black rounded-full border border-rose-200">
                                        En retard
                                    </span>
                                @endif
                            @else
                                <p class="text-slate-400 italic mt-0.5">Aucune relance planifiée</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Note Modal -->
    <div x-show="openNoteModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @keydown.escape="openNoteModal = false" style="display: none;">
        <div class="bg-white rounded-3xl p-6 w-full max-w-md shadow-2xl border border-slate-100 relative" @click.stop>
            <h3 class="text-lg font-black text-[#03123F] mb-4 pb-2 border-b border-slate-100">Ajouter une note</h3>
            
            <form action="{{ route('prospects.add-note', $contact) }}" method="POST" class="space-y-4">
                @csrf
                
                <textarea name="note" placeholder="Votre note commerciale..." class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-[#03123F] bg-slate-50/50 text-slate-800" rows="4" required></textarea>
                
                <div class="flex gap-3">
                    <button type="button" @click="openNoteModal = false" class="flex-1 px-4 py-2.5 bg-slate-100 text-slate-600 font-bold text-xs rounded-xl hover:bg-slate-200 transition">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2.5 text-xs font-extrabold text-white rounded-xl shadow-md transition hover:scale-[1.02] active:scale-[0.98]"
                            style="background: linear-gradient(135deg, #C57A1E 0%, #E5983B 100%);">
                        Ajouter la note
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Schedule Follow-up Modal -->
    <div x-show="openFollowupModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @keydown.escape="openFollowupModal = false" style="display: none;">
        <div class="bg-white rounded-3xl p-6 w-full max-w-md shadow-2xl border border-slate-100 relative" @click.stop>
            <h3 class="text-lg font-black text-[#03123F] mb-4 pb-2 border-b border-slate-100">Planifier une relance</h3>
            
            <form action="{{ route('prospects.schedule-followup', $contact) }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1">Date et heure</label>
                    <input type="datetime-local" name="followup_date" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-[#03123F] bg-slate-50/50 text-slate-800" required>
                </div>
                
                <div>
                    <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1">Raison / Note (optionnel)</label>
                    <textarea name="followup_note" placeholder="Raison de la relance..." class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-[#03123F] bg-slate-50/50 text-slate-800" rows="3"></textarea>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" @click="openFollowupModal = false" class="flex-1 px-4 py-2.5 bg-slate-100 text-slate-600 font-bold text-xs rounded-xl hover:bg-slate-200 transition">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2.5 text-xs font-extrabold text-white rounded-xl shadow-md transition hover:scale-[1.02] active:scale-[0.98]"
                            style="background: linear-gradient(135deg, #C57A1E 0%, #E5983B 100%);">
                        Planifier la relance
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
