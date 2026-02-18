<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Plantillas de Prescripción Médica
                @endslot
                @slot('li_1')
                    <a href="{{ route('dash') }}">Inicio</a>
                @endslot
                @slot('li_2')
                    Configuración
                @endslot
                @slot('li_3')
                    Plantillas de Prescripción
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="row">
                <div class="col-12">
                    <div class="card bg-light-info mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-prescription fa-2x text-info"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">Personaliza tus Prescripciones Médicas</h5>
                                    <p class="mb-0 text-muted">
                                        Selecciona el diseño que mejor se adapte a tus necesidades. La plantilla seleccionada
                                        se aplicará a todas las nuevas prescripciones médicas que generes.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prescription Template Selector Component -->
            @livewire('settings.prescription-template-selector')

        </div>
    </div>
</x-app-layout>
