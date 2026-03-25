<?php

namespace App\Livewire\Doctor;

use App\Models\Condition;
use App\Models\EncounterDiagnosis;
use App\Models\Icd10Code;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TopActiveConditions extends Component
{
    public $timeFrame = '30'; // Por defecto últimos 30 días

    public $topConditions = [];

    public $order;

    public $isLoading = true;

    public function mount()
    {
        // Inicializar variables para evitar errores
        $this->topConditions = collect();
    }

    public function loadData()
    {
        $this->loadTopConditions();
        $this->isLoading = false;
    }

    public function updatedTimeFrame()
    {
        $this->loadTopConditions();
    }

    public function loadTopConditions()
    {
        $practitionerId = auth()->user()->practitioner->id;
        $days = (int) $this->timeFrame;

        // Obtener las condiciones más frecuentes en los encounters del doctor
        $this->topConditions = EncounterDiagnosis::query()
            ->select([
                'conditions.code',
                'conditions.category',
                DB::raw('COUNT(*) as count'),
                DB::raw('COUNT(DISTINCT encounter_diagnoses.encounter_id) as encounter_count'),
                DB::raw('COUNT(DISTINCT encounters.patient_id) as patient_count'),
            ])
            ->join('encounters', 'encounter_diagnoses.encounter_id', '=', 'encounters.id')
            ->join('conditions', 'encounter_diagnoses.condition_id', '=', 'conditions.id')
            ->where('encounters.practitioner_id', $practitionerId)
            ->where('encounters.status', 'finished')
            ->when($days > 0, function ($query) use ($days) {
                return $query->where('encounters.start', '>=', now()->subDays($days));
            })
            ->groupBy('conditions.code', 'conditions.category')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                // Obtener la descripción de la condición
                $icde10 = Icd10Code::where('code', $item->code)->first();
                $condition = Condition::where('code', $item->code)->first();
                $condition_description = '';
                if ($icde10) {
                    $condition_description = $icde10->description_es;
                } elseif ($condition) {
                    $condition_description = $condition->onset_info;
                }

                return [
                    'code' => $item->code,
                    'description' => $condition_description,
                    'category' => $item->category,
                    'count' => $item->count,
                    'encounter_count' => $item->encounter_count,
                    'patient_count' => $item->patient_count,
                    'percentage' => 0, // Se calculará después
                ];
            });

        // Calcular porcentajes
        $totalCount = $this->topConditions->sum('count');
        $this->topConditions = $this->topConditions->map(function ($condition) use ($totalCount) {
            $condition['percentage'] = $totalCount > 0 ? round(($condition['count'] / $totalCount) * 100, 1) : 0;

            return $condition;
        });
    }

    public function getColorForCondition($index)
    {
        $colors = [
            '#FF6384', // Rosa
            '#36A2EB', // Azul
            '#FFCE56', // Amarillo
            '#4BC0C0', // Verde azulado
            '#9966FF',  // Morado
        ];

        return $colors[$index % count($colors)];
    }

    public function render()
    {
        return view('livewire.doctor.top-active-conditions');
    }
}
