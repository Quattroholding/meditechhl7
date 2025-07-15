<div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Table Header -->
                    @component('components.table-header',array('show_create'=>false))
                        @slot('title')

                        @endslot
                        @slot('li_1')
                            {{ route('appointment.create') }}
                        @endslot
                    @endcomponent
                    <!-- /Table Header -->
                    @include('partials.message')
                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0">
                            <thead {{--}}class="thead-light"{{--}}>
                            <tr>
                                <th><x-table-sort-button title="ID" columnName="identifier" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th><x-table-sort-button title="{{ __('invoice.type') }}" columnName=""></x-table-sort-button></th>
                                <th><x-table-sort-button title="{{__('invoice.created_at')}}" columnName="created_at" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th><x-table-sort-button title="{{ __('invoice.bill_to') }}" columnName=""/></th>
                                <th><x-table-sort-button title="{{__('invoice.amount')}}" columnName="total_amount" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th><x-table-sort-button title="{{__('invoice.due_date')}}" columnName="due_date" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th><x-table-sort-button title="{{ __('invoice.statuss') }}" columnName=""></x-table-sort-button></th>
                                <th class="text-end"><x-table-sort-button title="{{__('Acciones')}}" columnName=""/></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($invoices as $invoice)
                                <tr>
                                    <td><a href="{{ route('invoice.show', $invoice->id) }}" class="invoice-link">{{ $invoice->invoice_number }}</a> </td>
                                    <td>{{ __('invoice.types.' . ($invoice->type ?? 'invoice')) }}</td>
                                    <td>{{ $invoice->created_at }}</td>
                                    <td>@if($invoice->patient)   {!!  $invoice->patient->profile_name !!}@else<span class="text-muted">N/A</span> @endif</td>
                                    <td class="text-primary fw-bold">
                                        {{ $invoice->currency }} {{ number_format($invoice->total_amount, 2) }}
                                    </td>
                                    <td>
                                        {{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td>
                                        @if($invoice->payment_status)
                                            <small class="custom-badge {{__('invoice.payment_status_class.'.$invoice->payment_status)}}  mt-1">{{ __('invoice.payment_status.' . $invoice->payment_status) }}</small>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown dropdown-action">
                                            <a href="javascript:;" class="action-icon dropdown-toggle"
                                               data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="{{ route('invoice.show', $invoice->id) }}">
                                                    <i class="far fa-eye me-2"></i>{{ __('invoice.view_details') }}
                                                </a>
                                                @if($invoice->balance > 0)
                                                    <a class="dropdown-item" href="javascript:;" wire:click="openPaymentModal({{ $invoice->id }})">
                                                        <i class="fas fa-credit-card me-2"></i>{{ __('Registrar Pago') }}
                                                    </a>
                                                @endif
                                                <a class="dropdown-item" href="{{ route('invoice.download', $invoice->id) }}" target="_blank">
                                                    <i class="fas fa-download me-2"></i>{{ __('invoice.download_pdf') }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-close fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">{{ __('Sin resultados de busqueda') }}</h5>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>


                    </div>
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <p class="text-muted mb-0">
                                Mostrando del {{ $invoices->firstItem() }} al {{ $invoices->lastItem() }}
                                de {{ $invoices->total() }} resultados
                            </p>
                        </div>
                        <div>
                            {{ $invoices->links('vendor.pagination.custom-pagination') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Payment Modal -->
    @livewire('invoice.payment-modal')
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showToastr', (event) => {
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
