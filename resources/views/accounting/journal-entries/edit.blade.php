<x-app-layout>
    <div class="page-wrapper">
        <div class="row content">
            @component('components.page-header')
                @slot('title')
                    Módulo Financiero
                @endslot
                @slot('li_1')
                    Editar Asiento Contable
                @endslot
            @endcomponent
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="col-12">
                            <div class="form-heading">
                                <h4>Editar Asiento Contable</h4>
                            </div>
                        </div>
                        @livewire('accounting.journal-entry.journal-entry-modal', ['isModal' => false, 'journalEntry' => $journalEntry])
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
