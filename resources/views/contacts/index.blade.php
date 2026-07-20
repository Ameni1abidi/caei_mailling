<x-app-layout>
    <div class="p-6">
        <h1 class="text-xl font-bold mb-4">Contacts</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-2 mb-4">{{ session('success') }}</div>
        @endif

        <form action="{{ route('contacts.import') }}" method="POST" enctype="multipart/form-data" class="mb-4">
            @csrf
            <input type="file" name="file" required>
            <button type="submit">Importer Excel/CSV</button>
        </form>
        

        <a href="{{ route('contacts.create') }}">+ Ajouter un contact</a>

        <table class="w-full mt-4 border">
            <thead>
                <tr>
                    <th>Nom</th><th>Prénom</th><th>Entreprise</th><th>Email</th><th>Pays</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contacts as $contact)
                <tr>
                    <td>{{ $contact->nom }}</td>
                    <td>{{ $contact->prenom }}</td>
                    <td>{{ $contact->entreprise }}</td>
                    <td>{{ $contact->email }}</td>
                    <td>{{ $contact->pays }}</td>
                    <td>
                        <a href="{{ route('contacts.edit', $contact) }}">Modifier</a>
                        <form action="{{ route('contacts.destroy', $contact) }}" method="POST" style="display:inline">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Supprimer ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ $contacts->links() }}
    </div>
</x-app-layout>