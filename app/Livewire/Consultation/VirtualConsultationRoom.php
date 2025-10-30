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
        $this->isDoctor = Auth::check() && Auth::user()->hasRole('doctor');
        $this->displayMode = $displayMode;

        // Generate patient join URL with secure token
        $token = hash_hmac('sha256', $this->appointment->id.$this->appointment->patient_id, config('app.key'));
        $this->patientJoinUrl = route('virtual-consultation.join', [
            'appointment' => $this->appointment->id,
            'token' => $token,
        ]);

        // Create room if it doesn't exist
        if (! $this->appointment->virtual_room_id) {
            $this->jitsiService->createRoom($this->appointment);
            $this->appointment->refresh();
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
        $this->jitsiConfig = $this->jitsiService->getJitsiConfig(
            $this->appointment,
            [
                'name' => $displayName,
                'email' => $userEmail,
                'id' => $userId,
                'is_moderator' => $this->isDoctor,
                'can_record' => $this->isDoctor,
            ]
        );
    }

    public function startSession()
    {
        if (! $this->isDoctor) {
            $this->dispatch('error', message: 'Solo el médico puede iniciar la consulta');

            return;
        }

        $this->appointment->update([
            'virtual_session_started_at' => now(),
        ]);

        $this->sessionActive = true;
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
