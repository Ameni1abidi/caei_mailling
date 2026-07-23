<x-app-layout>
    <div class="p-6 space-y-6" x-data="{
        openNoteModal: false,
        openFollowupModal: false,
    }">
        <!-- Header -->
        <div class="flex items-center justify-between bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center gap-4">
                <a href="{{ route('prospects.index') }}" class="p-2 hover:bg-slate-100 rounded-lg transition">
                    <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900">{{ $contact->prenom }} {{ $contact->nom }}</h1>
                    <p class="text-sm text-slate-500 mt-1">{{ $contact->entreprise }} • {{ $contact->fonction }}</p>
                </div>
            </div>
            
            <span class="px-3 py-1.5 rounded-full text-sm font-bold {{ $statuses[$contact->status]['badge'] ?? 'bg-slate-100' }}">
                {{ $statuses[$contact->status]['label'] ?? $contact->status }}
            </span>
        </div>

        <!-- Flash messages -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition class="flex items-center justify-between gap-3 bg-emerald-50 border border-emerald-200 text-emerald-900 p-4 rounded-xl text-sm font-medium shadow-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">
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
                    <h2 class="text-lg font-bold text-slate-900 mb-4">Informations de contact</h2>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-slate-500 uppercase">Email</label>
                            <p class="text-slate-900 font-medium">{{ $contact->email }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500 uppercase">Téléphone</label>
                            <p class="text-slate-900 font-medium">{{ $contact->telephone ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500 uppercase">Pays</label>
                            <p class="text-slate-900 font-medium">{{ $contact->pays ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500 uppercase">Secteur d'activité</label>
                            <p class="text-slate-900 font-medium">{{ $contact->secteur_activite ?? '—' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Interaction History -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-900 mb-4">Historique des interactions</h2>
                    
                    @if($contact->interactions->isNotEmpty())
                        <div class="space-y-4">
                            @foreach($contact->interactions as $interaction)
                                @php
                                    $typeData = $interactionTypes[$interaction->type] ?? [];
                                @endphp
                                <div class="flex gap-4 pb-4 border-b border-slate-200 last:border-b-0">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full {{ $typeData['bg'] ?? 'bg-slate-50' }} flex items-center justify-center">
                                            <svg class="w-5 h-5 {{ $typeData['color'] ?? 'text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <div class="flex-grow">
                                        <div class="flex items-center justify-between mb-1">
                                            <h3 class="font-semibold text-slate-900">{{ $typeData['label'] ?? $interaction->type }}</h3>
                                            <span class="text-xs text-slate-500">{{ $interaction->created_at->diffForHumans() }}</span>
                                        </div>
                                        
                                        @if($interaction->description)
                                            <p class="text-sm text-slate-600">{{ $interaction->description }}</p>
                                        @endif
                                        
                                        @if($interaction->campaign)
                                            <div class="text-xs text-slate-500 mt-2 p-2 bg-slate-50 rounded">
                                                Campagne: <strong>{{ $interaction->campaign->nom }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-slate-500">Aucune interaction enregistrée</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                    <h3 class="font-bold text-slate-900 mb-4">Statut actuel</h3>
                    
                    <form action="{{ route('prospects.update-status', $contact) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        
                        <select name="status" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm" required>
                            @foreach($statuses as $key => $meta)
                                <option value="{{ $key }}" {{ $contact->status === $key ? 'selected' : '' }}>
                                    {{ $meta['label'] }}
                                </option>
                            @endforeach
                        </select>
                        
                        <textarea name="note" placeholder="Note (optionnel)" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm" rows="2"></textarea>
                        
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition">
                            Mettre à jour
                        </button>
                    </form>
                </div>

                <!-- Campaign Info Card -->
                @if($contact->lastCampaign)
                    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                        <h3 class="font-bold text-slate-900 mb-3">Dernière campagne</h3>
                        <div class="space-y-2">
                            <div>
                                <p class="text-xs text-slate-500 uppercase font-semibold">Campagne</p>
                                <p class="text-slate-900 font-medium">{{ $contact->lastCampaign->nom }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 uppercase font-semibold">Objet</p>
                                <p class="text-sm text-slate-600">{{ $contact->lastCampaign->objet }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 uppercase font-semibold">Envoyé le</p>
                                <p class="text-sm text-slate-600">{{ $contact->last_interaction?->format('d/m/Y H:i') ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Quick Actions -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                    <h3 class="font-bold text-slate-900 mb-4">Actions rapides</h3>
                    
                    <div class="space-y-3">
                        <!-- Add Note Button -->
                        <button @click="openNoteModal = true" class="w-full px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-900 font-semibold rounded-lg transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Ajouter une note
                        </button>

                        <!-- Schedule Follow-up Button -->
                        <button @click="openFollowupModal = true" class="w-full px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-900 font-semibold rounded-lg transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Planifier une relance
                        </button>
                    </div>
                </div>

                <!-- Dates Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                    <h3 class="font-bold text-slate-900 mb-4">Dates importantes</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-slate-500 uppercase font-semibold">Dernière interaction</p>
                            <p class="text-slate-900 font-medium">{{ $contact->last_interaction?->format('d/m/Y H:i') ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase font-semibold">Prochaine relance</p>
                            @if($contact->next_followup_date)
                                <p class="text-slate-900 font-medium">{{ $contact->next_followup_date->format('d/m/Y H:i') }}</p>
                                @if($contact->next_followup_date < now())
                                    <span class="inline-block mt-1 px-2 py-1 bg-rose-50 text-rose-700 text-xs font-semibold rounded">
                                        En retard
                                    </span>
                                @endif
                            @else
                                <p class="text-slate-500">Aucune relance planifiée</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes Section -->
        @if($contact->notes)
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900 mb-4">Notes</h2>
                <div class="prose prose-sm max-w-none text-slate-600 whitespace-pre-wrap">{{ $contact->notes }}</div>
            </div>
        @endif
    </div>

    <!-- Add Note Modal -->
    <div x-show="openNoteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @keydown.escape="openNoteModal = false">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md" @click.stop>
            <h3 class="text-lg font-bold text-slate-900 mb-4">Ajouter une note</h3>
            
            <form action="{{ route('prospects.add-note', $contact) }}" method="POST">
                @csrf
                
                <textarea name="note" placeholder="Votre note..." class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm mb-4" rows="4" required></textarea>
                
                <div class="flex gap-3">
                    <button type="button" @click="openNoteModal = false" class="flex-1 px-4 py-2 bg-slate-100 text-slate-900 font-semibold rounded-lg hover:bg-slate-200 transition">
                        Annuler
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition">
                        Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Schedule Follow-up Modal -->
    <div x-show="openFollowupModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @keydown.escape="openFollowupModal = false">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md" @click.stop>
            <h3 class="text-lg font-bold text-slate-900 mb-4">Planifier une relance</h3>
            
            <form action="{{ route('prospects.schedule-followup', $contact) }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Date et heure</label>
                    <input type="datetime-local" name="followup_date" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Note (optionnel)</label>
                    <textarea name="followup_note" placeholder="Raison de la relance..." class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm" rows="3"></textarea>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" @click="openFollowupModal = false" class="flex-1 px-4 py-2 bg-slate-100 text-slate-900 font-semibold rounded-lg hover:bg-slate-200 transition">
                        Annuler
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition">
                        Planifier
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
