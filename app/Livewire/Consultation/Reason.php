<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Reason extends Component
{
    public $reason;

    public $encounter_id;

    public $encounter;

    public $section_name;

    public $section_id;

    protected $listeners = ['voice-dictation-reason' => 'updateFromVoice'];

    public function mount()
    {
        $this->encounter = Encounter::find($this->encounter_id);
        $this->reason = $this->encounter->reason;

    }

    /**
     * Update reason from voice dictation
     * Only updates if reason is currently empty to preserve original reason
     */
    public function updateFromVoice($data)
    {
        // Only update if current reason is empty (preserve original reason)
        if (empty($this->reason) && isset($data['reason']) && ! empty($data['reason'])) {
            $this->reason = $data['reason'];
            $this->save();

            \Log::info('Reason updated from voice dictation', [
                'encounter_id' => $this->encounter_id,
                'reason' => $this->reason,
            ]);
        } else {
            \Log::info('Reason NOT updated - already exists', [
                'encounter_id' => $this->encounter_id,
                'existing_reason' => $this->reason,
                'dictated_reason' => $data['reason'] ?? null,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.consultation.reason');
    }

    public function updatedReason()
    {
        $this->save();
    }

    public function save()
    {
        $key = 'reason';
        try {
            // Delay para que el usuario vea el spinner "Guardando..."
            sleep(1);

            $this->encounter->reason = $this->reason;
            $this->encounter->save();

            $this->dispatch('saved-'.$key);

            // Emitir evento al componente padre para actualizar el checkmark de la sección completada
            $this->dispatch('sectionDataSaved', sectionId: $this->section_id);

            // Emitir evento al componente padre para calcular si el button finished se debe habilitar
            $this->dispatch('findFinishedButtonStatus');
        } catch (\Exception $e) {
            Log::error('Error guardando razón de consulta en Reason', [
                'encounter_id' => $this->encounter_id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('error-'.$key, $e->getMessage());
        }
    }
}
