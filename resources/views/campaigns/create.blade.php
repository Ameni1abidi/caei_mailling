<x-app-layout>
    <div class="p-6">
        <h1 class="text-xl font-bold mb-4">Nouvelle campagne</h1>

        @if($errors->any())
            <div class="bg-red-100 text-red-800 p-2 mb-4">
                <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('campaigns.store') }}" method="POST">
            @csrf

            <div class="mb-2">
                <label>Nom de la campagne *</label>
                <input type="text" name="nom" value="{{ old('nom') }}" required class="border w-full p-1"
                       placeholder="Ex: Séminaire International IA & Fraude">
            </div>

            <div class="mb-2">
                <label>Objet de l'email *</label>
                <input type="text" name="objet" value="{{ old('objet') }}" required class="border w-full p-1"
                       placeholder="Ex: Invitation officielle au séminaire international CAEI">
            </div>

            <div class="mb-2">
                <label>Liste ciblée</label>
                <select name="category_id" class="border w-full p-1">
                    <option value="">-- Tous les contacts --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-2">
                <label>Contenu de l'email (HTML) *</label>
                <p class="text-sm text-gray-500">
                    Variables disponibles : @{{Nom}}, @{{Prenom}}, @{{Entreprise}}, @{{Fonction}}, @{{Pays}}
                </p>
                <textarea name="contenu" rows="12" required class="border w-full p-2 font-mono text-sm">@if(old('contenu')){{ old('contenu') }}@else Bonjour @{{Prenom}} @{{Nom}},

Nous avons le plaisir de vous inviter...

Cordialement,
L'équipe CAEI @endif</textarea>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 mt-2">Créer la campagne</button>
            <a href="{{ route('campaigns.index') }}" class="ml-2">Annuler</a>
        </form>
    </div>
</x-app-layout>