
<div class="">
    <div class="">
        <!-- Table Header -->
        @component('components.table-header',array('show_create'=>false))
            @slot('title')

            @endslot
            @slot('filters')
            <div class="d-flex flex-wrap gap-2">
                <div class="input-block local-forms mb-0">
                    <label>{{__('Estatus')}}</label>
                    <x-select-input wire:model.live="statusFilter" name="estatus" :options="\App\Enums\PaymentStatus::cases()" :selected="[]" class="form-select"/>
                </div>
                <div class="input-block local-forms mb-0">
                    <label>{{__('Metodo de pago')}}</label>
                    <x-select-input wire:model.live="methodFilter" name="metodo" :options="\App\Enums\PaymentMethod::cases()" :selected="[]" class="form-select"/>
                </div>
            </div>
            @endslot
            @slot('li_1')

            @endslot
        @endcomponent
        <!-- /Table Header -->
        <div class="table-responsive">
            <table class="table border-0 custom-table comman-table mb-0 responsive-table">
                <thead>
                <tr>
                    <th data-column="client_invoice_payments.id" data-priority="1">
                        <x-table-sort-button title="ID" columnName="client_invoice_payments.id" :sortField="$sortField" :sortDirection="$sortDirection"/>
                        <span class="expand-control d-none">  <i class="fas fa-plus-circle text-primary"></i></span>
                    </th>
                    <th data-column="reference" data-priority="1">
                        Referencia
                    </th>
                    <th data-column="invoice" data-priority="2">
                        Factura
                    </th>
                    @if(auth()->user()->hasRole(['admin', 'contabilidad']))
                        <th data-column="client" data-priority="3">
                            Cliente
                        </th>
                    @endif
                    <th data-column="amount" data-priority="4">
                        <x-table-sort-button title="Monto" columnName="amount" :sortField="$sortField" :sortDirection="$sortDirection"/>
                    </th>
                    <th data-column="payment_method" data-priority="5">
                        Método
                    </th>
                    <th data-column="payment_date" data-priority="6">
                        <x-table-sort-button title="Fecha" columnName="payment_date" :sortField="$sortField" :sortDirection="$sortDirection"/>
                    </th>
                    <th data-column="status" data-priority="7">
                        Estado
                    </th>
                    <th data-column="acciones" data-priority="1" class="text-end">
                        Acciones
                    </th>
                </tr>
                </thead>
                <tbody>
                @forelse ($payments as $payment)
                    <tr class="table-row" data-row-id="{{ $payment->id }}">
                        <td data-column="id" data-priority="1" data-label="ID">
                            <span class="row-expand-btn d-none me-2" onclick="toggleRowDetails(this)">
                                <i class="fas fa-plus-circle text-primary" style="cursor: pointer;"></i>
                            </span>
                            <span class="cell-content">{{ $payment->id}}</span>
                        </td>
                        <td data-column="reference" data-priority="1" data-label="Referencia">
                            <span class="cell-content">
                                {{ $payment->payment_reference ?? 'N/A' }}
                                @if($payment->receipt_file_path)
                                    <i class="fas fa-paperclip text-primary ms-1" title="Tiene comprobante adjunto"></i>
                                @endif
                            </span>
                        </td>
                        <td data-column="invoice" data-priority="2" data-label="Factura">
                            <span class="cell-content">
                                @if($payment->invoice)
                                    <a href="{{ route('suscriptions.invoices.show', $payment->invoice->id) }}" class="text-primary">
                                        {{ $payment->invoice->invoice_number }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </span>
                        </td>
                        @if(auth()->user()->hasRole(['admin', 'contabilidad']))
                            <td data-column="client" data-priority="3" data-label="Cliente">
                                <span class="cell-content">
                                    {{ $payment->invoice->client->name ?? 'N/A' }}
                                </span>
                            </td>
                        @endif
                        <td data-column="amount" data-priority="4" data-label="Monto">
                            <span class="cell-content text-success fw-bold">
                                ${{ number_format($payment->amount, 2) }}
                            </span>
                        </td>
                        <td data-column="payment_method" data-priority="5" data-label="Método">
                            <span class="cell-content">

                                 <span class="badge bg-{{ $payment->payment_method->color() }}">
                                    {{ $payment->payment_method->label() }}
                                </span>
                            </span>
                        </td>
                        <td data-column="payment_date" data-priority="6" data-label="Fecha">
                            <span class="cell-content">
                                {{ $payment->payment_date ? $payment->payment_date->format('d/m/Y') : 'N/A' }}
                            </span>
                        </td>
                        <td data-column="status" data-priority="7" data-label="Estado">
                            <span class="cell-content">
                                <span class="badge bg-{{ $payment->status->color() }}">
                                    {{ $payment->status->label() }}
                                </span>
                            </span>
                        </td>
                        <td data-column="acciones" data-priority="1" data-label="Acciones" class="text-end">
                            <div class="btn-group btn-group-sm">
                                @can('suscriptions.payments.show')
                                    <a href="{{ route('suscriptions.payments.show', $payment->id) }}"
                                       class="btn btn-info btn-sm"
                                       title="Ver detalles">
                                        <i class="far fa-eye"></i>
                                    </a>
                                @endcan
                                @can('suscriptions.payments.download')
                                    <a href="{{ route('suscriptions.payments.download', $payment->id) }}"
                                       target="_blank"
                                       class="btn btn-secondary btn-sm"
                                       title="Descargar recibo">
                                        <i class="fas fa-download"></i>
                                    </a>
                                @endcan
                                @can('suscriptions.payments.show')
                                    @if($payment->receipt_file_path)
                                        <a href="{{ route('suscriptions.payments.download-receipt', $payment->id) }}"
                                           class="btn btn-primary btn-sm"
                                           title="Descargar comprobante">
                                            <i class="fas fa-paperclip"></i>
                                        </a>
                                    @endif
                                @endcan
                                @can('suscriptions.payments.verify')
                                    @if($payment->status->value === 'pending')
                                        <button type="button"
                                           class="btn btn-success btn-sm"
                                           wire:click="verifyPayment({{ $payment->id }})"
                                           wire:confirm="¿Está seguro de que desea aprobar este pago?"
                                           title="Aprobar pago">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button"
                                           class="btn btn-danger btn-sm"
                                           wire:click="openRejectModal({{ $payment->id }})"
                                           title="Rechazar pago">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    <!-- Hidden row for expanded details -->
                    <tr class="row-details d-none" data-parent-row="{{ $payment->id }}">
                        <td colspan="8" class="p-3 bg-light">
                            <div class="row-details-content">
                                <!-- Details will be populated by JavaScript -->
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->hasRole(['admin', 'contabilidad']) ? '8' : '7' }}" class="text-center py-4">
                            <div class="d-flex flex-column align-items-center">
                                <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No se encontraron pagos</h5>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @include('partials.pagination',['data'=>$payments])
    </div>

    <!-- Reject Payment Modal -->
    @if($showRejectModal)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-times-circle me-2"></i>Rechazar Pago
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeRejectModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Atención:</strong> Esta acción rechazará el pago permanentemente.
                        </div>
                        <div class="mb-3">
                            <label for="rejectReason" class="form-label">
                                Motivo del rechazo <span class="text-danger">*</span>
                            </label>
                            <textarea
                                wire:model="rejectReason"
                                id="rejectReason"
                                class="form-control @error('rejectReason') is-invalid @enderror"
                                rows="4"
                                placeholder="Ingrese el motivo por el cual rechaza este pago (mínimo 10 caracteres)..."
                            ></textarea>
                            @error('rejectReason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Caracteres: <span x-text="$wire.rejectReason.length">0</span> / 1000
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeRejectModal">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="rejectPayment">
                            <i class="fas fa-ban me-1"></i> Rechazar Pago
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        // Listen for Livewire toastr events
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showRejectdPaymentToastr', (event) => {
                toastr[event.type](event.message, '', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: 5000,
                });
            });
        });
    </script>
</div>


