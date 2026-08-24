<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class GeneralNote extends Component
{
    public $general_note;

    public $encounter_id;

    public $encounter;

    public $section_name;

    public $section_id;

    public function mount()
    {
        $this->encounter = Encounter::find($this->encounter_id);
        $this->general_note = $this->encounter->general_note;

    }

    public function render()
    {
        return view('livewire.consultation.general-note');
    }

    public function updatedGeneralNote()
    {
        $this->save();
    }

    public function save()
    {
        $key = 'general_note';

        try {
            // Delay para que el usuario vea el spinner "Guardando..."
            sleep(1);

            $this->encounter->general_note = $this->general_note;
            $this->encounter->save();

            $this->dispatch('saved-'.$key);

            // Emitir evento al componente padre para actualizar el checkmark de la sección completada
            $this->dispatch('sectionDataSaved', sectionId: $this->section_id);

            // Emitir evento al componente padre para calcular si el button finished se debe habilitar
            $this->dispatch('findFinishedButtonStatus');
        } catch (\Exception $e) {
            Log::error('Error guardando nota general en GeneralNote', [
                'encounter_id' => $this->encounter_id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('error-'.$key, $e->getMessage());
        }
    }
}
