<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0" style="color: #fff">
            <i class="fas fa-file-invoice-dollar me-2"></i>Facturas Pendientes
        </h5>
        @if($this->totalDebt > 0)
            <span class="badge bg-warning text-dark">
                Deuda Total: ${{ number_format($this->totalDebt, 2) }}
            </span>
        @endif
    </div>
    <div class="card-body">
        @if($this->outstandingInvoices->count() > 0)
            <div class="list-group list-group-flush">
                @foreach($this->outstandingInvoices as $invoice)
                    <div class="list-group-item px-0 py-3">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="bg-warning text-white rounded-circle p-2">
                                            <i class="fas fa-file-invoice"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0">{{ $invoice->invoice_number }}</h6>
                                        <small class="text-muted">
                                            {{ $invoice->issue_date ? $invoice->issue_date->format('d/m/Y') : 'Sin fecha' }}
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                @if($invoice->encounter)
                                    <small class="text-muted d-block">Consulta:</small>
                                    <span class="fw-bold">{{ $invoice->encounter->chief_complaint ?? 'Consulta médica' }}</span>
                                @else
                                    <span class="text-muted">Servicios médicos</span>
                                @endif
                            </div>

                            <div class="col-md-2">
                                <small class="text-muted d-block">Total:</small>
                                <span class="h6 text-primary">${{ number_format($invoice->total_net, 2) }}</span>
                            </div>

                            <div class="col-md-2">
                                <small class="text-muted d-block">Pendiente:</small>
                                <span class="h6 text-danger">${{ number_format($invoice->amount_due, 2) }}</span>
                            </div>

                            <div class="col-md-2 text-end">
                                <!-- Due Date Alert -->
                                @if($invoice->due_date)
                                    @php
                                        $daysOverdue = now()->diffInDays($invoice->due_date, false);
                                    @endphp

                                    @if($daysOverdue < 0)
                                        <span class="badge bg-danger mb-2">
                                            Vencida ({{ abs($daysOverdue) }} días)
                                        </span>
                                    @elseif($daysOverdue <= 7)
                                        <span class="badge bg-warning text-dark mb-2">
                                            Vence en {{ $daysOverdue }} días
                                        </span>
                                    @else
                                        <span class="badge bg-success mb-2">
                                            Vence: {{ $invoice->due_date->format('d/m') }}
                                        </span>
                                    @endif
                                @endif

                                <div class="dropdown">
                                    <button class="btn btn-link text-muted" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('invoice.show', $invoice->id) }}">
                                                <i class="fas fa-eye me-2"></i>Ver Factura
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('invoice.download', $invoice->id) }}" target="_blank">
                                                <i class="fas fa-download me-2"></i>Descargar PDF
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:;" wire:click="openPaymentModal({{ $invoice->id }})">
                                                <i class="fas fa-credit-card me-2"></i>{{ __('Pagar Ahora') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Status Progress -->
                        @if($invoice->amount_paid > 0)
                            @php
                                $paymentPercentage = ($invoice->amount_paid / $invoice->total_net) * 100;
                            @endphp
                            <div class="row mt-3">
                                <div class="col-12">
                                    <small class="text-muted">Progreso de pago:</small>
                                    <div class="progress mt-1" style="height: 6px;">
                                        <div class="progress-bar bg-success"
                                             style="width: {{ $paymentPercentage }}%"
                                             title="Pagado: ${{ number_format($invoice->amount_paid, 2) }}">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <small class="text-success">
                                            Pagado: ${{ number_format($invoice->amount_paid, 2) }}
                                        </small>
                                        <small class="text-muted">
                                            {{ number_format($paymentPercentage, 1) }}%
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            @if($this->outstandingInvoices->count() >= $limit)
                <div class="text-center mt-3">
                    <a href="{{ route('patient.invoices') }}" class="btn btn-outline-primary btn-sm">
                        Ver Todas las Facturas
                    </a>
                </div>
            @endif

            <!-- Payment Summary -->
            @if($this->totalDebt > 0)
                <div class="alert alert-info mt-3 mb-0">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle fa-lg"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <strong>Total a pagar: ${{ number_format($this->totalDebt, 2) }}</strong>
                            <p class="mb-0">
                                Puedes pagar tus facturas de forma individual o contactar con administración para un plan de pagos.
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="{{ route('patient.payment.multiple') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-credit-card me-1"></i>Pagar Todo
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-4">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h6 class="text-success">¡Todas las facturas están al día!</h6>
                <p class="text-muted mb-3">No tienes facturas pendientes de pago</p>
                <a href="{{ route('invoice.index') }}" class="btn btn-outline-primary">
                    Ver Historial de Facturas
                </a>
            </div>
        @endif
    </div>
    @livewire('invoice.payment-modal')
</div>
