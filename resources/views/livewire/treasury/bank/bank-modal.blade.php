@if($isModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-light px-6 py-4 border-b d-flex justify-content-between align-items-center">
                <h3 class="text-lg font-bold">
                    {{ $this->bank ? __('treasury.banks.edit_title') : __('treasury.banks.create_title') }}
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
                    <!-- Bank Name -->
                    <div class="col-12">
                        <div class="input-block local-forms">
                            <x-input-label for="bank_name" :value="__('treasury.banks.name')" required="true"/>
                            <x-text-input wire:model="bank_name" id="bank_name" class="block mt-1 w-full" type="text" name="bank_name" placeholder="Ej: Banco Latinoamericano"/>
                            <x-input-error :messages="$errors->get('bank_name')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Account Number -->
                    <div class="col-12">
                        <div class="input-block local-forms">
                            <x-input-label for="account_number" :value="__('treasury.banks.account_number')" required="true"/>
                            <x-text-input wire:model="account_number" id="account_number" class="block mt-1 w-full" type="text" name="account_number" placeholder="Ej: 123456789"/>
                            <x-input-error :messages="$errors->get('account_number')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Account Type -->
                    <div class="col-12 col-md-6">
                        <div class="input-block local-forms">
                            <x-input-label for="account_type" :value="__('treasury.banks.account_type')" required="true"/>
                            <x-select-input wire:model="account_type" id="account_type" name="account_type" :options="[
                                'checking' => __('treasury.banks.types.checking'),
                                'savings' => __('treasury.banks.types.savings'),
                                'money_market' => __('treasury.banks.types.money_market'),
                                'credit_line' => __('treasury.banks.types.credit_line'),
                            ]" :selected="$this->account_type" class="block w-full"/>
                            <x-input-error :messages="$errors->get('account_type')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Currency -->
                    <div class="col-12 col-md-6">
                        <div class="input-block local-forms">
                            <x-input-label for="currency" :value="__('generic.currency')" required="true"/>
                            <x-select-input wire:model="currency" id="currency" name="currency" :options="[
                                'PAB' => __('treasury.currency.pab'),
                                'USD' => __('treasury.currency.usd'),
                                'EUR' => __('treasury.currency.eur'),
                            ]" :selected="$this->currency" class="block w-full"/>
                            <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Accounting Account -->
                    <div class="col-12">
                        <div class="input-block local-forms">
                            <x-input-label for="accounting_account_id" :value="__('treasury.banks.accounting_account')" required="true"/>
                            <select wire:model="accounting_account_id" id="accounting_account_id" class="form-control">
                                <option value="">{{ __('generic.select') }}</option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}">
                                        {{ $account->code }} - {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('accounting_account_id')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Status -->
                    <div class="col-12 col-md-6">
                        <div class="input-block local-forms">
                            <x-input-label for="status" :value="__('generic.status')"/>
                            <x-select-input wire:model="status" id="status" name="status" :options="[
                                'active' => __('generic.active'),
                                'inactive' => __('generic.inactive'),
                                'suspended' => __('treasury.banks.status.suspended'),
                            ]" :selected="$this->status" class="block w-full"/>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="doctor-submit text-end">
                    @if ($this->bank)
                        <button type="button" wire:click="delete" wire:confirm="{{ __('treasury.banks.confirm_delete') }}" class="btn btn-danger me-2">
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
