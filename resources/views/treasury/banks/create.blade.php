<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Nuevo Banco
                @endslot
                @slot('li_1')
                    Módulo Financiero
                @endslot
            @endcomponent

            <div class="card">
                <div class="card-body text-center py-5">
                    <h4>En Construcción</h4>
                    <p class="text-muted mt-3">Esta página será implementada en la siguiente iteración del Sprint 3</p>
                    <small class="text-secondary">Ruta: treasury/banks/create</small>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
