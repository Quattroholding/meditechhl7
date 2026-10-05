@if($isModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-light px-6 py-4 border-b d-flex justify-content-between align-items-center">
                <h3 class="text-lg font-bold">
                    {{ $this->paymentMethod ? __('treasury.payment_method.edit_title') : __('treasury.payment_method.create_title') }}
                </h3>
                <button
                    type="button"
                    wire:click="$dispatch('closeModal')"
                    class="text-gray-500 hover:text-gray-700"
                    style="border: none; background: none; cursor: pointer;"
                >
                    ×
                </button>
            </div>

            <form wire:submit.prevent="save" class="p-6">
@else
    <form wire:submit.prevent="save">
@endif
                <div class="row">
                    <!-- Name -->
                    <div class="col-12">
                        <div class="input-block local-forms">
                            <x-input-label for="name" :value="__('treasury.payment_method.name')" required="true"/>
                            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" placeholder="Ej: Transferencia Bancaria"/>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Destination Type -->
                    <div class="col-12">
                        <div class="input-block local-forms">
                            <x-input-label for="destination_type" :value="__('treasury.payment_method.destination_type')" required="true"/>
                            <x-select-input wire:model="destination_type" id="destination_type" name="destination_type" :options="[
                                'bank' => __('treasury.payment_method.types.bank'),
                                'cash' => __('treasury.payment_method.types.cash'),
                            ]" :selected="['bank']" class="block w-full"/>
                            <x-input-error :messages="$errors->get('destination_type')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Bank Selection (conditional) -->
                @if ($destination_type === 'bank')
                    <div class="row">
                        <div class="col-12">
                            <div class="input-block local-forms">
                                <x-input-label for="default_bank_id" :value="__('treasury.payment_method.default_bank')" required="true"/>
                                <select wire:model="default_bank_id" id="default_bank_id" class="form-control">
                                    <option value="">{{ __('generic.select') }}</option>
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank->id }}">
                                            {{ $bank->bank_name }} - {{ $bank->account_number }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('default_bank_id')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Cash Register Selection (conditional) -->
                @if ($destination_type === 'cash')
                    <div class="row">
                        <div class="col-12">
                            <div class="input-block local-forms">
                                <x-input-label for="default_cash_register_id" :value="__('treasury.payment_method.default_cash_register')" required="true"/>
                                <select wire:model="default_cash_register_id" id="default_cash_register_id" class="form-control">
                                    <option value="">{{ __('generic.select') }}</option>
                                    @foreach ($cashRegisters as $register)
                                        <option value="{{ $register->id }}">
                                            {{ $register->name }} ({{ $register->branch->name }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('default_cash_register_id')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <!-- Status -->
                    <div class="col-12 col-md-6">
                        <div class="input-block local-forms">
                            <x-input-label for="status" :value="__('generic.status')"/>
                            <x-select-input wire:model="status" id="status" name="status" :options="[
                                'active' => __('generic.active'),
                                'inactive' => __('generic.inactive'),
                            ]" :selected="['active']" class="block w-full"/>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="doctor-submit text-end">
                    @if ($this->paymentMethod)
                        <button type="button" wire:click="delete" wire:confirm="{{ __('treasury.payment_method.confirm_delete') }}" class="btn btn-danger me-2">
                            {{ __('button.delete') }}
                        </button>
                    @endif
                    <button type="submit" class="btn btn-primary me-2">
                        {{ __('button.save') }}
                    </button>
                    @if($isModal)
                        <button type="button" wire:click="$dispatch('closeModal')" class="btn btn-secondary">
                            {{ __('button.cancel') }}
                        </button>
                    @endif
                </div>
            </form>
        @if($isModal)
            </div>
        </div>
        @endif
