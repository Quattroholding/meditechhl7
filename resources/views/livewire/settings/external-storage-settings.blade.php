<div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
            Almacenamiento Externo
        </h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Configure el almacenamiento externo para archivos de consultas médicas.
        </p>
    </div>

    <form wire:submit="saveSettings">
        <!-- Toggle para habilitar almacenamiento externo -->
        <div class="mb-6">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" wire:model.live="enabled" class="sr-only peer">
                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">
                    Habilitar Almacenamiento Externo
                </span>
            </label>
        </div>

        @if($enabled)
            <!-- Selector de proveedor -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Proveedor
                </label>
                <select wire:model.live="provider"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="dropbox">Dropbox</option>
                </select>
            </div>

            @if($provider === 'dropbox')
                <!-- Configuración de Dropbox -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Dropbox Access Token
                    </label>
                    <input type="password"
                           wire:model="dropbox_access_token"
                           placeholder="Ingrese su token de acceso de Dropbox"
                           class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">

                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        <p class="mb-2">Para obtener un token de acceso:</p>
                        <ol class="list-decimal ml-5 space-y-1">
                            <li>Vaya a <a href="https://www.dropbox.com/developers/apps" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">Dropbox App Console</a></li>
                            <li>Cree una nueva app con "Scoped access"</li>
                            <li>Seleccione tipo "Full Dropbox" o "App folder"</li>
                            <li>Agregue permisos: files.content.write, files.content.read, sharing.write</li>
                            <li>En la pestaña "Settings", genere un Access Token</li>
                            <li>Copie el token y péguelo arriba</li>
                        </ol>
                    </div>
                </div>

                <!-- Botón de prueba de conexión -->
                <div class="mb-6">
                    <button type="button"
                            wire:click="testDropboxConnection"
                            wire:loading.attr="disabled"
                            wire:target="testDropboxConnection"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                        <svg wire:loading.remove wire:target="testDropboxConnection" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <svg wire:loading wire:target="testDropboxConnection" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Probar Conexión
                    </button>

                    @if($connectionTestResult)
                        @php
                            [$type, $message] = explode(':', $connectionTestResult, 2);
                        @endphp
                        <div class="mt-3 p-3 rounded-md {{ $type === 'success' ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800' }}">
                            <p class="text-sm {{ $type === 'success' ? 'text-green-800 dark:text-green-200' : 'text-red-800 dark:text-red-200' }}">
                                {{ $message }}
                            </p>
                        </div>
                    @endif
                </div>
            @endif
        @endif

        <!-- Botones de acción -->
        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
            <button type="submit"
                    wire:loading.attr="disabled"
                    wire:target="saveSettings"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                <svg wire:loading.remove wire:target="saveSettings" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <svg wire:loading wire:target="saveSettings" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Guardar Configuración
            </button>
        </div>
    </form>

    <!-- Mensajes flash -->
    @if (session()->has('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50"
             x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
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
             x-init="setTimeout(() => show = false, 3000)"
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
