<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Detalle de Pago
                @endslot
                @slot('li_1')
                    Pago de Suscripción
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h4>Recibo de Pago</h4>
                                    <p class="text-muted mb-0">Referencia: {{ $payment->payment_reference ?? 'N/A' }}</p>
                                    <p class="text-muted">Fecha: {{ $payment->payment_date->format('d/m/Y') }}</p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <span class="badge bg-{{ $payment->status->color() }} fs-5">
                                        {{ $payment->status->label() }}
                                    </span>
                                </div>
                            </div>

                            <hr>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="text-muted">Cliente</h6>
                                    <p class="mb-0"><strong>{{ $payment->invoice->client->name }}</strong></p>
                                    <p class="mb-0">{{ $payment->invoice->client->email }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Factura</h6>
                                    <p class="mb-0">
                                        <a href="{{ route('suscriptions.invoices.show', $payment->invoice->id) }}" class="text-primary">
                                            {{ $payment->invoice->invoice_number }}
                                        </a>
                                    </p>
                                    <p class="mb-0">{{ $payment->invoice->subscription->package->name }}</p>
                                </div>
                            </div>

                            <hr>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="text-muted">Método de Pago</h6>
                                    @php
                                        $methodLabels = [
                                            'cash' => 'Efectivo',
                                            'bank_transfer' => 'Transferencia Bancaria',
                                            'credit_card' => 'Tarjeta de Crédito',
                                            'yappy' => 'Yappy',
                                            'ach' => 'ACH',
                                        ];
                                    @endphp
                                    <p class="mb-0"><strong>{{ $methodLabels[$payment->payment_method->value] ?? $payment->payment_method->value }}</strong></p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Monto Pagado</h6>
                                    <h4 class="text-success mb-0">${{ number_format($payment->amount, 2) }}</h4>
                                </div>
                            </div>

                            @if($payment->gateway_transaction_id)
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <h6 class="text-muted">ID de Transacción</h6>
                                        <p class="mb-0"><code>{{ $payment->gateway_transaction_id }}</code></p>
                                    </div>
                                </div>
                            @endif

                            @if($payment->notes)
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <h6 class="text-muted">Notas</h6>
                                        <p class="mb-0">{{ $payment->notes }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($payment->receipt_file_path)
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <h6 class="text-muted">Comprobante de Transacción</h6>
                                        <a href="{{ route('suscriptions.payments.download-receipt', $payment->id) }}"
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-file-download me-2"></i>Descargar Comprobante
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if($payment->processedBy)

                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="text-muted">Procesado por</h6>
                                        <p class="mb-0">{{ $payment->processedBy->full_name }} <small>({{ $payment->processedBy->email }})</small></p>
                                    </div>
                                </div>
                            @endif

                            <hr>

                            <div class="text-end">
                                @can('suscriptions.payments.download')
                                    <a href="{{ route('suscriptions.payments.download', $payment->id) }}"
                                       target="_blank"
                                       class="btn btn-secondary">
                                        <i class="fas fa-download me-2"></i>Descargar Recibo
                                    </a>
                                @endcan

                                @can('suscriptions.payments.verify')
                                    @if($payment->status->value === 'pending')
                                        <a href="{{ route('suscriptions.payments.verify', $payment->id) }}"
                                           class="btn btn-success">
                                            <i class="fas fa-check me-2"></i>Verificar Pago
                                        </a>
                                    @endif
                                @endcan

                                <a href="{{ route('suscriptions.payments.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
