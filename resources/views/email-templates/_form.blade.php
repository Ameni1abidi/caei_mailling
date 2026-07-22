@if($errors->any())
    <div class="bg-red-100 text-red-800 p-2 mb-4">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid gap-4 md:grid-cols-3">
    <div class="md:col-span-2">
        <div class="mb-2">
            <label>Nom du template *</label>
            <input type="text" name="nom" value="{{ old('nom', $emailTemplate->nom ?? '') }}" required class="border w-full p-1"
                   placeholder="Ex: Invitation seminaire">
        </div>

        <div class="mb-2">
            <label>Objet de l'email</label>
            <input type="text" name="sujet" value="{{ old('sujet', $emailTemplate->sujet ?? '') }}" class="border w-full p-1"
                   placeholder="Ex: Invitation officielle au seminaire CAEI">
        </div>

        <div class="mb-2">
            <label>Type *</label>
            <select name="type" required class="border w-full p-1">
                @foreach($types as $value => $label)
                    <option value="{{ $value }}" {{ old('type', $emailTemplate->type ?? 'newsletter') === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-2">
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $emailTemplate->is_active ?? true) ? 'checked' : '' }}>
                <span>Template actif</span>
            </label>
        </div>

        <div class="mb-2">
            <label>Contenu de l'email (HTML ou texte) *</label>
            <textarea name="contenu" rows="14" required class="border w-full p-2 font-mono text-sm">{{ old('contenu', $emailTemplate->contenu ?? '') }}</textarea>
        </div>
    </div>

    <div class="border bg-gray-50 p-4">
        <h2 class="font-semibold mb-3">Variables disponibles</h2>
        <div class="space-y-2 text-sm">
            @foreach($variables as $variable => $description)
                <div>
                    <code class="bg-white border px-1 py-0.5">{{ $variable }}</code>
                    <div class="text-gray-600">{{ $description }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>
