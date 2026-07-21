<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">

        {{-- Header --}}
        <div class="mb-8">
            <nav class="flex text-sm text-gray-500 mb-3" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1">
                    <li><a href="{{ route('smtp-settings.index') }}" class="hover:text-gray-700 transition-colors">Paramètres SMTP</a></li>
                    <li>
                        <div class="flex items-center">
                            <span class="mx-2 text-gray-400">/</span>
                            <span class="text-gray-700 font-medium">Nouvelle configuration</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900">Nouvelle configuration SMTP</h1>
            <p class="mt-1 text-sm text-gray-500">Configurez un nouveau serveur mail pour l'envoi de vos campagnes</p>
        </div>

        {{-- Errors --}}
        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex gap-2">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-red-800">Veuillez corriger les erreurs suivantes :</p>
                        <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('smtp-settings.store') }}" method="POST" x-data="{ driver: '{{ old('driver', 'smtp') }}' }">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Left Column: Server Configuration --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                        </svg>
                        Configuration serveur
                    </h2>

                    {{-- Provider --}}
                    <div class="mb-4">
                        <label for="provider" class="block text-sm font-medium text-gray-700 mb-1">Fournisseur <span class="text-red-500">*</span></label>
                        <select name="provider" id="provider"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">-- Sélectionnez --</option>
                            <option value="SMTP personnalisé" {{ old('provider') === 'SMTP personnalisé' ? 'selected' : '' }}>SMTP personnalisé</option>
                            <option value="Brevo (Sendinblue)" {{ old('provider') === 'Brevo (Sendinblue)' ? 'selected' : '' }}>Brevo (Sendinblue)</option>
                            <option value="Amazon SES" {{ old('provider') === 'Amazon SES' ? 'selected' : '' }}>Amazon SES</option>
                            <option value="Mailgun" {{ old('provider') === 'Mailgun' ? 'selected' : '' }}>Mailgun</option>
                            <option value="SendGrid" {{ old('provider') === 'SendGrid' ? 'selected' : '' }}>SendGrid</option>
                            <option value="Mailtrap" {{ old('provider') === 'Mailtrap' ? 'selected' : '' }}>Mailtrap (Test)</option>
                        </select>
                        @error('provider') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Driver --}}
                    <div class="mb-4">
                        <label for="driver" class="block text-sm font-medium text-gray-700 mb-1">Driver <span class="text-red-500">*</span></label>
                        <select name="driver" id="driver" x-model="driver"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="smtp">SMTP</option>
                            <option value="ses">Amazon SES</option>
                            <option value="mailgun">Mailgun</option>
                            <option value="log">Log (test)</option>
                        </select>
                        @error('driver') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- SMTP Fields --}}
                    <div x-show="driver === 'smtp'" x-transition>
                        <div class="mb-4">
                            <label for="host" class="block text-sm font-medium text-gray-700 mb-1">Serveur SMTP <span class="text-red-500">*</span></label>
                            <input type="text" name="host" id="host" value="{{ old('host') }}"
                                   placeholder="smtp.example.com"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('host') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="port" class="block text-sm font-medium text-gray-700 mb-1">Port <span class="text-red-500">*</span></label>
                                <input type="number" name="port" id="port" value="{{ old('port', 587) }}"
                                       placeholder="587"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                @error('port') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="encryption" class="block text-sm font-medium text-gray-700 mb-1">Chiffrement</label>
                                <select name="encryption" id="encryption"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="">Aucun</option>
                                    <option value="tls" {{ old('encryption') === 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ old('encryption') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                </select>
                                @error('encryption') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Nom d'utilisateur</label>
                            <input type="text" name="username" id="username" value="{{ old('username') }}"
                                   placeholder="user@example.com"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('username') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                            <input type="password" name="password" id="password"
                                   placeholder="••••••••"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- API Key (non-SMTP drivers) --}}
                    <div x-show="driver !== 'smtp' && driver !== 'log'" x-transition>
                        <div class="mb-4">
                            <label for="api_key" class="block text-sm font-medium text-gray-700 mb-1">Clé API</label>
                            <input type="password" name="api_key" id="api_key"
                                   placeholder="Votre clé API"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('api_key') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Right Column: Sender Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Informations expéditeur
                    </h2>

                    <div class="mb-4">
                        <label for="sender_name" class="block text-sm font-medium text-gray-700 mb-1">Nom de l'expéditeur <span class="text-red-500">*</span></label>
                        <input type="text" name="sender_name" id="sender_name" value="{{ old('sender_name') }}"
                               placeholder="CAEI"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('sender_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="sender_email" class="block text-sm font-medium text-gray-700 mb-1">Email de l'expéditeur <span class="text-red-500">*</span></label>
                        <input type="email" name="sender_email" id="sender_email" value="{{ old('sender_email') }}"
                               placeholder="contact@caei-afri.com"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('sender_email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="reply_to_email" class="block text-sm font-medium text-gray-700 mb-1">Email de réponse</label>
                        <input type="email" name="reply_to_email" id="reply_to_email" value="{{ old('reply_to_email') }}"
                               placeholder="reply@caei-afri.com"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <p class="mt-1 text-xs text-gray-500">Laissez vide pour utiliser l'email de l'expéditeur</p>
                        @error('reply_to_email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="rate_limit" class="block text-sm font-medium text-gray-700 mb-1">Limite d'envoi</label>
                        <div class="relative">
                            <input type="number" name="rate_limit" id="rate_limit" value="{{ old('rate_limit', 100) }}"
                                   min="1" max="10000"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm pr-20">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-400 text-xs">emails/min</span>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Nombre maximum d'emails envoyés par minute</p>
                        @error('rate_limit') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Provider Tips --}}
                    <div class="mt-6 bg-gray-50 rounded-lg p-4 border border-gray-100">
                        <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Ports courants</h3>
                        <div class="space-y-1 text-xs text-gray-500">
                            <div class="flex justify-between"><span>SMTP + TLS</span><span class="font-mono">587</span></div>
                            <div class="flex justify-between"><span>SMTP + SSL</span><span class="font-mono">465</span></div>
                            <div class="flex justify-between"><span>SMTP (non chiffré)</span><span class="font-mono">25</span></div>
                            <div class="flex justify-between"><span>Mailtrap</span><span class="font-mono">2525</span></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 mt-6">
                <a href="{{ route('smtp-settings.index') }}"
                   class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Annuler
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition-all hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
