@if($isModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            {{-- Header --}}
            <div class="sticky top-0 bg-gray-100 px-6 py-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-bold">
                    {{ $this->costCenter ? __('finance.cost_centers.edit') : __('finance.cost_centers.create') }}
                </h3>
                <button wire:click="$dispatch('closeModal')" class="text-gray-500 hover:text-gray-700">
                    ✕
                </button>
            </div>

            {{-- Form --}}
            <form wire:submit.prevent="save" class="p-6">
@else
    <form wire:submit.prevent="save">
@endif
                <div class="row">
                    {{-- Code --}}
                    <div class="col-12 col-md-6">
                        <div class="input-block local-forms">
                            <x-input-label for="code" :value="__('finance.cost_centers.code')" required="true"/>
                            <x-text-input wire:model="code" id="code" class="block mt-1 w-full" type="text" name="code"/>
                            <x-input-error :messages="$errors->get('code')" class="mt-2" />
                        </div>
                    </div>

                    {{-- Name --}}
                    <div class="col-12 col-md-6">
                        <div class="input-block local-forms">
                            <x-input-label for="name" :value="__('finance.cost_centers.name')" required="true"/>
                            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name"/>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                    </div>
                </div>

                {{-- Description --}}
                <div class="row">
                    <div class="col-12">
                        <div class="input-block local-forms">
                            <x-input-label for="description" :value="__('finance.cost_centers.description')"/>
                            <x-textarea-input wire:model="description" id="description" class="block mt-1 w-full" name="description"/>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Branch --}}
                    <div class="col-12 col-md-6">
                        <div class="input-block local-forms">
                            <x-input-label for="branch_id" :value="__('finance.cost_centers.branch')" required="true"/>
                            <x-select-input wire:model="branch_id" id="branch_id" name="branch_id" :options="$branches->pluck('name', 'id')->toArray()" :selected="$this->branch_id" class="block w-full"/>
                            <x-input-error :messages="$errors->get('branch_id')" class="mt-2" />
                        </div>
                    </div>

                    {{-- Speciality --}}
                    <div class="col-12 col-md-6">
                        <div class="input-block local-forms">
                            <x-input-label for="medical_speciality_id" :value="__('finance.cost_centers.speciality')"/>
                            <x-select-input wire:model="medical_speciality_id" id="medical_speciality_id" name="medical_speciality_id" :options="$specialities->pluck('name', 'id')->toArray()" :selected="$this->medical_speciality_id" class="block w-full"/>
                            <x-input-error :messages="$errors->get('medical_speciality_id')" class="mt-2" />
                        </div>
                    </div>
                </div>

                {{-- Status --}}
                <div class="row">
                    <div class="col-12">
                        <div class="input-block local-forms">
                            <x-input-label for="status" :value="__('finance.cost_centers.status')"/>
                            <x-select-input wire:model="status" id="status" name="status" :options="['active' => __('generic.active'), 'inactive' => __('generic.inactive')]" :selected="$this->status" class="block w-full"/>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end mt-4">
                    <div class="doctor-submit text-end">
                        @if($isModal)
                            <button type="button" wire:click="$dispatch('closeModal')" class="btn btn-secondary me-2">
                                {{ __('button.cancel') }}
                            </button>
                        @endif
                        @if ($this->costCenter)
                            <button type="button" wire:click="delete" wire:confirm="{{ __('finance.cost_centers.delete_confirmation') }}" class="btn btn-danger me-2">
                                {{ __('button.delete') }}
                            </button>
                        @endif
                        <button type="submit" class="btn btn-primary me-2">
                            {{ $this->costCenter ? __('button.update') : __('button.save') }}
                        </button>
                        @if(!$isModal)
                        <a class="btn btn-secondary cancel-form" href="{{ route('finance.cost-centers.index') }}">  {{ __('button.cancel') }}</a>
                        @endif
                    </div>
                </div>
            </form>
        @if($isModal)
            </div>
        </div>
        @endif
