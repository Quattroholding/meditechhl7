<?php

namespace App\Livewire\Consultation;

use App\Models\ClinicalObservationType;
use App\Models\Encounter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;

class PhysicalExam extends Component
{
    public $encounter_id;

    public $encounter;

    public $items = [];

    public $values = [];

    // Nuevas propiedades para sugerencias
    public $activeInputCode = null;

    public $suggestedAnswered = '';

    protected $listeners = ['voice-dictation-physical-exam' => 'updateFromVoice'];

    public function mount()
    {
        $this->encounter = Encounter::find($this->encounter_id);

        $this->items = ClinicalObservationType::whereCategory('physical_exam')->get();

        foreach ($this->items as $i) {
            $result = $this->encounter->physicalExams()->whereCode($i->code)->first();
            $this->values[$i->code] = '';
            if ($result) {
                if (is_array($result->finding)) {
                    foreach ($result->finding as $key => $value) {

                        $this->values[$i->code] .= $value;
                    }
                }
            }
        }
    }

    /**
     * Update physical exam findings from voice dictation
     */
    public function updateFromVoice($data)
    {
        foreach ($data as $code => $finding) {
            // Validate that the code exists in our items
            if (isset($this->values[$code]) && ! empty($finding)) {
                $this->values[$code] = $finding;
                $this->save($code);
            }
        }
    }

    public function toggleInfo($code)
    {
        $this->activeInputCode = $code;
        $item = ClinicalObservationType::whereCode($code)->first();
        $this->suggestedAnswered = $item->default_answer_es;
    }

    public function updatedValues($value, $code)
    {
        $this->toggleInfo($code);
        $this->save($code);
    }

    public function usarSugerencia($code)
    {

        // Usar la primera sugerencia disponible (generalmente default_answer)
        $item = ClinicalObservationType::whereCode($code)->first();

        if ($item && ! empty($item->default_answer)) {
            $this->values[$code] = $item->default_answer_es;
            $this->cerrarSugerencias();
            $this->save($code);
        }
    }

    public function seleccionarSugerencia($texto, $code)
    {
        $this->values[$code] = $texto;
        $this->cerrarSugerencias();
        $this->save($code);
    }

    public function cerrarSugerencias()
    {
        $this->activeInputCode = null;
    }

    public function save($code)
    {
        $key = "physical_exam_{$code}";

        try {
            // Delay para que el usuario vea el spinner "Guardando..."
            sleep(1);

            $vs = $this->encounter->physicalExams()->whereEncounterId($this->encounter_id)->whereCode($code)->first();

            if (! $vs) {
                $vsType = ClinicalObservationType::whereCode($code)->first();
                $this->encounter->physicalExams()->create([
                    'fhir_id' => 'observation-'.Str::uuid(),
                    'code' => $code,
                    'status' => 'final',
                    'category' => 'exam',
                    'description' => $vsType->name.' realizado durante la consulta',
                    'finding' => ['text' => $this->values[$code]],
                    'effective_date' => now(),
                    'patient_id' => $this->encounter->patient_id,
                    'practitioner_id' => $this->encounter->practitioner_id,
                ]);
            } else {
                $vs->finding = ['text' => $this->values[$code]];
                $vs->save();
            }

            $this->dispatch('saved-'.$key);

        } catch (\Exception $e) {
            Log::error('Error guardando examen físico en PhysicalExam', [
                'encounter_id' => $this->encounter_id,
                'user_id' => Auth::id(),
                'observation_code' => $code,
                'value' => $this->values[$code] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('error-'.$key, $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.consultation.physical-exam');
    }
}
