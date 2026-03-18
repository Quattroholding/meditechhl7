<?php

namespace App\Services;

use App\Models\HemoScreenStandaloneResult;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class HemoScreenPdfExportService
{
    /**
     * Export a single HemoScreen result to PDF
     */
    public function exportSingleResult(HemoScreenStandaloneResult $result): \Barryvdh\DomPDF\PDF
    {
        $data = $this->prepareResultData($result);

        $pdf = Pdf::loadView('pdf.hemoscreen-result', $data);

        return $pdf->setPaper('letter', 'portrait');
    }

    /**
     * Export multiple HemoScreen results to PDF
     */
    public function exportMultipleResults(Collection $results): \Barryvdh\DomPDF\PDF
    {
        $resultsData = $results->map(function ($result) {
            return $this->prepareResultData($result);
        });

        $pdf = Pdf::loadView('pdf.hemoscreen-batch', [
            'results' => $resultsData,
            'totalResults' => $results->count(),
            'exportDate' => now(),
        ]);

        return $pdf->setPaper('letter', 'portrait');
    }

    /**
     * Prepare result data for PDF rendering
     */
    protected function prepareResultData(HemoScreenStandaloneResult $result): array
    {
        $observations = $result->observations ?? [];
        $referenceRanges = $result->getReferenceRanges();

        // Group observations by category
        $groups = [
            'Conteo Celular' => ['6690-2', '789-8', '777-3'],
            'Hemoglobina y Hematocrito' => ['718-7', '20570-8'],
            'Índices Eritrocitarios' => ['787-2', '785-6', '786-4'],
            'Diferencial de Leucocitos' => ['751-8', '736-9', '5905-5', '713-8', '706-2'],
        ];

        $groupedObservations = [];

        foreach ($groups as $groupName => $codes) {
            $groupItems = [];

            foreach ($observations as $obs) {
                $code = $obs['code'] ?? null;

                if (in_array($code, $codes)) {
                    $value = (float) ($obs['value'] ?? 0);
                    $unit = $obs['unit'] ?? '';
                    $name = $obs['name'] ?? '';

                    $reference = $referenceRanges[$code] ?? null;
                    $isAbnormal = $reference ? $result->isAbnormal($code, $value) : false;

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
                $groupedObservations[$groupName] = $groupItems;
            }
        }

        return [
            'result' => $result,
            'groupedObservations' => $groupedObservations,
            'hasAbnormal' => $result->hasAbnormalValues(),
            'abnormalCount' => count($result->getAbnormalObservations()),
            'practitioner' => $result->practitioner,
        ];
    }

    /**
     * Get reference ranges for all CBC parameters
     */
    public function getReferenceRanges(): array
    {
        return [
            '718-7' => ['min' => 13.5, 'max' => 17.5, 'unit' => 'g/dL', 'name' => 'Hemoglobin'],
            '789-8' => ['min' => 4.5, 'max' => 5.9, 'unit' => '10^12/L', 'name' => 'RBC'],
            '6690-2' => ['min' => 4.5, 'max' => 11.0, 'unit' => '10^9/L', 'name' => 'WBC'],
            '20570-8' => ['min' => 38.8, 'max' => 50.0, 'unit' => '%', 'name' => 'Hematocrit'],
            '787-2' => ['min' => 80.0, 'max' => 100.0, 'unit' => 'fL', 'name' => 'MCV'],
            '785-6' => ['min' => 27.0, 'max' => 32.0, 'unit' => 'pg', 'name' => 'MCH'],
            '786-4' => ['min' => 32.0, 'max' => 36.0, 'unit' => 'g/dL', 'name' => 'MCHC'],
            '777-3' => ['min' => 150.0, 'max' => 400.0, 'unit' => '10^9/L', 'name' => 'Platelets'],
            // Differential - Absolute counts (as sent by HemoScreen device)
            '751-8' => ['min' => 2.0, 'max' => 7.0, 'unit' => '10^9/L', 'name' => 'Neutrophils Absolute'],
            '731-0' => ['min' => 1.0, 'max' => 4.0, 'unit' => '10^9/L', 'name' => 'Lymphocytes Absolute'],
            '742-7' => ['min' => 0.2, 'max' => 1.0, 'unit' => '10^9/L', 'name' => 'Monocytes Absolute'],
            '711-2' => ['min' => 0.0, 'max' => 0.5, 'unit' => '10^9/L', 'name' => 'Eosinophils Absolute'],
            '704-7' => ['min' => 0.0, 'max' => 0.2, 'unit' => '10^9/L', 'name' => 'Basophils Absolute'],
            // Differential - Percentages (as sent by HemoScreen device)
            '770-8' => ['min' => 40.0, 'max' => 70.0, 'unit' => '%', 'name' => 'Neutrophils Percent'],
            '736-9' => ['min' => 20.0, 'max' => 40.0, 'unit' => '%', 'name' => 'Lymphocytes Percent'],
            '5905-5' => ['min' => 4.0, 'max' => 8.0, 'unit' => '%', 'name' => 'Monocytes Percent'],
            '713-8' => ['min' => 1.0, 'max' => 4.0, 'unit' => '%', 'name' => 'Eosinophils Percent'],
            '706-2' => ['min' => 0.0, 'max' => 1.0, 'unit' => '%', 'name' => 'Basophils Percent'],
        ];
    }

    /**
     * Check if a value is abnormal
     */
    public function isAbnormal(string $code, float $value): bool
    {
        $referenceRanges = $this->getReferenceRanges();

        if (! isset($referenceRanges[$code])) {
            return false;
        }

        $range = $referenceRanges[$code];

        return $value < $range['min'] || $value > $range['max'];
    }
}
