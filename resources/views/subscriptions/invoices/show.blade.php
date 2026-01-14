<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Factura {{ $invoice->invoice_number }}
                @endslot
                @slot('li_1')
                    Detalle de Factura
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h4>Factura de Suscripción</h4>
                                    <p class="text-muted mb-0">{{ $invoice->invoice_number }}</p>
                                    <p class="text-muted">Fecha: {{ $invoice->created_at }}</p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <span class="badge bg-{{ $invoice->status->color() }} fs-5">
                                        {{ $invoice->status->label() }}
                                    </span>
                                </div>
                            </div>

                            <hr>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="text-muted">Cliente</h6>
                                    <p class="mb-0"><strong>{{ $invoice->client->name }}</strong></p>
                                    <p class="mb-0">{{ $invoice->client->email }}</p>
                                    @if($invoice->client->ruc)
                                        <p class="mb-0">RUC: {{ $invoice->client->ruc }}-{{ $invoice->client->dv }}</p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Plan</h6>
                                    <p class="mb-0"><strong>{{ $invoice->subscription->package->name }}</strong></p>
                                    @if($invoice->period_start && $invoice->period_end)
                                        <p class="mb-0">Período: {{ $invoice->period_start->format('d/m/Y') }} - {{ $invoice->period_end->format('d/m/Y') }}</p>
                                    @endif
                                </div>
                            </div>

                            <hr>

                            <h6 class="mb-3">Detalle de Items</h6>
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Descripción</th>
                                            <th class="text-end">Cantidad</th>
                                            <th class="text-end">Precio Unit.</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($invoice->items as $item)
                                            <tr>
                                                <td>{{ $item->description }}</td>
                                                <td class="text-end">{{ $item->quantity }}</td>
                                                <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                                <td class="text-end">${{ number_format($item->total, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No hay items</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                            <td class="text-end"><strong>${{ number_format($invoice->subtotal, 2) }}</strong></td>
                                        </tr>
                                        @if($invoice->discount_amount > 0)
                                            <tr>
                                                <td colspan="3" class="text-end">
                                                    Descuento
                                                    @if($invoice->discount_reason)
                                                        <br><small class="text-muted">({{ $invoice->discount_reason }})</small>
                                                    @endif
                                                </td>
                                                <td class="text-end text-success">-${{ number_format($invoice->discount_amount, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-end">Subtotal después de descuento:</td>
                                                <td class="text-end">${{ number_format($invoice->subtotal - $invoice->discount_amount, 2) }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td colspan="3" class="text-end">
                                                <strong>ITBMS ({{ number_format(($invoice->tax_rate ?? 0.07) * 100, 0) }}%):</strong>
                                                <br><small class="text-muted">Impuesto sobre Bienes y Servicios</small>
                                            </td>
                                            <td class="text-end"><strong>${{ number_format($invoice->tax_amount ?? 0, 2) }}</strong></td>
                                        </tr>
                                        <tr class="table-primary">
                                            <td colspan="3" class="text-end"><h5 class="mb-0">Total a Pagar:</h5></td>
                                            <td class="text-end"><h5 class="mb-0">${{ number_format($invoice->total, 2) }}</h5></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            @if($invoice->payments->count() > 0)
                                <h6 class="mb-3">Historial de Pagos</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Método</th>
                                                <th>Referencia</th>
                                                <th class="text-end">Monto</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($invoice->payments as $payment)
                                                <tr>
                                                    <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                                    <td>{{ $payment->payment_method->value }}</td>
                                                    <td>{{ $payment->payment_reference ?? 'N/A' }}</td>
                                                    <td class="text-end">${{ number_format($payment->amount, 2) }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $payment->status->value === 'completed' ? 'success' : 'warning' }}">
                                                            {{ $payment->status->value }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Acciones</h5>
                            <a class="btn btn-warning  w-100 mb-2" href="{{route('suscriptions.payments.settings')}}" target="_blank"><i class="fa fa-credit-card"></i> Ver Formas de pago</a>
                            @can('suscriptions.invoices.download')
                                <a href="{{ route('suscriptions.invoices.download', $invoice->id) }}"
                                   target="_blank"
                                   class="btn btn-secondary w-100 mb-2">
                                    <i class="fas fa-download me-2"></i>Descargar PDF
                                </a>
                            @endcan

                            @can('suscriptions.payments.store')
                                @if($invoice->amount_due > 0)
                                    <button type="button"
                                            onclick="Livewire.dispatch('openPaymentModal', { invoiceId: {{ $invoice->id }} })"
                                            class="btn btn-primary w-100 mb-2">
                                        <i class="fas fa-credit-card me-2"></i>Registrar Pago
                                    </button>
                                @endif
                            @endcan

                            <a href="{{ route('suscriptions.invoices.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                            </a>
                        </div>
                    </div>

                    @if($invoice->due_date)
                        <div class="card mt-3">
                            <div class="card-body">
                                <h6 class="text-muted mb-2">Fecha de Vencimiento</h6>
                                <h5>{{ $invoice->due_date->format('d/m/Y') }}</h5>
                                @if($invoice->is_overdue)
                                    <p class="text-danger mb-0">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Factura vencida
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($invoice->amount_due > 0)
                        <div class="card mt-3 border-warning">
                            <div class="card-body">
                                <h6 class="text-muted mb-2">Saldo Pendiente</h6>
                                <h4 class="text-warning mb-0">${{ number_format($invoice->amount_due, 2) }}</h4>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    @livewire('subscription.payment-modal')
</x-app-layout>
