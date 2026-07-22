<x-app-layout>
    <div class="p-6">
        <h1 class="text-xl font-bold mb-4">Nouveau template email</h1>

        <form action="{{ route('email-templates.store') }}" method="POST">
            @csrf

            @include('email-templates._form')

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 mt-2">Creer le template</button>
            <a href="{{ route('email-templates.index') }}" class="ml-2">Annuler</a>
        </form>
    </div>
</x-app-layout>
