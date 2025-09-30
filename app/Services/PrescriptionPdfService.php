<?php

namespace App\Services;

use App\Models\Recepy\RecepyDoctorProfile;
use App\Models\Recepy\RecepyPrescription;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PrescriptionPdfService
{
    public function generatePdf(RecepyPrescription $prescription): string
    {
        // Load prescription with all relationships
        $prescription->load([
            'doctorProfile.user',
            'activeMedications',
        ]);

        // Generate PDF
        $pdf = Pdf::loadView('pdf.prescription', [
            'prescription' => $prescription,
            'pdfService' => $this,
        ]);

        // Configure PDF settings
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

    public function downloadPdf(RecepyPrescription $prescription): \Symfony\Component\HttpFoundation\Response
    {
        // Load prescription with all relationships
        $prescription->load([
            'doctorProfile.user',
            'activeMedications',
        ]);

        // Generate PDF
        $pdf = Pdf::loadView('pdf.prescription', [
            'prescription' => $prescription,
            'pdfService' => $this,
        ]);

        // Configure PDF settings
        $pdf->setPaper('A4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultMediaType' => 'screen',
                'isFontSubsettingEnabled' => true,
            ]);

        // Generate filename
        $filename = $this->generateFilename($prescription);

        return $pdf->download($filename);
    }

    public function unstreamPdf(RecepyPrescription $prescription): \Symfony\Component\HttpFoundation\Response
    {
        // Load prescription with all relationships
        $prescription->load([
            'doctorProfile.user',
            'activeMedications',
        ]);

        // Generate PDF
        $pdf = Pdf::loadView('pdf.prescription', [
            'prescription' => $prescription,
            'pdfService' => $this,
        ]);

        // Configure PDF settings
        $pdf->setPaper('A4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultMediaType' => 'screen',
                'isFontSubsettingEnabled' => true,
            ]);

        // Generate filename

        $filename = $this->generateFilename($prescription);

        return $pdf->stream($filename);
    }

    public function savePdf(RecepyPrescription $prescription, ?string $path = null): string
    {
        // Generate PDF content
        $pdfContent = $this->generatePdf($prescription);

        // Generate path if not provided
        if (! $path) {
            $filename = $this->generateFilename($prescription);
            $path = 'prescriptions/'.$filename;
        }

        // Save to private storage for security
        Storage::disk('local')->put($path, $pdfContent);

        return $path;
    }

    private function generateFilename(RecepyPrescription $prescription): string
    {
        // Clean patient name for filename
        $patientName = $this->sanitizeForFilename($prescription->patient_name);

        // Generate filename: RX-2025-123456_Maria_Gonzalez_2025-01-01.pdf
        return sprintf(
            '%s_%s_%s.pdf',
            $prescription->prescription_number,
            $patientName,
            $prescription->prescription_date->format('Y-m-d')
        );
    }

    private function sanitizeForFilename(string $string): string
    {
        // Remove accents and special characters
        $string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);

        // Replace spaces with underscores and remove non-alphanumeric characters
        $string = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $string);

        // Remove multiple underscores
        $string = preg_replace('/_+/', '_', $string);

        // Remove leading/trailing underscores
        return trim($string, '_');
    }

    public function getImagePath(string $imagePath): string
    {
        if (empty($imagePath)) {
            return '';
        }

        // If it's a full path, return as is
        if (str_starts_with($imagePath, 'http')) {
            return $imagePath;
        }

        // If it's a storage path, get the full public URL
        if (Storage::disk('public')->exists($imagePath)) {
            return public_path('storage/'.$imagePath);
        }

        return '';
    }

    /**
     * Get base64 encoded image for private storage files (signatures and seals)
     */
    public function getPrivateImageBase64(string $imagePath): string
    {
        if (empty($imagePath)) {
            return '';
        }
        // Check if file exists in private storage
        if (Storage::disk('local')->exists($imagePath)) {
            $fileContents = Storage::disk('local')->get($imagePath);

            return base64_encode($fileContents);
        }

        return '';
    }

    /**
     * Get image MIME type for private storage files
     */
    public function getPrivateImageMimeType(string $imagePath): string
    {
        if (empty($imagePath)) {
            return '';
        }

        if (Storage::disk('local')->exists($imagePath)) {
            return Storage::disk('local')->mimeType($imagePath);
        }

        return '';
    }

    /**
     * Generate data URI for private image files
     */
    public function getPrivateImageDataUri(string $imagePath): string
    {
        if (empty($imagePath)) {
            return '';
        }

        $base64 = $this->getPrivateImageBase64($imagePath);
        if (empty($base64)) {
            return '';
        }

        $extension = pathinfo($imagePath, PATHINFO_EXTENSION);

        return "data:image/{$extension};base64,{$base64}";
    }

    /**
     * Check if an image path refers to a private file (signature or seal)
     */
    public function isPrivateImage(string $imagePath): bool
    {
        return str_contains($imagePath, '/signatures/') || str_contains($imagePath, '/seals/');
    }

    public function formatPatientAge(RecepyPrescription $prescription): string
    {
        if (! $prescription->patient_birth_date) {
            return '';
        }

        $age = $prescription->patient_age;

        if ($age >= 1) {
            return $age.' años';
        }

        // For babies less than 1 year old, show months
        $months = $prescription->patient_birth_date->diffInMonths(now());

        if ($months >= 1) {
            return $months.' meses';
        }

        // For newborns, show days
        $days = $prescription->patient_birth_date->diffInDays(now());

        return $days.' días';
    }

    public function formatGender(?string $gender = null): string
    {
        if (! $gender) {
            return '';
        }

        return match ($gender) {
            'M' => 'Masculino',
            'F' => 'Femenino',
            'O' => 'Otro',
            default => ''
        };
    }

    public function formatDate(\DateTime|string $date): string
    {
        if (is_string($date)) {
            $date = new \DateTime($date);
        }

        // Format: Caracas, 15 de enero de 2025
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

    // Methods with doctor profile ID override
    public function generatePdfWithProfile(RecepyPrescription $prescription, int $doctorProfileId): string
    {
        // Override the doctor profile with the specified one
        $doctorProfile = RecepyDoctorProfile::with('user')->findOrFail($doctorProfileId);
        $prescription->setRelation('doctorProfile', $doctorProfile);

        // Load medications
        $prescription->load('activeMedications');

        // Generate PDF
        $pdf = Pdf::loadView('pdf.prescription', [
            'prescription' => $prescription,
            'pdfService' => $this,
        ]);

        // Configure PDF settings
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

    public function downloadPdfWithProfile(RecepyPrescription $prescription, int $doctorProfileId): \Symfony\Component\HttpFoundation\Response
    {
        // Override the doctor profile with the specified one
        $doctorProfile = RecepyDoctorProfile::with('user')->findOrFail($doctorProfileId);
        $prescription->setRelation('doctorProfile', $doctorProfile);

        // Load medications
        $prescription->load('activeMedications');

        // Generate PDF
        $pdf = Pdf::loadView('pdf.prescription', [
            'prescription' => $prescription,
            'pdfService' => $this,
        ]);

        // Configure PDF settings
        $pdf->setPaper('A4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultMediaType' => 'screen',
                'isFontSubsettingEnabled' => true,
            ]);

        // Generate filename
        $filename = $this->generateFilenameWithProfile($prescription, $doctorProfile);

        return $pdf->download($filename);
    }

    public function streamPdfWithProfile(RecepyPrescription $prescription, int $doctorProfileId): \Symfony\Component\HttpFoundation\Response
    {
        // Override the doctor profile with the specified one
        $doctorProfile = RecepyDoctorProfile::with('user')->findOrFail($doctorProfileId);
        $prescription->setRelation('doctorProfile', $doctorProfile);

        // Load medications
        $prescription->load('activeMedications');

        // Generate PDF
        $pdf = Pdf::loadView('pdf.prescription', [
            'prescription' => $prescription,
            'pdfService' => $this,
        ]);

        // Configure PDF settings
        $pdf->setPaper('A4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultMediaType' => 'screen',
                'isFontSubsettingEnabled' => true,
            ]);

        // Generate filename
        $filename = $this->generateFilenameWithProfile($prescription, $doctorProfile);

        return $pdf->stream($filename);
    }

    public function savePdfWithProfile(RecepyPrescription $prescription, int $doctorProfileId, ?string $path = null): string
    {
        // Override the doctor profile with the specified one
        $doctorProfile = RecepyDoctorProfile::with('user')->findOrFail($doctorProfileId);

        // Generate PDF content
        $pdfContent = $this->generatePdfWithProfile($prescription, $doctorProfileId);

        // Generate path if not provided
        if (! $path) {
            $filename = $this->generateFilenameWithProfile($prescription, $doctorProfile);
            $path = 'prescriptions/'.$filename;
        }

        // Save to private storage for security
        Storage::disk('local')->put($path, $pdfContent);

        return $path;
    }

    private function generateFilenameWithProfile(RecepyPrescription $prescription, RecepyDoctorProfile $doctorProfile): string
    {
        // Clean patient name for filename
        $patientName = $this->sanitizeForFilename($prescription->patient_name);

        // Clean doctor name for filename
        $doctorName = $this->sanitizeForFilename($doctorProfile->user->first_name.'_'.$doctorProfile->user->last_name);

        // Generate filename: RX-2025-123456_Dr_Juan_Perez_Maria_Gonzalez_2025-01-01.pdf
        return sprintf(
            '%s_%s_%s_%s.pdf',
            $prescription->prescription_number,
            $doctorName,
            $patientName,
            $prescription->prescription_date->format('Y-m-d')
        );
    }
}
