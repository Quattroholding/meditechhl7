<form wire:submit="save" id="form">
    @csrf
    <div class="row">
        <!-- Name -->
        <div class="col-12">
            <div class="input-block local-forms">
                <x-input-label for="name" :value="__('treasury.cash-registers.name')" required="true"/>
                <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" placeholder="Ej: Caja Principal"/>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Branch -->
        <div class="col-12 col-md-6">
            <div class="input-block local-forms">
                <x-input-label for="branch_id" :value="__('treasury.cash-registers.branch_id')" required="true"/>
                <select wire:model="branch_id" id="branch_id" class="form-control">
                    <option value="">{{ __('generic.select') }}</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('branch_id')" class="mt-2" />
            </div>
        </div>

        <!-- Responsible User -->
        <div class="col-12 col-md-6">
            <div class="input-block local-forms">
                <x-input-label for="responsible_user_id" :value="__('treasury.cash-registers.responsible_user_id')" required="true"/>
                <select wire:model="responsible_user_id" id="responsible_user_id" class="form-control">
                    <option value="">{{ __('generic.select') }}</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->full_name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('responsible_user_id')" class="mt-2" />
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Accounting Account -->
        <div class="col-12">
            <div class="input-block local-forms">
                <x-input-label for="accounting_account_id" :value="__('treasury.cash-registers.accounting_account')" required="true"/>
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
                    'active' =>  __('treasury.cash-registers.statuses.active'),
                    'closed' => __('treasury.cash-registers.statuses.closed'),
                    'inactive' =>  __('treasury.cash-registers.statuses.inactive'),
                ]" :selected="$this->status" class="block w-full"/>
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
            </div>
        </div>
    </div>

    <div class="doctor-submit text-end">
        @if ($this->cashRegister)
            <button type="button" wire:click="delete" wire:confirm="{{ __('treasury.cash-registers.confirm_delete') }}" class="btn btn-danger me-2">
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
        @if(!$isModal)
            <a class="btn btn-secondary cancel-form" href="{{ route('treasury.cash-registers.index') }}">  {{ __('button.cancel') }}</a>
        @endif
    </div>
</form>
