<div>
    @if($showModal)
        <div class="modal-overlay" wire:click="closePaymentSchedulingModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 10000; display: flex; align-items: center; justify-content: center;">
            <div class="modal-content" wire:click.stop style="position: relative; max-width: 700px; width: 90%; max-height: 90vh; overflow-y: auto;">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('finance.payment_schedule.title', ['invoice' => $invoice->invoice_number]) }}</h5>
                    <button type="button" class="close" wire:click="closeModal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="p-6">
                <!-- Invoice Info -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="alert alert-light" role="alert">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <small class="text-muted d-block mb-1">{{ __('finance.payment_schedule.supplier') }}:</small>
                                    <strong>{{ $invoice->supplier->legal_name }}</strong>
                                </div>
                                <div class="col-12 col-md-6">
                                    <small class="text-muted d-block mb-1">{{ __('finance.payment_schedule.total') }}:</small>
                                    <strong>{{ number_format($invoice->total_amount, 2) }}</strong>
                                </div>
                                <div class="col-12 col-md-6 mt-2">
                                    <small class="text-muted d-block mb-1">{{ __('finance.payment_schedule.paid') }}:</small>
                                    <strong>{{ number_format($invoice->paid_amount, 2) }}</strong>
                                </div>
                                <div class="col-12 col-md-6 mt-2">
                                    <small class="text-muted d-block mb-1">{{ __('finance.payment_schedule.remaining') }}:</small>
                                    <strong class="text-primary">{{ number_format($this->remainingAmount, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Schedule Table -->
                <form wire:submit.prevent="save">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('finance.payment_schedule.payment_date') }}</th>
                                    <th class="text-right">{{ __('generic.amount') }}</th>
                                    <th class="text-center">{{ __('generic.status') }}</th>
                                    <th class="text-center">{{ __('generic.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($schedules as $index => $schedule)
                                    <tr>
                                        <td>
                                            <x-text-input type="date" wire:model="schedules.{{ $index }}.payment_date" class="form-control form-control-sm"/>
                                        </td>
                                        <td>
                                            <x-text-input type="number" step="0.01" wire:model.live="schedules.{{ $index }}.amount" class="form-control form-control-sm text-right"/>
                                        </td>
                                        <td class="text-center">
                                            @if ($schedule['paid'])
                                                <span class="badge bg-success">{{ __('generic.paid') }}</span>
                                            @else
                                                <span class="badge bg-warning">{{ __('generic.pending') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button type="button" wire:click="removeSchedule({{ $index }})" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            {{ __('finance.payment_schedule.no_schedules') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Add Schedule Button -->
                    <div class="mb-3">
                        <button type="button" wire:click="addSchedule" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-plus me-2"></i> {{ __('finance.payment_schedule.add_payment') }}
                        </button>
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Banco *</label>
                                <select wire:model="defaultBankId" class="form-select form-select-sm">
                                    <option value="">Selecciona un banco</option>
                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->balance_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Caja</label>
                                <select wire:model="defaultCashRegisterId" class="form-select form-select-sm">
                                    <option value="">Selecciona una caja</option>
                                    @foreach($cashRegisters as $register)
                                        <option value="{{ $register->id }}">{{ $register->balance_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Remaining Info -->
                    <div class="alert alert-info mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="d-block">{{ __('finance.payment_schedule.remaining_to_schedule') }}:</small>
                                <strong class="text-primary" style="font-size: 1.25rem;">{{ number_format($this->remainingAmount, 2) }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="doctor-submit text-end">
                        <button type="submit" class="btn btn-primary me-2">
                            {{ __('finance.payment_schedule.save_schedule') }}
                        </button>
                        <button type="button" wire:click="closeModal" class="btn btn-secondary">
                            {{ __('button.cancel') }}
                        </button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    @endif
</div>
