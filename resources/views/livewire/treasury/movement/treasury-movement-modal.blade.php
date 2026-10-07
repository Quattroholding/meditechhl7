<form wire:submit.prevent="save">
    @csrf

    <div class="row">
        <!-- Movement Type -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="input-block local-forms">
                <x-input-label for="movement_type" :value="__('Tipo de Movimiento')" required="true" />
                <select wire:model.live="movement_type" id="movement_type" class="form-control form-select" required>
                    <option value="">Seleccione un tipo</option>
                    <option value="deposit">Depósito</option>
                    <option value="withdrawal">Retiro</option>
                    <option value="transfer">Transferencia</option>
                    <option value="adjustment">Ajuste</option>
                </select>
                <x-input-error :messages="$errors->get('movement_type')" class="mt-2" />
            </div>
        </div>

        <!-- Movement Date -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="input-block local-forms">
                <x-input-label for="movement_date" :value="__('Fecha del Movimiento')" required="true" />
                <x-text-input wire:model="movement_date" id="movement_date" class="block mt-1 w-full" type="date" name="movement_date" required />
                <x-input-error :messages="$errors->get('movement_date')" class="mt-2" />
            </div>
        </div>

        <!-- Amount -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="input-block local-forms">
                <x-input-label for="amount" :value="__('Monto')" required="true" />
                <x-text-input wire:model="amount" id="amount" class="block mt-1 w-full" type="number" name="amount" step="0.01" min="0.01" required />
                <x-input-error :messages="$errors->get('amount')" class="mt-2" />
            </div>
        </div>
    </div>

    <!-- Bank and Cash Sections -->
    <div class="row">
        <!-- Bank Section -->
        <div class="col-12 col-md-6">
            <div class="input-block local-forms">
                <x-input-label for="bank_id" :value="__('Banco')" />
                <select wire:model="bank_id" id="bank_id" class="form-control form-select">
                    <option value="">Seleccione un banco</option>
                    @foreach ($banks as $bank)
                        <option value="{{ $bank->id }}">
                            {{ $bank->bank_name }} - {{ $bank->account_number }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('bank_id')" class="mt-2" />
            </div>
        </div>

        <!-- Cash Register Section -->
        <div class="col-12 col-md-6">
            <div class="input-block local-forms">
                <x-input-label for="cash_register_id" :value="__('Caja')" />
                <select wire:model="cash_register_id" id="cash_register_id" class="form-control form-select">
                    <option value="">Seleccione una caja</option>
                    @foreach ($cashRegisters as $cash)
                        <option value="{{ $cash->id }}">
                            {{ $cash->name }} - {{ $cash->branch->name ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('cash_register_id')" class="mt-2" />
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Description -->
        <div class="col-12 col-md-6">
            <div class="input-block local-forms">
                <x-input-label for="description" :value="__('Descripción')" required="true" />
                <textarea wire:model="description" id="description" class="form-control" rows="3" placeholder="Descripción del movimiento" required></textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>
        </div>

        <!-- Reference Number -->
        <div class="col-12 col-md-6">
            <div class="input-block local-forms">
                <x-input-label for="reference_number" :value="__('Número de Referencia')" />
                <x-text-input wire:model="reference_number" id="reference_number" class="block mt-1 w-full" type="text" name="reference_number" placeholder="Ej: CHQ-001, TRF-123" />
                <small class="text-muted">Opcional: número de cheque, voucher, etc</small>
                <x-input-error :messages="$errors->get('reference_number')" class="mt-2" />
            </div>
        </div>
    </div>

    <div class="doctor-submit text-end">
        <button type="submit" class="btn btn-primary me-2">
            {{ __('button.save') }}
        </button>
        @if($isModal)
            <button type="button" wire:click="$dispatch('closeModal')" class="btn btn-secondary">
                {{ __('button.cancel') }}
            </button>
        @else
            <a class="btn btn-secondary cancel-form" href="{{ route('treasury.movements.index') }}">
                {{ __('button.cancel') }}
            </a>
        @endif
    </div>
</form>
