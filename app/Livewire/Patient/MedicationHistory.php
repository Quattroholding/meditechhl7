<?php

namespace App\Livewire\Patient;

use App\Models\MedicationRequest;
use App\Models\Patient;
use App\Models\Encounter;
use Livewire\Component;
use Carbon\Carbon;

class MedicationHistory extends Component
{
    public $showOffcanvas = false;
    public $patient_id;
    public $patient;
    public $medicationHistory = [];
    public $groupedByEncounter = [];
    public $selectedMedicationIds = [];
    public $selectedEncounterId = null;

    protected $listeners = ['showMedicationHistory', 'hideMedicationHistory'];

    public function showMedicationHistory($patientId)
    {
        $this->patient_id = $patientId;
        $this->patient = Patient::find($patientId);
        $this->loadMedicationHistory();
        $this->showOffcanvas = true;
    }

    public function hideMedicationHistory()
    {
        $this->showOffcanvas = false;
        $this->reset(['patient_id', 'patient', 'medicationHistory', 'groupedByEncounter', 'selectedMedicationIds', 'selectedEncounterId']);
    }

    public function loadMedicationHistory()
    {
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        
        $this->medicationHistory = MedicationRequest::with(['medicine', 'encounter', 'practitioner'])
            ->where('patient_id', $this->patient_id)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->orderBy('created_at', 'desc')
            ->get();

        $this->groupedByEncounter = $this->medicationHistory
            ->groupBy('encounter_id')
            ->map(function ($medications, $encounterId) {
                $encounter = Encounter::find($encounterId);
                return [
                    'encounter' => $encounter,
                    'medications' => $medications,
                    'date' => $encounter ? $encounter->created_at : null,
                ];
            })
            ->sortByDesc('date');
    }

    public function toggleMedicationSelection($medicationId)
    {
        if (in_array($medicationId, $this->selectedMedicationIds)) {
            $this->selectedMedicationIds = array_filter($this->selectedMedicationIds, fn($id) => $id != $medicationId);
        } else {
            $this->selectedMedicationIds[] = $medicationId;
        }
    }

    public function selectEntireRecipe($encounterId)
    {
        $this->selectedEncounterId = $encounterId;
        $encounterMedications = $this->groupedByEncounter[$encounterId]['medications'] ?? collect();
        
        $medicationIds = $encounterMedications->pluck('id')->toArray();
        
        if ($this->selectedEncounterId === $encounterId && 
            !empty(array_intersect($medicationIds, $this->selectedMedicationIds))) {
            // Si ya está seleccionada, deseleccionar
            $this->selectedMedicationIds = array_diff($this->selectedMedicationIds, $medicationIds);
            $this->selectedEncounterId = null;
        } else {
            // Seleccionar toda la receta
            $this->selectedMedicationIds = array_unique(array_merge($this->selectedMedicationIds, $medicationIds));
            $this->selectedEncounterId = $encounterId;
        }
    }

    public function copySelectedMedications()
    {
        if (empty($this->selectedMedicationIds)) {
            session()->flash('message.error', 'No hay medicamentos seleccionados para copiar.');
            return;
        }

        $selectedMedications = $this->medicationHistory->whereIn('id', $this->selectedMedicationIds);
        
        $this->dispatch('copyMedicationsToCurrentRecipe', $selectedMedications->values()->toArray());
        
        session()->flash('message.success', count($selectedMedications) . ' medicamento(s) copiado(s) a la receta actual.');
        
        $this->hideMedicationHistory();
    }

    public function render()
    {
        return view('livewire.patient.medication-history');
    }
}