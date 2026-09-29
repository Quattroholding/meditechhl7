<x-app-layout>
<div class="page-wrapper">
    <div class="content">
        <!-- Page Header -->
        @component('components.page-header')
            @slot('title')
                Subir Nuevo Documento
            @endslot
            @slot('li_1')
                Documentos
            @endslot
        @endcomponent
        <!-- /Page Header -->

        @include('partials.message')

        <!-- /Page Header -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="col-12">
                            <div class="form-heading">
                                <h4>Subir Documento (Factura, Comprobante, etc.)</h4>
                            </div>
                        </div>
                        @livewire('documents.document-upload-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
