<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Contabilidad
                @endslot
                @slot('li_1')
                    Balance de Comprobación
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <livewire:accounting.reports.trial-balance />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
