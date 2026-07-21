<x-app-layout>
    <div class="p-6">
        <h1 class="text-xl font-bold mb-4">Modifier : {{ $emailTemplate->nom }}</h1>

        <form action="{{ route('email-templates.update', $emailTemplate) }}" method="POST">
            @csrf
            @method('PUT')

            @include('email-templates._form', ['emailTemplate' => $emailTemplate])

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 mt-2">Enregistrer</button>
            <a href="{{ route('campaigns.create', ['template_id' => $emailTemplate->id]) }}" class="ml-2 bg-gray-600 text-white px-4 py-2 inline-block">Utiliser</a>
            <a href="{{ route('email-templates.index') }}" class="ml-2">Retour</a>
        </form>
    </div>
</x-app-layout>
