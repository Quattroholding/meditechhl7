<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Almacenamiento Externo
                @endslot
                @slot('li_1')
                    <a href="{{ route('dash') }}">Inicio</a>
                @endslot
                @slot('li_2')
                    Configuración
                @endslot
                @slot('li_3')
                    Almacenamiento Externo
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="row">
                <div class="col-12">
                    <div class="card bg-light-info mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-info"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">Configura el Almacenamiento Externo</h5>
                                    <p class="mb-0 text-muted">
                                        Conecta tu cuenta de Dropbox para almacenar automáticamente los archivos de consultas médicas en la nube.
                                        Los archivos estarán seguros y accesibles desde cualquier lugar.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- External Storage Settings Component -->
            @livewire('settings.external-storage-settings')

        </div>
    </div>
</x-app-layout>
