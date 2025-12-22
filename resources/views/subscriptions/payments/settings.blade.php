<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Configuración de Métodos de Pago
                @endslot
                @slot('li_1')
                    Métodos de Pago
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Métodos de Pago Disponibles</h4>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Configure sus métodos de pago preferidos para las suscripciones.
                            </div>

                            <form>
                                <div class="mb-4">
                                    <h5 class="mb-3">Transferencia Bancaria</h5>
                                    <div class="ps-4">
                                        <p><strong>Banco:</strong> Banco General</p>
                                        <p><strong>Cuenta:</strong> 04-99-99-999999-9</p>
                                        <p><strong>Tipo:</strong> Cuenta Corriente</p>
                                        <p><strong>Beneficiario:</strong> Meditech S.A.</p>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h5 class="mb-3">Yappy</h5>
                                    <div class="ps-4">
                                        <p><strong>Número:</strong> 6XXX-XXXX</p>
                                        <p><strong>Nombre:</strong> Meditech</p>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h5 class="mb-3">ACH</h5>
                                    <div class="ps-4">
                                        <p><strong>Cuenta:</strong> 04-99-99-999999-9</p>
                                        <p><strong>Beneficiario:</strong> Meditech S.A.</p>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h5 class="mb-3">Tarjeta de Crédito</h5>
                                    <div class="ps-4">
                                        <p class="text-muted">Próximamente disponible</p>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                        Volver
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
