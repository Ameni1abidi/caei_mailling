<x-app-layout>
    <div class="p-6 space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center gap-4">
                <a href="{{ route('contacts.index') }}" class="p-2 hover:bg-slate-100 rounded-lg transition text-slate-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div class="p-3.5 bg-gradient-to-br from-violet-500 to-indigo-600 text-white rounded-2xl shadow-md shadow-indigo-100">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Historique des imports</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Suivi de tous les fichiers CSV / Excel importés dans la base de contacts</p>
                </div>
            </div>
            <div class="flex items-center gap-2 self-start md:self-auto">
                <span class="px-3 py-1.5 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl">
                    {{ $imports->total() }} import(s) au total
                </span>
            </div>
        </div>

        <!-- Flash messages -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition class="flex items-center justify-between gap-3 bg-emerald-50 border border-emerald-200 text-emerald-900 p-4 rounded-xl text-sm font-medium shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-1 bg-emerald-100 rounded-lg text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
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

        <!-- Import History Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            @if($imports->isEmpty())
                <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
                    <div class="w-20 h-20 bg-slate-50 text-slate-300 rounded-2xl flex items-center justify-center mx-auto mb-5">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Aucun import enregistré</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">Les imports futurs apparaîtront ici avec leurs statistiques.</p>
                    <a href="{{ route('contacts.index') }}" class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-4 py-2 rounded-xl transition shadow-sm">
                        Importer un fichier
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50/80 text-xs uppercase font-bold text-slate-500 border-b border-slate-200/80">
                            <tr>
                                <th class="px-6 py-4">Fichier</th>
                                <th class="px-6 py-4">Date d'import</th>
                                <th class="px-6 py-4">Importé par</th>
                                <th class="px-6 py-4 text-center">Lignes lues</th>
                                <th class="px-6 py-4 text-center">Ajoutés</th>
                                <th class="px-6 py-4 text-center">Doublons</th>
                                <th class="px-6 py-4 text-center">Erreurs</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($imports as $import)
                                @php
                                    $successRate = $import->total_rows > 0
                                        ? round(($import->imported / $import->total_rows) * 100)
                                        : 0;
                                    $hasErrors   = $import->errors > 0;
                                    $hasDupes    = $import->duplicates > 0;
                                @endphp
                                <tr class="hover:bg-slate-50/60 transition group">
                                    <!-- Filename -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="p-2 bg-indigo-50 text-indigo-500 rounded-lg shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 group-hover:text-indigo-600 transition truncate max-w-[220px]" title="{{ $import->filename }}">
                                                    {{ $import->filename }}
                                                </div>
                                                <div class="text-xs text-slate-400 mt-0.5">Import #{{ $import->id }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Date -->
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-slate-700">{{ $import->created_at->format('d/m/Y') }}</div>
                                        <div class="text-xs text-slate-400">{{ $import->created_at->format('H:i') }}</div>
                                    </td>

                                    <!-- User -->
                                    <td class="px-6 py-4">
                                        @if($import->user)
                                            <div class="flex items-center gap-2">
                                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white text-xs font-bold flex items-center justify-center shrink-0">
                                                    {{ strtoupper(substr($import->user->name, 0, 1)) }}
                                                </div>
                                                <span class="font-medium text-slate-700 text-xs">{{ $import->user->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400 italic">Inconnu</span>
                                        @endif
                                    </td>

                                    <!-- Total Rows -->
                                    <td class="px-6 py-4 text-center">
                                        <span class="font-bold text-slate-700">{{ number_format($import->total_rows) }}</span>
                                    </td>

                                    <!-- Imported -->
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            {{ number_format($import->imported) }}
                                        </span>
                                    </td>

                                    <!-- Duplicates -->
                                    <td class="px-6 py-4 text-center">
                                        @if($hasDupes)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                {{ number_format($import->duplicates) }}
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-300 font-medium">—</span>
                                        @endif
                                    </td>

                                    <!-- Errors -->
                                    <td class="px-6 py-4 text-center">
                                        @if($hasErrors)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                {{ number_format($import->errors) }}
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-300 font-medium">—</span>
                                        @endif
                                    </td>

                                    <!-- Action -->
                                    <td class="px-6 py-4 text-right">
                                        @if($import->imported > 0)
                                            <a href="{{ route('contacts.index', ['import_log_id' => $import->id]) }}"
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition border border-indigo-100">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                Voir les contacts ({{ number_format($import->contacts_count) }})
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-400 italic">Aucun contact</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($imports->hasPages())
                    <div class="px-6 py-4 border-t border-slate-200/80 bg-slate-50/50">
                        {{ $imports->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
