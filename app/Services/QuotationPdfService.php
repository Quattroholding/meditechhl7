<?php

namespace App\Services;

use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class QuotationPdfService
{
    public function generatePdf(Quotation $quotation): string
    {
        $quotation->load('items.serviceType');

        $pdf = Pdf::loadView('documents.quotation', [
            'quotation' => $quotation,
            'pdfService' => $this,
        ]);

        $pdf->setPaper('A4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultMediaType' => 'screen',
                'isFontSubsettingEnabled' => true,
            ]);

        return $pdf->output();
    }

    public function downloadPdf(Quotation $quotation): Response
    {
        $quotation->load('items.serviceType');

        $pdf = Pdf::loadView('documents.quotation', [
            'quotation' => $quotation,
            'pdfService' => $this,
        ]);

        $pdf->setPaper('A4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultMediaType' => 'screen',
                'isFontSubsettingEnabled' => true,
            ]);

        $filename = $this->generateFilename($quotation);

        return $pdf->download($filename);
    }

    public function streamPdf(Quotation $quotation): Response
    {
        $quotation->load('items.serviceType');

        $pdf = Pdf::loadView('documents.quotation', [
            'quotation' => $quotation,
            'pdfService' => $this,
        ]);

        $pdf->setPaper('A4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultMediaType' => 'screen',
                'isFontSubsettingEnabled' => true,
            ]);

        $filename = $this->generateFilename($quotation);

        return $pdf->stream($filename);
    }

    private function generateFilename(Quotation $quotation): string
    {
        $companyName = $this->sanitizeForFilename($quotation->client_company_name);

        return sprintf(
            'Cotizacion_%s_%s_%s.pdf',
            $quotation->quotation_number,
            $companyName,
            $quotation->issue_date->format('Y-m-d')
        );
    }

    private function sanitizeForFilename(string $string): string
    {
        $string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
        $string = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $string);
        $string = preg_replace('/_+/', '_', $string);

        return trim($string, '_');
    }

    public function formatDate(\DateTime|string $date): string
    {
        if (is_string($date)) {
            $date = new \DateTime($date);
        }

        $months = [
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
            5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
            9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre',
        ];

        $day = $date->format('d');
        $month = $months[(int) $date->format('m')];
        $year = $date->format('Y');

        return "{$day} de {$month} de {$year}";
    }
}
