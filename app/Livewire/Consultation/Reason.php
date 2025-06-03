<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use Livewire\Component;

class Reason extends Component
{
    public $reason;
    public $encounter_id;
    public $encounter;
    public $section_name;
    public $section_id;
    public $saving = false;
    public $saved = false;

    public function mount(){
        $this->encounter = Encounter::find($this->encounter_id);
        $this->reason = $this->encounter->reason;

    }

    public function render()
    {
        return view('livewire.consultation.reason');
    }

    public function save()
    {
        $this->saved = false;
        // Simular guardado en base de datos
        // Aquí puedes guardar en tu modelo específico
        try {
            // Ejemplo: YourModel::updateOrCreate(['id' => $this->modelId], ['content' => $this->content]);
            $this->encounter->reason = $this->reason;
            $this->encounter->save();
            // Simular tiempo de guardado
            sleep(1);
            $this->saved = true;

        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    public function updatedReason(){
        $this->saved = false;
    }
}
