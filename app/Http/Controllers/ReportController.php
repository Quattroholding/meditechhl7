<?php

namespace App\Http\Controllers;

use App\Services\Reports\ReportRegistry;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        $reports = ReportRegistry::all();

        return view('reports.index', compact('reports'));
    }

    public function show(string $reportName)
    {
        $report = ReportRegistry::get($reportName);

        if (! $report || ! $report->userHasAccess()) {
            abort(403, 'No tienes acceso a este reporte');
        }

        $filters = $report->getAvailableFilters();

        return view('reports.show', compact('report', 'filters'));
    }

    public function excel(string $reportName, Request $request)
    {
        $report = ReportRegistry::get($reportName);

        if (! $report || ! $report->canExportExcel()) {
            abort(403);
        }

        $filters = $request->all();
        $exportClass = 'App\\Exports\\'.ucfirst($reportName).'Export';

        return Excel::download(
            new $exportClass($filters),
            "{$reportName}-".now()->format('Y-m-d-His').'.xlsx'
        );
    }

    public function pdf(string $reportName, Request $request)
    {
        $report = ReportRegistry::get($reportName);

        if (! $report || ! $report->canExportPdf()) {
            abort(403);
        }

        $filters = $request->all();
        $data = $report->getData($filters);

        $viewData = [
            'title' => $report->getTitle(),
            'data' => $data->map(fn ($item) => $report->mapDataForExcel($item)),
            'columns' => $report->getExcelColumns(),
            'appliedFilters' => $this->formatFilters($filters, $report),
        ];

        $pdf = Pdf::loadView("reports.pdf.{$reportName}", $viewData);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download("{$reportName}-".now()->format('Y-m-d-His').'.pdf');
    }

    protected function formatFilters(array $filters, $report): array
    {
        $formatted = [];
        $availableFilters = $report->getAvailableFilters();

        foreach ($filters as $key => $value) {
            if (empty($value) || $key === '_token') {
                continue;
            }

            $label = $availableFilters[$key]['label'] ?? ucwords(str_replace('_', ' ', $key));

            if (is_array($value)) {
                $formatted[$label] = implode(', ', $value);
            } else {
                $formatted[$label] = $value;
            }
        }

        return $formatted;
    }
}
