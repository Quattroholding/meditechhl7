<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Contabilidad
                @endslot
                @slot('li_1')
                    Asientos Contables
                @endslot
                @slot('li_2')
                    Detalle
                @endslot
            @endcomponent

            <livewire:accounting.journal-entry.detail :journalEntry="$journalEntry" />
        </div>
    </div>
</x-app-layout>
