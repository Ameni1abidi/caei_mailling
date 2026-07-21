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
        <form action="{{ route('campaigns.update', $campaign) }}" method="POST">
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

        {{-- Formulaire 2 : envoi de la campagne (indépendant, pas imbriqué dans le précédent) --}}
        <form action="{{ route('campaigns.send', $campaign) }}" method="POST"
              onsubmit="return confirm('Envoyer cette campagne à {{ $nbDestinataires }} contact(s) ?')" class="mt-4">
            @csrf
            <button type="submit" class="bg-green-600 text-white px-4 py-2">
                Envoyer maintenant ({{ $nbDestinataires }} destinataires)
            </button>
        </form>
    </div>
</x-app-layout>
