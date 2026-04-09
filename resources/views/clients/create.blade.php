<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('client.title') }}
                @endslot
                @slot('li_1')
                    {{ __('generic.create') }} {{ __('client.titles') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>{{ __('generic.create') }} {{ __('client.title') }}</h4>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('client.store') }}" enctype="multipart/form-data" id="form">
                                @csrf

                                <!-- Información Básica -->
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="mb-3 text-primary">Información Básica del Cliente</h5>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="input-block local-forms">
                                            <x-input-label for="name" :value="__('Nombre Corto')" required/>
                                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" autofocus/>
                                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-8">
                                        <div class="input-block local-forms">
                                            <x-input-label for="long_name" :value="__('Nombre Completo')" required/>
                                            <x-text-input id="long_name" class="block mt-1 w-full" type="text" name="long_name" :value="old('long_name')"/>
                                            <x-input-error :messages="$errors->get('long_name')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6 col-xl-2">
                                        <div class="input-block local-forms">
                                            <x-input-label for="dv" :value="__('DV')" required/>
                                            <x-text-input id="dv" class="block mt-1 w-full" type="number" name="dv" :value="old('dv')" maxlength="2" min="1"/>
                                            <x-input-error :messages="$errors->get('dv')" class="mt-2" />
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="input-block local-forms">
                                            <x-input-label for="ruc" :value="__('Ruc')" required/>
                                            <x-text-input id="ruc" class="block mt-1 w-full" type="number" name="ruc" :value="old('ruc')"/>
                                            <x-input-error :messages="$errors->get('ruc')" class="mt-2" />
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="input-block local-forms">
                                            <x-input-label for="phone" :value="__('Teléfono')" required/>
                                            <x-phone-input
                                                name="phone"
                                                id="phone"
                                                :value="old('phone')"
                                                :error="$errors->get('phone')"
                                                required
                                                class="block mt-1 w-full"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="input-block local-forms">
                                            <x-input-label for="email" :value="__('Email')" required/>
                                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"/>
                                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="input-block local-forms">
                                            <x-input-label for="update_password_password" :value="__('Contraseña')" required/>
                                            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                                            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="input-block local-forms">
                                            <x-input-label for="update_password_password_confirmation" :value="__('user.confirm_password')" required/>
                                            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                                            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="form-group local-top-form">
                                            <label class="local-top">Logo</label>
                                            <div class="settings-btn upload-files-avator">
                                                <input type="file" accept="image/*" name="logo" id="file" onchange="loadFile(event)" class="hide-input">
                                                <label for="file" class="upload">{{__('Escoger archivo')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <!-- Plan y Suscripción -->
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="mb-3 text-primary">Plan de Suscripción</h5>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="input-block local-forms">
                                            <x-input-label for="package_id" :value="__('Paquete')" required/>
                                            <select required name="package_id" id="package_id" class="form-control block w-full" onchange="updatePackageInfo()">
                                                <option value="">Seleccione un paquete</option>
                                                @foreach(\App\Models\Package::get() as $package)
                                                    <option value="{{ $package->id }}"
                                                        data-price="{{ $package->base_price }}"
                                                        data-doctors="{{ $package->max_doctors_included }}"
                                                        data-users="{{ $package->max_users }}"
                                                        data-extra-price="{{ $package->price_per_extra_doctor }}"
                                                        data-period="{{ $package->billing_period }}"
                                                        data-period-days="{{ $package->billing_period_days }}"
                                                        {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                                        {{ $package->name }} - ${{ number_format($package->base_price, 2) }} / {{ $package->billing_period }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <x-input-error :messages="$errors->get('package_id')" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-3">
                                        <div class="input-block local-forms">
                                            <x-input-label for="extra_doctors" value="Doctores Adicionales"/>
                                            <x-text-input id="extra_doctors" class="block mt-1 w-full" type="number" name="extra_doctors" :value="old('extra_doctors', 0)" min="0" max="100" onchange="updatePackageInfo()"/>
                                            <small class="text-muted">Costo por doctor adicional: $<span id="extra_doctor_price">0.00</span></small>
                                            <x-input-error :messages="$errors->get('extra_doctors')" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-3">
                                        <div class="input-block local-forms">
                                            <x-input-label for="billing_day" value="Día de Facturación"/>
                                            <x-text-input id="billing_day" class="block mt-1 w-full" type="number" name="billing_day" :value="old('billing_day', date('d'))" min="1" max="28"/>
                                            <small class="text-muted">Día del mes para generar facturas (1-28)</small>
                                            <x-input-error :messages="$errors->get('billing_day')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Detalles del Paquete Seleccionado -->
                                <div class="row" id="package-details" style="display:none;">
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <h6 class="mb-2">Resumen del Plan Seleccionado:</h6>
                                            <ul class="mb-0">
                                                <li>Precio Base: $<span id="base_price_display">0.00</span></li>
                                                <li>Doctores Incluidos: <span id="doctors_included">0</span></li>
                                                <li>Doctores Adicionales: <span id="extra_doctors_display">0</span> ($<span id="extra_cost_display">0.00</span>)</li>
                                                <li>Período de Facturación: <span id="billing_period_display">-</span></li>
                                                <li class="mt-2 text-dark"><strong>Total Mensual: $<span id="total_price_display">0.00</span></strong></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <!-- Datos del Doctor (solo si max_doctors=1) -->
                                <div class="row" id="practitioner-section" style="display:none;">
                                    <div class="col-12">
                                        <h5 class="mb-3 text-primary">Datos del Doctor <small class="text-muted">(El administrador será el único doctor)</small></h5>
                                    </div>
                                </div>

                                <div id="practitioner-fields" style="display:none;">
                                    <div class="row">
                                        <div class="col-12 col-md-6 col-xl-6">
                                            <div class="input-block local-forms">
                                                <x-input-label for="practitioner_identifier_type" value="Tipo de Documento"/>
                                                <x-select-input id="practitioner_identifier_type" name="identifier_type" :options="\App\Models\Lista::documentType()" :selected="[old('identifier_type')]" class="block mt-1 w-full"/>
                                                <x-input-error :messages="$errors->get('identifier_type')" class="mt-2" />
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6 col-xl-6">
                                            <div class="input-block local-forms">
                                                <x-input-label for="practitioner_identifier" value="Número de Documento"/>
                                                <x-text-input id="practitioner_identifier" class="block mt-1 w-full" type="text" name="identifier" :value="old('identifier')"/>
                                                <x-input-error :messages="$errors->get('identifier')" class="mt-2" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-md-6 col-xl-6">
                                            <div class="input-block local-forms">
                                                <x-input-label for="practitioner_given_name" value="Nombres"/>
                                                <x-text-input id="practitioner_given_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')"/>
                                                <small class="text-muted">Nombres del doctor</small>
                                                <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6 col-xl-6">
                                            <div class="input-block local-forms">
                                                <x-input-label for="practitioner_family_name" value="Apellidos"/>
                                                <x-text-input id="practitioner_family_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')"/>
                                                <small class="text-muted">Apellidos del doctor</small>
                                                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-md-6 col-xl-6">
                                            <div class="input-block local-forms">
                                                <x-input-label for="practitioner_gender" value="Género"/>
                                                <x-select-input name="gender" :options="\App\Models\Lista::gender()" :selected="[old('gender')]" class="block w-full"/>
                                                <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                                            </div>
                                            <!-- SPECIALTY -->
                                            <div class="input-block  local-forms">
                                                <x-input-label for="medical_speciality" :value="__('doctor.qualifications')"/>
                                                <x-select-input name="medical_speciality[]" :options="\App\Models\MedicalSpeciality::pluck('name','id')->toArray()"
                                                                class="block  w-full" multiple aria-label="multiple select example" :selected="[old('medical_speciality')]"/>
                                                <x-input-error class="mt-2" :messages="$errors->get('medical_speciality')" /><p>&nbsp;</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-6 col-xl-12">
                                            <div class="alert alert-info mb-0">
                                                <i class="fas fa-info-circle"></i>
                                                El doctor usará el mismo email y teléfono del cliente.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <!-- Beneficios y Descuentos -->
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="mb-3 text-primary">Beneficios y Descuentos (Opcional)</h5>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="input-block local-forms">
                                            <x-input-label for="trial_days" value="Días de Prueba Gratis"/>
                                            <x-text-input id="trial_days" class="block mt-1 w-full" type="number" name="trial_days" :value="old('trial_days', 0)" min="0" max="365" onchange="updatePaymentInfo()"/>
                                            <small class="text-muted">0 = Sin período de prueba. El cliente pagará desde el inicio.</small>
                                            <x-input-error :messages="$errors->get('trial_days')" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="input-block local-forms">
                                            <x-input-label for="free_months" value="Meses Gratis"/>
                                            <x-text-input id="free_months" class="block mt-1 w-full" type="number" name="free_months" :value="old('free_months', 0)" min="0" max="12" onchange="updatePaymentInfo()"/>
                                            <small class="text-muted">Meses de suscripción gratuita adicional</small>
                                            <x-input-error :messages="$errors->get('free_months')" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="input-block local-forms">
                                            <x-input-label for="referral_code" value="Código de Referido"/>
                                            <x-text-input id="referral_code" class="block mt-1 w-full" type="text" name="referral_code" :value="old('referral_code')" placeholder="REF-XXXXXXXX"/>
                                            <small class="text-muted">Si tiene un código de referido, ingréselo aquí</small>
                                            <x-input-error :messages="$errors->get('referral_code')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Descuentos Personalizados (Opcional) -->
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6 class="text-muted">Descuentos Personalizados (Opcional)</h6>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6 col-xl-3">
                                        <div class="input-block local-forms">
                                            <x-input-label for="custom_discount_type" value="Tipo de Descuento"/>
                                            <select name="custom_discount_type" id="custom_discount_type" class="form-control" onchange="updateDiscountInfo()">
                                                <option value="">Sin descuento personalizado</option>
                                                <option value="percentage" {{ old('custom_discount_type') == 'percentage' ? 'selected' : '' }}>Porcentaje (%)</option>
                                                <option value="fixed_amount" {{ old('custom_discount_type') == 'fixed_amount' ? 'selected' : '' }}>Monto Fijo ($)</option>
                                            </select>
                                            <x-input-error :messages="$errors->get('custom_discount_type')" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-3">
                                        <div class="input-block local-forms">
                                            <x-input-label for="custom_discount_value" value="Valor del Descuento"/>
                                            <x-text-input id="custom_discount_value" class="block mt-1 w-full" type="number" name="custom_discount_value" :value="old('custom_discount_value', 0)" min="0" step="0.01" onchange="updateDiscountInfo()"/>
                                            <small class="text-muted" id="discount_hint">Ingrese el valor del descuento</small>
                                            <x-input-error :messages="$errors->get('custom_discount_value')" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-3">
                                        <div class="input-block local-forms">
                                            <x-input-label for="custom_discount_invoices" value="Aplicar a N Facturas"/>
                                            <x-text-input id="custom_discount_invoices" class="block mt-1 w-full" type="number" name="custom_discount_invoices" :value="old('custom_discount_invoices', 1)" min="1" max="24"/>
                                            <small class="text-muted">Número de facturas a las que aplicar el descuento</small>
                                            <x-input-error :messages="$errors->get('custom_discount_invoices')" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-3">
                                        <div class="input-block local-forms">
                                            <x-input-label for="custom_discount_reason" value="Razón del Descuento"/>
                                            <x-text-input id="custom_discount_reason" class="block mt-1 w-full" type="text" name="custom_discount_reason" :value="old('custom_discount_reason')" placeholder="Ej: Promoción de lanzamiento"/>
                                            <small class="text-muted">Motivo del descuento (opcional)</small>
                                            <x-input-error :messages="$errors->get('custom_discount_reason')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Nota informativa -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="alert alert-warning">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Importante:</strong> Si no se configura período de prueba o meses gratis, se generará automáticamente la primera factura que el cliente deberá pagar para activar su suscripción (pago por adelantado).
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end mt-4">
                                    <div class="doctor-submit text-end">
                                        <button type="submit" class="btn btn-primary submit-form me-2">{{ __('button.register') }}</button>
                                        <a class="btn btn-secondary cancel-form" href="{{ route('client.index') }}">{{ __('button.cancel') }}</a>
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
        function updatePackageInfo() {
            const packageSelect = document.getElementById('package_id');
            const selectedOption = packageSelect.options[packageSelect.selectedIndex];
            const extraDoctors = parseInt(document.getElementById('extra_doctors').value) || 0;

            if (!selectedOption.value) {
                document.getElementById('package-details').style.display = 'none';
                document.getElementById('practitioner-section').style.display = 'none';
                document.getElementById('practitioner-fields').style.display = 'none';
                return;
            }

            const basePrice = parseFloat(selectedOption.dataset.price);
            const doctorsIncluded = parseInt(selectedOption.dataset.doctors);
            const usersIncluded = parseInt(selectedOption.dataset.users);
            const extraDoctorPrice = parseFloat(selectedOption.dataset.extraPrice);
            const billingPeriod = selectedOption.dataset.period;

            const extraCost = extraDoctors * extraDoctorPrice;
            const totalPrice = basePrice + extraCost;

            document.getElementById('base_price_display').textContent = basePrice.toFixed(2);
            document.getElementById('doctors_included').textContent = doctorsIncluded;
            document.getElementById('extra_doctors_display').textContent = extraDoctors;
            document.getElementById('extra_doctor_price').textContent = extraDoctorPrice.toFixed(2);
            document.getElementById('extra_cost_display').textContent = extraCost.toFixed(2);
            document.getElementById('billing_period_display').textContent = billingPeriod;
            document.getElementById('total_price_display').textContent = totalPrice.toFixed(2);

            document.getElementById('package-details').style.display = 'block';

            // Mostrar/ocultar campos de practitioner si max_doctors=1
            console.log("MAx User :"+usersIncluded);
            if (usersIncluded === 1) {
                document.getElementById('practitioner-section').style.display = 'block';
                document.getElementById('practitioner-fields').style.display = 'block';
                // Hacer los campos requeridos
                document.getElementById('practitioner_identifier_type').required = true;
                document.getElementById('practitioner_identifier').required = true;
                document.getElementById('practitioner_given_name').required = true;
                document.getElementById('practitioner_family_name').required = true;
                document.getElementById('practitioner_gender').required = true;
            } else {
                document.getElementById('practitioner-section').style.display = 'none';
                document.getElementById('practitioner-fields').style.display = 'none';
                // Remover requerimiento de los campos
                document.getElementById('practitioner_identifier_type').required = false;
                document.getElementById('practitioner_identifier').required = false;
                document.getElementById('practitioner_given_name').required = false;
                document.getElementById('practitioner_family_name').required = false;
                document.getElementById('practitioner_gender').required = false;
            }

            updatePaymentInfo();
        }

        function updatePaymentInfo() {
            const trialDays = parseInt(document.getElementById('trial_days').value) || 0;
            const freeMonths = parseInt(document.getElementById('free_months').value) || 0;

            // Actualizar el mensaje de la alerta
            const alertDiv = document.querySelector('.alert-warning');
            if (alertDiv) {
                if (trialDays > 0 || freeMonths > 0) {
                    let message = '<i class="fas fa-info-circle"></i> <strong>Importante:</strong> ';
                    if (trialDays > 0 && freeMonths > 0) {
                        message += `El cliente tendrá ${trialDays} días de prueba más ${freeMonths} mes(es) gratis. La primera factura se generará después de este período.`;
                    } else if (trialDays > 0) {
                        message += `El cliente tendrá ${trialDays} días de prueba gratis. La primera factura se generará después de este período.`;
                    } else {
                        message += `El cliente tendrá ${freeMonths} mes(es) gratis. La primera factura se generará después de este período.`;
                    }
                    alertDiv.innerHTML = message;
                    alertDiv.className = 'alert alert-info';
                } else {
                    alertDiv.innerHTML = '<i class="fas fa-info-circle"></i> <strong>Importante:</strong> Se generará automáticamente la primera factura que el cliente deberá pagar para activar su suscripción (pago por adelantado).';
                    alertDiv.className = 'alert alert-warning';
                }
            }
        }

        function updateDiscountInfo() {
            const discountType = document.getElementById('custom_discount_type').value;
            const discountValue = parseFloat(document.getElementById('custom_discount_value').value) || 0;
            const hintElement = document.getElementById('discount_hint');

            if (!discountType || discountValue === 0) {
                hintElement.textContent = 'Ingrese el valor del descuento';
                return;
            }

            if (discountType === 'percentage') {
                if (discountValue > 100) {
                    hintElement.textContent = 'El porcentaje no puede ser mayor a 100%';
                    hintElement.classList.add('text-danger');
                } else {
                    hintElement.textContent = `Se aplicará un ${discountValue}% de descuento`;
                    hintElement.classList.remove('text-danger');
                }
            } else if (discountType === 'fixed_amount') {
                hintElement.textContent = `Se aplicará un descuento de $${discountValue.toFixed(2)}`;
                hintElement.classList.remove('text-danger');
            }
        }

        // Inicializar al cargar la página si hay un paquete seleccionado
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('package_id').value) {
                updatePackageInfo();
            }
            updateDiscountInfo();
        });
    </script>
    @endpush
</x-app-layout>
