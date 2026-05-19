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
                                    <h5 class="mb-3">ACH</h5>
                                    <div class="ps-4">
                                        <p><strong>Banco:</strong> BANCO GENERAL</p>
                                        <p><strong>Cuenta:</strong> {{env('ACCOUNT_ACH_BG_NAME')}}</p>
                                        <p><strong>Tipo:</strong> {{env('ACCOUNT_ACH_BG_NUMBER')}}</p>
                                        <p><strong>Beneficiario:</strong> {{env('ACCOUNT_ACH_BG_TYPE')}}</p>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h5 class="mb-3">YAPPY</h5>
                                    <div class="ps-4">
                                        {{--}}
                                        <p><strong>Número:</strong> 6XXX-XXXX</p>
                                        <p><strong>Nombre:</strong> Soluciones Meditec S.A.</p>
                                        <p><strong>Cliente YAPPY:</strong>  {{auth()->user()->getCurrentClient()->yappy_code}}</p>
                                        <p><strong>NOTA :</strong> En el mensaje del envio de yappy poner su número de cliente yappy (<b>{{auth()->user()->getCurrentClient()->yappy_code}}</b>) para poder cargar el pago a su cuenta.</p>
                                        {{--}}
                                        <p>USAR EL BÓTON PAGAR CON YAPPY EN EL DETALLE DE LA FACTURA</p>
                                    </div>
                                </div>
                                @if(config('services.neopayments.enabled'))
                                    <div class="mb-4">
                                        <h5 class="mb-3">Tarjetas de Crédito</h5>
                                        <div class="ps-4">
                                            @livewire('payment.neo-credit-card-manager')
                                        </div>
                                    </div>
                                @else
                                    <div class="mb-4">
                                        <h5 class="mb-3">Tarjeta de Crédito</h5>
                                        <div class="ps-4">
                                            <p class="text-muted">Próximamente disponible</p>
                                        </div>
                                    </div>
                                @endif

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
