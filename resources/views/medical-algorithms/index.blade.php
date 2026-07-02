<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('menu.algorithms') }}
                @endslot
                @slot('li_1')
                    {{ __('generic.list') }} {{ __('menu.algorithms') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- Intro Section -->
                            <div class="mb-8">
                                <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">
                                    Algoritmos ACLS para Emergencias Médicas
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">
                                    Esta sección contiene recursos educativos basados en las pautas de soporte vital cardíaco avanzado (ACLS) de la American Heart Association.
                                    Los algoritmos están diseñados para guiar a los profesionales médicos en el manejo de emergencias cardíacas y eventos clínicos críticos.
                                </p>
                                <p class="text-gray-500 dark:text-gray-500 text-sm">
                                    <strong>Fuente:</strong> American Heart Association (aclscertification.org)
                                </p>
                            </div>

                            <!-- Algorithm Cards Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($algorithms as $filename => $algorithm)
                                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden border border-gray-200 dark:border-gray-700">
                                        <!-- Card Header -->
                                        <div class="bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 p-4 border-b border-gray-200 dark:border-gray-700">
                                            <div class="flex items-center mb-3">
                                                <div class="w-10 h-10 bg-red-100 dark:bg-red-900/40 rounded-full flex items-center justify-center mr-3">
                                                    <i class="fas fa-heartbeat text-red-600 dark:text-red-400"></i>
                                                </div>
                                                <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                                                    {{ $algorithm['title'] }}
                                                </h3>
                                            </div>
                                        </div>

                                        <!-- Card Body -->
                                        <div class="p-4">
                                            <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed mb-4">
                                                {{ $algorithm['description'] }}
                                            </p>
                                        </div>

                                        <!-- Card Footer -->
                                        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
                                            <a href="{{ route('algorithms.view', ['filename' => $filename]) }}"
                                               target="_blank"
                                               rel="noopener noreferrer"
                                               class="inline-flex items-center justify-center w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md transition-colors">
                                                <i class="fas fa-file-pdf mr-2"></i>
                                                Ver PDF
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Footer Info -->
                            <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                    <div class="flex items-start">
                                        <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mt-1 mr-3 flex-shrink-0"></i>
                                        <div class="text-sm text-gray-700 dark:text-gray-300">
                                            <p class="font-semibold mb-1">Acerca de estos algoritmos</p>
                                            <p>
                                                Los algoritmos ACLS presentados aquí se basan en las directrices más recientes de la American Heart Association
                                                para el Soporte Vital Cardíaco Avanzado. Estos recursos son para uso educativo y deben ser complementados
                                                con capacitación formal y supervisión clínica en su aplicación.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
</x-app-layout>
