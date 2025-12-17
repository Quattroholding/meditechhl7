<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use App\Models\PresentIllnesType;
use Illuminate\Support\Str;
use Livewire\Component;

class PresentIllness extends Component
{
    public $present_illness;

    public $reason;

    public $encounter_id;

    public $encounter;

    public $location = [];

    public $severity;

    public $duration;

    public $timing;

    public $description;

    public $aggravating_factors;

    public $alleviating_factors;

    public $associated_symptoms;

    public $items = [];

    public function mount()
    {
        $this->encounter = Encounter::find($this->encounter_id);
        $this->present_illness = $this->encounter->presentIllnesses;
        $this->loadPressentIllness();
    }

    public function loadPressentIllness()
    {
        $arr = ['location' => 'Ubicación', 'severity' => 'Gravedad', 'duration' => 'Duración', 'timing' => 'Momento'];
        $this->location = [];
        foreach ($arr as $key => $value) {
            $this->items[$key]['title'] = $value;
            $this->items[$key]['items'] = PresentIllnesType::whereType($key)->orderBy('value_esp')->get();
            if ($this->present_illness) {
                if ($key == 'location') {
                    $this->location[$key] = $this->encounter->presentIllnesses->locations;
                } else {
                    $this->$key = $this->encounter->presentIllnesses->$key;
                }
            }
        }

        if ($this->present_illness) {
            $this->description = $this->present_illness->description;
            $this->aggravating_factors = $this->present_illness->aggravating_factors;
            $this->alleviating_factors = $this->present_illness->alleviating_factors;
            $this->associated_symptoms = $this->present_illness->associated_symptoms;
        }
    }

    public function render()
    {
        return view('livewire.consultation.present-illness');
    }

    public function create()
    {
        $this->present_illness = $this->encounter->presentIllnesses()->create([
            'fhir_id' => 'condition-'.Str::uuid(),
            'description' => '',
            'location' => $this->location,
            'severity' => $this->severity,
            'duration' => $this->duration,
            'timing' => $this->timing,
            'patient_id' => $this->encounter->patient_id,
            'practitioner_id' => $this->encounter->practitioner_id,
        ]);
    }

    public function save($type, $value, $multiple = false)
    {

        if ($type == 'location') {
            $this->location = $value;
        }
        if ($type == 'severity') {
            $this->severity = $value;
        }
        if ($type == 'duration') {
            $this->duration = $value;
        }
        if ($type == 'timing') {
            $this->timing = $value;
        }

        if (! isset($this->encounter->presentIllnesses->fhir_id)) {
            $this->create();
        } else {
            $locations = null;
            if ($type == 'location') {
                $locations = $this->encounter->presentIllnesses->addLocationIfMissing($this->location);
                $this->encounter->presentIllnesses->locations = $locations;
            }

            $this->encounter->presentIllnesses->location = $this->location;
            $this->encounter->presentIllnesses->severity = $this->severity;
            $this->encounter->presentIllnesses->duration = $this->duration;
            $this->encounter->presentIllnesses->timing = $this->timing;
            $this->encounter->presentIllnesses->save();

        }

        $this->loadPressentIllness();
        $this->dispatch('findFinishedButtonStatus');
    }

    public function delete($type, $value)
    {

        $locations = [];
        foreach ($this->encounter->presentIllnesses->locations as $l) {
            if ($l != $value) {
                $locations[] = $l;
            }
        }

        $this->encounter->presentIllnesses->locations = $locations;
        $this->encounter->presentIllnesses->save();
        $this->loadPressentIllness();
    }

    public function updatedAggravatingFactors()
    {
        $this->saveAggravatingFactors();
    }

    public function updatedAlleviatingFactors()
    {
        $this->saveAlleviatingFactors();
    }

    public function updatedAssociatedSymptoms()
    {
        $this->saveAssociatedSymptoms();
    }

    public function updatedDescription()
    {
        $this->saveDescription();
    }

    public function saveAggravatingFactors()
    {
        $key = 'aggravating_factors';

        try {
            // Delay para que el usuario vea el spinner "Guardando..."
            sleep(1);

            if (! $this->encounter->presentIllnesses) {
                $this->create();
            } else {
                $this->present_illness->aggravating_factors = $this->aggravating_factors;
                $this->present_illness->save();
            }

            $this->dispatch('saved-'.$key);

        } catch (\Exception $e) {
            $this->dispatch('error-'.$key,$e->getMessage());
        }
    }

    public function saveAlleviatingFactors()
    {
        $key = 'alleviating_factors';

        try {
            // Delay para que el usuario vea el spinner "Guardando..."
            sleep(1);

            if (! $this->encounter->presentIllnesses) {
                $this->create();
            } else {
                $this->present_illness->alleviating_factors = $this->alleviating_factors;
                $this->present_illness->save();
            }

            $this->dispatch('saved-'.$key);
        } catch (\Exception $e) {
            $this->dispatch('error-'.$key,$e->getMessage());
        }
    }

    public function saveAssociatedSymptoms()
    {
        $key = 'associated_symptoms';
        try {
            // Delay para que el usuario vea el spinner "Guardando..."
            sleep(1);

            if (! $this->encounter->presentIllnesses) {
                $this->create();
            } else {
                $this->present_illness->associated_symptoms = $this->associated_symptoms;
                $this->present_illness->save();
            }

            $this->dispatch('saved-'.$key);
        } catch (\Exception $e) {
            $this->dispatch('error-'.$key,$e->getMessage());
        }
    }

    public function saveDescription()
    {
        $key = 'description';

        try {
            // Delay para que el usuario vea el spinner "Guardando..."
            sleep(1);

            if (! $this->encounter->presentIllnesses) {
                $this->create();
            } else {
                $this->present_illness->description = $this->description;
                $this->present_illness->save();
            }

            $this->dispatch('saved-'.$key);

            $this->dispatch('findFinishedButtonStatus');
        } catch (\Exception $e) {
            $this->dispatch('error-'.$key,$e->getMessage());
        }
    }
}
