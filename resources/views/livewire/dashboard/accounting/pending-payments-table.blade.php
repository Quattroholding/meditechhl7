<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-clock text-warning me-2"></i>
            Pagos Pendientes de Verificación
        </h5>
        @can('suscriptions.payments.index')
            <a href="{{ route('suscriptions.payments.index', ['statusFilter' => 'pending']) }}" class="btn btn-sm btn-outline-primary">
                Ver Todos
            </a>
        @endcan
    </div>
    <div class="card-body">
        @if($payments->isEmpty())
            <div class="text-center py-4">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <p class="text-muted">No hay pagos pendientes de verificación</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Factura</th>
                            <th>Monto</th>
                            <th>Fecha</th>
                            <th>Método</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td>
                                    <strong>{{ $payment->invoice->client->name ?? 'N/A' }}</strong>
                                </td>
                                <td>
                                    <a href="{{ route('suscriptions.invoices.show', $payment->invoice->id) }}" class="text-primary">
                                        {{ $payment->invoice->invoice_number }}
                                    </a>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">${{ number_format($payment->amount, 2) }}</span>
                                </td>
                                <td>
                                    {{ $payment->payment_date->format('d/m/Y') }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ $payment->payment_method->color() }}">
                                        {{ $payment->payment_method->label() }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    @can('suscriptions.payments.verify')
                                        <a href="{{ route('suscriptions.payments.verify', $payment->id) }}"
                                           class="btn btn-sm btn-success"
                                           onclick="return confirm('¿Aprobar este pago?')"
                                           title="Aprobar">
                                            <i class="fas fa-check"></i>
                                        </a>
                                    @endcan
                                    @can('suscriptions.payments.show')
                                        <a href="{{ route('suscriptions.payments.show', $payment->id) }}"
                                           class="btn btn-sm btn-info"
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
