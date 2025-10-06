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
                                    <h4>  {{ __('Configurar secciones de consulta') }}</h4>
                                </div>
                            </div>
                            <ul class="nav nav-pills navtab-bg nav-justified" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a href="#generales" data-bs-toggle="tab" aria-expanded="false" class="nav-link active" aria-selected="false" tabindex="-1" role="tab">
                                        Generales
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a href="#urologia" data-bs-toggle="tab" aria-expanded="true" class="nav-link" aria-selected="true" role="tab">
                                        Urología
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane  show active" id="generales" role="tabpanel">
                                    <livewire:item-transfer wire:key="generales"/>
                                </div>
                                <div class="tab-pane" id="urologia" role="tabpanel">
                                    <livewire:item-transfer category="Urologia" wire:key="urologia"/>
                                </div>
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
