<?php

namespace App\Livewire\Doctor;

use Illuminate\Support\Facades\DB;
use App\Models\EncounterDiagnosis;
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
        $doctorSpecialtyId = auth()->user()->practitioner->specialties->pluck('id')->toArray();
        //dd($doctorSpecialtyId);
        $this->diagnostics = DB::table(function($query) {
        $query->from('encounter_diagnoses')
            ->selectRaw('
                encounter_diagnoses.id as ed_id,
                encounter_diagnoses.condition_id,
                encounter_diagnoses.encounter_id,
                conditions.onset_info,
                medical_specialties.id as specialty_id,
                medical_specialties.name as specialty,
                CASE
                    WHEN TIMESTAMPDIFF(YEAR, patients.birth_date, CURDATE()) BETWEEN 0 AND 12 THEN "0-12 años"
                    WHEN TIMESTAMPDIFF(YEAR, patients.birth_date, CURDATE()) BETWEEN 13 AND 17 THEN "13-17 años"
                    WHEN TIMESTAMPDIFF(YEAR, patients.birth_date, CURDATE()) BETWEEN 18 AND 25 THEN "18-25 años"
                    WHEN TIMESTAMPDIFF(YEAR, patients.birth_date, CURDATE()) BETWEEN 26 AND 59 THEN "26-59 años"
                    ELSE "60+ años"
                END as age_group
            ')
            ->join('conditions', 'encounter_diagnoses.condition_id', '=', 'conditions.id')
            ->join('encounters', 'encounter_diagnoses.encounter_id', '=', 'encounters.id')
            ->join('appointments', 'encounters.appointment_id', '=', 'appointments.id')
            ->join('patients', 'encounters.patient_id', '=', 'patients.id')
            ->join('medical_specialties', 'appointments.medical_speciality_id', '=', 'medical_specialties.id');
    }, 'base_data')
    ->selectRaw('
        base_data.onset_info,
        base_data.specialty,
        base_data.age_group,
        COUNT(base_data.ed_id) as total,
        (COUNT(base_data.ed_id) * 100.0 / specialties_total.total_specialty) as percentage
    ')
    ->joinSub(
        DB::table(function($query) use ($doctorSpecialtyId) {
            $query->from('encounter_diagnoses')
                ->selectRaw('
                    medical_specialties.id as specialty_id,
                    CASE
                        WHEN TIMESTAMPDIFF(YEAR, patients.birth_date, CURDATE()) BETWEEN 0 AND 12 THEN "0-12 años"
                        WHEN TIMESTAMPDIFF(YEAR, patients.birth_date, CURDATE()) BETWEEN 13 AND 17 THEN "13-17 años"
                        WHEN TIMESTAMPDIFF(YEAR, patients.birth_date, CURDATE()) BETWEEN 18 AND 25 THEN "18-25 años"
                        WHEN TIMESTAMPDIFF(YEAR, patients.birth_date, CURDATE()) BETWEEN 26 AND 59 THEN "26-59 años"
                        ELSE "60+ años"
                    END as age_group
                ')
                ->join('encounters', 'encounter_diagnoses.encounter_id', '=', 'encounters.id')
                ->join('appointments', 'encounters.appointment_id', '=', 'appointments.id')
                ->join('patients', 'encounters.patient_id', '=', 'patients.id')
                ->join('medical_specialties', 'appointments.medical_speciality_id', '=', 'medical_specialties.id')
                ->whereIn('medical_specialties.id', $doctorSpecialtyId);
        }, 'totals_base')
        ->selectRaw('
            totals_base.specialty_id,
            totals_base.age_group,
            COUNT(*) as total_specialty
        ')
        ->groupBy('totals_base.specialty_id', 'totals_base.age_group'),
        'specialties_total',
        function($join) {
            $join->on('base_data.specialty_id', '=', 'specialties_total.specialty_id')
                ->on('base_data.age_group', '=', 'specialties_total.age_group');
        }
    )
    ->groupBy('base_data.onset_info', 'base_data.specialty', 'base_data.age_group', 'specialties_total.total_specialty')
    ->limit(5)
    ->get();
            

    }

    public function render()
    {
        return view('livewire.doctor.diagnostics-by-age-groups');
    }
}
