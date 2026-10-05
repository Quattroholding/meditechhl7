<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Editar Movimiento
                @endslot
                @slot('li_1')
                    Módulo Financiero
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>Editar Movimiento</h4>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('treasury.movements.update', $movement) }}" id="movementForm">
                                @csrf
                                @method('PUT')

                                <!-- Información del Movimiento -->
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="mb-3 text-primary">Información del Movimiento</h5>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="input-block local-forms">
                                            <x-input-label for="movement_type" :value="__('Tipo de Movimiento')" required/>
                                            <select id="movement_type" name="movement_type" class="form-control block w-full" required onchange="updateFormFields()" disabled>
                                                <option value="">Seleccione un tipo</option>
                                                <option value="deposit" {{ old('movement_type', $movement->movement_type) == 'deposit' ? 'selected' : '' }}>Depósito</option>
                                                <option value="withdrawal" {{ old('movement_type', $movement->movement_type) == 'withdrawal' ? 'selected' : '' }}>Retiro</option>
                                                <option value="transfer" {{ old('movement_type', $movement->movement_type) == 'transfer' ? 'selected' : '' }}>Transferencia</option>
                                                <option value="adjustment" {{ old('movement_type', $movement->movement_type) == 'adjustment' ? 'selected' : '' }}>Ajuste</option>
                                            </select>
                                            <small class="text-muted">No se puede cambiar el tipo de movimiento</small>
                                            <x-input-error :messages="$errors->get('movement_type')" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="input-block local-forms">
                                            <x-input-label for="movement_date" :value="__('Fecha del Movimiento')" required/>
                                            <x-text-input id="movement_date" class="block mt-1 w-full" type="date" name="movement_date" :value="old('movement_date', $movement->movement_date->toDateString())" required/>
                                            <x-input-error :messages="$errors->get('movement_date')" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="input-block local-forms">
                                            <x-input-label for="amount" :value="__('Monto')" required/>
                                            <x-text-input id="amount" class="block mt-1 w-full" type="number" name="amount" :value="old('amount', $movement->amount)" step="0.01" min="0.01" required/>
                                            <small class="text-muted">Monto anterior: {{ number_format($movement->getOriginal('amount'), 2) }}</small>
                                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6 col-xl-6" id="bank_section">
                                        <div class="input-block local-forms">
                                            <x-input-label for="bank_id" :value="__('Banco')"/>
                                            <select id="bank_id" name="bank_id" class="form-control block w-full" disabled>
                                                <option value="">Seleccione un banco</option>
                                                @forelse(\App\Models\Treasury\Bank::where('client_id', auth()->user()->client_id)->get() as $bank)
                                                    <option value="{{ $bank->id }}" {{ old('bank_id', $movement->bank_id) == $bank->id ? 'selected' : '' }}>
                                                        {{ $bank->bank_name }} - {{ $bank->account_number }}
                                                    </option>
                                                @empty
                                                    <option disabled>No hay bancos disponibles</option>
                                                @endforelse
                                            </select>
                                            <small class="text-muted">No se puede cambiar el banco</small>
                                            <x-input-error :messages="$errors->get('bank_id')" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-6" id="cash_section">
                                        <div class="input-block local-forms">
                                            <x-input-label for="cash_register_id" :value="__('Caja')"/>
                                            <select id="cash_register_id" name="cash_register_id" class="form-control block w-full" disabled>
                                                <option value="">Seleccione una caja</option>
                                                @forelse(\App\Models\Treasury\CashRegister::where('client_id', auth()->user()->client_id)->get() as $cash)
                                                    <option value="{{ $cash->id }}" {{ old('cash_register_id', $movement->cash_register_id) == $cash->id ? 'selected' : '' }}>
                                                        {{ $cash->name }} - {{ $cash->branch->name ?? 'N/A' }}
                                                    </option>
                                                @empty
                                                    <option disabled>No hay cajas disponibles</option>
                                                @endforelse
                                            </select>
                                            <small class="text-muted">No se puede cambiar la caja</small>
                                            <x-input-error :messages="$errors->get('cash_register_id')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="input-block local-forms">
                                            <x-input-label for="description" :value="__('Descripción')" required/>
                                            <textarea id="description" name="description" class="form-control block w-full" rows="3" placeholder="Descripción del movimiento" required>{{ old('description', $movement->description) }}</textarea>
                                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="input-block local-forms">
                                            <x-input-label for="reference_number" :value="__('Número de Referencia')" />
                                            <x-text-input id="reference_number" class="block mt-1 w-full" type="text" name="reference_number" :value="old('reference_number', $movement->reference_number)" placeholder="Ej: CHQ-001, TRF-123"/>
                                            <small class="text-muted">Opcional: número de cheque, voucher, etc</small>
                                            <x-input-error :messages="$errors->get('reference_number')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end mt-4">
                                    <div class="doctor-submit text-end">
                                        <button type="submit" class="btn btn-primary submit-form me-2">Guardar Cambios</button>
                                        <a class="btn btn-secondary cancel-form" href="{{ route('treasury.movements.index') }}">Cancelar</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function updateFormFields() {
            const movementType = document.getElementById('movement_type').value;
            const bankSection = document.getElementById('bank_section');
            const cashSection = document.getElementById('cash_section');

            if (movementType === 'transfer') {
                bankSection.style.display = 'block';
                cashSection.style.display = 'block';
            } else if (movementType === 'deposit' || movementType === 'withdrawal' || movementType === 'adjustment') {
                bankSection.style.display = 'block';
                cashSection.style.display = 'block';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateFormFields();
        });
    </script>
    @endpush
</x-app-layout>
