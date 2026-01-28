<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Plantillas de Factura
                @endslot
                @slot('li_1')
                    <a href="{{ route('dash') }}">Inicio</a>
                @endslot
                @slot('li_2')
                    Configuración
                @endslot
                @slot('li_3')
                    Plantillas de Factura
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="row">
                <div class="col-12">
                    <div class="card bg-light-info mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-info-circle fa-2x text-info"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">Personaliza tus Facturas</h5>
                                    <p class="mb-0 text-muted">
                                        Selecciona el diseño que mejor represente tu marca. La plantilla seleccionada
                                        se aplicará a todas las nuevas facturas que generes.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Template Selector Component -->
            @livewire('settings.invoice-template-selector')

        </div>
    </div>
</x-app-layout>