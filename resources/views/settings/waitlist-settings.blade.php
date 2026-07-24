<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Configuración de Lista de Espera
                @endslot
                @slot('li_1')
                    <a href="{{ route('dash') }}">Inicio</a>
                @endslot
                @slot('li_2')
                    Configuración
                @endslot
                @slot('li_3')
                    Lista de Espera
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="row">
                <div class="col-12">
                    <div class="card bg-light-info mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-clipboard-list fa-2x text-info"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">Gestión de Asignación de Espacios</h5>
                                    <p class="mb-0 text-muted">
                                        Configura cómo se asignarán automáticamente los espacios liberados en la agenda cuando se cancele o no se asista a una cita.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Waitlist Settings Component -->
            @livewire('settings.waitlist-settings')

        </div>
    </div>
</x-app-layout>
