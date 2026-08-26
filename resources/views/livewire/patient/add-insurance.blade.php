<div>

    <!-- Botón puede ser usado como acción o componente independiente -->
    @if(!isset($hideButton) || !$hideButton)
        <button wire:click="openModal()" class="btn-head btn-head-light">
            🛡️ {{ __('patient.insurance.manage_insurance') }}
        </button>
    @endif


    @if($showInsuranceModal)
    <!-- Modal -->
    <div class="modal-overlay" wire:click="closeModal" style="z-index: 10000;">
        <div class="modal-content" wire:click.stop style="max-width: 900px;">
            <div class="modal-header">
                <h2 class="modal-title" style="color: #000;">
                    {{ __('patient.insurance.manage_medical_insurance') }} : {{ $patient->name }}
                </h2>
                <button wire:click="closeModal()" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">

                <!-- Tabla de seguros existentes -->
                @if($existingPolicies->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('patient.insurance.current_insurance') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('patient.insurance.priority') }}</th>
                                        <th>{{ __('patient.insurance.company') }}</th>
                                        <th>{{ __('patient.insurance.policy_number') }}</th>
                                        <th>{{ __('patient.insurance.holder') }}</th>
                                        <th>{{ __('patient.insurance.coverage') }}</th>
                                        <th>{{ __('patient.insurance.status') }}</th>
                                        <th>{{ __('patient.insurance.validity') }}</th>
                                        <th>{{ __('patient.acciones') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($existingPolicies as $policy)
                                    <tr>
                                        <td>
                                            <span class="badge bg-{{ $policy->priority == 'primary' ? 'primary' : ($policy->priority == 'secondary' ? 'secondary' : 'info') }}">
                                                {{ ucfirst($policy->priority) }}
                                            </span>
                                        </td>
                                        <td>{{ $policy->insuranceCompany->name ?? 'N/A' }}</td>
                                        <td>{{ $policy->policy_number }}</td>
                                        <td>{{ $policy->subscriber_name }}</td>
                                        <td>{{ $policy->coverage_percentage ?? 0 }}%</td>
                                        <td>
                                            @if($policy->is_active && !$policy->isExpired())
                                                <span class="badge bg-success">{{ __('patient.insurance.active') }}</span>
                                            @elseif($policy->isExpired())
                                                <span class="badge bg-warning">{{ __('patient.insurance.expired') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('patient.insurance.inactive') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($policy->expiration_date)
                                                {{ $policy->expiration_date->format('d/m/Y') }}
                                            @else
                                                {{ __('patient.insurance.no_expiration') }}
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button wire:click="togglePolicyStatus({{ $policy->id }})"
                                                        class="btn btn-sm {{ $policy->is_active ? 'btn-warning' : 'btn-success' }}"
                                                        title="{{ $policy->is_active ? __('patient.insurance.deactivate') : __('patient.insurance.activate') }}">
                                                    <i class="fas fa-{{ $policy->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                                <button wire:click="deletePolicy({{ $policy->id }})"
                                                        class="btn btn-sm btn-danger"
                                                        title="{{ __('button.delete') }}"
                                                        onclick="return confirm('{{ __('patient.insurance.confirm_delete') }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Formulario para agregar nuevo seguro -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-plus"></i> {{ __('patient.insurance.add_new_insurance') }}
                        </h5>
                    </div>
                    <div class="card-body py-4">
                <!-- Warning message for duplicate priority -->
                @if(session()->has('warning'))
                    <div class="alert alert-warning">
                        {{ session('warning') }}
                    </div>
                @endif

                <!-- Row 1: Insurance Company and Priority -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="input-block local-forms">
                            <x-input-label for="insurance_company_id" :value="__('patient.insurance.company')" required="true"/>
                            <select wire:model="insurance_company_id" class="form-control" name="insurance_company_id">
                                <option value="">{{ __('patient.insurance.select_company') }}</option>
                                @foreach($insuranceCompanies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('insurance_company_id')"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-block local-forms">
                            <x-input-label for="priority" :value="__('patient.insurance.priority')" required="true"/>
                            <select wire:model.live="priority" class="form-control" name="priority">
                                @foreach($this->getPriorityOptions() as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('priority')"/>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Policy and Group Numbers -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-block local-forms">
                            <x-input-label for="policy_number" :value="__('patient.insurance.policy_number')" required="true"/>
                            <x-text-input wire:model="policy_number" class="block mt-1 w-full" type="text" name="policy_number" :placeholder="__('patient.insurance.policy_number_placeholder')"/>
                            <x-input-error :messages="$errors->get('policy_number')"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-block local-forms">
                            <x-input-label for="group_number" :value="__('patient.insurance.group_number')"/>
                            <x-text-input wire:model="group_number" class="block mt-1 w-full" type="text" name="group_number" :placeholder="__('patient.insurance.group_number_placeholder')"/>
                            <x-input-error :messages="$errors->get('group_number')"/>
                        </div>
                    </div>
                </div>

                <!-- Row 3: Subscriber Info -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-block local-forms">
                            <x-input-label for="subscriber_id" :value="__('patient.insurance.subscriber_id')" required="true"/>
                            <x-text-input wire:model="subscriber_id" class="block mt-1 w-full" type="text" name="subscriber_id" :placeholder="__('patient.insurance.subscriber_id_placeholder')"/>
                            <x-input-error :messages="$errors->get('subscriber_id')"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-block local-forms">
                            <x-input-label for="subscriber_name" :value="__('patient.insurance.subscriber_name')" required="true"/>
                            <x-text-input wire:model="subscriber_name" class="block mt-1 w-full" type="text" name="subscriber_name" :placeholder="__('patient.insurance.subscriber_name_placeholder')"/>
                            <x-input-error :messages="$errors->get('subscriber_name')"/>
                        </div>
                    </div>
                </div>

                <!-- Row 4: Relationship and Dates -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-block local-forms">
                            <x-input-label for="relationship_to_subscriber" :value="__('patient.insurance.relationship')" required="true"/>
                            <select wire:model="relationship_to_subscriber" class="form-control" name="relationship_to_subscriber">
                                @foreach($this->getRelationshipOptions() as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('relationship_to_subscriber')"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-block local-forms">
                            <x-input-label for="effective_date" :value="__('patient.insurance.effective_date')" required="true"/>
                            <x-text-input wire:model="effective_date" class="block mt-1 w-full" type="date" name="effective_date"/>
                            <x-input-error :messages="$errors->get('effective_date')"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-block local-forms">
                            <x-input-label for="expiration_date" :value="__('patient.insurance.expiration_date')"/>
                            <x-text-input wire:model="expiration_date" class="block mt-1 w-full" type="date" name="expiration_date"/>
                            <x-input-error :messages="$errors->get('expiration_date')"/>
                        </div>
                    </div>
                </div>

                <!-- Row 5: Coverage Details -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="input-block local-forms">
                            <x-input-label for="coverage_percentage" :value="__('patient.insurance.coverage_percentage')"/>
                            <x-text-input wire:model="coverage_percentage" class="block mt-1 w-full" type="number" name="coverage_percentage" min="0" max="100" placeholder="80"/>
                            <x-input-error :messages="$errors->get('coverage_percentage')"/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-block local-forms">
                            <x-input-label for="copay_amount" :value="__('patient.insurance.copay')"/>
                            <x-text-input wire:model="copay_amount" class="block mt-1 w-full" type="number" name="copay_amount" min="0" step="0.01" placeholder="25.00"/>
                            <x-input-error :messages="$errors->get('copay_amount')"/>
                        </div>
                    </div>
                    {{--}}
                    <div class="col-md-3">
                        <div class="input-block local-forms">
                            <x-input-label for="deductible_amount" :value="__('Deducible')"/>
                            <x-text-input wire:model="deductible_amount" class="block mt-1 w-full" type="number" name="deductible_amount" min="0" step="0.01" placeholder="500.00"/>
                            <x-input-error :messages="$errors->get('deductible_amount')"/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-block local-forms">
                            <x-input-label for="out_of_pocket_max" :value="__('Máx. de Bolsillo')"/>
                            <x-text-input wire:model="out_of_pocket_max" class="block mt-1 w-full" type="number" name="out_of_pocket_max" min="0" step="0.01" placeholder="2000.00"/>
                            <x-input-error :messages="$errors->get('out_of_pocket_max')"/>
                        </div>
                    </div>
                    {{--}}
                </div>

                <!-- Row 6: Status and Notes -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="input-block local-forms">
                            <div class="form-check">
                                <input wire:model="is_active" class="form-check-input" type="checkbox" id="is_active">
                                <label class="form-check-label" for="is_active">
                                    {{ __('patient.insurance.active_insurance') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="input-block local-forms">
                            <x-input-label for="notes" :value="__('patient.insurance.notes')"/>
                            <x-textarea-input wire:model="notes" class="block mt-1 w-full" name="notes" rows="2" :placeholder="__('patient.insurance.notes_placeholder')"/>
                            <x-input-error :messages="$errors->get('notes')"/>
                        </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="margin-top: 20px; display: flex; gap: 15px;">
                <button wire:click="save" class="btn btn-primary" style="flex: 1;">
                    <i class="fas fa-save"></i> {{ __('patient.insurance.add_insurance') }}
                </button>
                <button type="button" wire:click="closeModal()" class="btn btn-secondary">
                    <i class="fas fa-times"></i> {{ __('generic.close') }}
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    </div>
    @endif
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showToastrAddInsurance', ({type, message}) => {
                console.log('Toast event received:', {type, message}); // Debug
                if (typeof toastr !== 'undefined') {
                    toastr[type](message, '', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        timeOut: 5000,
                    });
                } else {
                    console.warn('toastr not loaded');
                }
            });
        });
    </script>
</div>
