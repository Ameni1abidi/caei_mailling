<x-app-layout>
    <div class="p-6">
        <h1 class="text-xl font-bold mb-4">Modifier : {{ $campaign->nom }}</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-2 mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 text-red-800 p-2 mb-4">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 text-red-800 p-2 mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <p class="mb-4 text-sm text-gray-600">
            Destinataires ciblés : <strong>{{ $nbDestinataires }}</strong> contact(s)
        </p>

        {{-- Formulaire 1 : mise à jour de la campagne --}}
        <form action="{{ route('campaigns.update', $campaign) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-2">
                <label>Nom de la campagne *</label>
                <input type="text" name="nom" value="{{ old('nom', $campaign->nom) }}" required class="border w-full p-1">
            </div>

            <div class="mb-2">
                <label>Objet de l'email *</label>
                <input type="text" name="objet" value="{{ old('objet', $campaign->objet) }}" required class="border w-full p-1">
            </div>

            <div class="mb-2">
                <label>Liste ciblée</label>
                <select name="category_id" class="border w-full p-1">
                    <option value="">-- Tous les contacts --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $campaign->category_id == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-2">
                <label>Contenu de l'email (HTML) *</label>
                <p class="text-sm text-gray-500">
                    Variables : @{{nom}}, @{{prenom}}, @{{entreprise}}, @{{fonction}}, @{{pays}}, @{{nom_seminaire}}, @{{date}}, @{{lien}}
                </p>
                <textarea name="contenu" rows="12" required class="border w-full p-2 font-mono text-sm">{{ old('contenu', $campaign->contenu) }}</textarea>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 mt-2">Enregistrer</button>
            <a href="{{ route('campaigns.preview', $campaign) }}" class="ml-2 bg-gray-600 text-white px-4 py-2 inline-block">Aperçu</a>
            <a href="{{ route('campaigns.index') }}" class="ml-2">Retour</a>
        </form>

        {{-- Section : Pièces jointes de la campagne --}}
        <div class="mt-8 border-t pt-6">
            <h2 class="text-lg font-bold mb-3">Pièces jointes de cette campagne ({{ $campaign->attachments->count() }})</h2>

            @if($campaign->attachments->isNotEmpty())
                <div class="bg-white border rounded mb-4 overflow-hidden">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase">
                            <tr>
                                <th class="p-3">Fichier</th>
                                <th class="p-3">Type</th>
                                <th class="p-3">Taille</th>
                                <th class="p-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($campaign->attachments as $attachment)
                                <tr>
                                    <td class="p-3 font-medium text-gray-800">{{ $attachment->file_name }}</td>
                                    <td class="p-3">
                                        <span class="inline-block bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded">
                                            {{ $attachment->typeLabel() }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-xs text-gray-500 font-mono">
                                        {{ $attachment->file_size ? number_format($attachment->file_size / 1024, 1) . ' KB' : 'N/A' }}
                                    </td>
                                    <td class="p-3 text-right space-x-2">
                                        <a href="{{ route('attachments.download', $attachment) }}" class="text-blue-600 hover:underline text-xs font-semibold">
                                            Télécharger
                                        </a>
                                        <form action="{{ route('attachments.destroy', $attachment) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette pièce jointe ?')">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="redirect_to_campaign" value="1">
                                            <button type="submit" class="text-red-600 hover:underline text-xs font-semibold">
                                                Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500 mb-4">Aucune pièce jointe associée à cette campagne pour le moment.</p>
            @endif

            {{-- Formulaire d'ajout rapide de pièce jointe --}}
            <form action="{{ route('attachments.store') }}" method="POST" enctype="multipart/form-data" class="bg-gray-50 p-4 border rounded">
                @csrf
                <input type="hidden" name="campaign_id" value="{{ $campaign->id }}">
                <input type="hidden" name="redirect_to_campaign" value="1">

                <h3 class="text-sm font-semibold mb-2">Ajouter des pièces jointes à cette campagne</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Type (optionnel)</label>
                        <select name="attachment_type" class="border w-full p-1.5 text-sm rounded bg-white">
                            <option value="">-- Non spécifié --</option>
                            @foreach(\App\Models\CampaignAttachment::types() as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Fichier(s) *</label>
                        <input type="file" name="files[]" multiple required class="border w-full p-1 text-sm bg-white rounded">
                    </div>
                    <div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded">
                            Téléverser et Joindre
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Formulaire 2 : envoi de la campagne --}}
        <form action="{{ route('campaigns.send', $campaign) }}" method="POST"
              onsubmit="return confirm('Envoyer cette campagne à {{ $nbDestinataires }} contact(s) ?')" class="mt-8">
            @csrf
            <button type="submit" class="bg-green-600 text-white px-4 py-2">
                Envoyer maintenant ({{ $nbDestinataires }} destinataires)
            </button>
        </form>
    </div>
</x-app-layout>
