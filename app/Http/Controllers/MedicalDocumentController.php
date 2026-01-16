<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use App\Models\MedicalLeave;
use App\Models\MedicationRequest;
use App\Models\ServiceRequest;
use App\Services\EncounterPrescriptionPdfService;
use Barryvdh\DomPDF\Facade\Pdf;
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
                'pdfService' => app(\App\Services\PrescriptionPdfService::class),
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
                'pdfService' => app(\App\Services\PrescriptionPdfService::class),
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
            $medicalLeave = MedicalLeave::with(['patient', 'practitioner', 'condition'])
                ->findOrFail($id);

            $user = auth()->user();
            if (! $user->hasRole('admin') &&
                ! $user->hasRole('recepcionista') &&
                (! $user->practitioner || $user->practitioner->id !== $medicalLeave->practitioner_id)) {
                abort(403, 'No tiene permisos para descargar esta licencia médica.');
            }

            if ($request->has('view')) {
                return view('pdf.medical-leave', compact('medicalLeave'));
            }

            $pdf = Pdf::loadView('pdf.medical-leave', [
                'medicalLeave' => $medicalLeave,
            ]);

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
