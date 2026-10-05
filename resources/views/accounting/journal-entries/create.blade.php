<x-app-layout>
    <div class="page-wrapper">
        <div class="row content">
            @component('components.page-header')
                @slot('title')
                    Crear Asiento Contable
                @endslot
                @slot('li_1')
                    Contabilidad
                @endslot
            @endcomponent
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="col-12">
                            <div class="form-heading">
                                <h4>Crear Asiento Contable</h4>
                            </div>
                        </div>
                        @livewire('accounting.journal-entry.journal-entry-modal', ['isModal' => false])
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
