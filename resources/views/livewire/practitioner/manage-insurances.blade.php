<div>
    <!-- Botón para abrir el modal -->
    @if($showBigButton)
    <button wire:click="openModal()" class="btn-head btn-head-light">
        🛡️ {{__('Gestionar Seguros')}}
    </button>
    @endif
    @if($showSmallButton)
        <button wire:click="openModal()" class="btn btn-secondary btn-sm"> <i class="fa-solid fa fa-shield"></i> </button>
    @endif

    @if($showInsuranceModal)
    <!-- Modal -->
    <div class="modal-overlay" wire:click="closeModal" style="z-index: 10000;">
        <div class="modal-content" wire:click.stop style="max-width: 900px;">
            <div class="modal-header">
                <h2 class="modal-title" style="color: #000;">
                    {{__('Gestionar Seguros Médicos')}} : {{ $practitioner->name }}
                </h2>
                <button wire:click="closeModal()" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">

                <!-- Tabla de seguros existentes -->
                @if($existingInsurances->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Seguros Configurados</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Compañía de Seguros</th>
                                        <th>¿Acepta?</th>
                                        <th>Cobertura %</th>
                                        <th>Copago</th>
                                        <th>Notas</th>
                                        <th>Fecha Configuración</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($existingInsurances as $insurance)
                                    <tr>
                                        <td>{{ $insurance->name }}</td>
                                        <td>
                                            @if($insurance->pivot->accepts)
                                                <span class="badge bg-success">Sí Acepta</span>
                                            @else
                                                <span class="badge bg-danger">No Acepta</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($insurance->pivot->custom_coverage_percentage)
                                                {{ number_format($insurance->pivot->custom_coverage_percentage, 2) }}%
                                            @else
                                                <span class="text-muted">Defecto ({{ $insurance->default_coverage_percentage ?? 'N/A' }}%)</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($insurance->pivot->custom_copay_amount)
                                                ${{ number_format($insurance->pivot->custom_copay_amount, 2) }}
                                            @else
                                                <span class="text-muted">Defecto (${{ $insurance->default_copay_amount ?? 'N/A' }})</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($insurance->pivot->notes)
                                                <span title="{{ $insurance->pivot->notes }}">
                                                    {{ Str::limit($insurance->pivot->notes, 30) }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($insurance->pivot->created_at)->format('d/m/Y') }}
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button wire:click="editInsurance({{ $insurance->id }})"
                                                        class="btn btn-sm btn-warning"
                                                        title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button wire:click="deleteInsurance({{ $insurance->id }})"
                                                        class="btn btn-sm btn-danger"
                                                        title="Eliminar"
                                                        onclick="return confirm('¿Está seguro de eliminar esta relación con el seguro?')">
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

                <!-- Formulario para agregar/editar relación con seguro -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-plus"></i> {{ $insurance_company_id ? 'Editar' : 'Agregar' }} Relación con Seguro
                        </h5>
                    </div>
                    <div class="card-body py-4">

                        <!-- Row 1: Insurance Company and Acceptance -->
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
                                    <x-input-label for="accepts" :value="__('¿Acepta este seguro?')" required="true"/>
                                    <select wire:model="accepts" class="form-control" name="accepts">
                                        @foreach($this->getAcceptanceOptions() as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('accepts')"/>
                                </div>
                            </div>
                        </div>

                        <!-- Row 2: Custom Coverage and Copay (only show if accepts) -->
                        <div class="row" x-data="{ accepts: @entangle('accepts') }" x-show="accepts == 1">
                            <div class="col-md-6">
                                <div class="input-block local-forms">
                                    <x-input-label for="custom_coverage_percentage" :value="__('Cobertura Personalizada %')"/>
                                    <x-text-input wire:model="custom_coverage_percentage"
                                                  class="block mt-1 w-full"
                                                  type="number"
                                                  name="custom_coverage_percentage"
                                                  min="0"
                                                  max="100"
                                                  step="0.01"
                                                  placeholder="Dejar vacío para usar valor por defecto"/>
                                    <small class="form-text text-muted">Si está vacío, se usará el valor por defecto de la compañía</small>
                                    <x-input-error :messages="$errors->get('custom_coverage_percentage')"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-block local-forms">
                                    <x-input-label for="custom_copay_amount" :value="__('Copago Personalizado')"/>
                                    <x-text-input wire:model="custom_copay_amount"
                                                  class="block mt-1 w-full"
                                                  type="number"
                                                  name="custom_copay_amount"
                                                  min="0"
                                                  step="0.01"
                                                  placeholder="Dejar vacío para usar valor por defecto"/>
                                    <small class="form-text text-muted">Si está vacío, se usará el valor por defecto de la compañía</small>
                                    <x-input-error :messages="$errors->get('custom_copay_amount')"/>
                                </div>
                            </div>
                        </div>

                        <!-- Row 3: Notes -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-block local-forms">
                                    <x-input-label for="notes" :value="__('Notas adicionales')"/>
                                    <x-textarea-input wire:model="notes"
                                                      class="block mt-1 w-full"
                                                      name="notes"
                                                      rows="3"
                                                      placeholder="Notas sobre la relación con este seguro (ej: condiciones especiales, restricciones, etc.)"/>
                                    <x-input-error :messages="$errors->get('notes')"/>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal-footer" style="margin-top: 20px; display: flex; gap: 15px;">
                <button wire:click="save" class="btn btn-primary" style="flex: 1;">
                    <i class="fas fa-save"></i> {{ $insurance_company_id ? __('Actualizar Seguro') : __('Agregar Seguro') }}
                </button>
                <button type="button" wire:click="closeModal()" class="btn btn-secondary">
                    <i class="fas fa-times"></i> {{ __('Cerrar') }}
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            // Listen for success/error messages
            Livewire.on('showToastrMI{{$practitioner_id}}', (event) => {
                toastr[event.type](event.message, '', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: 5000,
                });
            });

            // Close modal on successful insurance relationship save
            Livewire.on('insurance-relationship-saved{{$practitioner_id}}', () => {
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
