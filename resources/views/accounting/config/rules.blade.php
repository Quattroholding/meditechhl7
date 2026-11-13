<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Configuración de Reglas Contables
                @endslot
                @slot('li_1')
                    Finanzas & Contabilidad
                @endslot
                @slot('li_2')
                    Configuración
                @endslot
            @endcomponent

            <div class="card">
                <livewire:accounting.config.accounting-rule-config-panel />
            </div>
        </div>
    </div>
</x-app-layout>
