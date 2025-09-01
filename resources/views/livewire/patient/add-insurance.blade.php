<div>
    {{--}}
    <!-- Botón puede ser usado como acción o componente independiente -->
    @if(!isset($hideButton) || !$hideButton)
        <button wire:click="openModal()" class="btn btn-primary float-right">
            🛡️ {{__('Gestionar Seguros')}}
        </button>
    @endif
    {{--}}

    @if($showInsuranceModal)
    <!-- Modal -->
    <div class="modal-overlay" wire:click="closeModal" style="z-index: 10000;">
        <div class="modal-content" wire:click.stop style="max-width: 900px;">
            <div class="modal-header">
                <h2 class="modal-title" style="color: #000;">
                    {{__('Gestionar Seguros Médicos')}} : {{ $patient->name }}
                </h2>
                <button wire:click="closeModal()" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">

                <!-- Tabla de seguros existentes -->
                @if($existingPolicies->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Seguros Actuales</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Prioridad</th>
                                        <th>Compañía</th>
                                        <th>Póliza</th>
                                        <th>Titular</th>
                                        <th>Cobertura</th>
                                        <th>Estado</th>
                                        <th>Vigencia</th>
                                        <th>Acciones</th>
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
                                                <span class="badge bg-success">Activo</span>
                                            @elseif($policy->isExpired())
                                                <span class="badge bg-warning">Expirado</span>
                                            @else
                                                <span class="badge bg-secondary">Inactivo</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($policy->expiration_date)
                                                {{ $policy->expiration_date->format('d/m/Y') }}
                                            @else
                                                Sin vencimiento
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button wire:click="togglePolicyStatus({{ $policy->id }})"
                                                        class="btn btn-sm {{ $policy->is_active ? 'btn-warning' : 'btn-success' }}"
                                                        title="{{ $policy->is_active ? 'Desactivar' : 'Activar' }}">
                                                    <i class="fas fa-{{ $policy->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                                <button wire:click="deletePolicy({{ $policy->id }})"
                                                        class="btn btn-sm btn-danger"
                                                        title="Eliminar"
                                                        onclick="return confirm('¿Está seguro de eliminar este seguro?')">
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
                            <i class="fas fa-plus"></i> Agregar Nuevo Seguro
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
                            <x-input-label for="insurance_company_id" :value="__('Compañía de Seguros')" required="true"/>
                            <select wire:model="insurance_company_id" class="form-control" name="insurance_company_id">
                                <option value="">Seleccione una compañía</option>
                                @foreach($insuranceCompanies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('insurance_company_id')"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-block local-forms">
                            <x-input-label for="priority" :value="__('Prioridad')" required="true"/>
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
                            <x-input-label for="policy_number" :value="__('Número de Póliza')" required="true"/>
                            <x-text-input wire:model.defer="policy_number" class="block mt-1 w-full" type="text" name="policy_number" placeholder="Ej: ABC123456"/>
                            <x-input-error :messages="$errors->get('policy_number')"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-block local-forms">
                            <x-input-label for="group_number" :value="__('Número de Grupo')"/>
                            <x-text-input wire:model.defer="group_number" class="block mt-1 w-full" type="text" name="group_number" placeholder="Ej: GRP001"/>
                            <x-input-error :messages="$errors->get('group_number')"/>
                        </div>
                    </div>
                </div>

                <!-- Row 3: Subscriber Info -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-block local-forms">
                            <x-input-label for="subscriber_id" :value="__('ID del Titular')" required="true"/>
                            <x-text-input wire:model.defer="subscriber_id" class="block mt-1 w-full" type="text" name="subscriber_id" placeholder="Ej: 8-123-456"/>
                            <x-input-error :messages="$errors->get('subscriber_id')"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-block local-forms">
                            <x-input-label for="subscriber_name" :value="__('Nombre del Titular')" required="true"/>
                            <x-text-input wire:model.defer="subscriber_name" class="block mt-1 w-full" type="text" name="subscriber_name" placeholder="Nombre completo del titular"/>
                            <x-input-error :messages="$errors->get('subscriber_name')"/>
                        </div>
                    </div>
                </div>

                <!-- Row 4: Relationship and Dates -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-block local-forms">
                            <x-input-label for="relationship_to_subscriber" :value="__('Relación')" required="true"/>
                            <select wire:model.defer="relationship_to_subscriber" class="form-control" name="relationship_to_subscriber">
                                @foreach($this->getRelationshipOptions() as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('relationship_to_subscriber')"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-block local-forms">
                            <x-input-label for="effective_date" :value="__('Fecha de Inicio')" required="true"/>
                            <x-text-input wire:model.defer="effective_date" class="block mt-1 w-full" type="date" name="effective_date"/>
                            <x-input-error :messages="$errors->get('effective_date')"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-block local-forms">
                            <x-input-label for="expiration_date" :value="__('Fecha de Vencimiento')"/>
                            <x-text-input wire:model.defer="expiration_date" class="block mt-1 w-full" type="date" name="expiration_date"/>
                            <x-input-error :messages="$errors->get('expiration_date')"/>
                        </div>
                    </div>
                </div>

                <!-- Row 5: Coverage Details -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="input-block local-forms">
                            <x-input-label for="coverage_percentage" :value="__('Cobertura %')"/>
                            <x-text-input wire:model.defer="coverage_percentage" class="block mt-1 w-full" type="number" name="coverage_percentage" min="0" max="100" placeholder="80"/>
                            <x-input-error :messages="$errors->get('coverage_percentage')"/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-block local-forms">
                            <x-input-label for="copay_amount" :value="__('Copago')"/>
                            <x-text-input wire:model.defer="copay_amount" class="block mt-1 w-full" type="number" name="copay_amount" min="0" step="0.01" placeholder="25.00"/>
                            <x-input-error :messages="$errors->get('copay_amount')"/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-block local-forms">
                            <x-input-label for="deductible_amount" :value="__('Deducible')"/>
                            <x-text-input wire:model.defer="deductible_amount" class="block mt-1 w-full" type="number" name="deductible_amount" min="0" step="0.01" placeholder="500.00"/>
                            <x-input-error :messages="$errors->get('deductible_amount')"/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-block local-forms">
                            <x-input-label for="out_of_pocket_max" :value="__('Máx. de Bolsillo')"/>
                            <x-text-input wire:model.defer="out_of_pocket_max" class="block mt-1 w-full" type="number" name="out_of_pocket_max" min="0" step="0.01" placeholder="2000.00"/>
                            <x-input-error :messages="$errors->get('out_of_pocket_max')"/>
                        </div>
                    </div>
                </div>

                <!-- Row 6: Status and Notes -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="input-block local-forms">
                            <div class="form-check">
                                <input wire:model.defer="is_active" class="form-check-input" type="checkbox" id="is_active">
                                <label class="form-check-label" for="is_active">
                                    Seguro Activo
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="input-block local-forms">
                            <x-input-label for="notes" :value="__('Notas')"/>
                            <x-textarea-input wire:model.defer="notes" class="block mt-1 w-full" name="notes" rows="2" placeholder="Notas adicionales sobre el seguro..."/>
                            <x-input-error :messages="$errors->get('notes')"/>
                        </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="margin-top: 20px; display: flex; gap: 15px;">
                <button wire:click="save" class="btn btn-primary" style="flex: 1;">
                    <i class="fas fa-save"></i> {{ __('Agregar Seguro') }}
                </button>
                <button type="button" wire:click="closeModal()" class="btn btn-secondary">
                    <i class="fas fa-times"></i> {{ __('Cerrar') }}
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            // Listen for success/error messages
            Livewire.on('showToastr', (event) => {
                toastr[event.type](event.message, '', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: 5000,
                });
            });

            // Close modal on successful insurance addition
            Livewire.on('insurance-added', () => {
                toastr.success('Seguro agregado exitosamente', '', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: 5000,
                });
            });
        });
    </script>
</div>
