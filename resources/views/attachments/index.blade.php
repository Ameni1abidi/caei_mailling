<x-app-layout>
    <div class="p-6 space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 3h7l5 5v13H7zM14 3v6h5M10 14h6M10 17h4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Fichiers & Pièces jointes</h1>
                    <p class="text-sm text-slate-500">Gérez les documents et pièces jointes associés à vos campagnes d'emailing</p>
                </div>
            </div>
            <div>
                <a href="{{ route('attachments.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow-sm transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajouter une pièce jointe
                </a>
            </div>
        </div>

        <!-- Flash messages -->
        @if(session('success'))
            <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl text-sm font-medium">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl text-sm font-medium">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Fichiers</div>
                    <div class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['total'] }}</div>
                </div>
                <div class="p-3 bg-slate-100 text-slate-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Programmes PDF</div>
                    <div class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['pdf_count'] }}</div>
                </div>
                <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Brochures & Autres</div>
                    <div class="text-2xl font-bold text-indigo-600 mt-1">{{ $stats['brochure_count'] }}</div>
                </div>
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <form method="GET" action="{{ route('attachments.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par nom de fichier..." class="w-full text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <select name="campaign_id" class="w-full text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Toutes les campagnes</option>
                        @foreach($campaigns as $camp)
                            <option value="{{ $camp->id }}" {{ request('campaign_id') == $camp->id ? 'selected' : '' }}>
                                {{ $camp->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="type" class="w-full text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tous les types</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold py-2 px-4 rounded-lg transition">
                        Filtrer
                    </button>
                    @if(request()->hasAny(['search', 'campaign_id', 'type']))
                        <a href="{{ route('attachments.index') }}" class="px-3 py-2 text-slate-500 hover:text-slate-700 text-sm font-semibold">
                            Réinitialiser
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Attachments Table -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4">Fichier</th>
                            <th class="px-6 py-4">Type</th>
                            <th class="px-6 py-4">Campagne liée</th>
                            <th class="px-6 py-4">Taille</th>
                            <th class="px-6 py-4">Date d'ajout</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($attachments as $attachment)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-6 py-4 font-medium text-slate-900 flex items-center gap-3">
                                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div class="truncate max-w-xs" title="{{ $attachment->file_name }}">
                                        {{ $attachment->file_name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $attachment->typeLabel() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($attachment->campaign)
                                        <a href="{{ route('campaigns.edit', $attachment->campaign) }}" class="text-blue-600 hover:underline font-medium">
                                            {{ $attachment->campaign->nom }}
                                        </a>
                                    @else
                                        <span class="text-slate-400">Non spécifiée</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-500 font-mono text-xs">
                                    {{ $attachment->file_size ? number_format($attachment->file_size / 1024, 1) . ' KB' : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-slate-500">
                                    {{ $attachment->created_at ? $attachment->created_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('attachments.download', $attachment) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-2.5 py-1.5 rounded-md transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Télécharger
                                    </a>
                                    <form action="{{ route('attachments.destroy', $attachment) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette pièce jointe ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 text-xs font-semibold text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 px-2.5 py-1.5 rounded-md transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="font-medium text-slate-600">Aucune pièce jointe trouvée</p>
                                    <p class="text-xs text-slate-400 mt-1">Ajoutez un nouveau fichier pour commencer à joindre des documents à vos campagnes.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($attachments->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                    {{ $attachments->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
