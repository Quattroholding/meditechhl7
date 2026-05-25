<?php

namespace App\Livewire\Consultation;

use App\Models\ClinicalObservationType;
use App\Models\Encounter;
use Illuminate\Support\Str;
use Livewire\Component;

class VitalSigns extends Component
{
    public $reason;

    public $encounter_id;

    public $encounter;

    public $items;

    public $values = [];

    protected $listeners = ['voice-dictation-vital-signs' => 'updateFromVoice'];

    public function mount()
    {
        $this->encounter = Encounter::find($this->encounter_id);

        $this->items = ClinicalObservationType::whereCategory('vital_sign')->get();

        foreach ($this->items as $i) {
            $this->values[$i->code] = $this->encounter->vitalSigns()->whereCode($i->code)->first()?->value ?? '';
        }
    }

    /**
     * Update vital signs from voice dictation
     */
    public function updateFromVoice($data)
    {
        foreach ($data as $code => $value) {
            // Validate that the code exists in our items and value is numeric
            if (isset($this->values[$code]) && ! empty($value) && is_numeric($value)) {
                $this->values[$code] = $value;
                $this->save($code);
            } else {
                // Log skipped non-numeric values for debugging
                if (isset($this->values[$code]) && ! empty($value) && ! is_numeric($value)) {
                    \Log::info('Skipped non-numeric vital sign from voice dictation', [
                        'code' => $code,
                        'value' => $value,
                        'encounter_id' => $this->encounter_id,
                    ]);
                }
            }
        }
    }

    public function updatedValues($value, $code)
    {
        $this->save($code);
    }

    public function render()
    {
        return view('livewire.consultation.vital-signs');
    }

    public function save($code)
    {
        $key = "vital_sign_{$code}";
        try {
            // Validate that value is numeric before attempting to save
            if (! empty($this->values[$code]) && ! is_numeric($this->values[$code])) {
                throw new \InvalidArgumentException(
                    "El valor del signo vital debe ser numérico. Valor recibido: {$this->values[$code]}"
                );
            }

            $vs = $this->encounter->vitalSigns()->whereEncounterId($this->encounter_id)->whereCode($code)->first();

            if (! empty($this->values[$code])) {
                if (! $vs) {
                    $vsType = ClinicalObservationType::whereCode($code)->first();
                    $this->encounter->vitalSigns()->create([
                        'fhir_id' => 'observation-'.Str::uuid(),
                        'code' => $code,
                        'status' => 'final',
                        'category' => 'vital-signs',
                        'value' => $this->values[$code],
                        'unit' => $vsType->default_unit,
                        'effective_date' => now(),
                        'issued_date' => now(),
                        'patient_id' => $this->encounter->patient_id,
                        'practitioner_id' => $this->encounter->practitioner_id,
                    ]);
                } else {
                    $vs->value = $this->values[$code];
                    $vs->save();
                }

                // Solo calcular IMC automáticamente cuando se modifique peso o estatura
                // No recalcular si se está editando directamente el IMC
                if (in_array($code, ['29463-7', '8302-2'])) {
                    $this->calculateIMC();
                }
            }

            // Actualizar el valor original después de guardar exitosamente
            // $this->originalValues[$code] = $this->values[$code];
            $this->dispatch('saved-'.$code);

        } catch (\Exception $e) {
            $this->dispatch('error-'.$code, $e->getMessage());
        }
    }

    private function calculateIMC()
    {
        // validar que si ya se guardo el peso corporal y la estatura se calcule automaticamente el imc
        if (! empty($this->values['29463-7']) && ! empty($this->values['8302-2'])) {
            $estaturaNmts = $this->values['8302-2'] / 100;
            $this->values['39156-5'] = floor($this->values['29463-7'] / ($estaturaNmts * $estaturaNmts));

            $imc = $this->encounter->vitalSigns()->whereEncounterId($this->encounter_id)->whereCode('39156-5')->first();

            if (! $imc) {
                $this->encounter->vitalSigns()->create([
                    'fhir_id' => 'observation-'.Str::uuid(),
                    'code' => '39156-5',
                    'status' => 'final',
                    'category' => 'vital-signs',
                    'value' => $this->values['39156-5'],
                    'unit' => 'kg/m²',
                    'effective_date' => now(),
                    'issued_date' => now(),
                    'patient_id' => $this->encounter->patient_id,
                    'practitioner_id' => $this->encounter->practitioner_id,
                ]);
            } else {
                $imc->value = $this->values['39156-5'];
                $imc->save();
            }
        }
    }
}
