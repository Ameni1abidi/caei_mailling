<x-app-layout>
    <div class="p-6">
        <h1 class="text-xl font-bold mb-4">Ajouter un contact</h1>

        @if($errors->any())
            <div class="bg-red-100 text-red-800 p-2 mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('contacts.store') }}" method="POST">
            @csrf

            <div class="mb-2">
                <label>Nom *</label>
                <input type="text" name="nom" value="{{ old('nom') }}" required class="border w-full p-1">
            </div>

            <div class="mb-2">
                <label>Prénom *</label>
                <input type="text" name="prenom" value="{{ old('prenom') }}" required class="border w-full p-1">
            </div>

            <div class="mb-2">
                <label>Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="border w-full p-1">
            </div>

            <div class="mb-2">
                <label>Entreprise</label>
                <input type="text" name="entreprise" value="{{ old('entreprise') }}" class="border w-full p-1">
            </div>

            <div class="mb-2">
                <label>Fonction</label>
                <input type="text" name="fonction" value="{{ old('fonction') }}" class="border w-full p-1">
            </div>

            <div class="mb-2">
                <label>Téléphone</label>
                <input type="text" name="telephone" value="{{ old('telephone') }}" class="border w-full p-1">
            </div>

            <div class="mb-2">
                <label>WhatsApp</label>
                <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" class="border w-full p-1">
            </div>

            <div class="mb-2">
                <label>Pays</label>
                <input type="text" name="pays" value="{{ old('pays') }}" class="border w-full p-1">
            </div>

            <div class="mb-2">
                <label>Ville</label>
                <input type="text" name="ville" value="{{ old('ville') }}" class="border w-full p-1">
            </div>

            <div class="mb-2">
                <label>Secteur d'activité</label>
                <input type="text" name="secteur_activite" value="{{ old('secteur_activite') }}" class="border w-full p-1">
            </div>

            <div class="mb-2">
                <label>Source du contact</label>
                <input type="text" name="source" value="{{ old('source') }}" class="border w-full p-1">
            </div>

            <div class="mb-2">
                <label>Catégorie / Liste</label>
                <input type="text" name="categorie" value="{{ old('categorie') }}" class="border w-full p-1" placeholder="Ex: Banques Côte d'Ivoire">
            </div>

            <div class="mb-2">
                <label>Notes</label>
                <textarea name="notes" class="border w-full p-1">{{ old('notes') }}</textarea>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 mt-2">Enregistrer</button>
            <a href="{{ route('contacts.index') }}" class="ml-2">Annuler</a>
        </form>
    </div>
</x-app-layout>