<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('consultation.title') }} - {{ $encounter->identifier }}
                @endslot
                @slot('li_1')
                    {{ __('generic.detail') }}  {{ __('consultation.title') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- Tab Navigation -->
                            <ul class="nav nav-tabs" id="consultationTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                                        <i class="fa fa-info-circle"></i> {{ __('consultation.general_information') }}
                                    </button>
                                </li>
                                @if($encounter->vitalSigns->count() > 0)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="vitals-tab" data-bs-toggle="tab" data-bs-target="#vitals" type="button" role="tab" aria-controls="vitals" aria-selected="false">
                                        <i class="fa fa-heartbeat"></i> {{ __('consultation.vital_signs') }}
                                    </button>
                                </li>
                                @endif
                                @if($encounter->presentIllnesses)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="illness-tab" data-bs-toggle="tab" data-bs-target="#illness" type="button" role="tab" aria-controls="illness" aria-selected="false">
                                        <i class="fa fa-stethoscope"></i> {{ __('consultation.present_illness') }}
                                    </button>
                                </li>
                                @endif
                                @if($encounter->diagnoses->count() > 0)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="diagnosis-tab" data-bs-toggle="tab" data-bs-target="#diagnosis" type="button" role="tab" aria-controls="diagnosis" aria-selected="false">
                                            <i class="fa fa-stethoscope"></i> {{ __('consultation.diagnostics') }}
                                        </button>
                                    </li>
                                @endif
                                @if($encounter->physicalExams->count() > 0)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="exams-tab" data-bs-toggle="tab" data-bs-target="#exams" type="button" role="tab" aria-controls="exams" aria-selected="false">
                                        <i class="fa fa-user-md"></i> {{ __('consultation.physical_exams') }}
                                    </button>
                                </li>
                                @endif
                                @if($encounter->medicationRequests->count() > 0)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="medications-tab" data-bs-toggle="tab" data-bs-target="#medications" type="button" role="tab" aria-controls="medications" aria-selected="false">
                                        <i class="fa fa-pills"></i> {{ __('consultation.medication_requests') }}
                                    </button>
                                </li>
                                @endif
                                @if($encounter->serviceRequests->count() > 0)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="services-tab" data-bs-toggle="tab" data-bs-target="#services" type="button" role="tab" aria-controls="services" aria-selected="false">
                                        <i class="fa fa-clipboard-list"></i> {{ __('consultation.service_requests') }}
                                    </button>
                                </li>
                                @endif
                                @if($encounter->referrals->count() > 0)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="referrals-tab" data-bs-toggle="tab" data-bs-target="#referrals" type="button" role="tab" aria-controls="referrals" aria-selected="false">
                                        <i class="fa fa-share-alt"></i> {{ __('consultation.referrals') }}
                                    </button>
                                </li>
                                @endif
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content" id="consultationTabsContent">
                                <!-- General Information Tab -->
                                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><strong>{{ __('encounter.identifier') }}:</strong></label>
                                                        <span>{{ $encounter->identifier }}</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label><strong>{{ __('encounter.patient') }}:</strong></label>
                                                        <span>{!! $encounter->patient->profile_name !!}</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label><strong>{{ __('encounter.practitioner') }}:</strong></label>
                                                        <span>{!! $encounter->practitioner->profile_name !!}</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label><strong>{{ __('encounter.reason') }}:</strong></label>
                                                        <span>{!! $encounter->reason !!}</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><strong>{{ __('encounter.status') }}:</strong></label>
                                                        {!! ucfirst($encounter->status) !!}
                                                    </div>
                                                    <div class="form-group">
                                                        <label><strong>{{ __('encounter.speciality') }}:</strong></label>
                                                        <span>{!! $encounter->medicalSpeciality->name !!}</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label><strong>{{ __('encounter.start') }}:</strong></label>
                                                        <span>{{ $encounter->start ? $encounter->start->format('Y-m-d H:i:s') : 'N/A' }}</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label><strong>{{ __('encounter.end') }}:</strong></label>
                                                        <span>{{ $encounter->end ? $encounter->end->format('Y-m-d H:i:s') : 'N/A' }}</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label><strong>{{ __('encounter.general_note') }}:</strong></label>
                                                        <span>{{ $encounter->general_note ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if($encounter->vitalSigns->count() > 0)
                                <!-- Vital Signs Tab -->
                                <div class="tab-pane fade" id="vitals" role="tabpanel" aria-labelledby="vitals-tab">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('encounter.vital_sign_type') }}</th>
                                                            <th>{{ __('encounter.value') }}</th>
                                                            <th>{{ __('encounter.unit') }}</th>
                                                            {{--}}
                                                            <th>{{ __('encounter.recorded_date') }}</th>
                                                            {{--}}
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($encounter->vitalSigns as $vitalSign)
                                                            <tr>
                                                                <td>{{ $vitalSign->observationType->description ?? $vitalSign->code }}</td>
                                                                <td>{{ $vitalSign->value }}</td>
                                                                <td>{{ $vitalSign->observationType->default_unit ?? $vitalSign->unit }}</td>
                                                                {{--}}
                                                                <td>{{ $vitalSign->effective_date ? $vitalSign->effective_date->format('Y-m-d H:i') : 'N/A' }}</td>
                                                                {{--}}
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if($encounter->presentIllnesses)
                                <!-- Present Illness Tab -->
                                <div class="tab-pane fade" id="illness" role="tabpanel" aria-labelledby="illness-tab">
                                    <div class="card">
                                        <div class="card-body">
                                                <dl class="dl-horizontal">
                                                    <dt>{{ __('encounter.locations') }}</dt>
                                                    <dd>

                                                        @if(is_array($encounter->presentIllnesses->locations) && count($encounter->presentIllnesses->locations)>1)
                                                            @foreach($encounter->presentIllnesses->locations as $v)
                                                                @isset($v)
                                                                    {{__('present_illness.'.$v)}} <br/>
                                                                @endisset
                                                            @endforeach
                                                        @elseif($encounter->presentIllnesses->location <> '{"location":null}')
                                                            {{__('present_illness.'.$encounter->presentIllnesses->location)}}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </dd>
                                                    <dt>{{ __('encounter.severity') }}</dt>
                                                    <dd>{{__('present_illness.'.$encounter->presentIllnesses->severity)}}</dd>
                                                    <dt>{{ __('encounter.duration') }}</dt>
                                                    <dd>{{__('present_illness.'.$encounter->presentIllnesses->duration)}}</dd>
                                                    <dt>{{ __('encounter.timing') }}</dt>
                                                    <dd>{{__('present_illness.'.$encounter->presentIllnesses->timing) }}</dd>
                                                    <dt>{{ __('encounter.aggravating_factors') }}</dt>
                                                    <dd>{{$encounter->presentIllnesses->aggravating_factors}}</dd>
                                                    <dt>{{ __('encounter.alleviating_factors') }}</dt>
                                                    <dd>{{$encounter->presentIllnesses->alleviating_factors}}</dd>
                                                    <dt>{{ __('encounter.associated_symptoms') }}</dt>
                                                    <dd>{{$encounter->presentIllnesses->associated_symptoms}}</dd>
                                                    <dt>{{ __('encounter.description') }}</dt>
                                                    <dd>{{$encounter->presentIllnesses->description}}</dd>
                                                </dl>


                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if($encounter->diagnoses->count()>0)
                                    <!-- Present Illness Tab -->
                                    <div class="tab-pane fade" id="diagnosis" role="tabpanel" aria-labelledby="diagnosis-tab">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-striped">
                                                        <thead>
                                                        <tr>
                                                            <th>{{ __('encounter.code') }}</th>
                                                            <th>{{ __('encounter.description') }}</th>
                                                            <th>{{ __('encounter.category') }}</th>
                                                            <th>{{ __('encounter.clinical_status') }}</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($encounter->diagnoses as $diag)
                                                            @if($diag->condition)
                                                            <tr>
                                                                <td>{{ $diag->condition->code }}</td>
                                                                <td>{{ $diag->condition->onset_info ?? $diag->condition->icd10Code->description_es }}</td>
                                                                <td>{{ $diag->condition->category ?? 'ICD10'}}</td>
                                                                <td>
                                                                    <span class="badge bg-{{ $diag->condition->clinical_status == 'active' ? 'success' : 'secondary' }}">
                                                                        {{ ucfirst($diag->condition->clinical_status) }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            @endif
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if($encounter->physicalExams->count() > 0)
                                <!-- Physical Exams Tab -->
                                <div class="tab-pane fade" id="exams" role="tabpanel" aria-labelledby="exams-tab">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>{{ __('encounter.body_system') }}</th>
                                                        <th>{{ __('encounter.findings') }}</th>
                                                        {{--}}
                                                        <th>{{ __('encounter.exam_date') }}</th>
                                                        {{--}}
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($encounter->physicalExams as $exam)
                                                        <tr>
                                                            <td>{{ $exam->observationType->name }}</td>
                                                            <td>

                                                                @foreach((array)$exam->finding as $key=>$value)
                                                                    {{$value}}
                                                                @endforeach
                                                            </td>
                                                            {{--}}
                                                            <td>{{ $exam->effective_date ? $exam->effective_date->format('Y-m-d H:i') : 'N/A' }}</td>
                                                            {{--}}
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if($encounter->medicationRequests->count() > 0)
                                <!-- Medication Requests Tab -->
                                <div class="tab-pane fade" id="medications" role="tabpanel" aria-labelledby="medications-tab">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('consultation.medication') }}</th>
                                                            <th>{{ __('consultation.dosage') }}</th>
                                                            <th>{{ __('consultation.status') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($encounter->medicationRequests as $medication)
                                                            <tr>
                                                                <td>
                                                                    @if(!empty($medication->medication_id2) && !empty($medication->medication2))
                                                                        {{$medication->medication2->display}}
                                                                    @else
                                                                    {{ $medication->medicine ? $medication->medicine->full_name :  $medication->medication }}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    {{$medication->dosage_text ?? $medication->dosage_instruction }}
                                                                </td>
                                                                <td>{!! $medication->status !!}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if($encounter->serviceRequests->count() > 0)
                                <!-- Service Requests Tab -->
                                <div class="tab-pane fade" id="services" role="tabpanel" aria-labelledby="services-tab">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('encounter.service_code') }}</th>
                                                            <th>{{ __('encounter.service_description') }}</th>
                                                            <th>{{ __('encounter.type') }}</th>
                                                            <th>{{ __('encounter.status') }}</th>
                                                            <th>{{ __('encounter.request_date') }}</th>
                                                            <th>{{ __('encounter.priority') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($encounter->serviceRequests as $service)
                                                            <tr>
                                                                <td>{{ $service->code }}</td>
                                                                <td>{{ $service->cpt->description_es }}</td>
                                                                <td>{{ \App\Enums\ServiceType::labelFromValue( $service->service_type) }}</td>
                                                                <td>
                                                                    @php
                                                                        $statusEnum = \App\Enums\ServiceRequestStatus::fromString($service->status);
                                                                    @endphp
                                                                    @if($statusEnum)
                                                                        <span class="badge {{ $statusEnum->badgeClass() }}">
                                                                            {{ $statusEnum->label() }}
                                                                        </span>
                                                                    @else
                                                                        <span class="badge bg-secondary">
                                                                            {{ ucfirst($service->status) }}
                                                                        </span>
                                                                    @endif
                                                                </td>

                                                                <td>{{ $service->authored_on ? $service->authored_on->format('Y-m-d') : 'N/A' }}</td>
                                                                <td>
                                                                    @php
                                                                        $priorityEnum = \App\Enums\ServiceRequestPriority::fromString($service->priority);
                                                                    @endphp
                                                                    @if($priorityEnum)
                                                                        <span class="badge {{ $priorityEnum->badgeClass() }}">
                                                                            {{ $priorityEnum->label() }}
                                                                        </span>
                                                                    @else
                                                                        <span class="badge bg-secondary">
                                                                            {{ ucfirst($service->priority) }}
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if($encounter->referrals->count() > 0)
                                <!-- Referrals Tab -->
                                <div class="tab-pane fade" id="referrals" role="tabpanel" aria-labelledby="referrals-tab">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('encounter.specialty') }}</th>
                                                            <th>{{ __('encounter.reason') }}</th>
                                                            <th>{{ __('encounter.status') }}</th>
                                                            <th>{{ __('encounter.request_date') }}</th>
                                                            <th>{{ __('encounter.referred_to') }}</th>
                                                            <th>{{ __('encounter.priority') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($encounter->referrals as $referral)
                                                            <tr>
                                                                <td>{{ $referral->speciality->name }}</td>
                                                                <td>{{ $referral->reason }}</td>
                                                                <td>
                                                                    <span class="badge badge-{{ $referral->status == 'active' ? 'success' : 'secondary' }}">
                                                                        {{ ucfirst($referral->status) }}
                                                                    </span>
                                                                </td>
                                                                <td>{{$referral->referredTo->name}}</td>
                                                                <td>{{ $referral->ocurrence_date ? $referral->ocurrence_date->format('Y-m-d H:i') : 'N/A' }}</td>
                                                                <td>{{ ucfirst($referral->priority) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="text-center mt-4">

                                <a href="{{ route('consultation.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> {{ __('generic.back') }}
                                </a>
                                @if($encounter->medicationRequests->count() > 0)
                                    <a href="{{route('prescription.download',$encounter->id)}}" target="_blank" class="btn btn-primary"><i class="fa fa-file-pdf"></i> {{__('Descargar receta')}} </a>
                                @endif
                                @if($encounter->serviceRequests->count() > 0)
                                    @php
                                        // Agrupar service requests por tipo
                                        $servicesByType = $encounter->serviceRequests->groupBy('service_type');
                                    @endphp

                                    @foreach($servicesByType as $serviceType => $services)
                                        <a href="{{route('medical-order.download',$encounter->id)}}?service_type={{$serviceType}}"
                                           target="_blank"
                                           class="btn btn-primary">
                                            <i class="fa fa-file-pdf"></i> Descargar Orden {{ \App\Enums\ServiceType::pluralLabelFromValue($serviceType) }}
                                        </a>
                                    @endforeach
                                @endif
                                <a href="{{ route('consultation.download_resumen', $encounter->appointment_id) }}" class="btn btn-primary" target="_blank">
                                    <i class="fa fa-download"></i> {{ __('consultation.download_resumen') }}
                                </a>

                                @php
                                    // Check if there are unsent prescriptions or service requests
                                    $hasUnsentMedications = $encounter->medicationRequests->whereNull('notification_sent_at')->count() > 0;
                                    $hasUnsentServiceRequests = $encounter->serviceRequests->whereNull('notification_sent_at')->count() > 0;
                                    $canResendWhatsApp = $hasUnsentMedications || $hasUnsentServiceRequests;
                                @endphp

                                @if($canResendWhatsApp && ($encounter->patient->whatsapp_phone || $encounter->patient->phone))
                                    <form action="{{ route('consultation.resend-whatsapp', $encounter->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Está seguro de enviar las prescripciones por WhatsApp al paciente?');">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="fab fa-whatsapp"></i> Enviar Recetas por WhatsApp
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
</x-app-layout>
