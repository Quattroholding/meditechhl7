<?php

namespace App\Livewire\Consultation;

use App\Models\Appointment;
use App\Services\JitsiService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class VirtualConsultationRoom extends Component
{
    public Appointment $appointment;

    public array $jitsiConfig;

    public bool $isDoctor;

    public bool $sessionActive = false;

    public string $displayMode = 'sidebar'; // 'sidebar', 'modal', 'fullscreen'

    public string $patientJoinUrl = '';

    protected JitsiService $jitsiService;

    public function boot(JitsiService $jitsiService)
    {
        $this->jitsiService = $jitsiService;
    }

    public function mount(Appointment $appointment, string $displayMode = 'sidebar')
    {
        $this->appointment = $appointment;

        // Check if user is a doctor (has 'doctor' role) or is the practitioner for this appointment
        $user = Auth::user();
        $isPractitionerForThisAppointment = $user && $appointment->practitioner_id &&
                                             $user->practitioner &&
                                             $user->practitioner->id === $appointment->practitioner_id;

        $this->isDoctor = Auth::check() && (Auth::user()->hasRole('doctor') || $isPractitionerForThisAppointment);

        \Log::info('VirtualConsultationRoom mounted', [
            'isDoctor' => $this->isDoctor,
            'hasRole' => Auth::check() ? Auth::user()->hasRole('doctor') : false,
            'isPractitioner' => $isPractitionerForThisAppointment,
            'userId' => Auth::id(),
            'appointmentPractitioner' => $appointment->practitioner_id,
        ]);

        $this->displayMode = $displayMode;

        // Generate patient join URL with secure token
        $token = hash_hmac('sha256', $this->appointment->id.$this->appointment->patient_id, config('app.key'));
        $this->patientJoinUrl = route('virtual-consultation.join', [
            'appointment' => $this->appointment->id,
            'token' => $token,
        ]);

        // Create Jitsi room if it doesn't exist
        if (! $this->appointment->virtual_room_id) {
            try {
                $this->jitsiService->createRoom($this->appointment);
                $this->appointment->refresh();
            } catch (\Exception $e) {
                \Log::error('Failed to create Jitsi room', ['error' => $e->getMessage()]);
                $this->dispatch('error', message: 'Error al crear la sala de videoconferencia');
            }
        }

        // Check if session is already active
        $this->sessionActive = $this->appointment->hasActiveVirtualSession();

        // Get the appropriate display name based on user role
        if (Auth::check()) {
            $displayName = $this->isDoctor
                ? ($this->appointment->practitioner->name ?? Auth::user()->name)
                : ($this->appointment->patient->name ?? Auth::user()->name);
            $userEmail = Auth::user()->email ?? '';
            $userId = Auth::id();
        } else {
            // For non-authenticated users (patients joining via link)
            $displayName = $this->appointment->patient->name;
            $userEmail = $this->appointment->patient->email ?? '';
            $userId = 'patient_'.$this->appointment->patient_id;
        }

        // Prepare Jitsi configuration
        try {
            $this->jitsiConfig = $this->jitsiService->getJitsiConfig(
                $this->appointment,
                [
                    'name' => $displayName,
                    'email' => $userEmail,
                    'id' => $userId,
                    'is_moderator' => $this->isDoctor,
                ]
            );
        } catch (\Exception $e) {
            \Log::error('Failed to get Jitsi config', ['error' => $e->getMessage()]);
            $this->dispatch('error', message: 'Error al cargar configuración de Jitsi');
        }
    }

    public function startSession()
    {
        if (! $this->isDoctor) {
            \Log::warning('Non-doctor user tried to start session', [
                'userId' => Auth::id(),
                'appointmentId' => $this->appointment->id,
            ]);
            $this->dispatch('error', message: 'Solo el médico puede iniciar la consulta');

            return;
        }

        try {
            $this->appointment->update([
                'virtual_session_started_at' => now(),
            ]);

            $this->sessionActive = true;

            \Log::info('Virtual session started', [
                'appointmentId' => $this->appointment->id,
                'doctorId' => Auth::id(),
                'timestamp' => now(),
            ]);

            $this->dispatch('info', message: 'Sesión iniciada. El paciente puede unirse ahora.');
        } catch (\Exception $e) {
            \Log::error('Failed to start session', [
                'error' => $e->getMessage(),
                'appointmentId' => $this->appointment->id,
            ]);
            $this->dispatch('error', message: 'Error al iniciar la sesión');
        }
    }

    public function endSession()
    {
        if (! $this->isDoctor) {
            $this->dispatch('error', message: 'Solo el médico puede finalizar la consulta');

            return;
        }

        $duration = null;
        if ($this->appointment->virtual_session_started_at) {
            $duration = now()->diffInMinutes($this->appointment->virtual_session_started_at);
        }

        $this->appointment->update([
            'virtual_session_ended_at' => now(),
            'virtual_session_metadata' => array_merge(
                $this->appointment->virtual_session_metadata ?? [],
                [
                    'duration_minutes' => $duration,
                    'ended_by' => Auth::check() ? Auth::user()->name : 'System',
                ]
            ),
        ]);

        $this->sessionActive = false;
    }

    public function joinSession()
    {
        $this->sessionActive = true;
    }

    public function toggleDisplayMode()
    {
        $modes = ['sidebar', 'modal', 'fullscreen'];
        $currentIndex = array_search($this->displayMode, $modes);
        $nextIndex = ($currentIndex + 1) % count($modes);
        $this->displayMode = $modes[$nextIndex];

        $this->dispatch('jitsi-display-mode-changed', mode: $this->displayMode);
    }

    public function render()
    {
        return view('livewire.consultation.virtual-consultation-room');
    }
}
