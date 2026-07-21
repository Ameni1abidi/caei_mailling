<x-app-layout>
    <div class="p-6">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-bold">Templates emails</h1>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('email-templates.create') }}" class="bg-blue-600 text-white px-4 py-2 inline-block">
                    + Nouveau template
                </a>
                <form action="{{ route('email-templates.install-defaults') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-gray-700 text-white px-4 py-2">
                        Ajouter les exemples
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-2 mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 text-red-800 p-2 mb-4">{{ session('error') }}</div>
        @endif

        <form method="GET" action="{{ route('email-templates.index') }}" class="mb-4 grid gap-2 md:grid-cols-4">
            <input type="text" name="search" value="{{ request('search') }}" class="border p-2 md:col-span-2"
                   placeholder="Rechercher par nom, objet ou contenu">
            <select name="type" class="border p-2">
                <option value="">Tous les types</option>
                @foreach($types as $value => $label)
                    <option value="{{ $value }}" {{ request('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="status" class="border p-2">
                <option value="">Tous les statuts</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actifs</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactifs</option>
            </select>
            <div class="md:col-span-4">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2">Filtrer</button>
                <a href="{{ route('email-templates.index') }}" class="ml-2">Reinitialiser</a>
            </div>
        </form>

        <table class="w-full border">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Type</th>
                    <th>Objet</th>
                    <th>Statut</th>
                    <th>Apercu</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $template)
                    <tr>
                        <td class="p-2">{{ $template->nom }}</td>
                        <td class="p-2">
                            <span class="rounded bg-blue-100 px-2 py-1 text-xs text-blue-800">{{ $template->type_label }}</span>
                        </td>
                        <td class="p-2">{{ $template->sujet ?: '-' }}</td>
                        <td class="p-2">
                            <span class="rounded px-2 py-1 text-xs {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
                                {{ $template->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="p-2">{{ \Illuminate\Support\Str::limit(strip_tags($template->contenu), 80) }}</td>
                        <td class="p-2">
                            @if($template->is_active)
                                <a href="{{ route('campaigns.create', ['template_id' => $template->id]) }}">Utiliser</a>
                            @endif
                            <a href="{{ route('email-templates.preview', $template) }}">Previsualiser</a>
                            <a href="{{ route('email-templates.edit', $template) }}">Modifier</a>
                            <form action="{{ route('email-templates.toggle', $template) }}" method="POST" style="display:inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit">{{ $template->is_active ? 'Desactiver' : 'Activer' }}</button>
                            </form>
                            <form action="{{ route('email-templates.destroy', $template) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Supprimer ce template ?')">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-4 text-center text-gray-500">
                            Aucun template trouve.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $templates->links() }}
    </div>
</x-app-layout>
