<?php

namespace App\Livewire\HemoScreen;

use App\Models\HemoScreenStandaloneResult;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StandaloneResultDetail extends Component
{
    public HemoScreenStandaloneResult $result;

    public function mount($resultId)
    {
        // Load result and verify ownership
        $this->result = HemoScreenStandaloneResult::query()
            ->forUser(Auth::id())
            ->with('practitioner')
            ->findOrFail($resultId);
    }

    public function exportToPdf()
    {
        return redirect()->route('hemoscreen.export-single-pdf', $this->result->id);
    }

    public function getGroupedObservationsProperty()
    {
        $observations = $this->result->observations ?? [];
        $referenceRanges = $this->result->getReferenceRanges();

        // Group observations by category
        $groups = [
            'Conteo Celular' => ['6690-2', '789-8', '777-3'],
            'Hemoglobina y Hematocrito' => ['718-7', '20570-8'],
            'Índices Eritrocitarios' => ['787-2', '785-6', '786-4'],
            'Diferencial de Leucocitos' => ['751-8', '736-9', '5905-5', '713-8', '706-2'],
        ];

        $groupedData = [];

        foreach ($groups as $groupName => $codes) {
            $groupItems = [];

            foreach ($observations as $obs) {
                $code = $obs['code'] ?? null;

                if (in_array($code, $codes)) {
                    $value = (float) ($obs['value'] ?? 0);
                    $unit = $obs['unit'] ?? '';
                    $name = $obs['name'] ?? '';

                    $reference = $referenceRanges[$code] ?? null;
                    $isAbnormal = $reference ? $this->result->isAbnormal($code, $value) : false;

                    $status = 'normal';
                    if ($isAbnormal && $reference) {
                        if ($value < $reference['min']) {
                            $status = 'low';
                        } elseif ($value > $reference['max']) {
                            $status = 'high';
                        }
                    }

                    $groupItems[] = [
                        'code' => $code,
                        'name' => $name,
                        'value' => $value,
                        'unit' => $unit,
                        'reference' => $reference,
                        'status' => $status,
                        'isAbnormal' => $isAbnormal,
                    ];
                }
            }

            if (! empty($groupItems)) {
                $groupedData[$groupName] = $groupItems;
            }
        }

        return $groupedData;
    }

    public function render()
    {
        return view('livewire.hemo-screen.standalone-result-detail', [
            'groupedObservations' => $this->groupedObservations,
        ])->layout('layouts.app');
    }
}
