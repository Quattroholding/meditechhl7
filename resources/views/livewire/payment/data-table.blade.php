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
                            <table class="table border-0 custom-table comman-table mb-0">
                                <thead>
                                    <tr>
                                        <th wire:click="sortBy('payment_number')" style="cursor: pointer;">
                                            {{ __('Número de Pago') }}
                                            @if($sortField === 'payment_number')
                                                <i class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </th>
                                        <th>{{ __('Factura') }}</th>
                                        <th>{{ __('Paciente') }}</th>
                                        <th wire:click="sortBy('payment_method')" style="cursor: pointer;">
                                            {{ __('Método de Pago') }}
                                            @if($sortField === 'payment_method')
                                                <i class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </th>
                                        <th wire:click="sortBy('payment_date')" style="cursor: pointer;">
                                            {{ __('Fecha de Pago') }}
                                            @if($sortField === 'payment_date')
                                                <i class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </th>
                                        <th wire:click="sortBy('amount')" style="cursor: pointer;">
                                            {{ __('Monto') }}
                                            @if($sortField === 'amount')
                                                <i class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </th>
                                        <th wire:click="sortBy('status')" style="cursor: pointer;">
                                            {{ __('Estado') }}
                                            @if($sortField === 'status')
                                                <i class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </th>
                                        <th>{{ __('Acciones') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($this->payments as $payment)
                                        <tr>
                                            <td>
                                                <strong>{{ $payment->payment_number }}</strong>
                                                @if($payment->reference_number)
                                                    <br><small class="text-muted">Ref: {{ $payment->reference_number }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($payment->invoice)
                                                    <a href="{{ route('invoice.show', $payment->invoice->id) }}" class="text-primary">
                                                        {{ $payment->invoice->invoice_number }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($payment->patient)
                                                    <div class="profile-image">
                                                        <a href="{{ route('patient.profile', $payment->patient->id) }}">
                                                         {!!  $payment->patient->profile_name!!}
                                                        </a>
                                                    </div>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $payment->payment_method_label }}</span>
                                                @if($payment->transaction_id)
                                                    <br><small class="text-muted">ID: {{ $payment->transaction_id }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                            <td class="text-success fw-bold">
                                                ${{ number_format($payment->amount, 2) }}
                                            </td>
                                            <td>
                                                <span class="badge
                                                    @switch($payment->status)
                                                        @case('completed')
                                                            badge-success
                                                            @break
                                                        @case('pending')
                                                            badge-warning
                                                            @break
                                                        @case('failed')
                                                        @case('cancelled')
                                                            badge-danger
                                                            @break
                                                        @case('refunded')
                                                            badge-info
                                                            @break
                                                        @default
                                                            badge-secondary
                                                    @endswitch
                                                ">
                                                    {{ $payment->status_label }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="dropdown dropdown-action">
                                                    <a href="javascript:;" class="action-icon dropdown-toggle"
                                                       data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        @if($payment->invoice)
                                                            <a class="dropdown-item" href="{{ route('invoice.show', $payment->invoice->id) }}">
                                                                <i class="far fa-eye me-2"></i>{{ __('Ver Factura') }}
                                                            </a>
                                                        @endif
                                                        @if($payment->patient)
                                                            <a class="dropdown-item" href="{{ route('patient.profile', $payment->patient->id) }}">
                                                                <i class="fas fa-user me-2"></i>{{ __('Ver Paciente') }}
                                                            </a>
                                                        @endif
                                                        @if($payment->status === 'completed' && \Carbon\Carbon::parse($payment->created_at)->diffInHours() < 24)
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item text-warning" href="javascript:;" onclick="confirm('¿Está seguro de reembolsar este pago?')">
                                                                <i class="fas fa-undo me-2"></i>{{ __('Reembolsar') }}
                                                            </a>
                                                        @endif
                                                    </div>
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
