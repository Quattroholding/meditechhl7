<div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
            Almacenamiento Externo
        </h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Configure el almacenamiento externo para archivos de consultas médicas con renovación automática de tokens.
        </p>
    </div>

    <!-- Estado de conexión -->
    @if($isConnected)
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-green-800 dark:text-green-200">
                        Dropbox Conectado
                    </h3>
                    <div class="mt-2 text-sm text-green-700 dark:text-green-300">
                        @if($accountInfo)
                            <p>Cuenta: {{ $accountInfo }}</p>
                        @endif
                        @if($expiresAt)
                            <p>Token válido hasta: {{ \Carbon\Carbon::parse($expiresAt)->format('d/m/Y H:i') }}</p>
                            <p class="text-xs mt-1 text-green-600 dark:text-green-400">
                                ✓ Los tokens se renovarán automáticamente
                            </p>
                        @endif
                    </div>
                    <div class="mt-3">
                        <button type="button"
                                wire:click="disconnectDropbox"
                                class="inline-flex items-center px-3 py-1.5 border border-green-600 dark:border-green-400 rounded-md text-xs font-medium text-green-700 dark:text-green-300 hover:bg-green-100 dark:hover:bg-green-900/40 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Desconectar Dropbox
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toggle para habilitar/deshabilitar -->
        <div class="mb-6">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" wire:model.live="enabled" wire:click="toggleEnabled" class="sr-only peer">
                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">
                    {{ $enabled ? 'Almacenamiento Externo Activo' : 'Almacenamiento Externo Inactivo' }}
                </span>
            </label>
            <p class="mt-2 ml-14 text-xs text-gray-500 dark:text-gray-400">
                {{ $enabled ? 'Los archivos nuevos se guardarán en Dropbox' : 'Los archivos nuevos se guardarán localmente' }}
            </p>
        </div>
    @else
        <!-- Botón para conectar con Dropbox -->
        <div class="mb-6">
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                <div class="flex items-start">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.5 16L10 13l4.5 3L20 13l-5.5-3L10 13 5.5 10 0 13l5.5 3zm0-6L10 7l4.5 3L20 7l-5.5-3L10 7 5.5 4 0 7l5.5 3z"/>
                    </svg>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                            Conectar con Dropbox
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Conecte su cuenta de Dropbox para almacenar archivos de consultas médicas de forma segura en la nube.
                        </p>
                        <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-2 mb-4">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Renovación automática de tokens - sin interrupciones</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Almacenamiento seguro y encriptado</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Acceso desde cualquier dispositivo</span>
                            </li>
                        </ul>
                        <button type="button"
                                wire:click="connectDropbox"
                                class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5.5 16L10 13l4.5 3L20 13l-5.5-3L10 13 5.5 10 0 13l5.5 3zm0-6L10 7l4.5 3L20 7l-5.5-3L10 7 5.5 4 0 7l5.5 3z"/>
                            </svg>
                            Conectar con Dropbox
                        </button>
                        <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                            Será redirigido a Dropbox para autorizar la conexión de forma segura.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Información adicional -->
    <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
            <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            Información Importante
        </h4>
        <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-1 ml-5">
            <li>• Los archivos se organizan por paciente y consulta en Dropbox</li>
            <li>• Los tokens de acceso se renuevan automáticamente sin intervención manual</li>
            <li>• Solo imágenes (JPG, PNG, GIF) hasta 10MB por archivo</li>
            <li>• Los archivos locales existentes no se migran automáticamente</li>
        </ul>
    </div>

    <!-- Mensajes flash -->
    @if (session()->has('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50"
             x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 5000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50"
             x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 5000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            {{ session('error') }}
        </div>
    @endif
</div>
