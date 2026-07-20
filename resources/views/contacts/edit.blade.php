<x-app-layout>
    <div class="p-6">
        <h1 class="text-xl font-bold mb-4">Modifier le contact</h1>

        @if($errors->any())
            <div class="bg-red-100 text-red-800 p-2 mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('contacts.update', $contact) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-2">
                <label>Nom *</label>
                <input type="text" name="nom" value="{{ old('nom', $contact->nom) }}" required class="border w-full p-1">
            </div>

            <div class="mb-2">
                <label>Prénom *</label>
                <input type="text" name="prenom" value="{{ old('prenom', $contact->prenom) }}" required class="border w-full p-1">
            </div>

            <div class="mb-2">
                <label>Email *</label>
                <input type="email" name="email" value="{{ old('email', $contact->email) }}" required class="border w-full p-1">
            </div>

            <div class="mb-2">
                <label>Entreprise</label>
                <input type="text" name="entreprise" value="{{ old('entreprise', $contact->entreprise) }}" class="border w-full p-1">
            </div>

            <div class="mb-2">
                <label>Fonction</label>
                <input type="text" name="fonction" value="{{ old('fonction', $contact->fonction) }}" class="border w-full p-1">
            </div>

            <div class="mb-2">
                <label>Téléphone</label>
                <input type="text" name="telephone" value="{{ old('telephone', $contact->telephone) }}" class="border w-full p-1">
            </div>

            <div class="mb-2">
                <label>WhatsApp</label>
                <input type="text" name="whatsapp" value="{{ old('whatsapp', $contact->whatsapp) }}" class="border w-full p-1">
            </div>

            <div class="mb-2">
                <label>Pays</label>
                <input type="text" name="pays" value="{{ old('pays', $contact->pays) }}" class="border w-full p-1">
            </div>

            <div class="mb-2">
                <label>Ville</label>
                <input type="text" name="ville" value="{{ old('ville', $contact->ville) }}" class="border w-full p-1">
            </div>

            <div class="mb-2">
                <label>Secteur d'activité</label>
                <input type="text" name="secteur_activite" value="{{ old('secteur_activite', $contact->secteur_activite) }}" class="border w-full p-1">
            </div>

            <div class="mb-2">
                <label>Source du contact</label>
                <input type="text" name="source" value="{{ old('source', $contact->source) }}" class="border w-full p-1">
            </div>

            <div class="mb-2">
                <label>Catégorie / Liste</label>
                <input type="text" name="categorie" value="{{ old('categorie', $contact->categorie) }}" class="border w-full p-1">
            </div>

            <div class="mb-2">
                <label>Notes</label>
                <textarea name="notes" class="border w-full p-1">{{ old('notes', $contact->notes) }}</textarea>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 mt-2">Mettre à jour</button>
            <a href="{{ route('contacts.index') }}" class="ml-2">Annuler</a>
        </form>
    </div>
</x-app-layout>