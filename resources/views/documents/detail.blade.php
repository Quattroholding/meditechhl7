<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Detalle de Documento
                @endslot
                @slot('li_1')
                    Documentos
                @endslot
                @slot('li_2')
                    {{ $document->original_filename }}
                @endslot
            @endcomponent

            @include('partials.message')

            <livewire:documents.document-detail :document="$document" />
        </div>
    </div>
</x-app-layout>
