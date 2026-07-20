<x-app-layout>
    <div class="p-6">
        <h1 class="text-xl font-bold mb-4">Campagnes</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-2 mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 text-red-800 p-2 mb-4">{{ session('error') }}</div>
        @endif

        <a href="{{ route('campaigns.create') }}" class="bg-blue-600 text-white px-4 py-2 inline-block mb-4">
            + Nouvelle campagne
        </a>

        <table class="w-full border">
            <thead>
                <tr>
                    <th>Nom</th><th>Objet</th><th>Cible</th><th>Statut</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($campaigns as $campaign)
                <tr>
                    <td>{{ $campaign->nom }}</td>
                    <td>{{ $campaign->objet }}</td>
                    <td>{{ $campaign->category?->name ?? 'Tous les contacts' }}</td>
                    <td>
                        <span class="px-2 py-1 text-xs rounded
                            @if($campaign->statut === 'brouillon') bg-gray-200
                            @elseif($campaign->statut === 'envoyee') bg-green-200
                            @else bg-yellow-200 @endif">
                            {{ $campaign->statut }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('campaigns.edit', $campaign) }}">Modifier</a>
                        <a href="{{ route('campaigns.preview', $campaign) }}">Aperçu</a>
                        @if($campaign->statut === 'brouillon')
                        <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST" style="display:inline">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Supprimer ?')">Supprimer</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ $campaigns->links() }}
    </div>
</x-app-layout>