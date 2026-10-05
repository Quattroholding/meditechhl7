<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Nueva Caja
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
                                    <h4>Registrar Nueva Caja</h4>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('treasury.cash-registers.store') }}" id="cashRegisterForm">
                                @csrf

                                <!-- Información Básica de la Caja -->
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="mb-3 text-primary">Información de la Caja</h5>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="input-block local-forms">
                                            <x-input-label for="name" :value="__('Nombre de la Caja')" required/>
                                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" placeholder="Ej: Caja Recepción"/>
                                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="input-block local-forms">
                                            <x-input-label for="branch_id" :value="__('Sucursal')" required/>
                                            <select id="branch_id" name="branch_id" class="form-control block w-full" required>
                                                <option value="">Seleccione una sucursal</option>
                                                @forelse(\App\Models\Branch::where('client_id', auth()->user()->client_id)->get() as $branch)
                                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                        {{ $branch->name }}
                                                    </option>
                                                @empty
                                                    <option disabled>No hay sucursales disponibles</option>
                                                @endforelse
                                            </select>
                                            <x-input-error :messages="$errors->get('branch_id')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="input-block local-forms">
                                            <x-input-label for="responsible_user_id" :value="__('Usuario Responsable')" required/>
                                            <select id="responsible_user_id" name="responsible_user_id" class="form-control block w-full" required>
                                                <option value="">Seleccione un usuario</option>
                                                @forelse(\App\Models\User::where('client_id', auth()->user()->client_id)->get() as $user)
                                                    <option value="{{ $user->id }}" {{ old('responsible_user_id') == $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }} ({{ $user->email }})
                                                    </option>
                                                @empty
                                                    <option disabled>No hay usuarios disponibles</option>
                                                @endforelse
                                            </select>
                                            <x-input-error :messages="$errors->get('responsible_user_id')" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="input-block local-forms">
                                            <x-input-label for="balance" :value="__('Saldo Inicial')" required/>
                                            <x-text-input id="balance" class="block mt-1 w-full" type="number" name="balance" :value="old('balance', 0)" step="0.01" min="0"/>
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
                                                    <option value="{{ $account->id }}" {{ old('accounting_account_id') == $account->id ? 'selected' : '' }}>
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
                                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Activo</option>
                                                <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>Cerrado</option>
                                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                                            </select>
                                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end mt-4">
                                    <div class="doctor-submit text-end">
                                        <button type="submit" class="btn btn-primary submit-form me-2">Guardar</button>
                                        <a class="btn btn-secondary cancel-form" href="{{ route('treasury.cash-registers.index') }}">Cancelar</a>
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
