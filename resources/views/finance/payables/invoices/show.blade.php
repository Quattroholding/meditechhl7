<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Módulo Financiero
                @endslot
                @slot('li_1')
                    Detalle de Factura de Proveedor
                @endslot
            @endcomponent

            <div class="row">
                <!-- Columna Izquierda -->
                <div class="col-md-6">
                    <!-- Información de Factura -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Información de Factura</h5>

                            <div class="mb-3">
                                <label class="form-label text-muted">Número de Factura</label>
                                <p class="mb-0 fw-bold">{{ $invoice->invoice_number }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Proveedor</label>
                                <p class="mb-0">
                                    <a href="{{ route('finance.payables.suppliers.show', $invoice->supplier) }}"
                                       class="fw-bold">{{ $invoice->supplier->legal_name }}</a>
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Fecha de Factura</label>
                                <p class="mb-0">{{ $invoice->invoice_date->format('d/m/Y') }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Fecha de Recepción</label>
                                <p class="mb-0">{{ $invoice->received_date->format('d/m/Y') }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Fecha de Vencimiento</label>
                                <p class="mb-0 fw-bold">
                                    {{ $invoice->due_date->format('d/m/Y') }}
                                    @if($invoice->isOverdue())
                                        <span class="badge bg-danger ms-2">Vencida</span>
                                    @endif
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Estado</label>
                                <p class="mb-0">
                                    @switch($invoice->status)
                                        @case('draft')
                                            <span class="badge bg-secondary">Borrador</span>
                                            @break
                                        @case('registered')
                                            <span class="badge bg-info">Registrada</span>
                                            @break
                                        @case('approved')
                                            <span class="badge bg-success">Aprobada</span>
                                            @break
                                        @case('partial')
                                            <span class="badge bg-warning">Pagada Parcialmente</span>
                                            @break
                                        @case('paid')
                                            <span class="badge bg-success">Pagada</span>
                                            @break
                                        @case('overdue')
                                            <span class="badge bg-danger">Vencida</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge bg-dark">Cancelada</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ ucfirst($invoice->status) }}</span>
                                    @endswitch
                                </p>
                            </div>

                            <hr class="my-3">

                            <div class="mb-3">
                                <label class="form-label text-muted">Aprobado Por</label>
                                <p class="mb-0">{{ $invoice->approvedBy->full_name ?? 'N/A' }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Fecha de Aprobación</label>
                                <p class="mb-0">{{ $invoice->approved_at ? $invoice->approved_at->format('d/m/Y H:i') : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Montos -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Resumen Financiero</h5>

                            <div class="mb-3">
                                <label class="form-label text-muted">Subtotal</label>
                                <p class="mb-0 fw-bold">B/. {{ number_format($invoice->subtotal, 2) }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Impuesto</label>
                                <p class="mb-0 fw-bold">B/. {{ number_format($invoice->tax_amount, 2) }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Moneda</label>
                                <p class="mb-0 fw-bold">{{ $invoice->currency }}</p>
                            </div>

                            <hr class="my-3">

                            <div class="mb-3">
                                <label class="form-label text-muted">Monto Total</label>
                                <p class="mb-0 fs-5 fw-bold text-primary">B/. {{ number_format($invoice->total_amount, 2) }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Monto Pagado</label>
                                <p class="mb-0 fs-5 fw-bold text-success">B/. {{ number_format($invoice->paid_amount, 2) }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Saldo Pendiente</label>
                                <p class="mb-0 fs-5 fw-bold text-warning">B/. {{ number_format($invoice->balance, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Acciones</h5>
                            <div class="d-grid gap-2">
                                @can('payables.invoices.create')
                                    <a href="{{ route('finance.payables.invoices.edit', $invoice) }}"
                                       class="btn btn-primary">
                                        <i class="fas fa-edit me-2"></i>Editar
                                    </a>
                                @endcan
                                <a href="{{ route('finance.payables.invoices.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Volver
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Columna Central -->
                <div class="col-md-6">

                    <!-- Documento -->
                    @if($invoice->document_path)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Vista Previa del Documento</h5>
                            </div>
                            <div class="card-body p-0">
                                <embed src="{{ route('invoice.document', ['path' => str_replace('documents/', '', $invoice->document_path)]) }}"
                                       type="application/pdf"
                                       class="w-100"
                                       style="height: 500px;" />
                            </div>
                            <div class="card-body border-top">
                                <div class="mb-0">
                                    <label class="form-label text-muted">Archivo</label>
                                    <p class="mb-0">
                                        <i class="fas fa-file-pdf me-2 text-danger"></i>
                                        <a href="{{ route('invoice.document', ['path' => str_replace('documents/', '', $invoice->document_path)]) }}"
                                           target="_blank" class="fw-bold">
                                            {{ $invoice->document_filename }}
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Información de Costos -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Centro de Costo</h5>

                            @if($invoice->costCenter)
                                <div class="mb-3">
                                    <label class="form-label text-muted">Centro de Costo</label>
                                    <p class="mb-0 fw-bold">{{ $invoice->costCenter->code }} - {{ $invoice->costCenter->name }}</p>
                                </div>
                            @else
                                <p class="text-muted mb-0">No hay centro de costo asignado</p>
                            @endif
                        </div>
                    </div>



                    <!-- Asiento Contable -->
                    @if($invoice->journalEntry)
                        <div class="card mt-3">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Asiento Contable</h5>

                                <div class="mb-3">
                                    <label class="form-label text-muted">Número de Asiento</label>
                                    <p class="mb-0">
                                        <a href="{{ route('accounting.journal-entries.show', $invoice->journalEntry) }}"
                                           class="fw-bold">{{ $invoice->journalEntry->entry_number }}</a>
                                    </p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-muted">Estado</label>
                                    <p class="mb-0">
                                        @if($invoice->journalEntry->status->value === 'draft')
                                            <span class="badge bg-warning">Borrador</span>
                                        @elseif($invoice->journalEntry->status->value === 'posted')
                                            <span class="badge bg-success">Publicado</span>
                                        @else
                                            <span class="badge bg-danger">Revertido</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Observaciones -->
                    @if($invoice->notes)
                        <div class="card mt-3">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Observaciones</h5>
                                <p class="mb-0 small">{{ $invoice->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Columna Derecha -->
                <div class="col-md-12">


                </div>
            </div>

            <!-- Cronograma de Pagos -->
            @if($invoice->paymentSchedules()->count() > 0)
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Cronograma de Pagos</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>Fecha de Pago</th>
                                                <th>Monto</th>
                                                <th>Pagado</th>
                                                <th>Movimiento de Tesorería</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($invoice->paymentSchedules as $schedule)
                                                <tr>
                                                    <td>{{ $schedule->payment_date->format('d/m/Y') }}</td>
                                                    <td class="fw-bold">B/. {{ number_format($schedule->amount, 2) }}</td>
                                                    <td>
                                                        @if($schedule->paid)
                                                            <i class="fas fa-check text-success"></i>
                                                        @else
                                                            <i class="fas fa-times text-danger"></i>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($schedule->treasuryMovement)
                                                            <a href="{{ route('treasury.movements.show', $schedule->treasuryMovement) }}"
                                                               class="fw-bold">{{ $schedule->treasuryMovement->movement_number }}</a>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($schedule->paid)
                                                            <span class="badge bg-success">Pagado</span>
                                                        @else
                                                            <span class="badge bg-secondary">Pendiente</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
