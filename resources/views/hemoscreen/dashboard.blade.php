<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Dashboard
                @endslot
                @slot('li_1')
                    Hemoscreen Dashboard
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <!-- Download Gateway Section -->
            <div class="mb-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between flex-col sm:flex-row gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                Descargar Gateway HemoScreen
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">
                                Descarga la aplicación gateway para comunicarte con tu dispositivo HemoScreen
                            </p>
                        </div>
                        <a href="/storage/HemoScreen Gateway Setup 1.0.0.exe"
                           download
                           class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white font-medium rounded-lg transition-colors whitespace-nowrap">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Descargar (v1.0.0)
                        </a>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between flex-col sm:flex-row gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                Manual de Configuración
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">
                                Guía completa para instalar y configurar el Gateway HemoScreen
                            </p>
                        </div>
                        <a href="{{ route('hemoscreen.config') }}"
                           class="inline-flex items-center gap-2 px-6 py-2 bg-orange-600 hover:bg-orange-700 dark:bg-orange-700 dark:hover:bg-orange-800 text-white font-medium rounded-lg transition-colors whitespace-nowrap">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.596 6.253 2 10.849 2 16.5S6.596 26.747 12 26.747s10-4.596 10-10.247S17.404 6.253 12 6.253z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11h6M9 15h6"/>
                            </svg>
                            Ver Guía
                        </a>
                    </div>
                </div>
            </div>

            @livewire(\App\Livewire\HemoScreen\StandaloneDashboard::class)
        </div>
    </div>
</x-app-layout>
