<div class="consultation-ficha">
    <!-- Consultation Header CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/consultation-header.css') }}">
    <!-- Consultation Info Section -->
    <div class="consultation-ficha-contenido">
        <div class="consultation-ficha-item">
            <i class="fas fa-file-medical consultation-ficha-icon"></i>
            <b>{{ __('consultation.header.consultation_number') }}</b> {{ $encounter->id }}
        </div>
        <div class="consultation-ficha-item">
            <i class="fas fa-calendar-alt consultation-ficha-icon"></i>
            <b>{{ __('consultation.header.date_of_service') }}</b> {{ $encounter->created_at }}
        </div>
        <div class="consultation-ficha-item">
            <i class="fas fa-building consultation-ficha-icon"></i>
            <b>{{ __('consultation.header.consulting_room') }}</b> {{ $encounter->appointment->consultingRoom->name }}
        </div>
        <div class="consultation-ficha-item">
            <i class="fas fa-cocktail consultation-ficha-icon"></i>
            <b>{{ __('consultation.header.service_type') }}</b> {{ $encounter->appointment->service_type }}
        </div>
    </div>

    <!-- Patient Info Section -->
    <div class="consultation-ficha-contenido">
        <div class="consultation-ficha-item">
            <i class="fas fa-user consultation-ficha-icon"></i>
                <b>{{ __('consultation.header.patient') }}</b>
                <span class="profile-image">{!!  $patient->profile_name!!}</span>
        </div>
        <div class="consultation-ficha-item">
            <i class="fas fa-birthday-cake consultation-ficha-icon"></i>
                <b>{{ __('consultation.header.age') }}</b> {{ \Carbon\Carbon::parse($patient->birth_date)->age }} {{ __('consultation.header.years') }}
        </div>
        <div class="consultation-ficha-item">
            <i class="fas fa-venus-mars consultation-ficha-icon"></i>
                <b>{{ __('consultation.header.gender') }}</b> {{ $patient->gender }}
        </div>
        {{--}}
        <div class="consultation-ficha-item">
            <i class="fas fa-shield-alt consultation-ficha-icon"></i>
            <div>
                <b>Seguro:</b> {{ $patient->patient_type }}
            </div>
        </div>
        {{--}}
        <div class="consultation-ficha-item">
            <i class="fas fa-calendar consultation-ficha-icon"></i>
                <b>{{ __('consultation.header.birth_date') }}</b> {{ \Carbon\Carbon::parse($patient->birthdate)->format('d-m-Y') }}
        </div>
        <div class="consultation-ficha-item">
            <i class="fas fa-id-card consultation-ficha-icon"></i>
                <b>{{$patient->identifier_type}}:</b> {{ $patient->identifier }}
        </div>
    </div>

    <!-- Doctor Info Section -->
    <div class="consultation-ficha-contenido">
        <div class="consultation-ficha-item">
            <i class="fas fa-user-md consultation-ficha-icon"></i>
                <b>{{ __('consultation.header.doctor') }}</b>
                <span class="profile-image">{!! $encounter->practitioner->profile_name  !!}</span>
        </div>
        @if( $appointment->practitioner->qualifications()->first())
        <div class="consultation-ficha-item">
            <i class="fas fa-stethoscope consultation-ficha-icon"></i>
                <b>{{ __('consultation.header.speciality') }}</b> {{ $appointment->medicalSpeciality->name }}
        </div>
        @endif
    </div>


</div>

