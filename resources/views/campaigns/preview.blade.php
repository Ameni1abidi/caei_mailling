<x-app-layout>
    <div class="p-6">
        <h1 class="text-xl font-bold mb-4">Aperçu — {{ $campaign->nom }}</h1>

        <div class="mb-6 max-w-md">
            <form method="GET" action="{{ route('campaigns.preview', $campaign) }}">
                <label for="contact_id" class="block text-sm font-medium text-gray-700 mb-1">Sélectionner un contact pour l'aperçu :</label>
                <select name="contact_id" id="contact_id" onchange="this.form.submit()" class="border w-full p-2 rounded">
                    @foreach($contactsDisponibles as $cd)
                        <option value="{{ $cd->id }}" {{ $contact->id == $cd->id ? 'selected' : '' }}>
                            {{ $cd->nom }} {{ $cd->prenom }} ({{ $cd->email }})
                        </option>
                    @endforeach
                </select>
            </form>
            <p class="text-sm text-gray-500 mt-1">
                {{ $contactsDisponibles->count() }} destinataire(s) au total pour cette campagne
            </p>
        </div>

        <p class="text-sm text-gray-600 mb-4">
            Aperçu généré pour : <strong>{{ $contact->prenom }} {{ $contact->nom }}</strong> ({{ $contact->email }})
        </p>

        <div class="border p-4 bg-white max-w-2xl">
            <p class="text-sm text-gray-500 border-b pb-2 mb-2">
                <strong>Objet :</strong> {{ $objetPersonnalise }}
            </p>
            <div>{!! nl2br(e($contenuPersonnalise)) !!}</div>
        </div>

        <a href="{{ route('campaigns.edit', $campaign) }}" class="mt-4 inline-block">← Retour à l'édition</a>
    </div>
</x-app-layout>