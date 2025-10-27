<div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Table Header -->
                    @component('components.table-header', ['show_create' => false])
                        @slot('title')
                        @endslot
                    @endcomponent
                    <!-- /Table Header -->
                    <div class="staff-search-table">
                            <div class="row">
                                <div class="col-12 col-md-3 col-xl-3">
                                    <div class="input-block  local-forms cal-icon">
                                        <x-input-label for="dateFrom" :value="__('Pagado desde')" />
                                        <input wire:model.live="dateFrom" wire:click="clickedDateTo" class="form-control datetimepicker" type="text">
                                    </div>
                                </div>
                                <div class="col-12 col-md-3 col-xl-3">
                                    <div class="input-block  local-forms cal-icon">
                                        <x-input-label for="dateTo" :value="__('Pagado hasta')" />
                                        <input wire:model.live="dateTo" class="form-control datetimepicker" type="text">
                                    </div>
                                </div>
                                <div class="col-12 col-md-3 col-xl-3 ">
                                    <div class="input-block  local-forms">
                                        <x-input-label for="status" :value="__('Estatus')" />
                                        <x-select-input wire:model.live="status" name="status" :options="$this->statusOptions" :selected="['']" class="block mt-1 w-full"/>
                                    </div>
                                </div>
                                <div class="col-12 col-md-3 col-xl-3 ">
                                    <div class="input-block  local-forms">
                                        <x-input-label for="paymentMethod" :value="__('Metodo de pago')" />
                                        <x-select-input wire:model.live="paymentMethod" name="paymentMethod" :options="$this->paymentMethods" :selected="['']" class="block mt-1 w-full"/>
                                    </div>
                                </div>
                            </div>

                    </div>
                    @include('partials.message')

                    @if($this->payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table border-0 custom-table comman-table mb-0 responsive-table">
                                <thead>
                                    <tr>
                                        <th data-column="payment_number" data-priority="1" wire:click="sortBy('payment_number')" style="cursor: pointer;">
                                            {{ __('Número de Pago') }}
                                            @if($sortField === 'payment_number')
                                                <i class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </th>
                                        <th data-column="invoice" data-priority="2">{{ __('Factura') }}</th>
                                        <th data-column="patient" data-priority="3">{{ __('Paciente') }}</th>
                                        <th data-column="payment_method" data-priority="4" wire:click="sortBy('payment_method')" style="cursor: pointer;">
                                            {{ __('Método de Pago') }}
                                            @if($sortField === 'payment_method')
                                                <i class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </th>
                                        <th data-column="payment_date" data-priority="5" wire:click="sortBy('payment_date')" style="cursor: pointer;">
                                            {{ __('Fecha de Pago') }}
                                            @if($sortField === 'payment_date')
                                                <i class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </th>
                                        <th data-column="amount" data-priority="6" wire:click="sortBy('amount')" style="cursor: pointer;">
                                            {{ __('Monto') }}
                                            @if($sortField === 'amount')
                                                <i class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </th>
                                        <th data-column="status" data-priority="7" wire:click="sortBy('status')" style="cursor: pointer;">
                                            {{ __('Estado') }}
                                            @if($sortField === 'status')
                                                <i class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </th>
                                        <th data-column="acciones" data-priority="1">{{ __('Acciones') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($this->payments as $payment)
                                        <tr class="table-row" data-row-id="{{ $payment->id }}">
                                            <td data-column="payment_number" data-priority="1" data-label="{{ __('Número de Pago') }}">
                                                <span class="row-expand-btn d-none me-2" onclick="toggleRowDetails(this)">
                                                    <i class="fas fa-plus-circle text-primary" style="cursor: pointer;"></i>
                                                </span>
                                                <span class="cell-content">
                                                    <strong>{{ $payment->payment_number }}</strong>
                                                    @if($payment->reference_number)
                                                        <br><small class="text-muted">Ref: {{ $payment->reference_number }}</small>
                                                    @endif
                                                </span>
                                            </td>
                                            <td data-column="invoice" data-priority="2" data-label="{{ __('Factura') }}">
                                                <span class="cell-content">
                                                    @if($payment->invoice)
                                                        <a href="{{ route('invoice.show', $payment->invoice->id) }}" class="text-primary">
                                                            {{ $payment->invoice->invoice_number }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </span>
                                            </td>
                                            <td data-column="patient" data-priority="3" data-label="{{ __('Paciente') }}">
                                                <span class="cell-content">
                                                    @if($payment->patient)
                                                        <div class="profile-image">
                                                            <a href="{{ route('patient.profile', $payment->patient->id) }}">
                                                             {!!  $payment->patient->profile_name!!}
                                                            </a>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </span>
                                            </td>
                                            <td data-column="payment_method" data-priority="4" data-label="{{ __('Método de Pago') }}">
                                                <span class="cell-content">
                                                    <span class="badge bg-info">{{ $payment->payment_method_label }}</span>
                                                    @if($payment->transaction_id)
                                                        <br><small class="text-muted">ID: {{ $payment->transaction_id }}</small>
                                                    @endif
                                                </span>
                                            </td>
                                            <td data-column="payment_date" data-priority="5" data-label="{{ __('Fecha de Pago') }}">
                                                <span class="cell-content">{{ $payment->payment_date->format('d/m/Y') }}</span>
                                            </td>
                                            <td data-column="amount" data-priority="6" data-label="{{ __('Monto') }}">
                                                <span class="cell-content text-success fw-bold">
                                                    ${{ number_format($payment->amount, 2) }}
                                                </span>
                                            </td>
                                            <td data-column="status" data-priority="7" data-label="{{ __('Estado') }}">
                                                <span class="cell-content">
                                                    <span class="badge
                                                        @switch($payment->status)
                                                            @case('completed')
                                                                bg-success
                                                                @break
                                                            @case('pending')
                                                                bg-warning
                                                                @break
                                                            @case('failed')
                                                            @case('cancelled')
                                                                bg-danger
                                                                @break
                                                            @case('refunded')
                                                                bg-info
                                                                @break
                                                            @default
                                                                bg-secondary
                                                        @endswitch
                                                    ">
                                                        {{ $payment->status_label }}
                                                    </span>
                                                </span>
                                            </td>
                                            <td data-column="acciones" data-priority="1" data-label="{{ __('Acciones') }}" class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    @if($payment->invoice)
                                                        <a  href="{{ route('invoice.show', $payment->invoice->id) }}" target="_blank" class="btn btn-info btn-sm" title="{{__('generic.show')}}">
                                                            <i  class="fa-solid fa-eye m-r-5  text-white"></i>
                                                        </a>
                                                    @endif
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
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @include('partials.pagination',['data'=>$this->payments])
                    @else
                        <div class="text-center py-4">
                            <div class="d-flex flex-column align-items-center">
                                <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">{{ __('No se encontraron pagos') }}</h5>
                                <p class="text-muted">No hay pagos registrados que coincidan con los filtros aplicados.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
