<?php

namespace App\Livewire\Doctor;

use App\Models\MedicationRequest;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TopPrescribedMedications extends Component
{
    public $timeFrame = '30'; // Por defecto últimos 30 días

    public $topMedications = [];

    public $order;

    public $isLoading = true;

    public function mount()
    {
        // Inicializar variables para evitar errores
        $this->topMedications = collect();
    }

    public function loadData()
    {
        $this->loadTopMedications();
        $this->isLoading = false;
    }

    public function updatedTimeFrame()
    {
        $this->loadTopMedications();
    }

    public function loadTopMedications()
    {
        $practitionerId = auth()->user()->practitioner->id;
        $days = (int) $this->timeFrame;

        // Obtener los medicamentos más prescritos por el doctor
        $this->topMedications = MedicationRequest::query()
            ->select([
                'medicines.id as medicine_id',
                'medicines.generic_name',
                'medicines.home_name',
                'medicines.type',
                'medicines.mgs',
                'medicines.mgs_type',
                'medicines2.id as medicine_id2',
                'medicines2.generic_name',
                'medicines2.home_name',
                'medicines2.type',
                'medicines2.mgs',
                'medicines2.mgs_type',
                'medication_requests.medication',
                'medication_requests.frequency',
                'medication_requests.route',
                DB::raw('COUNT(*) as prescription_count'),
                DB::raw('COUNT(DISTINCT medication_requests.encounter_id) as encounter_count'),
                DB::raw('COUNT(DISTINCT medication_requests.patient_id) as patient_count'),
                DB::raw('AVG(CAST(medication_requests.quantity AS DECIMAL(10,2))) as avg_quantity'),
            ])
            ->join('encounters', 'medication_requests.encounter_id', '=', 'encounters.id')
            ->leftJoin('medicines', 'medication_requests.medication_id', '=', 'medicines.id')
            ->leftJoin('medicines as medicines2', 'medication_requests.medication_id2', '=', 'medicines2.id')
            ->where('medication_requests.practitioner_id', $practitionerId)
            ->where('medication_requests.status', '!=', 'cancelled')
            ->when($days > 0, function ($query) use ($days) {
                return $query->where('encounters.start', '>=', now()->subDays($days));
            })
            ->groupBy([
                'medicines.id',
                'medicines.generic_name',
                'medicines.home_name',
                'medicines.type',
                'medicines.mgs',
                'medicines.mgs_type',
                'medicines2.id',
                'medicines2.generic_name',
                'medicines2.home_name',
                'medicines2.type',
                'medicines2.mgs',
                'medicines2.mgs_type',
                'medication_requests.medication',
                'medication_requests.frequency',
                'medication_requests.route',
            ])
            ->orderByDesc('prescription_count')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'medicine_id' => $item->medicine_id,
                    'generic_name' => $item->generic_name,
                    'home_name' => $item->home_name ?: $item->generic_name,
                    'medication' => $item->medication ,
                    'concentration' => $item->mgs.' '.$item->mgs_type,
                    'type' => $item->type,
                    'frequency' => $item->frequency ?: 'No especificada',
                    'route' => $item->route ?: 'Oral',
                    'prescription_count' => $item->prescription_count,
                    'encounter_count' => $item->encounter_count,
                    'patient_count' => $item->patient_count,
                    'avg_quantity' => round($item->avg_quantity ?? 0, 1),
                    'percentage' => 0, // Se calculará después
                ];
            });


        // Calcular porcentajes
        $totalCount = $this->topMedications->sum('prescription_count');
        $this->topMedications = $this->topMedications->map(function ($medication) use ($totalCount) {
            $medication['percentage'] = $totalCount > 0 ? round(($medication['prescription_count'] / $totalCount) * 100, 1) : 0;

            return $medication;
        });
    }

    public function getColorForMedication($index)
    {
        $colors = [
            '#FF6B6B', // Rojo suave
            '#4ECDC4', // Turquesa
            '#45B7D1', // Azul
            '#96CEB4', // Verde menta
            '#FECA57',  // Amarillo dorado
        ];

        return $colors[$index % count($colors)];
    }

    public function getMedicationIcon($type)
    {
        $icons = [
            'tablet' => 'fas fa-pills',
            'capsule' => 'fas fa-capsules',
            'syrup' => 'fas fa-prescription-bottle',
            'injection' => 'fas fa-syringe',
            'cream' => 'fas fa-pump-soap',
            'drop' => 'fas fa-eye-dropper',
            'default' => 'fas fa-prescription-bottle-alt',
        ];

        return $icons[strtolower($type)] ?? $icons['default'];
    }

    public function render()
    {
        return view('livewire.doctor.top-prescribed-medications');
    }
}
