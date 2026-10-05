<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Editar Banco
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
                                    <h4>Editar Banco</h4>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('treasury.banks.update', $bank) }}" id="bankForm">
                                @csrf
                                @method('PUT')

                                <!-- Información Básica del Banco -->
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="mb-3 text-primary">Información del Banco</h5>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="input-block local-forms">
                                            <x-input-label for="bank_name" :value="__('Nombre del Banco')" required/>
                                            <x-text-input id="bank_name" class="block mt-1 w-full" type="text" name="bank_name" :value="old('bank_name', $bank->bank_name)" placeholder="Ej: Banco Central"/>
                                            <x-input-error :messages="$errors->get('bank_name')" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="input-block local-forms">
                                            <x-input-label for="account_number" :value="__('Número de Cuenta')" required/>
                                            <x-text-input id="account_number" class="block mt-1 w-full" type="text" name="account_number" :value="old('account_number', $bank->account_number)" placeholder="Ej: 1234567890"/>
                                            <x-input-error :messages="$errors->get('account_number')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="input-block local-forms">
                                            <x-input-label for="account_type" :value="__('Tipo de Cuenta')" required/>
                                            <select id="account_type" name="account_type" class="form-control block w-full" required>
                                                <option value="">Seleccione un tipo</option>
                                                <option value="checking" {{ old('account_type', $bank->account_type) == 'checking' ? 'selected' : '' }}>Cuenta Corriente</option>
                                                <option value="savings" {{ old('account_type', $bank->account_type) == 'savings' ? 'selected' : '' }}>Cuenta de Ahorros</option>
                                            </select>
                                            <x-input-error :messages="$errors->get('account_type')" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="input-block local-forms">
                                            <x-input-label for="currency" :value="__('Moneda')" required/>
                                            <select id="currency" name="currency" class="form-control block w-full" required>
                                                <option value="">Seleccione una moneda</option>
                                                <option value="USD" {{ old('currency', $bank->currency) == 'USD' ? 'selected' : '' }}>USD - Dólar</option>
                                                <option value="DOP" {{ old('currency', $bank->currency) == 'DOP' ? 'selected' : '' }}>DOP - Peso Dominicano</option>
                                                <option value="EUR" {{ old('currency', $bank->currency) == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                            </select>
                                            <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="input-block local-forms">
                                            <x-input-label for="balance" :value="__('Saldo Actual')" required/>
                                            <x-text-input id="balance" class="block mt-1 w-full" type="number" name="balance" :value="old('balance', $bank->balance)" step="0.01" min="0"/>
                                            <small class="text-muted">Saldo anterior: {{ number_format($bank->getOriginal('balance'), 2) }}</small>
                                            <x-input-error :messages="$errors->get('balance')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <!-- Configuración Contable -->
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="mb-3 text-primary">Configuración Contable</h5>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="input-block local-forms">
                                            <x-input-label for="accounting_account_id" :value="__('Cuenta Contable')" required/>
                                            <select id="accounting_account_id" name="accounting_account_id" class="form-control block w-full" required>
                                                <option value="">Seleccione una cuenta contable</option>
                                                @forelse(\App\Models\Accounting\AccountingAccount::where('type', 'asset')->get() as $account)
                                                    <option value="{{ $account->id }}" {{ old('accounting_account_id', $bank->accounting_account_id) == $account->id ? 'selected' : '' }}>
                                                        {{ $account->account_number }} - {{ $account->account_name }}
                                                    </option>
                                                @empty
                                                    <option disabled>No hay cuentas contables disponibles</option>
                                                @endforelse
                                            </select>
                                            <x-input-error :messages="$errors->get('accounting_account_id')" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="input-block local-forms">
                                            <x-input-label for="status" :value="__('Estado')" required/>
                                            <select id="status" name="status" class="form-control block w-full" required>
                                                <option value="active" {{ old('status', $bank->status) == 'active' ? 'selected' : '' }}>Activo</option>
                                                <option value="suspended" {{ old('status', $bank->status) == 'suspended' ? 'selected' : '' }}>Suspendido</option>
                                                <option value="inactive" {{ old('status', $bank->status) == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                                            </select>
                                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end mt-4">
                                    <div class="doctor-submit text-end">
                                        <button type="submit" class="btn btn-primary submit-form me-2">Guardar Cambios</button>
                                        <a class="btn btn-secondary cancel-form" href="{{ route('treasury.banks.index') }}">Cancelar</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
