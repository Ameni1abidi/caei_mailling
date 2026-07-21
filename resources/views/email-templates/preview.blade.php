<x-app-layout>
    <div class="p-6">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-bold">Previsualisation : {{ $emailTemplate->nom }}</h1>
            <div>
                <a href="{{ route('email-templates.edit', $emailTemplate) }}" class="bg-blue-600 text-white px-4 py-2 inline-block">Modifier</a>
                <a href="{{ route('email-templates.index') }}" class="ml-2">Retour</a>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="lg:col-span-2 border bg-white p-5">
                <div class="mb-4 border-b pb-3">
                    <div class="text-sm text-gray-500">Objet</div>
                    <div class="text-lg font-semibold">{{ $preview['sujet'] }}</div>
                </div>
                <div class="prose max-w-none">
                    {!! nl2br(\App\Models\EmailTemplate::sanitizeContent($preview['contenu'])) !!}
                </div>
            </div>

            <div class="border bg-gray-50 p-4">
                <h2 class="font-semibold mb-3">Valeurs d'exemple</h2>
                <div class="space-y-2 text-sm">
                    @foreach($variables as $name => $value)
                        <div class="flex justify-between gap-3 border-b pb-1">
                            <code>{{ '{' . '{' . $name . '}' . '}' }}</code>
                            <span class="text-gray-700">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
