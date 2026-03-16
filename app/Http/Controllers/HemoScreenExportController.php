<?php

namespace App\Http\Controllers;

use App\Models\HemoScreenStandaloneResult;
use App\Services\HemoScreenPdfExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class HemoScreenExportController extends Controller
{
    public function __construct(protected HemoScreenPdfExportService $pdfService) {}

    /**
     * Export a single result to PDF
     */
    public function exportSinglePdf(HemoScreenStandaloneResult $result)
    {
        // Verify ownership
        if ($result->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this result');
        }

        $pdf = $this->pdfService->exportSingleResult($result);

        $filename = sprintf(
            'HemoScreen_%s_%s.pdf',
            $result->test_performed_at->format('Y-m-d'),
            $result->id
        );

        return $pdf->download($filename);
    }

    /**
     * Export multiple results to PDF (batch)
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
        ]);

        $ids = explode(',', $request->ids);

        // Get results and verify ownership
        $results = HemoScreenStandaloneResult::query()
            ->forUser(Auth::id())
            ->whereIn('id', $ids)
            ->orderBy('test_performed_at', 'desc')
            ->get();

        if ($results->isEmpty()) {
            abort(404, 'No results found');
        }

        $pdf = $this->pdfService->exportMultipleResults($results);

        $filename = sprintf(
            'HemoScreen_Batch_%s_%d_results.pdf',
            now()->format('Y-m-d'),
            $results->count()
        );

        return $pdf->download($filename);
    }

    /**
     * Export results to CSV
     */
    public function exportCsv(Request $request)
    {
        $query = HemoScreenStandaloneResult::query()
            ->forUser(Auth::id())
            ->with('practitioner')
            ->orderBy('test_performed_at', 'desc');

        // Apply filters
        if ($request->filled('dateFrom') && $request->filled('dateTo')) {
            $query->dateRange($request->dateFrom, $request->dateTo.' 23:59:59');
        }

        if ($request->filled('deviceSerial')) {
            $query->byDevice($request->deviceSerial);
        }

        $results = $query->get();

        if ($results->isEmpty()) {
            return back()->with('error', 'No hay resultados para exportar');
        }

        // Generate CSV
        $filename = sprintf(
            'HemoScreen_Export_%s.csv',
            now()->format('Y-m-d_His')
        );

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($results) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Headers
            fputcsv($file, [
                'Fecha',
                'Hora',
                'Dispositivo',
                'Panel',
                'Código CPT',
                'WBC',
                'WBC Unidad',
                'RBC',
                'RBC Unidad',
                'Hemoglobina',
                'Hemoglobina Unidad',
                'Hematocrito',
                'Hematocrito Unidad',
                'MCV',
                'MCV Unidad',
                'MCH',
                'MCH Unidad',
                'MCHC',
                'MCHC Unidad',
                'Plaquetas',
                'Plaquetas Unidad',
                'Neutrófilos',
                'Linfocitos',
                'Monocitos',
                'Eosinófilos',
                'Basófilos',
                'Estado',
            ]);

            // Data rows
            foreach ($results as $result) {
                $observations = collect($result->observations ?? []);

                $getObsValue = function ($code) use ($observations) {
                    $obs = $observations->firstWhere('code', $code);

                    return $obs['value'] ?? '';
                };

                $getObsUnit = function ($code) use ($observations) {
                    $obs = $observations->firstWhere('code', $code);

                    return $obs['unit'] ?? '';
                };

                fputcsv($file, [
                    $result->test_performed_at->format('d/m/Y'),
                    $result->test_performed_at->format('H:i:s'),
                    $result->device_serial,
                    $result->panel_name,
                    $result->panel_code,
                    $getObsValue('6690-2'), // WBC
                    $getObsUnit('6690-2'),
                    $getObsValue('789-8'), // RBC
                    $getObsUnit('789-8'),
                    $getObsValue('718-7'), // Hemoglobin
                    $getObsUnit('718-7'),
                    $getObsValue('20570-8'), // Hematocrit
                    $getObsUnit('20570-8'),
                    $getObsValue('787-2'), // MCV
                    $getObsUnit('787-2'),
                    $getObsValue('785-6'), // MCH
                    $getObsUnit('785-6'),
                    $getObsValue('786-4'), // MCHC
                    $getObsUnit('786-4'),
                    $getObsValue('777-3'), // Platelets
                    $getObsUnit('777-3'),
                    $getObsValue('751-8'), // Neutrophils
                    $getObsValue('736-9'), // Lymphocytes
                    $getObsValue('5905-5'), // Monocytes
                    $getObsValue('713-8'), // Eosinophils
                    $getObsValue('706-2'), // Basophils
                    $result->hasAbnormalValues() ? 'Anormal' : 'Normal',
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
