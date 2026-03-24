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
                'medications.id as medication_id',
                'medications.generic_name as generic_name1',
                'medications.home_name as home_name1',
                'medications.display as display1',
                'medications.form as form1',
                'medications2.id as medication_id2',
                'medications2.generic_name as generic_name2',
                'medications2.home_name as home_name2',
                'medications2.display as display2',
                'medications2.form as form2',
                'medication_requests.medication',
                'medication_requests.frequency',
                'medication_requests.route',
                DB::raw('COUNT(*) as prescription_count'),
                DB::raw('COUNT(DISTINCT medication_requests.encounter_id) as encounter_count'),
                DB::raw('COUNT(DISTINCT medication_requests.patient_id) as patient_count'),
                DB::raw('AVG(CAST(medication_requests.quantity AS DECIMAL(10,2))) as avg_quantity'),
            ])
            ->join('encounters', 'medication_requests.encounter_id', '=', 'encounters.id')
            ->leftJoin('medications', 'medication_requests.medication_id', '=', 'medications.id')
            ->leftJoin('medications as medications2', 'medication_requests.medication_id2', '=', 'medications2.id')
            ->where('medication_requests.practitioner_id', $practitionerId)
            ->where('medication_requests.status', '!=', 'cancelled')
            ->when($days > 0, function ($query) use ($days) {
                return $query->where('encounters.start', '>=', now()->subDays($days));
            })
            ->groupBy([
                'medications.id',
                'medications.generic_name',
                'medications.home_name',
                'medications.display',
                'medications.form',
                'medications2.id',
                'medications2.generic_name',
                'medications2.home_name',
                'medications2.display',
                'medications2.form',
                'medication_requests.medication',
                'medication_requests.frequency',
                'medication_requests.route',
            ])
            ->orderByDesc('prescription_count')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                // Determinar si hay un medicamento vinculado por ID
                $hasMedication2 = ! is_null($item->medication_id2) && ! is_null($item->generic_name2);
                $hasMedication1 = ! is_null($item->medication_id) && ! is_null($item->generic_name1);

                // Si no hay medicamento vinculado, usar el campo de texto
                if (! $hasMedication2 && ! $hasMedication1) {
                    return [
                        'medication_id' => null,
                        'generic_name' => $item->medication ?: 'Medicamento sin nombre',
                        'home_name' => $item->medication ?: 'Medicamento sin nombre',
                        'medication' => $item->medication,
                        'concentration' => 'N/A',
                        'form' => 'N/A',
                        'frequency' => $item->frequency ?: 'No especificada',
                        'route' => $item->route ?: 'Oral',
                        'prescription_count' => $item->prescription_count,
                        'encounter_count' => $item->encounter_count,
                        'patient_count' => $item->patient_count,
                        'avg_quantity' => round($item->avg_quantity ?? 0, 1),
                        'percentage' => 0,
                    ];
                }

                // Usar el medicamento disponible (prioritizar medication2)
                $useMedication2 = $hasMedication2;

                return [
                    'medication_id' => $useMedication2 ? $item->medication_id2 : $item->medication_id,
                    'generic_name' => $useMedication2 ? $item->generic_name2 : $item->generic_name1,
                    'home_name' => $useMedication2
                        ? ($item->home_name2 ?: $item->generic_name2)
                        : ($item->home_name1 ?: $item->generic_name1),
                    'medication' => $item->medication,
                    'display' => $useMedication2 ? $item->display2 : $item->display1,
                    'concentration' => $useMedication2
                        ? $this->extractConcentration($item->display2)
                        : $this->extractConcentration($item->display1),
                    'form' => $useMedication2 ? $item->form2 : $item->form1,
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

    protected function extractConcentration(?string $display): string
    {
        if (! $display) {
            return 'N/A';
        }

        // Extraer la concentración del display (ej: "PARACETAMOL TABLETS 500MG" -> "500MG")
        if (preg_match('/(\d+(?:\.\d+)?(?:MG|G|ML|MCG|%|UI|U))/i', $display, $matches)) {
            return strtoupper($matches[1]);
        }

        return 'N/A';
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

    public function getMedicationIcon($form)
    {
        $icons = [
            // Español
            'tabletas' => 'fas fa-pills',
            'comprimidos' => 'fas fa-pills',
            'capsulas' => 'fas fa-capsules',
            'cápsulas' => 'fas fa-capsules',
            'jarabe' => 'fas fa-prescription-bottle',
            'suspension' => 'fas fa-prescription-bottle',
            'inyectable' => 'fas fa-syringe',
            'ampolla' => 'fas fa-syringe',
            'crema' => 'fas fa-pump-soap',
            'pomada' => 'fas fa-pump-soap',
            'gotas' => 'fas fa-eye-dropper',
            // Inglés
            'tablet' => 'fas fa-pills',
            'tablets' => 'fas fa-pills',
            'capsule' => 'fas fa-capsules',
            'capsules' => 'fas fa-capsules',
            'syrup' => 'fas fa-prescription-bottle',
            'injection' => 'fas fa-syringe',
            'cream' => 'fas fa-pump-soap',
            'drop' => 'fas fa-eye-dropper',
            'drops' => 'fas fa-eye-dropper',
            'default' => 'fas fa-prescription-bottle-alt',
        ];

        return $icons[strtolower($form ?? '')] ?? $icons['default'];
    }

    public function render()
    {
        return view('livewire.doctor.top-prescribed-medications');
    }
}
