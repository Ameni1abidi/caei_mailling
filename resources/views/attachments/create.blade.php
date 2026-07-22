<x-app-layout>
    <div class="p-6 max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Ajouter une ou plusieurs pièces jointes</h1>
                <p class="text-sm text-slate-500">Téléversez un ou plusieurs documents et associez-les à une campagne d'emailing</p>
            </div>
            <a href="{{ route('attachments.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900 border border-slate-300 px-4 py-2 rounded-lg transition">
                Retour à la liste
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            @if($errors->any())
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-xl text-sm">
                    <div class="font-semibold mb-1">Veuillez corriger les erreurs ci-dessous :</div>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('attachments.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Campaign selection -->
                <div>
                    <label for="campaign_id" class="block text-sm font-semibold text-slate-700 mb-2">
                        Campagne associée <span class="text-rose-500">*</span>
                    </label>
                    <select name="campaign_id" id="campaign_id" required class="w-full text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Sélectionner une campagne --</option>
                        @foreach($campaigns as $camp)
                            <option value="{{ $camp->id }}" {{ old('campaign_id', request('campaign_id')) == $camp->id ? 'selected' : '' }}>
                                {{ $camp->nom }} ({{ $camp->statut }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500 mt-1">Les pièces jointes seront envoyées automatiquement avec les emails de cette campagne.</p>
                </div>

                <!-- Attachment Type -->
                <div>
                    <label for="attachment_type" class="block text-sm font-semibold text-slate-700 mb-2">
                        Type de pièce jointe <span class="text-slate-400 font-normal">(Optionnel)</span>
                    </label>
                    <select name="attachment_type" id="attachment_type" class="w-full text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Aucun / Pièce jointe générale --</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ old('attachment_type') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- File input -->
                <div>
                    <label for="files" class="block text-sm font-semibold text-slate-700 mb-2">
                        Fichier(s) à téléverser <span class="text-rose-500">*</span>
                    </label>
                    <input type="file" name="files[]" id="files" multiple required class="w-full text-sm text-slate-500 border border-slate-300 rounded-lg cursor-pointer bg-slate-50 focus:outline-none p-2.5">
                    <p class="text-xs text-slate-500 mt-1">Vous pouvez sélectionner un ou plusieurs fichiers simultanément. Formats autorisés: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG, GIF, ZIP, RAR, TXT. Taille max: 15 Mo par fichier.</p>
                </div>

                <!-- Submit buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <a href="{{ route('attachments.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-800 transition">
                        Annuler
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-6 py-2.5 rounded-lg shadow-sm transition">
                        Enregistrer la pièce jointe
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
