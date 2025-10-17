<?php

namespace App\Exports;

use App\Services\Reports\InvoicesPaymentsReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoicesPaymentsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    protected $filters;

    protected $report;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->report = new InvoicesPaymentsReport;
    }

    /**
     * Obtener la colección de datos
     */
    public function collection()
    {
        $data = $this->report->getData($this->filters);

        return $data->map(function ($item) {
            return $this->report->mapDataForExcel($item);
        });
    }

    /**
     * Encabezados de columnas
     */
    public function headings(): array
    {
        return $this->report->getExcelColumns();
    }

    /**
     * Estilos del Excel
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo de encabezado
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Título de la hoja
     */
    public function title(): string
    {
        return 'Facturas y Pagos';
    }
}
