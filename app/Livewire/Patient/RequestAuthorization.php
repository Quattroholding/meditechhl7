<?php

namespace App\Livewire\Patient;

use App\Models\AuthorizationCode;
use App\Models\Encounter;
use App\Models\Patient;
use App\Models\PatientPractitionerAuthorization;
use App\Models\Practitioner;
use App\Notifications\PatientAuthorizationCodeNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class RequestAuthorization extends Component
{
    public Patient $patient;

    public Practitioner $practitioner;

    public $digit1 = '';

    public $digit2 = '';

    public $digit3 = '';

    public $digit4 = '';

    public $showModal = false;

    public $showCodeInput = false;

    public $isAuthorized = false;

    public $codeSent = false;

    public function mount(Patient $patient)
    {
        $this->patient = $patient;
        $this->practitioner = Auth::user()->practitioner;

        // Verificar si ya está autorizado
        $this->isAuthorized = PatientPractitionerAuthorization::isAuthorized(
            $this->patient->id,
            $this->practitioner->id
        );
    }

    /**
     * Verifica si hay encounters adicionales con otros médicos
     */
    #[Computed]
    public function hasAdditionalEncounters()
    {
        // Obtener encounters sin el scope global, excluyendo los del médico actual
        $additionalEncountersCount = Encounter::withoutGlobalScopes()
            ->where('patient_id', $this->patient->id)
            ->where('practitioner_id', '!=', $this->practitioner->id)
            ->count();

        return $additionalEncountersCount > 0;
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->showCodeInput = false;
        $this->resetDigits();
    }

    private function resetDigits()
    {
        $this->digit1 = '';
        $this->digit2 = '';
        $this->digit3 = '';
        $this->digit4 = '';
    }

    public function requestCode()
    {
        try {
            // Generar código de autorización
            $authorizationCode = AuthorizationCode::generate(
                $this->patient->id,
                $this->practitioner->id
            );

            // Enviar notificación al paciente
            $this->patient->notify(
                new PatientAuthorizationCodeNotification($authorizationCode, $this->practitioner)
            );

            $this->codeSent = true;
            $this->showCodeInput = true;

            $this->dispatch('showToastrCodeSent',
                type: 'success',
                message: '✅ Se ha enviado un código de autorización al correo del paciente.'
            );
        } catch (\Exception $e) {
            $this->dispatch('showToastrCodeSent',
                type: 'error',
                message: '❌ Error al enviar el código: '.$e->getMessage()
            );
        }
    }

    public function verifyCode()
    {
        // Validar que todos los dígitos estén completos
        $this->validate([
            'digit1' => 'required|digits:1',
            'digit2' => 'required|digits:1',
            'digit3' => 'required|digits:1',
            'digit4' => 'required|digits:1',
        ], [
            'digit1.required' => 'Complete el código',
            'digit2.required' => 'Complete el código',
            'digit3.required' => 'Complete el código',
            'digit4.required' => 'Complete el código',
        ]);

        // Concatenar los 4 dígitos
        $code = $this->digit1.$this->digit2.$this->digit3.$this->digit4;

        // Buscar código válido
        $authorizationCode = AuthorizationCode::findValidCode(
            $this->patient->id,
            $this->practitioner->id,
            $code
        );

        if (! $authorizationCode) {
            $this->addError('digit1', 'Código inválido o expirado');
            $this->resetDigits();

            return;
        }

        // Marcar código como usado
        $authorizationCode->markAsUsed();

        // Crear autorización permanente
        PatientPractitionerAuthorization::create([
            'patient_id' => $this->patient->id,
            'practitioner_id' => $this->practitioner->id,
            'authorized_by_user_id' => Auth::id(),
            'authorized_at' => now(),
        ]);

        $this->isAuthorized = true;
        $this->showCodeInput = false;
        $this->closeModal();

        $this->dispatch('showToastrPatientRequestAutorization',
            type: 'success',
            message: '✅ Autorización concedida. Ahora tiene acceso completo al historial clínico de este paciente. En unos mommentos se recargaran los datos.'
        );
        // session()->flash('message', '✅ Autorización concedida. Ahora tiene acceso completo al historial clínico de este paciente.');

        // Refrescar la página para mostrar el historial completo
        // $this->dispatch('authorization-granted');
    }

    public function render()
    {
        return view('livewire.patient.request-authorization');
    }
}
