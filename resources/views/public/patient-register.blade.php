@extends('layouts.public')

@section('content')
@php
    $theme = $client->theme;
    $primaryColor = $theme?->primary_color ?? '#3498db';
    $secondaryColor = $theme?->secondary_color ?? '#2ecc71';

    // Priorizar logo del cliente, luego logo del tema
    $logoPath = null;
    if ($client->logo) {
        // Logo del cliente - usar asset con storage
        $logoPath = asset('storage/' . $client->logo);
    } elseif ($theme?->logo_url) {
        // Logo del tema - puede ser URL completa o ruta
        $logoPath = str_starts_with($theme->logo_url, 'http')
            ? $theme->logo_url
            : asset('storage/' . $theme->logo_url);
    }

    $fontFamily = $theme?->font_family ?? 'Arial, sans-serif';
@endphp

<style>
    :root {
        --primary-color: {{ $primaryColor }};
        --secondary-color: {{ $secondaryColor }};
        --font-family: {{ $fontFamily }};
    }

    .registration-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        padding: 40px;
        margin-bottom: 30px;
    }

    .clinic-header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 3px solid var(--primary-color);
    }

    .clinic-logo {
        max-height: 80px;
        margin-bottom: 15px;
    }

    .clinic-name {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 10px;
    }

    .form-section {
        margin-top: 30px;
    }

    .section-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
    }

    .btn-register {
        background: var(--primary-color);
        border: none;
        padding: 12px 40px;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s;
    }

    .btn-register:hover {
        background: var(--secondary-color);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }

    .required::after {
        content: ' *';
        color: #e74c3c;
    }

    .iti { width: 100%; }
</style>

<!-- International Telephone Input CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/css/intlTelInput.css">

<div class="registration-card">
    <div class="clinic-header">
        @if($logoPath)
            <img src="{{ $logoPath }}" alt="{{ $client->name }}" class="clinic-logo">
        @endif
        <h1 class="clinic-name">{{ $client->long_name }}</h1>
        <p class="text-muted mb-0">Registro de Paciente</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <h6 class="mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Por favor corrija los siguientes errores:</h6>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('public.patient.register.store', $client) }}" id="registrationForm">
        @csrf

        <!-- Sección: Identificación -->
        <div class="form-section">
            <h5 class="section-title"><i class="fas fa-id-card me-2"></i>Identificación</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="id_type" class="form-label required">Tipo de Documento</label>
                    <select name="id_type" id="id_type" class="form-control @error('id_type') is-invalid @enderror" required>
                        <option value="">Seleccione...</option>
                        <option value="CC" {{ old('id_type') == 'CC' ? 'selected' : '' }}>Cédula de Ciudadanía</option>
                        <option value="CE" {{ old('id_type') == 'CE' ? 'selected' : '' }}>Cédula Extranjera</option>
                        <option value="PA" {{ old('id_type') == 'PA' ? 'selected' : '' }}>Pasaporte</option>
                        <option value="PT" {{ old('id_type') == 'PT' ? 'selected' : '' }}>Permiso Temporal</option>
                        <option value="SS" {{ old('id_type') == 'SS' ? 'selected' : '' }}>Seguro Social</option>
                    </select>
                    @error('id_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="id_number" class="form-label required">Número de Documento</label>
                    <input type="text" name="id_number" id="id_number" class="form-control @error('id_number') is-invalid @enderror" value="{{ old('id_number') }}" required placeholder="Ej: 8-123-456">
                    <small class="text-muted" id="id_placeholder">Ingrese el número de documento</small>
                    @error('id_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="gender" class="form-label required">Género</label>
                    <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror" required>
                        <option value="">Seleccione...</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Masculino</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Femenino</option>
                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Otro</option>
                    </select>
                    @error('gender')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Sección: Información Personal -->
        <div class="form-section">
            <h5 class="section-title"><i class="fas fa-user me-2"></i>Información Personal</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="first_name" class="form-label required">Nombre</label>
                    <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="last_name" class="form-label required">Apellido</label>
                    <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="birthdate" class="form-label required">Fecha de Nacimiento</label>
                    <input type="date" name="birthdate" id="birthdate" class="form-control @error('birthdate') is-invalid @enderror" value="{{ old('birthdate') }}" max="{{ date('Y-m-d') }}" required>
                    @error('birthdate')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="marital_status" class="form-label required">Estado Civil</label>
                    <select name="marital_status" id="marital_status" class="form-control @error('marital_status') is-invalid @enderror" required>
                        <option value="">Seleccione...</option>
                        <option value="single" {{ old('marital_status') == 'single' ? 'selected' : '' }}>Soltero/a</option>
                        <option value="married" {{ old('marital_status') == 'married' ? 'selected' : '' }}>Casado/a</option>
                        <option value="divorced" {{ old('marital_status') == 'divorced' ? 'selected' : '' }}>Divorciado/a</option>
                        <option value="widowed" {{ old('marital_status') == 'widowed' ? 'selected' : '' }}>Viudo/a</option>
                        <option value="domestic_partner" {{ old('marital_status') == 'domestic_partner' ? 'selected' : '' }}>Unión Libre</option>
                    </select>
                    @error('marital_status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="blood_type" class="form-label">Tipo de Sangre</label>
                    <select name="blood_type" id="blood_type" class="form-control @error('blood_type') is-invalid @enderror">
                        <option value="">Seleccione...</option>
                        <option value="A+" {{ old('blood_type') == 'A+' ? 'selected' : '' }}>A+</option>
                        <option value="A-" {{ old('blood_type') == 'A-' ? 'selected' : '' }}>A-</option>
                        <option value="B+" {{ old('blood_type') == 'B+' ? 'selected' : '' }}>B+</option>
                        <option value="B-" {{ old('blood_type') == 'B-' ? 'selected' : '' }}>B-</option>
                        <option value="AB+" {{ old('blood_type') == 'AB+' ? 'selected' : '' }}>AB+</option>
                        <option value="AB-" {{ old('blood_type') == 'AB-' ? 'selected' : '' }}>AB-</option>
                        <option value="O+" {{ old('blood_type') == 'O+' ? 'selected' : '' }}>O+</option>
                        <option value="O-" {{ old('blood_type') == 'O-' ? 'selected' : '' }}>O-</option>
                    </select>
                    @error('blood_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Sección: Contacto -->
        <div class="form-section">
            <h5 class="section-title"><i class="fas fa-address-book me-2"></i>Información de Contacto</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label required">Correo Electrónico</label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                    <small class="text-muted">Este será su usuario para acceder al sistema</small>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label required">Teléfono</label>
                    <input type="tel" name="phone" id="phone" class="form-control @error('full_phone') is-invalid @enderror" value="{{ old('phone') }}" required>
                    <input type="hidden" name="full_phone" id="full_phone">
                    @error('full_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-12 mb-3">
                    <label for="physical_address" class="form-label required">Dirección Física</label>
                    <textarea name="physical_address" id="physical_address" class="form-control @error('physical_address') is-invalid @enderror" rows="3" required>{{ old('physical_address') }}</textarea>
                    @error('physical_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="country_id" class="form-label">País</label>
                    <select name="country_id" id="country_id" class="form-control @error('country_id') is-invalid @enderror">
                        <option value="">Seleccione un país...</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('country_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="state_id" class="form-label">Provincia/Estado</label>
                    <select name="state_id" id="state_id" class="form-control @error('state_id') is-invalid @enderror" disabled>
                        <option value="">Seleccione una provincia...</option>
                    </select>
                    @error('state_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Sección: Contacto de Emergencia (Opcional) -->
        <div class="form-section">
            <h5 class="section-title"><i class="fas fa-phone-alt me-2"></i>Contacto de Emergencia <small class="text-muted">(Opcional)</small></h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="contact_name" class="form-label">Nombre del Contacto</label>
                    <input type="text" name="contact_name" id="contact_name" class="form-control @error('contact_name') is-invalid @enderror" value="{{ old('contact_name') }}">
                    @error('contact_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="contact_email" class="form-label">Email del Contacto</label>
                    <input type="email" name="contact_email" id="contact_email" class="form-control @error('contact_email') is-invalid @enderror" value="{{ old('contact_email') }}">
                    @error('contact_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="contact_phone" class="form-label">Teléfono del Contacto</label>
                    <input type="tel" name="contact_phone" id="contact_phone" class="form-control @error('full_contact_phone') is-invalid @enderror" value="{{ old('contact_phone') }}">
                    <input type="hidden" name="full_contact_phone" id="full_contact_phone">
                    @error('full_contact_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Sección: Información de Seguro Médico (Opcional) -->
        <div class="form-section">
            <h5 class="section-title">
                <i class="fas fa-shield-alt me-2"></i>Información de Seguro Médico
                <small class="text-muted">(Opcional)</small>
            </h5>

            <div class="row">
                <div class="col-12 mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="has_insurance" id="has_insurance"
                               class="form-check-input @error('has_insurance') is-invalid @enderror"
                               value="1" {{ old('has_insurance') ? 'checked' : '' }}>
                        <label for="has_insurance" class="form-check-label">
                            Tengo seguro médico
                        </label>
                        @error('has_insurance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div id="insurance-fields" style="display: {{ old('has_insurance') ? 'block' : 'none' }};">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="insurance_company_id" class="form-label">Compañía de Seguros</label>
                        <select name="insurance_company_id" id="insurance_company_id"
                                class="form-control @error('insurance_company_id') is-invalid @enderror">
                            <option value="">Seleccione...</option>
                            @foreach($insuranceCompanies as $company)
                                <option value="{{ $company->id }}" {{ old('insurance_company_id') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('insurance_company_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="policy_number" class="form-label">Número de Póliza</label>
                        <input type="text" name="policy_number" id="policy_number"
                               class="form-control @error('policy_number') is-invalid @enderror"
                               value="{{ old('policy_number') }}"
                               placeholder="Ej: POL-123456">
                        @error('policy_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="group_number" class="form-label">Número de Grupo (Opcional)</label>
                        <input type="text" name="group_number" id="group_number"
                               class="form-control @error('group_number') is-invalid @enderror"
                               value="{{ old('group_number') }}"
                               placeholder="Ej: GRP-789">
                        @error('group_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="relationship_to_subscriber" class="form-label">Relación con el Titular</label>
                        <select name="relationship_to_subscriber" id="relationship_to_subscriber"
                                class="form-control @error('relationship_to_subscriber') is-invalid @enderror">
                            <option value="">Seleccione...</option>
                            <option value="self" {{ old('relationship_to_subscriber') == 'self' ? 'selected' : '' }}>Yo soy el titular</option>
                            <option value="spouse" {{ old('relationship_to_subscriber') == 'spouse' ? 'selected' : '' }}>Cónyuge</option>
                            <option value="child" {{ old('relationship_to_subscriber') == 'child' ? 'selected' : '' }}>Hijo/a</option>
                            <option value="parent" {{ old('relationship_to_subscriber') == 'parent' ? 'selected' : '' }}>Padre/Madre</option>
                            <option value="other" {{ old('relationship_to_subscriber') == 'other' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('relationship_to_subscriber')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row" id="subscriber-name-row" style="display: {{ old('relationship_to_subscriber') && old('relationship_to_subscriber') !== 'self' ? 'block' : 'none' }};">
                    <div class="col-12 mb-3">
                        <label for="subscriber_name" class="form-label">Nombre del Titular del Seguro</label>
                        <input type="text" name="subscriber_name" id="subscriber_name"
                               class="form-control @error('subscriber_name') is-invalid @enderror"
                               value="{{ old('subscriber_name') }}"
                               placeholder="Nombre completo del titular">
                        <small class="text-muted">Ingrese el nombre de la persona que es titular de la póliza</small>
                        @error('subscriber_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Botón de Envío -->
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary btn-register">
                <i class="fas fa-user-plus me-2"></i>Registrarse
            </button>
        </div>

        <div class="text-center mt-3">
            <p class="text-muted mb-0">
                <small>¿Ya tiene una cuenta? <a href="{{ route('login') }}" style="color: var(--primary-color);">Iniciar Sesión</a></small>
            </p>
        </div>
    </form>
</div>

<!-- International Telephone Input JS -->
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/js/intlTelInput.min.js"></script>

<script>
    // Placeholders para tipos de documento
    const idPlaceholders = {
        'CC': 'Números y guiones',
        'CE': 'Números y guiones',
        'PA': 'Ej: PA1234567',
        'PT': 'Ej: PT-12345678',
        'SS': 'Números y guiones'
    };

    // Actualizar placeholder cuando cambia el tipo de documento
    document.getElementById('id_type').addEventListener('change', function() {
        const placeholder = idPlaceholders[this.value] || 'Ingrese el número de documento';
        document.getElementById('id_number').placeholder = placeholder;
        document.getElementById('id_placeholder').textContent = placeholder;
    });

    // International Telephone Input para teléfono principal
    const phoneInput = document.querySelector("#phone");
    const phoneIti = window.intlTelInput(phoneInput, {
        initialCountry: "pa",
        preferredCountries: ["pa", "us", "co", "mx"],
        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/js/utils.js",
    });

    // International Telephone Input para teléfono de contacto
    const contactPhoneInput = document.querySelector("#contact_phone");
    const contactPhoneIti = window.intlTelInput(contactPhoneInput, {
        initialCountry: "pa",
        preferredCountries: ["pa", "us", "co", "mx"],
        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/js/utils.js",
    });

    // Al enviar el formulario, guardar el número completo en el campo oculto
    document.getElementById('registrationForm').addEventListener('submit', function(e) {
        const fullPhone = phoneIti.getNumber();
        const fullContactPhone = contactPhoneIti.getNumber();

        document.getElementById('full_phone').value = fullPhone;
        document.getElementById('full_contact_phone').value = fullContactPhone;

        // Validar que el teléfono principal tenga el formato correcto
        if (!phoneIti.isValidNumber()) {
            e.preventDefault();
            alert('Por favor ingrese un número de teléfono válido con el código de país.');
            phoneInput.focus();
            return false;
        }

        // Validar teléfono de contacto solo si fue ingresado
        if (contactPhoneInput.value && !contactPhoneIti.isValidNumber()) {
            e.preventDefault();
            alert('Por favor ingrese un teléfono de contacto válido con el código de país.');
            contactPhoneInput.focus();
            return false;
        }
    });

    // Mostrar/ocultar campos de seguro
    const hasInsuranceCheckbox = document.getElementById('has_insurance');
    const insuranceFields = document.getElementById('insurance-fields');

    hasInsuranceCheckbox.addEventListener('change', function() {
        if (this.checked) {
            insuranceFields.style.display = 'block';
        } else {
            insuranceFields.style.display = 'none';
            // Limpiar campos de seguro al ocultar
            document.getElementById('insurance_company_id').value = '';
            document.getElementById('policy_number').value = '';
            document.getElementById('group_number').value = '';
            document.getElementById('relationship_to_subscriber').value = '';
            document.getElementById('subscriber_name').value = '';
            document.getElementById('subscriber-name-row').style.display = 'none';
        }
    });

    // Mostrar/ocultar nombre del titular según la relación
    const relationshipSelect = document.getElementById('relationship_to_subscriber');
    const subscriberNameRow = document.getElementById('subscriber-name-row');

    relationshipSelect.addEventListener('change', function() {
        if (this.value && this.value !== 'self') {
            subscriberNameRow.style.display = 'block';
        } else {
            subscriberNameRow.style.display = 'none';
            document.getElementById('subscriber_name').value = '';
        }
    });

    // Cargar estados cuando se selecciona un país
    document.getElementById('country_id').addEventListener('change', function() {
        const countryId = this.value;
        const stateSelect = document.getElementById('state_id');

        // Limpiar y deshabilitar el select de estados
        stateSelect.innerHTML = '<option value="">Seleccione una provincia...</option>';
        stateSelect.disabled = true;

        if (countryId) {
            // Hacer petición AJAX para obtener estados
            fetch(`/api/countries/${countryId}/states`)
                .then(response => response.json())
                .then(states => {
                    if (states.length > 0) {
                        states.forEach(state => {
                            const option = document.createElement('option');
                            option.value = state.id;
                            option.textContent = state.name;
                            stateSelect.appendChild(option);
                        });
                        stateSelect.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error cargando estados:', error);
                });
        }
    });
</script>
@endsection
