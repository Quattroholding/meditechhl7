<div class="consultation-ficha">
    <!-- Consultation Header CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/consultation-header.css') }}">
    <!-- Consultation Info Section -->
    <div class="consultation-ficha-contenido">
        <div class="consultation-ficha-item">
            <i class="fas fa-file-medical consultation-ficha-icon"></i>
            <b>Consulta #</b> {{ $encounter->id }}
        </div>
        <div class="consultation-ficha-item">
            <i class="fas fa-calendar-alt consultation-ficha-icon"></i>
            <b>Fecha de Atención:</b> {{ $encounter->created_at }}
        </div>
        <div class="consultation-ficha-item">
            <i class="fas fa-building consultation-ficha-icon"></i>
            <b>Consultorio:</b> {{ $encounter->appointment->consultingRoom->name }}
        </div>
        <div class="consultation-ficha-item">
            <i class="fas fa-cocktail consultation-ficha-icon"></i>
            <b>Tipo de Servicio:</b> {{ $encounter->appointment->service_type }}
        </div>
    </div>

    <!-- Patient Info Section -->
    <div class="consultation-ficha-contenido">
        <div class="consultation-ficha-item">
            <i class="fas fa-user consultation-ficha-icon"></i>
                <b>Paciente:</b>
                <span class="profile-image">{!!  $patient->profile_name!!}</span>
        </div>
        <div class="consultation-ficha-item">
            <i class="fas fa-birthday-cake consultation-ficha-icon"></i>
                <b>Edad:</b> {{ \Carbon\Carbon::parse($patient->birth_date)->age }} años
        </div>
        <div class="consultation-ficha-item">
            <i class="fas fa-venus-mars consultation-ficha-icon"></i>
                <b>Género:</b> {{ $patient->gender }}
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
                <b>Fecha Nacimiento:</b> {{ \Carbon\Carbon::parse($patient->birthdate)->format('d-m-Y') }}
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
                <b>Doctor:</b>
                <span class="profile-image">{!! $encounter->practitioner->profile_name  !!}</span>
        </div>
        @if( $appointment->practitioner->qualifications()->first())
        <div class="consultation-ficha-item">
            <i class="fas fa-stethoscope consultation-ficha-icon"></i>
                <b>Especialidad:</b> {{ $appointment->medicalSpeciality->name }}
        </div>
        @endif
    </div>


</div>

