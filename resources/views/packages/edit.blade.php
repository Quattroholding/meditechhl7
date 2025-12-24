<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Paquetes
                @endslot
                @slot('li_1')
                    Editar Paquete
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>Editar Paquete</h4>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('package.update', $data->id) }}">
                            @csrf
                            <div class="row">
                                <div class="col-12 col-md-6 col-xl-6">
                                    <!-- NAME -->
                                    <div class="input-block local-forms">
                                        <x-input-label for="name" :value="__('Nombre')" required/>
                                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $data->name)" autofocus/>
                                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-6">
                                    <!-- SLUG -->
                                    <div class="input-block local-forms">
                                        <x-input-label for="slug" :value="__('Slug')" />
                                        <x-text-input id="slug" class="block mt-1 w-full" type="text" name="slug" :value="old('slug', $data->slug)" placeholder="Se genera automáticamente"/>
                                        <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                                        <small class="text-muted">Deja vacío para generar automáticamente</small>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 col-xl-6">
                                    <!-- MAX DOCTORS INCLUDED -->
                                    <div class="input-block local-forms">
                                        <x-input-label for="max_users" :value="__('Usuarios Incluidos')" required/>
                                        <x-text-input id="max_users" class="block mt-1 w-full" type="number" name="max_users" :value="old('max_users',$data->max_users)" min="1"/>
                                        <x-input-error :messages="$errors->get('max_users')" class="mt-2" />
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-6">
                                    <!-- MAX DOCTORS INCLUDED -->
                                    <div class="input-block local-forms">
                                        <x-input-label for="max_doctors_included" :value="__('Doctores Incluidos')" required/>
                                        <x-text-input id="max_doctors_included" class="block mt-1 w-full" type="number" name="max_doctors_included" :value="old('max_doctors_included', $data->max_doctors_included)" min="1"/>
                                        <x-input-error :messages="$errors->get('max_doctors_included')" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                            <!-- Pricing Section -->
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="mt-3 mb-3">Información de Precios</h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 col-xl-6">
                                    <!-- BASE PRICE -->
                                    <div class="input-block local-forms">
                                        <x-input-label for="base_price" :value="__('Precio Base')" required/>
                                        <x-text-input id="base_price" class="block mt-1 w-full" type="number" name="base_price" :value="old('base_price', $data->base_price)" min="0" step="0.01"/>
                                        <x-input-error :messages="$errors->get('base_price')" class="mt-2" />
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-6">
                                    <!-- PRICE PER EXTRA DOCTOR -->
                                    <div class="input-block local-forms">
                                        <x-input-label for="price_per_extra_doctor" :value="__('Precio por Doctor Adicional')" />
                                        <x-text-input id="price_per_extra_doctor" class="block mt-1 w-full" type="number" name="price_per_extra_doctor" :value="old('price_per_extra_doctor', $data->price_per_extra_doctor)" min="0" step="0.01"/>
                                        <x-input-error :messages="$errors->get('price_per_extra_doctor')" class="mt-2" />
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 col-xl-6">
                                    <!-- BILLING PERIOD -->
                                    <div class="input-block local-forms">
                                        <x-input-label for="billing_period" :value="__('Período de Facturación')" required/>
                                        <select id="billing_period" name="billing_period" class="form-control">
                                            <option value="monthly" {{ old('billing_period', $data->billing_period?->value ?? 'monthly') == 'monthly' ? 'selected' : '' }}>Mensual</option>
                                            <option value="quarterly" {{ old('billing_period', $data->billing_period?->value ?? 'monthly') == 'quarterly' ? 'selected' : '' }}>Trimestral</option>
                                            <option value="yearly" {{ old('billing_period', $data->billing_period?->value ?? 'monthly') == 'yearly' ? 'selected' : '' }}>Anual</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('billing_period')" class="mt-2" />
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <!-- BILLING PERIOD DAYS -->
                                    <div class="input-block local-forms">
                                        <x-input-label for="billing_period_days" :value="__('Días del Período')" />
                                        <x-text-input id="billing_period_days" class="block mt-1 w-full" type="number" name="billing_period_days" :value="old('billing_period_days', $data->billing_period_days)" min="1"/>
                                        <x-input-error :messages="$errors->get('billing_period_days')" class="mt-2" />
                                        <small class="text-muted">Se ajusta automáticamente según el período seleccionado</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Description Section -->
                            <div class="row">
                                <div class="col-12">
                                    <!-- DESCRIPTION -->
                                    <div class="input-block local-forms">
                                        <x-input-label for="description" :value="__('Descripción')" required/>
                                        <textarea id="description" class="form-control" name="description" rows="4">{{ old('description', $data->description) }}</textarea>
                                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Features Section -->
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="mt-3 mb-3">Características</h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <!-- FEATURES -->
                                    <div class="input-block local-forms">
                                        <x-input-label for="features" :value="__('Características (una por línea)')" />
                                        <textarea id="features" class="form-control" name="features" rows="6" placeholder="Administración y Agendamiento de citas&#10;Gestión de Pacientes&#10;Historial Clínico Digital">{{ old('features', is_array($data->features) ? implode("\n", $data->features) : '') }}</textarea>
                                        <x-input-error :messages="$errors->get('features')" class="mt-2" />
                                        <small class="text-muted">Escribe cada característica en una línea separada</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Settings Section -->
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="mt-3 mb-3">Configuración</h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <!-- IS ACTIVE -->
                                    <div class="input-block local-forms">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $data->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active" style="margin-top:10px;margin-left:10px;">
                                                Paquete Activo
                                            </label>
                                        </div><br/>
                                        <div class="input-block local-forms">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="agent_available" name="agent_available" value="1" {{ old('agent_available', $data->agent_available) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="agent_available" style="margin-top:10px;margin-left:10px;">
                                                Agente SAMI Disponible
                                            </label>
                                        </div>
                                        <small class="text-muted">Incluye el agente automatizado de citas</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <!-- AGENT AVAILABLE -->
                                    <div class="input-block local-forms">
                                        <x-input-label for="appointments_limit" :value="__('Límite de citas')" />
                                        <x-text-input id="appointments_limit" class="block mt-1 w-full" type="number" name="appointments_limit" :value="old('appointments_limit', $data->appointments_limit)"/>
                                        <x-input-error :messages="$errors->get('billing_period_days')" class="mt-2" />
                                        <small class="text-muted">Vacio significa citas infinitas</small>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <div class="doctor-submit text-end">
                                    <button type="submit" class="btn btn-primary submit-form me-2">     {{ __('button.edit') }} </button>
                                    <a class="btn btn-secondary cancel-form" href="{{ route('package.index') }}">  {{ __('button.cancel') }}</a>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const billingPeriodSelect = document.getElementById('billing_period');
            const billingPeriodDaysInput = document.getElementById('billing_period_days');
            const nameInput = document.getElementById('name');
            const slugInput = document.getElementById('slug');

            // Update billing period days based on selected period
            if (billingPeriodSelect && billingPeriodDaysInput) {
                billingPeriodSelect.addEventListener('change', function() {
                    const periodDays = {
                        'monthly': 30,
                        'quarterly': 90,
                        'yearly': 365
                    };
                    billingPeriodDaysInput.value = periodDays[this.value] || 30;
                });
            }

            // Auto-generate slug from name
            if (nameInput && slugInput) {
                nameInput.addEventListener('input', function() {
                    if (!slugInput.dataset.manuallyEdited) {
                        const slug = this.value
                            .toLowerCase()
                            .replace(/[^a-z0-9]+/g, '-')
                            .replace(/^-+|-+$/g, '');
                        slugInput.value = slug;
                    }
                });

                slugInput.addEventListener('input', function() {
                    if (this.value !== '') {
                        this.dataset.manuallyEdited = 'true';
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
