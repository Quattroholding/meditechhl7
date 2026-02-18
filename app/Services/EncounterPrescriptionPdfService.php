<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientPreference;
use App\Models\ClientTheme;
use App\Models\Encounter;
use App\Models\MedicationRequest;
use App\Models\Practitioner;
use App\Models\Recepy\RecepyDoctorProfile;
use App\Models\ServiceRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class EncounterPrescriptionPdfService
{
    /**
     * Generate prescription PDF for medications
     * Used by both MedicalDocumentController and EncounterPrescriptionNotification
     *
     * @param  Encounter  $encounter  The encounter
     * @param  Collection|array|null  $medications  Optional specific medications (if null, uses all from encounter)
     * @return string|null PDF content as string, or null if no medications
     */
    public function generatePrescriptionPdf(Encounter $encounter, $medications = null): ?string
    {
        // Load encounter with relationships if not already loaded
        $encounter = $this->loadEncounterWithRelations($encounter, 'prescription');

        // Get medications
        if ($medications === null) {
            $medications = $encounter->medicationRequests;
        } elseif (is_array($medications)) {
            $medications = MedicationRequest::with(['medicine', 'medication2.ingredients'])
                ->whereIn('id', $medications)
                ->get();
        }

        if ($medications->isEmpty()) {
            return null;
        }

        // Get client
        $client = $this->getClientFromEncounter($encounter);

        // Get selected prescription template
        $template = $this->getPrescriptionTemplate($client);

        // Prepare data for the view
        $data = [
            'encounter' => $encounter,
            'patient' => $encounter->patient,
            'practitioner' => $encounter->practitioner,
            'medications' => $medications,
            'diagnoses' => $encounter->diagnoses,
            'date' => Carbon::parse($encounter->end ?? now()),
            'prescriptionNumber' => 'RX-'.str_pad($encounter->id, 6, '0', STR_PAD_LEFT).'-'.date('Ymd'),
            'orderNumber' => 'RX-'.str_pad($encounter->id, 6, '0', STR_PAD_LEFT).'-'.date('Ymd'),
            'clientThemeCSS' => $this->getClientThemeCSS($client),
            'client' => $client,
            'doctorProfile' => $this->getDoctorProfile($encounter->practitioner),
            'pdfService' => new PrescriptionPdfService,
        ];

        // Generate PDF using selected prescription template
        $pdf = Pdf::loadView("documents.prescriptions.templates.{$template}", $data);
        $pdf->setPaper('letter', 'portrait');

        return $pdf->output();
    }

    /**
     * Generate medical order PDF for service requests
     * Used by both MedicalDocumentController and EncounterPrescriptionNotification
     *
     * @param  Encounter  $encounter  The encounter
     * @param  Collection|array|null  $serviceRequests  Optional specific service requests (if null, uses all from encounter)
     * @param  string|null  $serviceType  Optional service type filter
     * @return string|null PDF content as string, or null if no service requests
     */
    public function generateMedicalOrderPdf(Encounter $encounter, $serviceRequests = null, ?string $serviceType = null): ?string
    {
        // Load encounter with relationships if not already loaded
        $encounter = $this->loadEncounterWithRelations($encounter, 'medical-order');

        // Get service requests
        if ($serviceRequests === null) {
            $serviceRequests = $encounter->serviceRequests;
        } elseif (is_array($serviceRequests)) {
            $serviceRequests = ServiceRequest::with('cpt')
                ->whereIn('id', $serviceRequests)
                ->get();
        }

        if ($serviceRequests->isEmpty()) {
            return null;
        }

        // Get client
        $client = $this->getClientFromEncounter($encounter);

        // Prepare data for the view
        $data = [
            'encounter' => $encounter,
            'patient' => $encounter->patient,
            'practitioner' => $encounter->practitioner,
            'serviceRequests' => $serviceRequests,
            'diagnoses' => $encounter->diagnoses,
            'date' => Carbon::parse($encounter->end ?? now()),
            'orderNumber' => 'OM-'.str_pad($encounter->id, 6, '0', STR_PAD_LEFT).'-'.date('Ymd'),
            'clientThemeCSS' => $this->getClientThemeCSS($client),
            'client' => $client,
            'doctorProfile' => $this->getDoctorProfile($encounter->practitioner),
            'pdfService' => new PrescriptionPdfService,
            'serviceType' => $serviceType,
        ];

        // Generate PDF using generic medical order view
        $pdf = Pdf::loadView('documents.medical-order', $data);
        $pdf->setPaper('letter', 'portrait');

        return $pdf->output();
    }

    /**
     * Stream prescription PDF (for controller response)
     */
    public function streamPrescriptionPdf(Encounter $encounter, $medications = null): \Illuminate\Http\Response
    {
        $encounter = $this->loadEncounterWithRelations($encounter, 'prescription');

        if ($medications === null) {
            $medications = $encounter->medicationRequests;
        } elseif (is_array($medications)) {
            $medications = MedicationRequest::with(['medicine', 'medication2.ingredients'])
                ->whereIn('id', $medications)
                ->get();
        }

        $client = $this->getClientFromEncounter($encounter);

        // Get selected prescription template
        $template = $this->getPrescriptionTemplate($client);

        $data = [
            'encounter' => $encounter,
            'patient' => $encounter->patient,
            'practitioner' => $encounter->practitioner,
            'medications' => $medications,
            'diagnoses' => $encounter->diagnoses,
            'date' => Carbon::parse($encounter->end ?? now()),
            'prescriptionNumber' => 'RX-'.str_pad($encounter->id, 6, '0', STR_PAD_LEFT).'-'.date('Ymd'),
            'orderNumber' => 'RX-'.str_pad($encounter->id, 6, '0', STR_PAD_LEFT).'-'.date('Ymd'),
            'clientThemeCSS' => $this->getClientThemeCSS($client),
            'client' => $client,
            'doctorProfile' => $this->getDoctorProfile($encounter->practitioner),
            'pdfService' => new PrescriptionPdfService,
        ];

        $pdf = Pdf::loadView("documents.prescriptions.templates.{$template}", $data);
        $pdf->setPaper('letter', 'portrait');

        $fileName = 'receta_medica_'.($encounter->patient->identifier ?? $encounter->id).'_'.date('Ymd_His').'.pdf';

        return $pdf->stream($fileName);
    }

    /**
     * Stream medical order PDF (for controller response)
     */
    public function streamMedicalOrderPdf(Encounter $encounter, $serviceRequests = null, ?string $serviceType = null): \Illuminate\Http\Response
    {
        $encounter = $this->loadEncounterWithRelations($encounter, 'medical-order');

        if ($serviceRequests === null) {
            $serviceRequests = $encounter->serviceRequests;
        } elseif (is_array($serviceRequests)) {
            $serviceRequests = ServiceRequest::with('cpt')
                ->whereIn('id', $serviceRequests)
                ->get();
        }

        $client = $this->getClientFromEncounter($encounter);

        $data = [
            'encounter' => $encounter,
            'patient' => $encounter->patient,
            'practitioner' => $encounter->practitioner,
            'serviceRequests' => $serviceRequests,
            'diagnoses' => $encounter->diagnoses,
            'date' => Carbon::parse($encounter->end ?? now()),
            'orderNumber' => 'OM-'.str_pad($encounter->id, 6, '0', STR_PAD_LEFT).'-'.date('Ymd'),
            'clientThemeCSS' => $this->getClientThemeCSS($client),
            'client' => $client,
            'doctorProfile' => $this->getDoctorProfile($encounter->practitioner),
            'pdfService' => new PrescriptionPdfService,
            'serviceType' => $serviceType,
        ];

        $pdf = Pdf::loadView('documents.medical-order', $data);
        $pdf->setPaper('letter', 'portrait');

        $fileName = 'orden_medica_'.($encounter->patient->identifier ?? $encounter->id).'_'.date('Ymd_His').'.pdf';

        return $pdf->stream($fileName);
    }

    /**
     * Load encounter with necessary relationships
     */
    private function loadEncounterWithRelations(Encounter $encounter, string $type): Encounter
    {
        $relations = [
            'patient',
            'practitioner.user',
            'practitioner.qualifications',
            'diagnoses.condition',
            'appointment.client',
            'medicationRequests.medicine',
            'medicationRequests.medication2.ingredients',
            'serviceRequests.cpt',
        ];

        $encounter->load($relations);

        return $encounter;
    }

    /**
     * Get client from encounter (from appointment or practitioner)
     */
    public function getClientFromEncounter(Encounter $encounter): ?Client
    {
        if ($encounter->appointment && $encounter->appointment->client) {
            return $encounter->appointment->client;
        }

        if ($encounter->practitioner && $encounter->practitioner->user) {
            return $encounter->practitioner->user->clients->first();
        }

        return Client::find(1);
    }

    /**
     * Get doctor profile for practitioner
     */
    public function getDoctorProfile(Practitioner $practitioner): ?RecepyDoctorProfile
    {
        return RecepyDoctorProfile::where('user_id', $practitioner->user_id)->first();
    }

    /**
     * Get client theme CSS for PDF styling
     */
    public function getClientThemeCSS(?Client $client): string
    {
        if (! $client) {
            return '';
        }

        $theme = ClientTheme::getActiveForClient($client->id);

        if (! $theme) {
            return '';
        }

        return $theme->generatePdfCSS();
    }

    /**
     * Get selected prescription template for client
     */
    public function getPrescriptionTemplate(?Client $client): string
    {
        if (! $client) {
            return 'template_1';
        }

        return ClientPreference::getPrescriptionTemplate($client->id);
    }

    /**
     * Generate filename for prescription PDF
     */
    public function generatePrescriptionFilename(Encounter $encounter): string
    {
        return 'receta_medica_'.($encounter->patient->identifier ?? $encounter->id).'_'.date('Ymd_His').'.pdf';
    }

    /**
     * Generate filename for medical order PDF
     */
    public function generateMedicalOrderFilename(Encounter $encounter): string
    {
        return 'orden_medica_'.($encounter->patient->identifier ?? $encounter->id).'_'.date('Ymd_His').'.pdf';
    }

    // ========================================
    // Signature and Seal Methods
    // ========================================

    /**
     * Get signature as data URI for PDF embedding
     * First checks doctorProfile, then falls back to practitioner files
     */
    public function getSignatureDataUri(Practitioner $practitioner): string
    {
        // First try doctorProfile
        $doctorProfile = $this->getDoctorProfile($practitioner);

        if ($doctorProfile && ! empty($doctorProfile->signature)) {
            $dataUri = $this->getImageDataUri($doctorProfile->signature);
            if (! empty($dataUri)) {
                return $dataUri;
            }
        }

        // Fallback to practitioner files
        $signature = $practitioner->signature();

        if (! $signature || empty($signature->path)) {
            return '';
        }

        return $this->getPrivateImageDataUri($signature->path);
    }

    /**
     * Get seal as data URI for PDF embedding
     * First checks doctorProfile, then falls back to practitioner files
     */
    public function getSealDataUri(Practitioner $practitioner): string
    {
        // First try doctorProfile
        $doctorProfile = $this->getDoctorProfile($practitioner);

        if ($doctorProfile && ! empty($doctorProfile->seal)) {
            $dataUri = $this->getImageDataUri($doctorProfile->seal);
            if (! empty($dataUri)) {
                return $dataUri;
            }
        }

        // Fallback to practitioner files
        $seal = $practitioner->seal();

        if (! $seal || empty($seal->path)) {
            return '';
        }

        return $this->getPrivateImageDataUri($seal->path);
    }

    /**
     * Check if practitioner has both signature and seal uploaded
     * Checks both doctorProfile and practitioner files
     */
    public function practitionerHasSignatureAndSeal(Practitioner $practitioner): bool
    {
        // First check doctorProfile
        $doctorProfile = $this->getDoctorProfile($practitioner);

        if ($doctorProfile) {
            $hasSignatureInProfile = ! empty($doctorProfile->signature) && $this->imageExists($doctorProfile->signature);
            $hasSealInProfile = ! empty($doctorProfile->seal) && $this->imageExists($doctorProfile->seal);

            if ($hasSignatureInProfile && $hasSealInProfile) {
                return true;
            }
        }

        // Fallback to practitioner files
        $signature = $practitioner->signature();
        $seal = $practitioner->seal();

        if (! $signature || ! $seal) {
            return false;
        }

        $hasSignature = ! empty($signature->path) && Storage::disk('local')->exists($signature->path);
        $hasSeal = ! empty($seal->path) && Storage::disk('local')->exists($seal->path);

        return $hasSignature && $hasSeal;
    }

    // ========================================
    // Notification Helper Methods
    // ========================================

    /**
     * Get unsent medication requests for an encounter
     */
    public function getUnsentMedications(Encounter $encounter): Collection
    {
        return $encounter->medicationRequests()
            ->whereNull('notification_sent_at')
            ->get();
    }

    /**
     * Get unsent service requests for an encounter
     */
    public function getUnsentServiceRequests(Encounter $encounter): Collection
    {
        return $encounter->serviceRequests()
            ->whereNull('notification_sent_at')
            ->get();
    }

    /**
     * Check if there are any unsent prescriptions
     */
    public function hasUnsentPrescriptions(Encounter $encounter): bool
    {
        $hasMedications = $encounter->medicationRequests()
            ->whereNull('notification_sent_at')
            ->exists();

        $hasServiceRequests = $encounter->serviceRequests()
            ->whereNull('notification_sent_at')
            ->exists();

        return $hasMedications || $hasServiceRequests;
    }

    /**
     * Mark medications as sent
     */
    public function markMedicationsAsSent(Encounter $encounter): void
    {
        $encounter->medicationRequests()
            ->whereNull('notification_sent_at')
            ->update(['notification_sent_at' => now()]);
    }

    /**
     * Mark service requests as sent
     */
    public function markServiceRequestsAsSent(Encounter $encounter): void
    {
        $encounter->serviceRequests()
            ->whereNull('notification_sent_at')
            ->update(['notification_sent_at' => now()]);
    }

    // ========================================
    // Private Helper Methods
    // ========================================

    /**
     * Check if an image exists (public or private storage)
     */
    private function imageExists(string $imagePath): bool
    {
        if (empty($imagePath)) {
            return false;
        }

        if (Storage::disk('public')->exists($imagePath)) {
            return true;
        }

        if (Storage::disk('local')->exists($imagePath)) {
            return true;
        }

        if (file_exists(public_path('storage/'.$imagePath))) {
            return true;
        }

        return false;
    }

    /**
     * Get image data URI (handles both public and private storage)
     */
    private function getImageDataUri(string $imagePath): string
    {
        if (empty($imagePath)) {
            return '';
        }

        $extension = pathinfo($imagePath, PATHINFO_EXTENSION);

        if (Storage::disk('public')->exists($imagePath)) {
            $fileContents = Storage::disk('public')->get($imagePath);

            return 'data:image/'.$extension.';base64,'.base64_encode($fileContents);
        }

        if (Storage::disk('local')->exists($imagePath)) {
            $fileContents = Storage::disk('local')->get($imagePath);

            return 'data:image/'.$extension.';base64,'.base64_encode($fileContents);
        }

        $publicPath = public_path('storage/'.$imagePath);
        if (file_exists($publicPath)) {
            $fileContents = file_get_contents($publicPath);

            return 'data:image/'.$extension.';base64,'.base64_encode($fileContents);
        }

        return '';
    }

    /**
     * Generate data URI for private image files
     */
    private function getPrivateImageDataUri(string $imagePath): string
    {
        if (empty($imagePath)) {
            return '';
        }

        if (! Storage::disk('local')->exists($imagePath)) {
            return '';
        }

        $fileContents = Storage::disk('local')->get($imagePath);
        $extension = pathinfo($imagePath, PATHINFO_EXTENSION);

        return 'data:image/'.$extension.';base64,'.base64_encode($fileContents);
    }
}
