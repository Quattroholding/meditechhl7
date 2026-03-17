<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - SAMI</title>
    <link rel="icon" href="{{url('images/favicon.ico')}}" type="image/x-icon">


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @include('layout.partials.head')
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <link rel="stylesheet" href="{{ url('landing/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            margin: 0 auto;
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
            background: #fff;
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

        /* SAMI Alert Styles */
        .sami-alert {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border: 2px solid #2196F3;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            display: flex;
            gap: 20px;
            align-items: flex-start;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .sami-alert-icon {
            flex-shrink: 0;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse-sami 2s infinite;
        }

        .sami-alert-icon i {
            font-size: 30px;
            color: white;
        }

        @keyframes pulse-sami {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(33, 150, 243, 0.7);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 0 0 10px rgba(33, 150, 243, 0);
            }
        }

        .sami-alert-content h4 {
            color: #1976D2;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .sami-alert-content h4 i {
            color: #FFC107;
        }

        .sami-alert-content p {
            color: #333;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .sami-alert-content ul {
            list-style: none;
            padding: 0;
            margin: 10px 0;
        }

        .sami-alert-content ul li {
            padding: 5px 0;
            color: #1976D2;
            font-weight: 500;
        }

        .sami-alert-content ul li i {
            color: #4CAF50;
            margin-right: 8px;
        }

        .sami-highlight {
            background: white;
            padding: 12px;
            border-radius: 8px;
            border-left: 4px solid #FFC107;
            margin-top: 15px;
            font-size: 14px;
        }

        .sami-highlight i {
            color: #FF9800;
            margin-right: 5px;
        }

        @media (max-width: 768px) {
            .register-card {
                padding: 30px 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .sami-alert {
                flex-direction: column;
                text-align: center;
                padding: 20px 15px;
            }

            .sami-alert-icon {
                margin: 0 auto;
                width: 50px;
                height: 50px;
            }

            .sami-alert-icon i {
                font-size: 24px;
            }

            .sami-alert-content h4 {
                font-size: 16px;
            }
        }
    </style>
    @if(config('app.env') === 'production')
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
</head>
<body>
    <div class="register-container" x-data>
        <div class="register-card">
            <div class="register-header">
                <div><img src="{{ asset('images/logoSAMIV.jpg') }}" alt="SAMI"></div>
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
                    <input name="force_login" value="true" type="hidden">
                    <div class="form-row">
                        <div class="form-field">
                            <label>Paquete <span class="required">*</span></label>
                            <select name="package_id" id="package_id" required>
                                <option value="">Seleccione un paquete</option>
                                @foreach($packages as $pkg)
                                    <option value="{{ $pkg->id }}"
                                            {{ (old('package_id', $selectedPackage?->id) == $pkg->id) ? 'selected' : '' }}
                                            data-max-users="{{ $pkg->max_users }}"
                                            data-agent-available="{{ $pkg->agent_available }}">
                                        {{ $pkg->name }} - ${{ number_format($pkg->base_price, 2) }}/mes
                                        @if($pkg->id == 2) ⭐ RECOMENDADO @endif
                                        @if($pkg->agent_available) 🤖 @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('package_id')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div id="sami-info" style="display: none;" class="sami-alert">
                        <div class="sami-alert-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div class="sami-alert-content">
                            <h4><i class="fas fa-star"></i> ¡Excelente elección!</h4>
                            <p>
                                Este plan incluye acceso a <strong>SAMI</strong>, nuestro agente de WhatsApp con IA que:
                            </p>
                            <ul>
                                <li><i class="fas fa-check-circle"></i> Agenda citas automáticamente 24/7</li>
                                <li><i class="fas fa-check-circle"></i> Incluye tu consultorio en nuestro <strong>Directorio Médico</strong></li>
                                <li><i class="fas fa-check-circle"></i> Te conecta con nuevos pacientes constantemente</li>
                            </ul>
                            <p class="sami-highlight">
                                <i class="fas fa-users"></i> <strong>Potencial ilimitado de pacientes:</strong>
                                Los pacientes podrán encontrarte y agendar citas directamente a través de SAMI.
                            </p>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-field">
                            <label>Tipo de Documento <span class="required">*</span></label>
                            <select name="identifier_type" class="form-control select">
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
                            <x-phone-input
                                name="phone"
                                id="phone"
                                :value="old('phone')"
                                :error="$errors->get('phone')"
                                required
                            />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-field">
                            <label>Contraseña <span class="required">*</span></label>
                            <input type="password" name="password" required minlength="8" autocomplete="off">
                            @error('password')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-field">
                            <label>Confirmar Contraseña <span class="required">*</span></label>
                            <input type="password" name="password_confirmation" required minlength="8" autocomplete="off">
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
                                <input type="file" name="logo" id="logo" accept="image">
                                <label for="logo" class="file-input-label">
                                    <i class="fas fa-cloud-upload-alt"></i> Seleccionar imagen
                                </label>
                            </div>
                            @error('logo')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <!-- Código de Referido -->
                        <div class="form-field">
                            <h3><i class="fas fa-gift"></i> Código de Referido (Opcional)</h3>
                            <label for="referral_code">¿Tienes un código de referido?</label><br/>
                            <input class="form-control" type="text" id="referral_code" name="referral_code" value="{{ old('referral_code', request('ref')) }}" placeholder="Ej: REF-ABC12345"><br/>
                            <small class="form-help">
                                <i class="fas fa-tag" style="color: #4CAF50;"></i> <strong style="color: #2D1B69;">¡Obtén $4.99 de descuento en tu primera factura!</strong><br/>
                                Al usar un código de referido, recibirás un descuento instantáneo y ayudarás a otro médico.
                            </small>
                            @error('referral_code')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-field">
                            <x-input-label name="remember-me"  id="remember-me" class="custom_check mr-2 mb-0 d-inline-flex remember-me">{{__('Estoy de acuerdo con los')}}
                                <a href="javascript:;" x-on:click="$dispatch('open-modal', 'terms-privacy')">&nbsp {{__('Terminos de Servicios')}} </a>&nbsp y <a
                                    href="javascript:;" x-on:click="$dispatch('open-modal', 'terms-privacy')">&nbsp {{__('Politicas de Privacidad')}} </a>
                                <input type="checkbox" name="terms_and_privacy">
                                <span class="checkmark"></span>
                            </x-input-label>
                            <x-input-error :messages="$errors->get('terms_and_privacy')"/>

                        </div>
                    </div>
                {{--}}
                @if(config('app.env') === 'production')
                    <div class="form-group">
                        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.public_key') }}"></div>
                        <x-input-error :messages="$errors->get('cf-turnstile-response')" class="mt-2" />
                    </div>
                @endif
                {{--}}
                <button type="submit" class="submit-button">
                    <i class="fas fa-user-plus"></i> Registrarse
                </button>
            </form>

            <a href="{{ route('welcome') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Volver al inicio
            </a>
        </div>
    </div>
    <x-terms-privacy-modal />
    @livewireScripts

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mostrar/ocultar campos de practitioner según paquete
            const packageSelect = document.getElementById('package_id');

            // Ejecutar al cargar si hay paquete seleccionado
            if (packageSelect.value) {
                //togglePractitionerFields();
            }

            // Mostrar/ocultar información de SAMI según paquete
            const samiInfo = document.getElementById('sami-info');

            function toggleSamiInfo() {
                const selectedOption = packageSelect.options[packageSelect.selectedIndex];
                const agentAvailable = selectedOption.getAttribute('data-agent-available');

                if (agentAvailable === '1') {
                    samiInfo.style.display = 'flex';
                    // Animar entrada
                    setTimeout(() => {
                        samiInfo.style.opacity = '1';
                        samiInfo.style.transform = 'translateY(0)';
                    }, 10);
                } else {
                    samiInfo.style.opacity = '0';
                    samiInfo.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        samiInfo.style.display = 'none';
                    }, 300);
                }
            }

            //packageSelect.addEventListener('change', toggleSamiInfo);

            // Ejecutar al cargar si hay paquete seleccionado
            if (packageSelect.value) {
                //toggleSamiInfo();
            }

            // Actualizar nombre de archivo seleccionado
            const logoInput = document.getElementById('logo');
            const logoLabel = document.querySelector('.file-input-label');

            logoInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    logoLabel.innerHTML = '<i class="fas fa-check"></i> ' + this.files[0].name;
                }
            });

            // Escuchar evento del modal cuando se acepta
            window.addEventListener('terms-accepted', function(e) {
                const checkbox = document.querySelector('input[name="terms_and_privacy"]');
                if (checkbox) {
                    checkbox.checked = true;
                }
            });
        });
    </script>
</body>
</html>
