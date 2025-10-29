<?php
namespace App\Livewire\Doctor;

use App\Models\EncounterDiagnosis;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DiagnosticsByAgeGroups extends Component
{
    public $diagnostics;
    public $results; // Propiedad pública para almacenar los resultados procesados

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->diagnostics = EncounterDiagnosis::with([
            'condition',
            'encounter.appointment.medicalSpecialty',
            'encounter.patient'
        ])
        ->selectRaw('
            conditions.onset_info,
            medical_specialties.name as specialty,
            CASE
                WHEN TIMESTAMPDIFF(YEAR, patients.birth_date, CURDATE()) BETWEEN 0 AND 12 THEN "0-12 años"
                WHEN TIMESTAMPDIFF(YEAR, patients.birth_date, CURDATE()) BETWEEN 13 AND 17 THEN "13-17 años"
                WHEN TIMESTAMPDIFF(YEAR, patients.birth_date, CURDATE()) BETWEEN 18 AND 25 THEN "18-25 años"
                WHEN TIMESTAMPDIFF(YEAR, patients.birth_date, CURDATE()) BETWEEN 26 AND 59 THEN "26-59 años"
                ELSE "60+ años"
            END as age_group,
            COUNT(encounter_diagnoses.id) as total
        ')
        ->join('conditions', 'encounter_diagnoses.condition_id', '=', 'conditions.id')
        ->join('encounters', 'encounter_diagnoses.encounter_id', '=', 'encounters.id')
        ->join('patients', 'encounters.patient_id', '=', 'patients.id')
        ->join('appointments', 'encounters.appointment_id', '=', 'appointments.id')
        ->join('medical_specialties', 'appointments.medical_speciality_id', '=', 'medical_specialties.id')
        ->groupBy('conditions.onset_info', 'medical_specialties.name', 'age_group')
        ->orderByDesc('total')
        ->get();

        $grouped = $this->diagnostics->groupBy(['specialty', 'age_group']);
        $this->results = []; // Inicializa la propiedad

        foreach ($grouped as $specialty => $ageGroups) {
            foreach ($ageGroups as $ageGroup => $diagnosticsInGroup) {
                $totalInGroup = $diagnosticsInGroup->sum('total');
                foreach ($diagnosticsInGroup as $diagnostic) {
                    $diagnostic->percentage = ($diagnostic->total * 100) / $totalInGroup;
                    $this->results[] = $diagnostic; // Asigna a la propiedad pública
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.doctor.diagnostics-by-age-groups');
    }
}
