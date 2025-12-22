<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Soluciones Meditec</title>
    <link rel="icon" href="{{url('images/favicon.ico')}}" type="image/x-icon">
    <link rel="stylesheet" href="{{ url('landing/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.css">
    <style>
        .register-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #2D1B69 0%, #1E3A8A 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .register-card {
            background: white;
            border-radius: 20px;
            padding: 50px;
            max-width: 800px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .register-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .register-header img {
            max-width: 150px;
            margin-bottom: 20px;
        }

        .register-header h1 {
            color: #2D1B69;
            margin-bottom: 10px;
        }

        .register-header p {
            color: #666;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-field {
            display: flex;
            flex-direction: column;
        }

        .form-field label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-field label .required {
            color: #e74c3c;
        }

        .form-field input,
        .form-field select {
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-field input:focus,
        .form-field select:focus {
            outline: none;
            border-color: #2D1B69;
        }

        .form-field .error {
            color: #e74c3c;
            font-size: 12px;
            margin-top: 5px;
        }

        .section-title {
            color: #2D1B69;
            margin-top: 30px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-input-wrapper input[type=file] {
            position: absolute;
            left: -9999px;
        }

        .file-input-label {
            display: block;
            padding: 12px;
            border: 2px dashed #e0e0e0;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .file-input-label:hover {
            border-color: #2D1B69;
            background: #f8f9fa;
        }

        .submit-button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #2D1B69 0%, #1E3A8A 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.3s ease;
            margin-top: 30px;
        }

        .submit-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(45, 27, 105, 0.3);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #666;
            text-decoration: none;
        }

        .back-link:hover {
            color: #2D1B69;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        #practitioner-fields {
            display: none;
        }

        @media (max-width: 768px) {
            .register-card {
                padding: 30px 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <img src="{{ asset('landing/images/Icono-8.png') }}" alt="Soluciones Meditec">
                <h1>Registro de Cliente</h1>
                @if($selectedPackage)
                    <p>Plan seleccionado: <strong>{{ $selectedPackage->name }}</strong> - ${{ number_format($selectedPackage->base_price, 2) }}/mes</p>
                @else
                    <p>Complete el formulario para registrarse</p>
                @endif
            </div>

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('public.register.store') }}" enctype="multipart/form-data" id="registrationForm">
                @csrf
                    <div class="form-row">
                        <div class="form-field">
                            <label>Paquete <span class="required">*</span></label>
                            <select name="package_id" id="package_id" required>
                                <option value="">Seleccione un paquete</option>
                                @foreach($packages as $pkg)
                                    <option value="{{ $pkg->id }}"
                                            {{ (old('package_id', $selectedPackage?->id) == $pkg->id) ? 'selected' : '' }}
                                            data-max-users="{{ $pkg->max_users }}">
                                        {{ $pkg->name }} - ${{ number_format($pkg->base_price, 2) }}/mes
                                    </option>
                                @endforeach
                            </select>
                            @error('package_id')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                            <div class="form-field">
                                <label>Tipo de Documento <span class="required">*</span></label>
                                <select name="identifier_type">
                                    <option value="">Seleccione...</option>
                                    <option value="CC" {{ old('identifier_type') == 'CC' ? 'selected' : '' }}>Cédula (CC)</option>
                                    <option value="PA" {{ old('identifier_type') == 'PA' ? 'selected' : '' }}>Pasaporte (PA)</option>
                                    <option value="CE" {{ old('identifier_type') == 'CE' ? 'selected' : '' }}>Cédula Extranjera (CE)</option>
                                    <option value="PT" {{ old('identifier_type') == 'PT' ? 'selected' : '' }}>Permiso Temporal (PT)</option>
                                </select>
                                @error('identifier_type')
                                <span class="error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-field">
                                <label>Número de Documento <span class="required">*</span></label>
                                <input type="text" name="identifier" value="{{ old('identifier') }}">
                                @error('identifier')
                                <span class="error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    <div class="form-row">
                        <div class="form-field">
                            <label>Nombres <span class="required">*</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" required>
                            @error('first_name')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label>Apellidos <span class="required">*</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" required>
                            @error('last_name')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-field">
                            <label>Email <span class="required">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-field">
                            <label>Teléfono <span class="required">*</span></label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required>
                            @error('phone')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-field">
                            <label>Contraseña <span class="required">*</span></label>
                            <input type="password" name="password" required minlength="8">
                            @error('password')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label>Confirmar Contraseña <span class="required">*</span></label>
                            <input type="password" name="password_confirmation" required minlength="8">
                        </div>
                    </div>
                    <div class="form-row">
                    <div class="form-field">
                        <label>Género <span class="required">*</span></label>
                        <select name="gender">
                            <option value="">Seleccione...</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Masculino</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Femenino</option>
                        </select>
                        @error('gender')
                        <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-field">
                        <label>Especialidad médica <span class="required">*</span></label>
                        <select name="medical_speciality">
                            <option value="">Seleccione...</option>
                            @foreach(\App\Models\MedicalSpeciality::get() as $em)
                                <option value="{{$em->id}}" {{ old('medical_speciality') == $em->id ? 'selected' : '' }}>{{$em->name}}</option>
                            @endforeach
                        </select>
                        @error('medical_speciality')
                        <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                    <div class="form-row">

                        <div class="form-field">
                            <label>Logo (Opcional)</label>
                            <div class="file-input-wrapper">
                                <input type="file" name="logo" id="logo" accept="image/*">
                                <label for="logo" class="file-input-label">
                                    <i class="fas fa-cloud-upload-alt"></i> Seleccionar imagen
                                </label>
                            </div>
                            @error('logo')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{--}}
                    <div class="form-field">
                        <label>Tipo <span class="required">*</span></label>
                        <select name="type" required id="tipo_cliente">
                            <option value="">Seleccione un tipo</option>
                            @foreach(\App\Enums\ClientType::cases() as $tc)
                                <option value="{{$tc->label()}}"  {{ old('type') == $tc->label() ? 'selected' : '' }}>{{$tc->label()}}</option>
                            @endforeach
                        </select>
                        @error('name')
                        <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-field" style="display: none;" id="type_client_name">
                        <label id="type_client_name_label">Nombre del lugar de consulta <span class="required">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ej: Clínica Santa María">
                        @error('name')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                {{--}}

                @if(config('app.env') === 'production')
                    <div class="form-group">
                        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.public_key') }}"></div>
                        <x-input-error :messages="$errors->get('cf-turnstile-response')" class="mt-2" />
                    </div>
                @endif
                <button type="submit" class="submit-button">
                    <i class="fas fa-user-plus"></i> Registrarse
                </button>
            </form>

            <a href="{{ route('welcome') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Volver al inicio
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
    <script>
        // Inicializar intl-tel-input
        const phoneInput = document.querySelector("#phone");
        const iti = window.intlTelInput(phoneInput, {
            initialCountry: "pa",
            preferredCountries: ["pa", "us", "co", "mx"],
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js"
        });

        // Mostrar/ocultar campos de practitioner según paquete
        const packageSelect = document.getElementById('package_id');
        const practitionerFields = document.getElementById('practitioner-fields');

        function togglePractitionerFields() {
            const selectedOption = packageSelect.options[packageSelect.selectedIndex];
            const maxUsers = selectedOption.getAttribute('data-max-users');

            if (maxUsers === '1') {
                practitionerFields.style.display = 'block';
                // Hacer campos requeridos
                practitionerFields.querySelectorAll('select, input').forEach(field => {
                    if (field.name !== 'identifier' && field.name !== 'identifier_type' && field.name !== 'gender') {
                        return;
                    }
                    field.required = true;
                });
            } else {
                practitionerFields.style.display = 'none';
                // Remover required
                practitionerFields.querySelectorAll('select, input').forEach(field => {
                    field.required = false;
                });
            }
        }

        packageSelect.addEventListener('change', togglePractitionerFields);

        // Ejecutar al cargar si hay paquete seleccionado
        if (packageSelect.value) {
            togglePractitionerFields();
        }

        // Actualizar nombre de archivo seleccionado
        const logoInput = document.getElementById('logo');
        const logoLabel = document.querySelector('.file-input-label');
        const tipo_cliente = document.getElementById('tipo_cliente');
        const type_client_name = document.getElementById('type_client_name');
        const type_client_name_label = document.getElementById('type_client_name_label');

        logoInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                logoLabel.innerHTML = '<i class="fas fa-check"></i> ' + this.files[0].name;
            }
        });

        tipo_cliente.addEventListener('change', function () {
            console.log(this.value);

            type_client_name.style.display = 'none';

            if(this.value == 'Centro de Atencion Primario' || this.value == 'Clinica' || this.value == 'Hospital' || this.value == 'Otro'){
                type_client_name.style.display = 'block';
                type_client_name_label.innerHTML = 'Nombre del(a) '+this.value;
            }

        });

        // Validar formulario antes de enviar
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            // Actualizar el campo phone con el número completo internacional
            phoneInput.value = iti.getNumber();
        });
    </script>
</body>
</html>
