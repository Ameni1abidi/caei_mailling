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
                            <span class="text-gray-700 font-medium">Modifier « {{ $smtpSetting->provider }} »</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900">Modifier la configuration</h1>
            <p class="mt-1 text-sm text-gray-500">Mettez à jour les paramètres du serveur « {{ $smtpSetting->provider }} »</p>
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

        <form action="{{ route('smtp-settings.update', $smtpSetting) }}" method="POST"
              x-data="{
                  driver: '{{ old('driver', $smtpSetting->driver) }}',
                  testResult: null,
                  testing: false
              }">
            @csrf
            @method('PUT')

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
                            @php
                                $providers = ['SMTP personnalisé', 'Brevo (Sendinblue)', 'Amazon SES', 'Mailgun', 'SendGrid', 'Mailtrap'];
                                $currentProvider = old('provider', $smtpSetting->provider);
                            @endphp
                            @foreach($providers as $p)
                                <option value="{{ $p }}" {{ $currentProvider === $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                            @unless(in_array($currentProvider, $providers))
                                <option value="{{ $currentProvider }}" selected>{{ $currentProvider }}</option>
                            @endunless
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
                            <input type="text" name="host" id="host" value="{{ old('host', $smtpSetting->host) }}"
                                   placeholder="smtp.example.com"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('host') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="port" class="block text-sm font-medium text-gray-700 mb-1">Port <span class="text-red-500">*</span></label>
                                <input type="number" name="port" id="port" value="{{ old('port', $smtpSetting->port) }}"
                                       placeholder="587"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                @error('port') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="encryption" class="block text-sm font-medium text-gray-700 mb-1">Chiffrement</label>
                                <select name="encryption" id="encryption"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="">Aucun</option>
                                    <option value="tls" {{ old('encryption', $smtpSetting->encryption) === 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ old('encryption', $smtpSetting->encryption) === 'ssl' ? 'selected' : '' }}>SSL</option>
                                </select>
                                @error('encryption') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Nom d'utilisateur</label>
                            <input type="text" name="username" id="username" value="{{ old('username', $smtpSetting->username) }}"
                                   placeholder="user@example.com"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('username') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                            <input type="password" name="password" id="password"
                                   placeholder="Laisser vide pour ne pas modifier"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <p class="mt-1 text-xs text-gray-500">Laissez vide pour conserver le mot de passe actuel</p>
                            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- API Key --}}
                    <div x-show="driver !== 'smtp' && driver !== 'log'" x-transition>
                        <div class="mb-4">
                            <label for="api_key" class="block text-sm font-medium text-gray-700 mb-1">Clé API</label>
                            <input type="password" name="api_key" id="api_key"
                                   placeholder="Laisser vide pour ne pas modifier"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <p class="mt-1 text-xs text-gray-500">Laissez vide pour conserver la clé actuelle</p>
                            @error('api_key') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Test Connection Button --}}
                    @if($smtpSetting->driver === 'smtp')
                        <div class="mt-5 pt-5 border-t border-gray-100">
                            <button
                                type="button"
                                @click="
                                    testing = true;
                                    testResult = null;
                                    fetch('{{ route('smtp-settings.test', $smtpSetting) }}', {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                            'Accept': 'application/json'
                                        }
                                    })
                                    .then(r => r.json())
                                    .then(data => { testResult = data; testing = false; })
                                    .catch(() => { testResult = { success: false, message: 'Erreur réseau' }; testing = false; })
                                "
                                :disabled="testing"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg border-2 border-dashed border-gray-300 text-gray-700 bg-gray-50 hover:bg-white hover:border-indigo-300 transition-all disabled:opacity-50 disabled:cursor-wait">
                                <svg x-show="!testing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <svg x-show="testing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="testing ? 'Test en cours...' : 'Tester la connexion'"></span>
                            </button>

                            <template x-if="testResult !== null">
                                <div class="mt-3 p-3 rounded-lg text-sm"
                                     :class="testResult.success ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200'">
                                    <div class="flex items-center gap-2">
                                        <svg x-show="testResult.success" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        <svg x-show="!testResult.success" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                        <span x-text="testResult.message"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    @endif
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
                        <input type="text" name="sender_name" id="sender_name" value="{{ old('sender_name', $smtpSetting->sender_name) }}"
                               placeholder="CAEI"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('sender_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="sender_email" class="block text-sm font-medium text-gray-700 mb-1">Email de l'expéditeur <span class="text-red-500">*</span></label>
                        <input type="email" name="sender_email" id="sender_email" value="{{ old('sender_email', $smtpSetting->sender_email) }}"
                               placeholder="contact@caei-afri.com"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('sender_email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="reply_to_email" class="block text-sm font-medium text-gray-700 mb-1">Email de réponse</label>
                        <input type="email" name="reply_to_email" id="reply_to_email" value="{{ old('reply_to_email', $smtpSetting->reply_to_email) }}"
                               placeholder="reply@caei-afri.com"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <p class="mt-1 text-xs text-gray-500">Laissez vide pour utiliser l'email de l'expéditeur</p>
                        @error('reply_to_email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="rate_limit" class="block text-sm font-medium text-gray-700 mb-1">Limite d'envoi</label>
                        <div class="relative">
                            <input type="number" name="rate_limit" id="rate_limit" value="{{ old('rate_limit', $smtpSetting->rate_limit) }}"
                                   min="1" max="10000"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm pr-20">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-400 text-xs">emails/min</span>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Nombre maximum d'emails envoyés par minute</p>
                        @error('rate_limit') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Status info --}}
                    <div class="mt-6 p-4 rounded-lg border {{ $smtpSetting->is_active ? 'bg-emerald-50 border-emerald-200' : 'bg-gray-50 border-gray-200' }}">
                        <div class="flex items-center gap-2">
                            @if($smtpSetting->is_active)
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-sm font-medium text-emerald-700">Cette configuration est actuellement active</span>
                            @else
                                <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                <span class="text-sm font-medium text-gray-600">Cette configuration est inactive</span>
                            @endif
                        </div>
                        <p class="mt-1 text-xs {{ $smtpSetting->is_active ? 'text-emerald-600' : 'text-gray-500' }}">
                            Créée le {{ $smtpSetting->created_at->format('d/m/Y à H:i') }}
                            · Modifiée le {{ $smtpSetting->updated_at->format('d/m/Y à H:i') }}
                        </p>
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
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
