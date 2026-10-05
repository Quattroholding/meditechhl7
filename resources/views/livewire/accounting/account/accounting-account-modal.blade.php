@if($isModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-light px-6 py-4 border-b d-flex justify-content-between align-items-center">
                <h3 class="text-lg font-bold">
                    {{ $this->account ? __('accounting.account.edit_title') : __('accounting.account.create_title') }}
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
                    <!-- Code -->
                    <div class="col-12 col-md-6">
                        <div class="input-block local-forms">
                            <x-input-label for="code" :value="__('accounting.account.code')" required="true"/>
                            <x-text-input wire:model="code" id="code" class="block mt-1 w-full" type="text" name="code" placeholder="Ej: 1000"/>
                            <x-input-error :messages="$errors->get('code')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Name -->
                    <div class="col-12 col-md-6">
                        <div class="input-block local-forms">
                            <x-input-label for="name" :value="__('accounting.account.name')" required="true"/>
                            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" placeholder="Ej: Caja"/>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Description -->
                    <div class="col-12">
                        <div class="input-block local-forms">
                            <x-input-label for="description" :value="__('accounting.account.description')"/>
                            <x-textarea-input wire:model="description" id="description" class="block mt-1 w-full" name="description" placeholder="Descripción opcional..."/>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Account Type -->
                    <div class="col-12 col-md-4">
                        <div class="input-block local-forms">
                            <x-input-label for="account_type" :value="__('accounting.account.type')" required="true"/>
                            <x-select-input wire:model="account_type" id="account_type" name="account_type" :options="[
                                'asset' => __('accounting.account.types.asset'),
                                'liability' => __('accounting.account.types.liability'),
                                'equity' => __('accounting.account.types.equity'),
                                'income' => __('accounting.account.types.income'),
                                'expense' => __('accounting.account.types.expense'),
                                'cost' => __('accounting.account.types.cost'),
                            ]" :selected="[null]" class="block w-full"/>
                            <x-input-error :messages="$errors->get('account_type')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Parent Account -->
                    <div class="col-12 col-md-4">
                        <div class="input-block local-forms">
                            <x-input-label for="parent_id" :value="__('accounting.account.parent')"/>
                            <select wire:model="parent_id" id="parent_id" class="form-control">
                                <option value="">{{ __('accounting.account.no_parent') }}</option>
                                @foreach ($parentAccounts as $acc)
                                    <option value="{{ $acc->id }}">
                                        {{ $acc->code }} - {{ $acc->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('parent_id')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Level -->
                    <div class="col-12 col-md-4">
                        <div class="input-block local-forms">
                            <x-input-label for="level" :value="__('accounting.account.level')"/>
                            <x-text-input wire:model="level" id="level" class="block mt-1 w-full" type="number" name="level" disabled/>
                            <x-input-error :messages="$errors->get('level')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Allows Transaction -->
                    <div class="col-12">
                        <div class="input-block local-forms">
                            <div class="form-check">
                                <input type="checkbox" wire:model="allows_transaction" id="allows_transaction" class="form-check-input"/>
                                <label class="form-check-label" for="allows_transaction">
                                    {{ __('accounting.account.allows_transaction') }}
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('allows_transaction')" class="mt-2" />
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
                            ]" :selected="['active']" class="block w-full"/>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="doctor-submit text-end">
                    @if ($this->account)
                        <button type="button" wire:click="delete" wire:confirm="{{ __('accounting.account.confirm_delete') }}" class="btn btn-danger me-2">
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
