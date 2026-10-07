<form wire:submit.prevent="save">
    @csrf
    <div class="row">
        <!-- RUC -->
        <div class="col-12 col-md-6">
            <div class="input-block local-forms">
                <x-input-label for="ruc" :value="__('finance.suppliers.ruc')" required="true"/>
                <x-text-input wire:model="ruc" id="ruc" class="block mt-1 w-full" type="text" name="ruc" placeholder="Ej: 123456789"/>
                <x-input-error :messages="$errors->get('ruc')" class="mt-2" />
            </div>
        </div>

        <!-- Credit Days -->
        <div class="col-12 col-md-6">
            <div class="input-block local-forms">
                <x-input-label for="credit_days" :value="__('finance.suppliers.credit_days')" required="true"/>
                <x-text-input wire:model="credit_days" id="credit_days" class="block mt-1 w-full" type="number" name="credit_days" min="0" max="365"/>
                <x-input-error :messages="$errors->get('credit_days')" class="mt-2" />
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Legal Name -->
        <div class="col-12">
            <div class="input-block local-forms">
                <x-input-label for="legal_name" :value="__('finance.suppliers.legal_name')" required="true"/>
                <x-text-input wire:model="legal_name" id="legal_name" class="block mt-1 w-full" type="text" name="legal_name" placeholder="Ej: Empresa XYZ S.A."/>
                <x-input-error :messages="$errors->get('legal_name')" class="mt-2" />
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Commercial Name -->
        <div class="col-12">
            <div class="input-block local-forms">
                <x-input-label for="commercial_name" :value="__('finance.suppliers.commercial_name')"/>
                <x-text-input wire:model="commercial_name" id="commercial_name" class="block mt-1 w-full" type="text" name="commercial_name" placeholder="Ej: XYZ"/>
                <x-input-error :messages="$errors->get('commercial_name')" class="mt-2" />
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Email -->
        <div class="col-12 col-md-6">
            <div class="input-block local-forms">
                <x-input-label for="email" :value="__('generic.email')"/>
                <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email"/>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
        </div>

        <!-- Phone -->
        <div class="col-12 col-md-6">
            <div class="input-block local-forms">
                <x-input-label for="phone" :value="__('generic.phone')"/>
                <x-text-input wire:model="phone" id="phone" class="block mt-1 w-full" type="tel" name="phone"/>
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Contact Person -->
        <div class="col-12">
            <div class="input-block local-forms">
                <x-input-label for="contact_person" :value="__('finance.suppliers.contact_person')"/>
                <x-text-input wire:model="contact_person" id="contact_person" class="block mt-1 w-full" type="text" name="contact_person"/>
                <x-input-error :messages="$errors->get('contact_person')" class="mt-2" />
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Address -->
        <div class="col-12">
            <div class="input-block local-forms">
                <x-input-label for="address" :value="__('generic.address')"/>
                <x-textarea-input wire:model="address" id="address" class="block mt-1 w-full" name="address"/>
                <x-input-error :messages="$errors->get('address')" class="mt-2" />
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Accounting Account -->
        <div class="col-12">
            <div class="input-block local-forms">
                <x-input-label for="accounting_account_id" :value="__('finance.suppliers.accounting_account')" required="true"/>
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
                ]" :selected="['active']" class="block w-full"/>
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
            </div>
        </div>
    </div>

    <div class="doctor-submit text-end">
        @if ($this->supplier)
            <button type="button" wire:click="delete" wire:confirm="{{ __('finance.supplier.confirm_delete') }}" class="btn btn-danger me-2">
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
            <a class="btn btn-secondary cancel-form" href="{{ route('finance.payables.suppliers.index') }}">  {{ __('button.cancel') }}</a>
        @endif
    </div>
</form>

