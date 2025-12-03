<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('Configuraciones') }}
                @endslot
                @slot('li_1')
                   {{ __('Plantilla Consulta') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>{{ __('Configurar secciones de consulta') }}</h4>
                                    <p class="text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        Se muestran solo las secciones generales y aquellas específicas de tus especialidades médicas.
                                    </p>
                                </div>
                            </div>

                            {{-- Single item-transfer component without category filter --}}
                            <livewire:item-transfer wire:key="all-sections"/>

                            <script>
                                    document.addEventListener('livewire:load', () => {
                                        let el = document.getElementById('sortable-list');
                                        new Sortable(el, {
                                            animation: 150,
                                            onEnd: function () {
                                                let orderedIds = [...el.children].map(li => li.getAttribute('data-id'));
                                                Livewire.emit('updateOrder', orderedIds);
                                            }
                                        });

                                    });
                                    document.addEventListener('livewire:initialized', () => {
                                        Livewire.on('showToastrItemTransfer', (event) => {
                                            toastr[event.type](event.message, '', {
                                                closeButton: true,
                                                progressBar: true,
                                                positionClass: 'toast-top-right',
                                                timeOut: 5000,
                                            });
                                        });
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
