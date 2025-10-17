<?php

namespace App\Exports;

use App\Services\Reports\AppointmentsReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AppointmentsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    protected $filters;

    protected $report;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->report = new AppointmentsReport;
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
        // Estilo para el encabezado (fila 1)
        $sheet->getStyle('A1:'.$this->getLastColumn().'1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Altura de la fila de encabezado
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Bordes para todas las celdas con datos
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $this->getLastColumn();

        if ($lastRow > 1) {
            $sheet->getStyle('A1:'.$lastColumn.$lastRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ]);
        }

        // Alineación para columnas específicas
        if ($lastRow > 1) {
            // Centrar las columnas de ID, Duración y Estatus
            $sheet->getStyle('A2:A'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I2:I'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J2:J'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }

    /**
     * Obtener la última columna con datos
     */
    protected function getLastColumn(): string
    {
        $columns = count($this->report->getExcelColumns());

        return chr(64 + $columns); // A=65, B=66, etc.
    }

    /**
     * Título de la hoja
     */
    public function title(): string
    {
        return 'Historial de Citas';
    }
}
