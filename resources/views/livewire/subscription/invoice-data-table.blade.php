<div class="">
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>¡Éxito!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>¡Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="">
        <!-- Table Header -->
        @component('components.table-header',array('show_create'=>false))
            @slot('title')

            @endslot
            @slot('filters')
            <div class="row">
                <div class="col-md-3 ">
                    <div class="input-block local-forms">
                        <label>{{__('Estatus')}}</label>
                        <x-select-input  wire:model.live="statusFilter" name="estatus" :options="\App\Enums\InvoiceStatus::cases()" :selected="[]" class="form-select d-inline-block"/>
                    </div>
                </div>
            </div>
            @endslot
            @slot('buttons')
                <a class="btn btn-primary btn-sm" href="{{route('suscriptions.payments.settings')}}" target="_blank"><i class="fa fa-credit-card"></i> Ver Formas de pago</a>
            @endslot
            @slot('li_1')

            @endslot
        @endcomponent
        <!-- /Table Header -->

        <div class="table-responsive">
            <table class="table border-0 custom-table comman-table mb-0 responsive-table">
                <thead>
                <tr>
                    <th data-column="client_invoices.id" data-priority="1">
                        <x-table-sort-button title="ID" columnName="client_invoices.id" :sortField="$sortField" :sortDirection="$sortDirection"/>
                        <span class="expand-control d-none">  <i class="fas fa-plus-circle text-primary"></i></span>
                    </th>
                    <th data-column="invoice_number" data-priority="1">
                        <x-table-sort-button title="N° Factura" columnName="invoice_number" :sortField="$sortField" :sortDirection="$sortDirection"/>
                    </th>
                    @if(auth()->user()->hasRole(['admin', 'contabilidad']))
                        <th data-column="client" data-priority="2">
                            Cliente
                        </th>
                    @endif
                    <th data-column="package" data-priority="3">
                        Plan
                    </th>
                    <th data-column="period" data-priority="4">
                        Período
                    </th>
                    <th data-column="total" data-priority="5">
                        <x-table-sort-button title="Total" columnName="total" :sortField="$sortField" :sortDirection="$sortDirection"/>
                    </th>
                    <th data-column="due_date" data-priority="6">
                        <x-table-sort-button title="Vencimiento" columnName="due_date" :sortField="$sortField" :sortDirection="$sortDirection"/>
                    </th>
                    <th data-column="status" data-priority="7">
                        Estado
                    </th>
                    {{--}}
                    <th data-column="yappy_pay" data-priority="1" >
                        Pagar con yappy
                    </th>
                    {{--}}
                    <th data-column="acciones" data-priority="1" class="text-end">
                        Acciones
                    </th>
                </tr>
                </thead>
                <tbody>
                @forelse ($invoices as $invoice)
                    <tr class="table-row" data-row-id="{{ $invoice->id }}">
                        <td data-column="id" data-priority="1" data-label="ID">
                                    <span class="row-expand-btn d-none me-2" onclick="toggleRowDetails(this)">
                                        <i class="fas fa-plus-circle text-primary" style="cursor: pointer;"></i>
                                    </span>
                            <span class="cell-content">{{ $invoice->id}}</span>
                        </td>
                        <td data-column="invoice_number" data-priority="1" data-label="N° Factura">
                            <span class="cell-content">
                                <a href="{{ route('suscriptions.invoices.show', $invoice->id) }}" class="text-primary fw-bold">
                                    {{ $invoice->invoice_number }}
                                </a>
                            </span>
                        </td>
                        @if(auth()->user()->hasRole(['admin', 'contabilidad']))
                            <td data-column="client" data-priority="2" data-label="Cliente">
                                <span class="cell-content">
                                    {{ $invoice->client->name ?? 'N/A' }}
                                </span>
                            </td>
                        @endif
                        <td data-column="package" data-priority="3" data-label="Plan">
                            <span class="cell-content">
                                {{ $invoice->subscription->package->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td data-column="period" data-priority="4" data-label="Período">
                            <span class="cell-content">
                                @if($invoice->period_start && $invoice->period_end)
                                    {{ $invoice->period_start->format('d/m/Y') }} - {{ $invoice->period_end->format('d/m/Y') }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </td>
                        <td data-column="total" data-priority="5" data-label="Total">
                            <span class="cell-content text-primary fw-bold">
                                ${{ number_format($invoice->total, 2) }}
                            </span>
                        </td>
                        <td data-column="due_date" data-priority="6" data-label="Vencimiento">
                            <span class="cell-content">
                                {{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : 'N/A' }}
                            </span>
                        </td>
                        <td data-column="status" data-priority="7" data-label="Estado">
                            <span class="cell-content">
                                <span class="badge bg-{{ $invoice->status->color() }}">
                                    {{ $invoice->status->label() }}
                                </span>
                            </span>
                        </td>
                        {{--}}
                        <td data-column="yappy_pay" data-priority="6" data-label="Pagar Con Yappy" class="text-end">
                            @if($invoice->amount_due > 0)
                                <livewire:subscription.pay-invoice-yappy
                                    :invoice="$invoice"
                                    :wire:key="'pay-invoice-'.$invoice->id"
                                />
                            @endif
                        </td>
                        {{--}}
                        <td data-column="acciones" data-priority="1" data-label="Acciones" >
                            <div class="btn-group btn-group-sm">

                                @can('createPayment', $invoice)
                                    <button type="button" title="{{__('Registrar Pago')}}"
                                            onclick="Livewire.dispatch('openPaymentModal', { invoiceId: {{ $invoice->id }} })"
                                            class="btn btn-primary btn-sm">
                                        <i class="fas fa-credit-card me-2"></i> Pagar ahora
                                    </button>
                                @elsecan('suscriptions.payments.store')
                                    @if($invoice->hasPendingPaymentForFullAmount())
                                        <span class="badge bg-warning text-dark" title="Hay un pago pendiente de aprobación por el monto total de esta factura">
                                            <i class="fas fa-clock me-1"></i>Pago en proceso
                                        </span>
                                    @endif
                                @endcan

                                @can('suscriptions.invoices.show')
                                    <a href="{{ route('suscriptions.invoices.show', $invoice->id) }}"
                                       class="btn btn-info btn-sm"
                                       title="Ver detalles">
                                        <i class="far fa-eye"></i>
                                    </a>
                                @endcan

                                @can('suscriptions.invoices.download')
                                    <a href="{{ route('suscriptions.invoices.download', $invoice->id) }}"
                                       target="_blank"
                                       class="btn btn-secondary btn-sm"
                                       title="Descargar PDF">
                                        <i class="fas fa-download"></i>
                                    </a>
                                @endcan
                                @if(auth()->user()->hasRole(['admin', 'contabilidad']))
                                    <button type="button"
                                            wire:click="$dispatch('confirm-resend', { invoiceId: {{ $invoice->id }}, invoiceNumber: '{{ $invoice->invoice_number }}' })"
                                            class="btn btn-warning btn-sm"
                                            title="Reenviar notificación de factura">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    <!-- Hidden row for expanded details -->
                    <tr class="row-details d-none" data-parent-row="{{ $invoice->id }}">
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
                                <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No se encontraron facturas</h5>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @include('partials.pagination',['data'=>$invoices])

    </div>
    <!-- Payment Modal -->
    @livewire('subscription.payment-modal')

    <!-- Confirmation Modal for Resending Notification -->
    <div class="modal fade" id="resendNotificationModal" tabindex="-1" aria-labelledby="resendNotificationModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-white" id="resendNotificationModalLabel">
                        <i class="fas fa-envelope me-2"></i>Reenviar Notificación de Factura
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Confirmación requerida</strong>
                    </div>
                    <p class="mb-0">¿Está seguro que desea reenviar la notificación de la factura <strong id="modal-invoice-number"></strong>?</p>
                    <p class="text-muted small mt-2">Se enviará un correo electrónico al cliente con los detalles de la factura.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-warning" id="confirmResendBtn">
                        <i class="fas fa-envelope me-1"></i>Reenviar Notificación
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            let currentInvoiceId = null;

            // Escuchar el evento de confirmación
            Livewire.on('confirm-resend', (event) => {
                // En Livewire 3, el evento puede venir directamente o como array
                const data = Array.isArray(event) ? event[0] : event;

                currentInvoiceId = data.invoiceId;
                document.getElementById('modal-invoice-number').textContent = data.invoiceNumber;

                // Mostrar el modal
                const modal = new bootstrap.Modal(document.getElementById('resendNotificationModal'));
                modal.show();
            });

            // Manejar el clic en el botón de confirmación
            document.getElementById('confirmResendBtn').addEventListener('click', function() {
                if (currentInvoiceId) {
                    // Mostrar loading en el botón
                    const btn = this;
                    const originalContent = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Enviando...';

                    // Llamar al método de Livewire
                    @this.call('resendNotification', currentInvoiceId).then(() => {
                        // Cerrar el modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('resendNotificationModal'));
                        modal.hide();

                        // Restaurar el botón
                        btn.disabled = false;
                        btn.innerHTML = originalContent;
                        currentInvoiceId = null;
                    });
                }
            });

            Livewire.on('showToastrPayment', (data) => {
                // Livewire 3 passes data as array, get first element
                const event = Array.isArray(data) ? data[0] : data;

                if (event && event.type && event.message) {
                    toastr[event.type](event.message, '', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        timeOut: 5000,
                    });
                }
            });
        });
    </script>
    @endpush
</div>
