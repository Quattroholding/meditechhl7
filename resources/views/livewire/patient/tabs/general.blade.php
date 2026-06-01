<div class="card">
    <div class="card-body">
        <div class="row">
            <!-- Personal Information -->
            <div class="col-md-6">
                <h5 class="mb-3">
                    <i class="feather-user me-2 text-primary"></i>{{ __('patient.info.personal_info') }}
                </h5>
                <div class="info-group">
                    <div class="info-item mb-3">
                        <label class="info-label">{{ __('patient.full_name') }}</label>
                        <p class="info-value">{{ $patient->name }}</p>
                    </div>
                    <div class="info-item mb-3">
                        <label class="info-label">{{ __('patient.first_name') }}</label>
                        <p class="info-value">{{ $patient->given_name }}</p>
                    </div>
                    <div class="info-item mb-3">
                        <label class="info-label">{{ __('patient.last_name') }}</label>
                        <p class="info-value">{{ $patient->family_name }}</p>
                    </div>
                    <div class="info-item mb-3">
                        <label class="info-label">{{ __('patient.gender') }}</label>
                        <p class="info-value">
                            <span class="badge bg-info">
                                {{ ucfirst($patient->gender) }}
                            </span>
                        </p>
                    </div>
                    <div class="info-item mb-3">
                        <label class="info-label">{{ __('patient.birthdate') }}</label>
                        <p class="info-value">{{ $patient->birth_date }} ({{ $patient->age }} {{ __('patient.years') }})</p>
                    </div>
                    <div class="info-item mb-3">
                        <label class="info-label">{{ __('patient.marital_status') }}</label>
                        <p class="info-value">{{ ucfirst($patient->marital_status ?? __('patient.info.not_specified')) }}</p>
                    </div>
                    <div class="info-item mb-3">
                        <label class="info-label">{{ __('patient.blood_type') }}</label>
                        <p class="info-value">
                            @if($patient->blood_type)
                                <span class="badge bg-danger">{{ $patient->blood_type }}</span>
                            @else
                                <span class="text-muted">{{ __('patient.info.not_specified') }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Identification & Contact -->
            <div class="col-md-6">
                <h5 class="mb-3">
                    <i class="feather-phone me-2 text-primary"></i>{{ __('patient.info.identification_contact') }}
                </h5>
                <div class="info-group">
                    <div class="info-item mb-3">
                        <label class="info-label">{{ __('patient.id_type') }}</label>
                        <p class="info-value">{{ $patient->identifier_type }}</p>
                    </div>
                    <div class="info-item mb-3">
                        <label class="info-label">{{ __('patient.full_id_number') }}</label>
                        <p class="info-value">{{ $patient->identifier }}</p>
                    </div>
                    <div class="info-item mb-3">
                        <label class="info-label">{{ __('patient.email') }}</label>
                        <p class="info-value">
                            <a href="mailto:{{ $patient->email }}" class="text-decoration-none">
                                {{ $patient->email }}
                            </a>
                        </p>
                    </div>
                    <div class="info-item mb-3">
                        <label class="info-label">{{ __('patient.phone') }}</label>
                        <p class="info-value">
                            <a href="tel:{{ $patient->phone }}" class="text-decoration-none">
                                {{ $patient->phone }}
                            </a>
                        </p>
                    </div>
                    @if($patient->whatsapp_phone)
                    <div class="info-item mb-3">
                        <label class="info-label">{{ __('patient.whatsapp') }}</label>
                        <p class="info-value">
                            <a href="https://wa.me/{{ str_replace(['+', '-', ' '], '', $patient->whatsapp_phone) }}"
                               target="_blank" class="text-decoration-none text-success">
                                <i class="feather-message-circle me-1"></i>{{ $patient->whatsapp_phone }}
                            </a>
                        </p>
                    </div>
                    @endif
                    <div class="info-item mb-3">
                        <label class="info-label">{{ __('patient.physical_address') }}</label>
                        <p class="info-value">{{ $patient->address ?: __('patient.info.not_specified') }}</p>
                    </div>
                    <div class="info-item mb-3">
                        <label class="info-label">{{ __('patient.country') }}</label>
                        <p class="info-value">{{ $patient->countryRelation->name ?: __('patient.info.not_specified') }}</p>
                    </div>
                    <div class="info-item mb-3">
                        <label class="info-label">{{ __('patient.state') }}</label>
                        <p class="info-value">{{ $patient->stateRelation->name ?: __('patient.info.not_specified') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Emergency Contact -->
        @if($patient->contact_name || $patient->contact_email || $patient->contact_phone)
        <div class="row mt-4">
            <div class="col-12">
                <h5 class="mb-3">
                    <i class="feather-alert-circle me-2 text-danger"></i>{{ __('patient.info.emergency_contact') }}
                </h5>
                <div class="info-group">
                    <div class="row">
                        @if($patient->contact_name)
                        <div class="col-md-4">
                            <div class="info-item mb-3">
                                <label class="info-label">{{ __('patient.emergency_contact.name') }}</label>
                                <p class="info-value">{{ $patient->contact_name }}</p>
                            </div>
                        </div>
                        @endif
                        @if($patient->contact_email)
                        <div class="col-md-4">
                            <div class="info-item mb-3">
                                <label class="info-label">{{ __('patient.emergency_contact.email') }}</label>
                                <p class="info-value">
                                    <a href="mailto:{{ $patient->contact_email }}" class="text-decoration-none">
                                        {{ $patient->contact_email }}
                                    </a>
                                </p>
                            </div>
                        </div>
                        @endif
                        @if($patient->contact_phone)
                        <div class="col-md-4">
                            <div class="info-item mb-3">
                                <label class="info-label">{{ __('patient.emergency_contact.phone') }}</label>
                                <p class="info-value">
                                    <a href="tel:{{ $patient->contact_phone }}" class="text-decoration-none">
                                        {{ $patient->contact_phone }}
                                    </a>
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Medical Information -->
        <div class="row mt-4">
            <div class="col-12">
                <h5 class="mb-3">
                    <i class="feather-activity me-2 text-primary"></i>{{ __('patient.info.medical_info') }}
                </h5>
                <div class="row">
                    {{--}}
                    <div class="col-md-4">
                        <div class="info-item mb-3">
                            <label class="info-label">ID FHIR</label>
                            <p class="info-value">
                                <code>{{ $patient->fhir_id }}</code>
                            </p>
                        </div>
                    </div>
                    {{--}}
                    <div class="col-md-4">
                        <div class="info-item mb-3">
                            <label class="info-label">{{ __('patient.status') }}</label>
                            <p class="info-value">
                                @if($patient->deceased)
                                    <span class="badge bg-dark">{{ __('patient.info.deceased') }}</span>
                                    @if($patient->deceased_date)
                                        <br><small class="text-muted">{{ $patient->deceased_date->format('d/m/Y') }}</small>
                                    @endif
                                @else
                                    <span class="badge bg-success">{{ __('patient.active') }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item mb-3">
                            <label class="info-label">{{ __('patient.info.registration_date') }}</label>
                            <p class="info-value">{{ $patient->created_at }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Communication Preferences -->
        {{--}}
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
        {{--}}
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

