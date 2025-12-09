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
                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0 responsive-table">
                            <thead {{--}}class="thead-light"{{--}}>
                            <tr>
                                <th data-column="invoice_number" data-priority="1">
                                    <x-table-sort-button title="ID" columnName="identifier" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="type" data-priority="2">
                                    <x-table-sort-button title="{{ __('invoice.type') }}" columnName=""></x-table-sort-button>
                                </th>
                                <th data-column="created_at" data-priority="3">
                                    <x-table-sort-button title="{{__('invoice.created_at')}}" columnName="created_at" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="patient" data-priority="4">
                                    <x-table-sort-button title="{{ __('invoice.bill_to') }}" columnName=""/>
                                </th>
                                <th data-column="total_amount" data-priority="5">
                                    <x-table-sort-button title="{{__('invoice.amount')}}" columnName="total_amount" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="due_date" data-priority="6">
                                    <x-table-sort-button title="{{__('invoice.due_date')}}" columnName="due_date" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="payment_status" data-priority="7">
                                    <x-table-sort-button title="{{ __('invoice.statuss') }}" columnName=""></x-table-sort-button>
                                </th>
                                <th data-column="acciones" data-priority="1" class="text-end">
                                    <x-table-sort-button title="{{__('Acciones')}}" columnName=""/>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($invoices as $invoice)
                                <tr class="table-row" data-row-id="{{ $invoice->id }}">
                                    <td data-column="invoice_number" data-priority="1" data-label="ID">
                                        <span class="row-expand-btn d-none me-2" onclick="toggleRowDetails(this)">
                                            <i class="fas fa-plus-circle text-primary" style="cursor: pointer;"></i>
                                        </span>
                                        <span class="cell-content">
                                            <a href="{{ route('invoice.show', $invoice->id) }}" class="invoice-link">{{ $invoice->invoice_number }}</a>
                                        </span>
                                    </td>
                                    <td data-column="type" data-priority="2" data-label="{{ __('invoice.type') }}">
                                        <span class="cell-content">{{ __('invoice.types.' . ($invoice->type ?? 'invoice')) }}</span>
                                    </td>
                                    <td data-column="created_at" data-priority="3" data-label="{{__('invoice.created_at')}}">
                                        <span class="cell-content">{{ $invoice->created_at }}</span>
                                    </td>
                                    <td data-column="patient" data-priority="4" data-label="{{ __('invoice.bill_to') }}">
                                        <span class="cell-content">
                                            @if($invoice->patient)
                                                {!!  $invoice->patient->profile_name !!}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td data-column="total_amount" data-priority="5" data-label="{{__('invoice.amount')}}">
                                        <span class="cell-content text-primary fw-bold">
                                            {{ $invoice->currency }} {{ number_format($invoice->total_amount, 2) }}
                                        </span>
                                    </td>
                                    <td data-column="due_date" data-priority="6" data-label="{{__('invoice.due_date')}}">
                                        <span class="cell-content">
                                            {{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : 'N/A' }}
                                        </span>
                                    </td>
                                    <td data-column="payment_status" data-priority="7" data-label="{{ __('invoice.statuss') }}">
                                        <span class="cell-content">
                                            @if($invoice->payment_status)
                                                <small class="custom-badge {{__('invoice.payment_status_class.'.$invoice->payment_status)}}  mt-1">{{ __('invoice.payment_status.' . $invoice->payment_status) }}</small>
                                            @endif
                                        </span>
                                    </td>
                                    <td data-column="acciones" data-priority="1" data-label="{{__('Acciones')}}" class="text-end">
                                        <div class="btn-group btn-group-sm">
                                                <a href="{{ route('invoice.show', $invoice->id) }}" class="btn btn-info btn-sm" title="{{ __('invoice.view_details') }}">
                                                    <i class="far fa-eye me-2"></i>
                                                </a>
                                                @if($invoice->balance > 0)
                                                    <a href="javascript:;" wire:click="openPaymentModal({{ $invoice->id }})" class="btn btn-primary btn-sm" title="{{ __('Registrar Pago') }}">
                                                        <i class="fas fa-credit-card me-2"></i>
                                                    </a>
                                                @endif
                                                <a href="{{ route('invoice.download', $invoice->id) }}" target="_blank" class="btn btn-secondary btn-sm" title="{{ __('invoice.download_pdf') }}">
                                                    <i class="fas fa-download me-2"></i>
                                                </a>
                                        </div>
                                        {{--}}<div class="dropdown dropdown-action">
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
                                        </div>{{--}}
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
                    @include('partials.pagination',['data'=>$invoices])
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
