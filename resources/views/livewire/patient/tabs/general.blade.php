<div class="card">
    <div class="card-body">
        <div class="row">
            <!-- Personal Information -->
            <div class="col-md-6">
                <h5 class="mb-3">
                    <i class="feather-user me-2 text-primary"></i>Información Personal
                </h5>
                <div class="info-group">
                    <div class="info-item mb-3">
                        <label class="info-label">Nombre Completo</label>
                        <p class="info-value">{{ $patient->name }}</p>
                    </div>
                    <div class="info-item mb-3">
                        <label class="info-label">Nombre</label>
                        <p class="info-value">{{ $patient->given_name }}</p>
                    </div>
                    <div class="info-item mb-3">
                        <label class="info-label">Apellido</label>
                        <p class="info-value">{{ $patient->family_name }}</p>
                    </div>
                    <div class="info-item mb-3">
                        <label class="info-label">Género</label>
                        <p class="info-value">
                            <span class="badge bg-info">
                                {{ ucfirst($patient->gender) }}
                            </span>
                        </p>
                    </div>
                    <div class="info-item mb-3">
                        <label class="info-label">Fecha de Nacimiento</label>
                        <p class="info-value">{{ $patient->birth_date }} ({{ $patient->age }} años)</p>
                    </div>
                    <div class="info-item mb-3">
                        <label class="info-label">Estado Civil</label>
                        <p class="info-value">{{ ucfirst($patient->marital_status ?? 'No especificado') }}</p>
                    </div>
                    <div class="info-item mb-3">
                        <label class="info-label">Tipo de Sangre</label>
                        <p class="info-value">
                            @if($patient->blood_type)
                                <span class="badge bg-danger">{{ $patient->blood_type }}</span>
                            @else
                                <span class="text-muted">No especificado</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Identification & Contact -->
            <div class="col-md-6">
                <h5 class="mb-3">
                    <i class="feather-phone me-2 text-primary"></i>Identificación y Contacto
                </h5>
                <div class="info-group">
                    <div class="info-item mb-3">
                        <label class="info-label">Tipo de Documento</label>
                        <p class="info-value">{{ $patient->identifier_type }}</p>
                    </div>
                    <div class="info-item mb-3">
                        <label class="info-label">Número de Documento</label>
                        <p class="info-value">{{ $patient->identifier }}</p>
                    </div>
                    <div class="info-item mb-3">
                        <label class="info-label">Email</label>
                        <p class="info-value">
                            <a href="mailto:{{ $patient->email }}" class="text-decoration-none">
                                {{ $patient->email }}
                            </a>
                        </p>
                    </div>
                    <div class="info-item mb-3">
                        <label class="info-label">Teléfono</label>
                        <p class="info-value">
                            <a href="tel:{{ $patient->phone }}" class="text-decoration-none">
                                {{ $patient->phone }}
                            </a>
                        </p>
                    </div>
                    @if($patient->whatsapp_phone)
                    <div class="info-item mb-3">
                        <label class="info-label">WhatsApp</label>
                        <p class="info-value">
                            <a href="https://wa.me/{{ str_replace(['+', '-', ' '], '', $patient->whatsapp_phone) }}"
                               target="_blank" class="text-decoration-none text-success">
                                <i class="feather-message-circle me-1"></i>{{ $patient->whatsapp_phone }}
                            </a>
                        </p>
                    </div>
                    @endif
                    <div class="info-item mb-3">
                        <label class="info-label">Dirección</label>
                        <p class="info-value">{{ $patient->address ?: 'No especificada' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical Information -->
        <div class="row mt-4">
            <div class="col-12">
                <h5 class="mb-3">
                    <i class="feather-activity me-2 text-primary"></i>Información Médica
                </h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-item mb-3">
                            <label class="info-label">ID FHIR</label>
                            <p class="info-value">
                                <code>{{ $patient->fhir_id }}</code>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item mb-3">
                            <label class="info-label">Estado</label>
                            <p class="info-value">
                                @if($patient->deceased)
                                    <span class="badge bg-dark">Fallecido</span>
                                    @if($patient->deceased_date)
                                        <br><small class="text-muted">{{ $patient->deceased_date->format('d/m/Y') }}</small>
                                    @endif
                                @else
                                    <span class="badge bg-success">Activo</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item mb-3">
                            <label class="info-label">Fecha de Registro</label>
                            <p class="info-value">{{ $patient->created_at }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Communication Preferences -->
        @if($patient->communication)
        <div class="row mt-4">
            <div class="col-12">
                <h5 class="mb-3">
                    <i class="feather-globe me-2 text-primary"></i>Preferencias de Comunicación
                </h5>
                <div class="info-item">
                    <label class="info-label">Idiomas</label>
                    <p class="info-value">
                        @php
                            $communication = is_string($patient->communication) ? json_decode($patient->communication, true) : $patient->communication;
                        @endphp
                        @if(is_array($communication))
                            @foreach($communication as $comm)
                                @if(isset($comm['language']))
                                    <span class="badge bg-secondary me-2">{{ strtoupper($comm['language']) }}</span>
                                    @if(isset($comm['preferred']) && $comm['preferred'])
                                        <small class="text-success">(Preferido)</small>
                                    @endif
                                @endif
                            @endforeach
                        @else
                            <span class="text-muted">No especificado</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
    <style>
        .info-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
            display: block;
        }

        .info-value {
            font-size: 0.95rem;
            color: #212529;
            margin-bottom: 0;
            line-height: 1.4;
        }

        .info-group {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
        }

        .info-item {
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 0.75rem;
        }

        .info-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
    </style>

</div>

