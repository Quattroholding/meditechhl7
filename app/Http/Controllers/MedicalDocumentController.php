<?php

namespace App\Http\Controllers;

use App\Models\ClientPreference;
use App\Models\Encounter;
use App\Models\MedicalLeave;
use App\Models\MedicationRequest;
use App\Models\ServiceRequest;
use App\Services\EncounterPrescriptionPdfService;
use App\Services\PrescriptionPdfService;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;

class MedicalDocumentController extends Controller
{
    public function __construct(
        protected EncounterPrescriptionPdfService $pdfService
    ) {}

    /**
     * Generar receta médica para un encounter específico
     */
    public function generatePrescription(Request $request, $encounterId)
    {
        try {
            $encounter = Encounter::findOrFail($encounterId);

            if ($encounter->medicationRequests->isEmpty()) {
                return response()->json([
                    'error' => 'Este encuentro no tiene medicamentos prescritos.',
                ], 400);
            }

            return $this->pdfService->streamPrescriptionPdf($encounter);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al generar la receta médica: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generar orden médica para un encounter específico
     */
    public function generateMedicalOrder(Request $request, $encounterId)
    {
        try {
            $encounter = Encounter::findOrFail($encounterId);

            if ($encounter->serviceRequests->isEmpty()) {
                return response()->json([
                    'error' => 'Este encuentro no tiene servicios solicitados.',
                ], 400);
            }

            $serviceType = $request->input('service_type');

            return $this->pdfService->streamMedicalOrderPdf($encounter, null, $serviceType);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al generar la orden médica: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generar orden de referencia para un encounter específico
     */
    public function generateReferralOrder(Request $request, $encounterId)
    {
        try {
            $encounter = Encounter::findOrFail($encounterId);

            if ($encounter->referrals->isEmpty()) {
                return response()->json([
                    'error' => 'Este encuentro no tiene referrals a especialistas.',
                ], 400);
            }

            return $this->pdfService->streamReferralOrderPdf($encounter);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al generar la orden de referencia: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generar receta médica para medicamentos específicos
     */
    public function generatePrescriptionByMedications(Request $request)
    {
        $medicationIds = $request->input('medication_ids', []);

        if (empty($medicationIds)) {
            return response()->json([
                'error' => 'No se especificaron medicamentos.',
            ], 400);
        }

        try {
            $medications = MedicationRequest::with([
                'patient',
                'practitioner.user',
                'practitioner.qualifications',
                'medicine',
                'encounter.diagnoses.condition',
            ])->whereIn('id', $medicationIds)->get();

            if ($medications->isEmpty()) {
                return response()->json([
                    'error' => 'No se encontraron los medicamentos especificados.',
                ], 400);
            }

            $encounter = $medications->first()->encounter;

            if (! $encounter) {
                return response()->json([
                    'error' => 'No se encontró el encounter asociado.',
                ], 400);
            }

            return $this->pdfService->streamPrescriptionPdf($encounter, $medicationIds);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al generar la receta médica: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generar orden médica para servicios específicos
     */
    public function generateMedicalOrderByServices(Request $request)
    {
        $serviceIds = $request->input('service_ids', []);

        if (empty($serviceIds)) {
            return response()->json([
                'error' => 'No se especificaron servicios.',
            ], 400);
        }

        try {
            $services = ServiceRequest::with([
                'patient',
                'practitioner.user',
                'practitioner.medicalSpeciality',
                'cpt',
                'encounter.diagnoses.condition',
            ])->whereIn('id', $serviceIds)->get();

            if ($services->isEmpty()) {
                return response()->json([
                    'error' => 'No se encontraron los servicios especificados.',
                ], 400);
            }

            $encounter = $services->first()->encounter;

            if (! $encounter) {
                return response()->json([
                    'error' => 'No se encontró el encounter asociado.',
                ], 400);
            }

            $serviceType = $request->input('service_type');

            return $this->pdfService->streamMedicalOrderPdf($encounter, $serviceIds, $serviceType);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al generar la orden médica: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Vista previa de la receta médica (sin descargar)
     */
    public function previewPrescription($encounterId)
    {
        try {
            $encounter = Encounter::with([
                'patient',
                'practitioner.user',
                'practitioner.qualifications',
                'medicationRequests.medicine',
                'diagnoses.condition',
                'appointment.client',
            ])->findOrFail($encounterId);

            if ($encounter->medicationRequests->isEmpty()) {
                abort(400, 'Este encuentro no tiene medicamentos prescritos.');
            }

            $client = $this->pdfService->getClientFromEncounter($encounter);

            $data = [
                'encounter' => $encounter,
                'patient' => $encounter->patient,
                'practitioner' => $encounter->practitioner,
                'medications' => $encounter->medicationRequests,
                'diagnoses' => $encounter->diagnoses,
                'date' => now(),
                'prescriptionNumber' => 'RX-'.str_pad($encounterId, 6, '0', STR_PAD_LEFT).'-'.date('Ymd'),
                'clientThemeCSS' => $this->pdfService->getClientThemeCSS($client),
                'doctorProfile' => $this->pdfService->getDoctorProfile($encounter->practitioner),
                'pdfService' => app(PrescriptionPdfService::class),
            ];

            return view('documents.prescription-new', $data);

        } catch (\Exception $e) {
            abort(500, 'Error al mostrar la receta médica: '.$e->getMessage());
        }
    }

    /**
     * Vista previa de la orden médica (sin descargar)
     */
    public function previewMedicalOrder($encounterId)
    {
        try {
            $encounter = Encounter::with([
                'patient',
                'practitioner.user',
                'practitioner.qualifications',
                'serviceRequests.cpt',
                'diagnoses.condition',
                'appointment.client',
            ])->findOrFail($encounterId);

            if ($encounter->serviceRequests->isEmpty()) {
                abort(400, 'Este encuentro no tiene servicios solicitados.');
            }

            $client = $this->pdfService->getClientFromEncounter($encounter);

            $data = [
                'encounter' => $encounter,
                'patient' => $encounter->patient,
                'practitioner' => $encounter->practitioner,
                'serviceRequests' => $encounter->serviceRequests,
                'diagnoses' => $encounter->diagnoses,
                'date' => now(),
                'orderNumber' => 'OM-'.str_pad($encounterId, 6, '0', STR_PAD_LEFT).'-'.date('Ymd'),
                'clientThemeCSS' => $this->pdfService->getClientThemeCSS($client),
                'client' => $client,
                'doctorProfile' => $this->pdfService->getDoctorProfile($encounter->practitioner),
                'pdfService' => app(PrescriptionPdfService::class),
                'serviceType' => null,
            ];

            return view('documents.medical-order', $data);

        } catch (\Exception $e) {
            abort(500, 'Error al mostrar la orden médica: '.$e->getMessage());
        }
    }

    /**
     * Descargar PDF de licencia médica
     */
    public function downloadMedicalLeavePdf(Request $request, $id)
    {
        try {
            $medicalLeave = MedicalLeave::with(['patient', 'practitioner.specialties', 'condition', 'client'])
                ->findOrFail($id);

            $user = auth()->user();
            if (! $user->hasRole('admin') &&
                ! $user->hasRole('recepcionista') &&
                (! $user->practitioner || $user->practitioner->id !== $medicalLeave->practitioner_id)) {
                abort(403, 'No tiene permisos para descargar esta licencia médica.');
            }

            // Obtener el template seleccionado para el cliente
            $template = ClientPreference::getMedicalLeaveTemplate($medicalLeave->client_id);

            // Preparar datos para el template
            $branch = (object) [
                'name' => $medicalLeave->clinic_name ?? $medicalLeave->client->name ?? 'Clínica',
                'address' => $medicalLeave->clinic_address ?? '',
                'phone' => $medicalLeave->clinic_phone ?? '',
                'email' => $medicalLeave->client->email ?? '',
            ];

            $patient = (object) [
                'full_name' => $medicalLeave->patient_name ?? $medicalLeave->patient->full_name,
                'identifier_value' => $medicalLeave->patient->identifier ?? '',
                'birth_date' => $medicalLeave->patient->birth_date,
                'age' => $medicalLeave->patient->age ?? null,
            ];

            // Obtener la primera especialidad del médico si existe
            $specialty = 'Medicina General';
            if ($medicalLeave->practitioner && $medicalLeave->practitioner->specialties->isNotEmpty()) {
                $specialty = $medicalLeave->practitioner->specialties->first()->name;
            }

            $doctor = (object) [
                'full_name' => $medicalLeave->practitioner_name ?? $medicalLeave->practitioner->full_name,
                'specialty' => $specialty,
                'license' => $medicalLeave->practitioner_license_number ?? $medicalLeave->practitioner->license_number ?? '',
            ];

            $diagnosis = $medicalLeave->diagnosis ?? 'Diagnóstico médico';
            $startDate = $medicalLeave->start_datetime->format('d/m/Y');
            $endDate = $medicalLeave->end_datetime->format('d/m/Y');
            $days = $medicalLeave->total_days;
            $city = $medicalLeave->client->city ?? 'Ciudad';
            $issueDate = $medicalLeave->issue_date->format('d/m/Y');
            $documentNumber = $medicalLeave->identifier;
            $firma = $medicalLeave->getPractitionerFilePathAttribute('signature') ?? '';
            $sello = $medicalLeave->getPractitionerFilePathAttribute('seal') ?? '';

            $start_time = $medicalLeave->start_datetime->format('H:i');
            $start_day = $medicalLeave->start_datetime->format('d');
            $start_month = $medicalLeave->start_datetime->format('m');
            $start_year = $medicalLeave->start_datetime->format('Y');
            $end_time = $medicalLeave->end_datetime->format('H:i');
            $end_day = $medicalLeave->end_datetime->format('d');
            $end_month = $medicalLeave->end_datetime->format('m');
            $end_year = $medicalLeave->end_datetime->format('Y');
            $logo = $medicalLeave->client->logo;

            // Generar QR para verificación
            $verificationUrl = route('medical-leave.verify', ['verificationHash' => $medicalLeave->verification_hash], true);
            $qrCode = new QrCode($verificationUrl);
            $writer = new PngWriter;
            $qrResult = $writer->write($qrCode);
            $qrDataUri = $qrResult->getDataUri();

            $data = [
                'branch' => $branch,
                'patient' => $patient,
                'doctor' => $doctor,
                'diagnosis' => $diagnosis,
                'startDate' => $startDate,
                'endDate' => $endDate,

                'start_time' => $start_time,
                'start_day' => $start_day,
                'start_month' => $start_month,
                'start_year' => $start_year,
                'end_time' => $end_time,
                'end_day' => $end_day,
                'end_month' => $end_month,
                'end_year' => $end_year,

                'days' => $days,
                'city' => $city,
                'issueDate' => $issueDate,
                'documentNumber' => $documentNumber,
                'medicalLeave' => $medicalLeave, // Mantener por compatibilidad
                'firma' => $firma,
                'sello' => $sello,
                'logo' => $logo,
                'qrCode' => $qrDataUri,
                'verificationUrl' => $verificationUrl,
            ];

            // Si es solo vista previa
            if ($request->has('view')) {
                return view("templates.medical_leave.{$template}", $data);
            }

            // Generar PDF
            $pdf = Pdf::loadView("templates.medical_leave.{$template}", $data);

            $filename = 'licencia-medica-'.$medicalLeave->identifier.'.pdf';

            return $pdf->stream($filename);

        } catch (\Exception $e) {
            \Log::error('Error al descargar licencia médica: '.$e->getMessage(), [
                'id' => $id,
                'exception' => $e,
            ]);
            abort(500, 'Error al generar el PDF de la incapacidad médica: '.$e->getMessage());
        }
    }
}
