<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use Livewire\Attributes\On;
use Livewire\Component;

class FinishedButton extends Component
{
    public $encounter_id;

    public $encounter;

    public $enabled = false;

    public $messages = [];

    public function mount()
    {

        $this->encounter = Encounter::find($this->encounter_id);

        $this->findFinishedButtonStatus();
    }

    public function render()
    {
        return view('livewire.consultation.finished-button');
    }

    #[On('findFinishedButtonStatus')]
    public function findFinishedButtonStatus()
    {
        if(auth()->user()->hasRole('asistente medico')){
            $this->enabled = $this->validateGeneralNote();
        }else{
            $this->enabled = $this->validateReason() && $this->validatePresentIllnesses() && $this->validateCondition() && $this->validateMedicationRequests() && $this->validateReferrals();
        }
    }

    public function validateReason()
    {

        $return = true;

        if (! empty($this->encounter->reason)) {
            unset($this->messages[1]);
        } else {
            $return = false;
            $this->messages[1] = '- Motivo de Consulta';
        }

        return $return;
    }

    public function validatePresentIllnesses()
    {

        $return = true;
        if (! $this->encounter->presentIllnesses) {
            $return = false;
            $this->messages[2] = '- Ubicación';
            $this->messages[3] = '- Gravedad';
            $this->messages[4] = '- Duración';
            $this->messages[5] = '- Momento';
            $this->messages[6] = '- Descripción';
        } else {
            if (empty($this->encounter->presentIllnesses->location)) {
                $this->messages[2] = '- Ubicación';
                $return = false;
            } else {
                unset($this->messages[2]);
            }
            if (empty($this->encounter->presentIllnesses->severity)) {
                $this->messages[3] = '- Gravedad';
                $return = false;
            } else {
                unset($this->messages[3]);
            }
            if (empty($this->encounter->presentIllnesses->duration)) {
                $this->messages[4] = '- Duración';
                $return = false;
            } else {
                unset($this->messages[4]);
            }
            if (empty($this->encounter->presentIllnesses->timing)) {
                $this->messages[5] = '- Momento';
                $return = false;
            } else {
                unset($this->messages[5]);
            }
            if (empty($this->encounter->presentIllnesses->description)) {
                $this->messages[6] = '- Descripción';
                $return = false;
            } else {
                unset($this->messages[6]);
            }
        }

        return $return;
    }

    public function validateCondition()
    {

        if ($this->encounter->diagnoses->count() > 0) {
            unset($this->messages[7]);

            return true;
        } else {
            $this->messages[7] = '- Al menos un diagnostico';

            return false;
        }
    }

    public function validateMedicationRequests()
    {
        $medicationRequests = $this->encounter->medicationRequests();

        // Si no hay medicamentos agregados, la validación pasa
        if ($medicationRequests->count() === 0) {
            unset($this->messages[8]);

            return true;
        }

        $incompleMedications = [];

        foreach ($medicationRequests->get() as $medication) {
            $missingFields = [];

            if (empty($medication->route)) {
                $missingFields[] = 'vía';
            }

            if (empty($medication->frequency)) {
                $missingFields[] = 'frecuencia';
            }

            if (empty($medication->quantity)) {
                $missingFields[] = 'cantidad';
            }

            if (empty($medication->duration)) {
                $missingFields[] = 'duración';
            }

            if (! empty($missingFields)) {
                $medicationName = $medication->medicine->full_name ?? 'Medicamento';
                $incompleMedications[] = $medicationName.' ('.implode(', ', $missingFields).')';
            }
        }

        if (! empty($incompleMedications)) {
            $this->messages[8] = '- Medicamentos incompletos: '.implode(', ', $incompleMedications);

            return false;
        } else {
            unset($this->messages[8]);

            return true;
        }
    }

    public function validateReferrals()
    {
        $referrals = $this->encounter->referrals();

        // Si no hay referrals agregados, la validación pasa
        if ($referrals->count() === 0) {
            unset($this->messages[9]);

            return true;
        }

        $incompleteReferrals = [];

        foreach ($referrals->get() as $referral) {
            $missingFields = [];

            if (empty($referral->reason)) {
                $missingFields[] = 'motivo de referencia';
            }

            /*if (empty($referral->referred_to_id)) {
                $missingFields[] = 'especialista asignado';
            }*/

            if (! empty($missingFields)) {
                $specialtyName = $referral->speciality->name ?? 'Especialidad desconocida';
                $incompleteReferrals[] = $specialtyName.' ('.implode(', ', $missingFields).')';
            }
        }

        if (! empty($incompleteReferrals)) {
            $this->messages[9] = '- Referencias incompletas: '.implode(', ', $incompleteReferrals);

            return false;
        } else {
            unset($this->messages[9]);

            return true;
        }
    }

    public function validateGeneralNote()
    {

        $return = true;

        if (! empty($this->encounter->general_note)) {
            unset($this->messages[1]);
        } else {
            $return = false;
            $this->messages[1] = '- Nota General';
        }

        return $return;
    }
}
