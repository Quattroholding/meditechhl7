<div>
    <div class="row">
        <!-- Left Column - Forms -->
        <div class="col-sm-5">
            <!-- CPT-based Service Form -->
            <div class="card">
                <div class="card-body">
                    <div class="col-12">
                        <div class="form-heading">
                            <h4>{{ __('Agregar Servicio desde CPT') }}</h4>
                        </div>
                    </div>
                    <form wire:submit="saveCptService">
                        @csrf
                        <!-- CPT Search -->
                        <div class="col-12 col-md-6 col-xl-12">
                            <div class="input-block local-forms">
                                <x-input-label for="query" :value="__('Buscar Procedimiento CPT')" required="true"/>
                                <input type="text" wire:model.live="query" class="form-control" placeholder="Buscar Procedimiento CPT">
                                @if(!empty($results))
                                    <ul class="absolute bg-white border w-full mt-1 rounded shadow-lg max-h-40 overflow-y-auto" style="z-index: 1000">
                                        @foreach($results as $result)
                                            <li class="p-2 hover:bg-gray-200 cursor-pointer text-sm" wire:click="selectOption({{ json_encode($result) }})">
                                                <strong>{{ $result['code'] ?? '' }}</strong> - {{ $result['name'] ?? '' }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                                @error('cpt_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Price -->
                            <div class="col-12 col-md-6">
                                <div class="input-block local-forms">
                                    <x-input-label for="cpt_price" :value="__('Precio Base')" required="true"/>
                                    <x-text-input wire:model="cpt_price" class="block mt-1 w-full" type="number" step="0.01" name="cpt_price"/>
                                    @error('cpt_price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <!-- Specialty -->
                            {{--}}<div class="col-12 col-md-6">
                                <div class="input-block local-forms">
                                    <x-input-label for="cpt_specialty" :value="__('Especialidad')"/>
                                    <x-text-input wire:model="cpt_specialty" class="block mt-1 w-full" type="text" name="cpt_specialty"/>
                                </div>
                            </div>{{--}}

                            <!-- Complexity -->
                            <div class="col-12 col-md-6">
                                <div class="input-block local-forms">
                                    <x-input-label for="cpt_complexity" :value="__('Complejidad')" required="true"/>
                                    <select wire:model="cpt_complexity" class="form-control">
                                        @foreach($this->getComplexityTypes() as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        {{--}}
                        <div class="row">
                            <!-- Copay -->
                            <div class="col-12 col-md-6">
                                <div class="input-block local-forms">
                                    <x-input-label for="cpt_patient_copay" :value="__('Copago Paciente')"/>
                                    <x-text-input wire:model="cpt_patient_copay" class="block mt-1 w-full" type="number" step="0.01" name="cpt_patient_copay"/>
                                </div>
                            </div>
                            <!-- Duration -->
                            <div class="col-12 col-md-6">
                                <div class="input-block local-forms">
                                    <x-input-label for="cpt_duration" :value="__('Duración (minutos)')"/>
                                    <x-text-input wire:model="cpt_duration" class="block mt-1 w-full" type="number" name="cpt_duration"/>
                                </div>
                            </div>
                        </div>
                        {{--}}

                        <div class="flex items-center justify-end mt-4">
                            <div class="doctor-submit text-end">
                                <button type="submit" class="btn btn-primary submit-form me-2">{{ __('button.register') }}</button>
                                <button type="button" wire:click="resetCptForm" class="btn btn-secondary">{{ __('Limpiar') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Custom Service Form -->
            <div class="card">
                <div class="card-body">
                    <div class="col-12">
                        <div class="form-heading">
                            <h4>{{ __('Crear Servicio Personalizado') }}</h4>
                        </div>
                    </div>
                    <form wire:submit="saveCustomService">
                        @csrf
                        <!-- Service Name -->
                        {{--}}<div class="col-12">
                            <div class="input-block local-forms">
                                <x-input-label for="custom_name" :value="__('Nombre del Servicio')" required="true"/>
                                <x-text-input wire:model="custom_name" class="block mt-1 w-full" type="text" name="custom_name"/>
                                @error('custom_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>{{--}}
                        <div class="col-12">
                            <div class="input-block local-forms">
                                <x-input-label for="custom_name" :value="__('Nombre del Servicio')" required="true"/>
                                <x-text-input
                                    wire:model.live="custom_name"
                                    class="block mt-1 w-full"
                                    type="text"
                                    name="custom_name"
                                    maxlength="500"
                                />
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    @error('custom_name')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @else
                                        <span></span>
                                    @enderror
                                    <small class="{{ strlen($custom_name ?? '') > 500 ? 'text-danger' : (strlen($custom_name ?? '') > 450 ? 'text-warning' : 'text-muted') }}">
                                        {{ strlen($custom_name ?? '') }}/500 caracteres
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <div class="input-block local-forms">
                                <x-input-label for="custom_description" :value="__('Descripción')"/>
                                <textarea wire:model="custom_description" class="form-control" rows="3" name="custom_description"></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Service Type -->
                            <div class="col-12 col-md-6">
                                <div class="input-block local-forms">
                                    <x-input-label for="custom_service_type" :value="__('Tipo de Servicio')" required="true"/>
                                    <select wire:model="custom_service_type" class="form-control">
                                        <option value="">{{ __('Seleccionar tipo') }}</option>
                                        @foreach($this->getServiceTypes() as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @error('custom_service_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="col-12 col-md-6">
                                <div class="input-block local-forms">
                                    <x-input-label for="custom_price" :value="__('Precio Base')" required="true"/>
                                    <x-text-input wire:model="custom_price" class="block mt-1 w-full" type="number" step="0.01" name="custom_price"/>
                                    @error('custom_price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <input type="hidden" wire:model="custom_complexity" value="medium">
                        {{--}}
                        <div class="row">
                            <!-- Specialty -->
                            <div class="col-12 col-md-6">
                                <div class="input-block local-forms">
                                    <x-input-label for="custom_specialty" :value="__('Especialidad')"/>
                                    <x-text-input wire:model="custom_specialty" class="block mt-1 w-full" type="text" name="custom_specialty"/>
                                </div>
                            </div>

                            <!-- Revenue Code -->
                            <div class="col-12 col-md-6">
                                <div class="input-block local-forms">
                                    <x-input-label for="custom_revenue_code" :value="__('Código de Ingresos')"/>
                                    <x-text-input wire:model="custom_revenue_code" class="block mt-1 w-full" type="text" name="custom_revenue_code"/>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Complexity -->
                            <div class="col-12 col-md-6">
                                <div class="input-block local-forms">
                                    <x-input-label for="custom_complexity" :value="__('Complejidad')" required="true"/>
                                    <select wire:model="custom_complexity" class="form-control">
                                        @foreach($this->getComplexityTypes() as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Duration -->
                            <div class="col-12 col-md-6">
                                <div class="input-block local-forms">
                                    <x-input-label for="custom_duration" :value="__('Duración (minutos)')"/>
                                    <x-text-input wire:model="custom_duration" class="block mt-1 w-full" type="number" name="custom_duration"/>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Copay -->
                            <div class="col-12 col-md-6">
                                <div class="input-block local-forms">
                                    <x-input-label for="custom_patient_copay" :value="__('Copago Paciente')"/>
                                    <x-text-input wire:model="custom_patient_copay" class="block mt-1 w-full" type="number" step="0.01" name="custom_patient_copay"/>
                                </div>
                            </div>

                            <!-- Checkboxes -->
                            <div class="col-12 col-md-6">
                                <div class="input-block">
                                    <div class="form-check">
                                        <input type="checkbox" wire:model="custom_requires_auth" class="form-check-input" id="custom_requires_auth">
                                        <label class="form-check-label" for="custom_requires_auth">
                                            {{ __('Requiere Autorización') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" wire:model="custom_covered_insurance" class="form-check-input" id="custom_covered_insurance">
                                        <label class="form-check-label" for="custom_covered_insurance">
                                            {{ __('Cubierto por Seguro') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                        {{--}}
                        <div class="flex items-center justify-end mt-4">
                            <div class="doctor-submit text-end">
                                <button type="submit" class="btn btn-primary submit-form me-2">{{ __('button.register') }}</button>
                                <button type="button" wire:click="resetCustomForm" class="btn btn-secondary">{{ __('Limpiar') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column - Services List -->
        <div class="col-sm-7">
            <div class="card">
                <div class="card-body">
                    <div class="col-12">
                        <div class="form-heading d-flex justify-content-between align-items-center">
                            <h4>{{ __('Catálogo de Servicios') }}</h4>
                            <span class="badge badge-info">{{ $this->services->count() }} servicios</span>
                        </div>
                    </div>

                    <!-- Search and filters -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <div class="input-group">
                                <input type="text" wire:model.live="search" class="form-control" placeholder="Buscar por nombre, descripción, CPT o especialidad...">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fa fa-search"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select wire:model.live="perPage" class="form-control">
                                <option value="5">5 por página</option>
                                <option value="10">10 por página</option>
                                <option value="25">25 por página</option>
                                <option value="50">50 por página</option>
                            </select>
                        </div>
                    </div>

                    @if($this->services->count() > 0)
                        <div class="table-responsive">
                            <table class="table border-0 custom-table comman-table mb-0">
                                <thead>
                                    <tr>
                                        <th wire:click="sortBy('name')" style="cursor: pointer;">
                                            {{ __('Nombre') }}
                                            @if($sortField === 'name')
                                                <i class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </th>
                                        <th wire:click="sortBy('service_type')" style="cursor: pointer;">
                                            {{ __('Tipo') }}
                                            @if($sortField === 'service_type')
                                                <i class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </th>
                                        <th wire:click="sortBy('base_price')" style="cursor: pointer;">
                                            {{ __('Precio') }}
                                            @if($sortField === 'base_price')
                                                <i class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </th>
                                        <th wire:click="sortBy('is_active')" style="cursor: pointer;">
                                            {{ __('Estado') }}
                                            @if($sortField === 'is_active')
                                                <i class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </th>
                                        <th>{{ __('Acciones') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($this->services as $service)
                                        <tr>
                                            <td>
                                                @if($editingId === $service->id)
                                                    @if(!empty($editingService['is_cpt_service']))
                                                        <div class="font-weight-bold text-muted" title="{{ $editingService['name'] }}">
                                                            {{ Str::limit($editingService['name'], 30, '...') }}
                                                        </div>
                                                        <small class="text-warning">
                                                            <i class="fa fa-lock"></i> Código CPT no editable
                                                        </small>
                                                    @else
                                                        <input type="text" wire:model="editingService.name" class="form-control form-control-sm">
                                                    @endif
                                                @else
                                                    <div class="font-weight-bold" title="{{ $service->name }}">
                                                        {{ Str::limit($service->name, 30, '...') }}
                                                    </div>
                                                    @if($service->cpt_code)
                                                        <small class="text-muted" title="{{ $service->description }}">CPT: {{ $service->cpt_code }} | {{ Str::limit($service->description, 25, '...') }}</small>
                                                    @endif
                                                    @if($service->specialty)
                                                        <br><small class="text-info" title="{{ $service->specialty }}">{{ Str::limit($service->specialty, 25, '...') }}</small>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ ucfirst(str_replace('_', ' ', $service->service_type)) }}
                                                </span>
                                                {{--}}
                                                @if($service->complexity)
                                                    <br><small class="text-muted">{{ ucfirst($service->complexity) }}</small>
                                                @endif
                                                {{--}}
                                            </td>
                                            <td>
                                                @if($editingId === $service->id)
                                                    <input type="number" step="0.01" wire:model="editingService.base_price" class="form-control form-control-sm">
                                                    @if(!empty($editingService['is_cpt_service']))
                                                        <small class="text-info d-block mt-1">
                                                            Sólo el precio es editable
                                                        </small>
                                                    @endif
                                                @else
                                                    <strong>${{ number_format($service->base_price, 2) }}</strong>
                                                    @if($service->patient_copay > 0)
                                                        <br><small class="text-warning">Copago: ${{ number_format($service->patient_copay, 2) }}</small>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="badge {{ $service->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $service->is_active ? 'Activo' : 'Inactivo' }}
                                                    </span>
                                                    {{--}}
                                                    @if($service->requires_authorization)
                                                        <small class="text-warning mt-1">
                                                            <i class="fa fa-lock"></i> Requiere autorización
                                                        </small>
                                                    @endif
                                                    @if($service->covered_by_insurance)
                                                        <small class="text-success mt-1">
                                                            <i class="fa fa-shield"></i> Cubierto por seguro
                                                        </small>
                                                    @endif
                                                    {{--}}
                                                </div>
                                            </td>
                                            <td>
                                                @if($editingId === $service->id)
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button wire:click="updateService" class="btn btn-success btn-sm" title="Guardar">
                                                            <i class="fa fa-check"></i>
                                                        </button>
                                                        <button wire:click="cancelEdit" class="btn btn-secondary btn-sm" title="Cancelar">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    </div>
                                                @else
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button wire:click="editService({{ $service->id }})" class="btn btn-info btn-sm" title="Editar">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                        <button wire:click="toggleActive({{ $service->id }})"
                                                                class="btn {{ $service->is_active ? 'btn-warning' : 'btn-success' }} btn-sm"
                                                                title="{{ $service->is_active ? 'Desactivar' : 'Activar' }}">
                                                            <i class="fa {{ $service->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                                        </button>
                                                        <button wire:click="deleteService({{ $service->id }})"
                                                                class="btn btn-danger btn-sm"
                                                                title="Eliminar"
                                                                 wire:confirm="¿Está seguro de eliminar este servicio?">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @include('partials.pagination',['data'=>$this->services])
                    @else
                        <div class="text-center py-4">
                            <i class="fa fa-medical-bag fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay servicios en el catálogo aún.</p>
                            <p class="text-sm text-muted">Agregue servicios desde CPT o cree servicios personalizados.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showToastrServiceCatalog', (event) => {
                toastr[event.type](event.message, '', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: 5000,
                });
            });
        });
    </script>
</div>
