@extends('layouts.public')

@section('content')
<!-- Appointment Details -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-lg">
            <div class="card-body">
                <h4 class="card-title mb-3">Tu Teleconsulta</h4>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Paciente:</strong> {{ $appointment->patient->name }}</p>
                        <p class="mb-1"><strong>Doctor:</strong> {{ $appointment->practitioner->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Fecha:</strong> {{ $appointment->start->format('d/m/Y') }}</p>
                        <p class="mb-1"><strong>Hora:</strong> {{ $appointment->start->format('H:i') }}</p>
                        <p class="mb-1"><strong>Especialidad:</strong> {{ $appointment->medicalSpeciality->name }}</p>
                    </div>
                </div>

                @if($appointment->virtual_session_started_at)
                    <div class="alert alert-success mt-3 mb-0">
                        <i class="fas fa-video me-2"></i>
                        La videoconsulta ha sido iniciada por el doctor. Haz clic en el botón de abajo para unirte.
                    </div>
                @else
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="fas fa-clock me-2"></i>
                        El doctor aún no ha iniciado la videoconsulta. Por favor espera a que inicie la sesión.
                    </div>
                @endif

                @php
                    $meetingPassword = $appointment->virtual_session_metadata['meeting_password'] ?? null;
                @endphp

                @if($meetingPassword)
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="fas fa-key me-2"></i>
                        <strong>Código de acceso de Zoom:</strong> <code style="background: #fff3cd; padding: 2px 6px; border-radius: 3px; font-family: monospace;">{{ $meetingPassword }}</code>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Instructions -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card shadow-lg">
            <div class="card-body">
                <h5 class="card-title">Instrucciones</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Asegúrate de tener una buena conexión a internet</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Permite el acceso a tu cámara y micrófono cuando se solicite</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Busca un lugar tranquilo y bien iluminado</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Ten a mano tus documentos médicos si es necesario</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Virtual Consultation Room Component -->
{{-- TODO: Cambiar a virtual-zoom-consultation-room cuando Zoom esté listo --}}
@livewire('consultation.virtual-zoom-consultation-room', [
    'appointment' => $appointment,
    'displayMode' => 'sidebar'
])

<script>
    // Auto-refresh every 10 seconds to check if doctor has started the session
    @if(!$appointment->virtual_session_started_at)
        setInterval(function() {
            location.reload();
        }, 10000);
    @endif
</script>
@endsection
