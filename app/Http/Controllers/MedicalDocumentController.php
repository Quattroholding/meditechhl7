<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientTheme;
use App\Models\Encounter;
use App\Models\MedicalLeave;
use App\Models\MedicationRequest;
use App\Models\Recepy\RecepyDoctorProfile;
use App\Models\ServiceRequest;
use App\Services\PrescriptionPdfService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MedicalDocumentController extends Controller
{
    /**
     * Generar receta médica para un encounter específico
     */
    public function generatePrescription(Request $request, $encounterId)
    {
        try {
            // Buscar el encounter con sus relaciones
            $encounter = Encounter::with([
                'patient',
                'practitioner.user',
                'practitioner.qualifications',
                'medicationRequests.medicine',
                'diagnoses.condition',
                'appointment',
            ])->findOrFail($encounterId);

            // Verificar que hay medication requests
            if ($encounter->medicationRequests->isEmpty()) {
                return response()->json([
                    'error' => 'Este encuentro no tiene medicamentos prescritos.',
                ], 400);
            }

            $client = Client::find(1);
            if ($encounter->appointment->client) {
                $client = $encounter->appointment->client;
            } elseif ($encounter->practitioner->user) {
                $client = $encounter->practitioner->user->clients->first();
            }
            // Obtener el cliente del encounter

            // Preparar datos para la vista
            $data = [
                'encounter' => $encounter,
                'patient' => $encounter->patient,
                'practitioner' => $encounter->practitioner,
                'medications' => $encounter->medicationRequests,
                'diagnoses' => $encounter->diagnoses,
                'date' => Carbon::parse($encounter->end),
                'prescriptionNumber' => 'RX-'.str_pad($encounterId, 6, '0', STR_PAD_LEFT).'-'.date('Ymd'),
                'clientThemeCSS' => $this->getClientThemeCSS($client),
                'doctorProfile' => RecepyDoctorProfile::whereUserId($encounter->practitioner->user_id)->first(),
                'pdfService' => new PrescriptionPdfService,
            ];

            // Generar PDF
            $pdf = PDF::loadView('documents.prescription-new', $data);
            $pdf->setPaper('letter', 'portrait');

            // Nombre del archivo
            $fileName = 'receta_medica_'.$encounter->patient->identifier.'_'.date('Ymd_His').'.pdf';

            // Retornar el PDF para descarga
            return $pdf->stream($fileName);

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
            // Buscar el encounter con sus relaciones
            $encounter = Encounter::with([
                'patient',
                'practitioner.user',
                'practitioner.qualifications',
                'serviceRequests.cpt',
                'diagnoses.condition',
                'appointment',
            ])->findOrFail($encounterId);

            // Verificar que hay service requests
            if ($encounter->serviceRequests->isEmpty()) {
                return response()->json([
                    'error' => 'Este encuentro no tiene servicios solicitados.',
                ], 400);
            }

            // Obtener el cliente del encounter
            $client = Client::find(1);

            if ($encounter->appointment->client) {
                $client = $encounter->appointment->client;
            } elseif ($encounter->practitioner->user) {
                $client = $encounter->practitioner->user->clients->first();
            }

            // Preparar datos para la vista
            $data = [
                'encounter' => $encounter,
                'patient' => $encounter->patient,
                'practitioner' => $encounter->practitioner,
                'serviceRequests' => $encounter->serviceRequests,
                'diagnoses' => $encounter->diagnoses,
                'date' => Carbon::parse($encounter->end),
                'orderNumber' => 'OM-'.str_pad($encounterId, 6, '0', STR_PAD_LEFT).'-'.date('Ymd'),
                'clientThemeCSS' => $this->getClientThemeCSS($client),
                'client' => $client,
                'doctorProfile' => RecepyDoctorProfile::whereUserId($encounter->practitioner->user_id)->first(),
                'pdfService' => new PrescriptionPdfService,
                'serviceType' => $request->input('service_type'),
            ];

            // Generar PDF
            $pdf = PDF::loadView('documents.medical-order', $data);
            $pdf->setPaper('letter', 'portrait');

            // Nombre del archivo
            $fileName = 'orden_medica_'.$encounter->patient->identifier.'_'.date('Ymd_His').'.pdf';

            // Retornar el PDF para descarga
            return $pdf->stream($fileName);

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
            // Buscar los medication requests
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

            // Obtener el encounter principal (del primer medicamento)
            $encounter = $medications->first()->encounter;
            $client = $encounter?->appointment?->client ?? $medications->first()->practitioner?->user?->clients?->first();

            // Preparar datos para la vista
            $data = [
                'encounter' => $encounter,
                'patient' => $medications->first()->patient,
                'practitioner' => $medications->first()->practitioner,
                'medications' => $medications,
                'diagnoses' => $encounter ? $encounter->diagnoses : collect([]),
                'date' => Carbon::now(),
                'prescriptionNumber' => 'RX-CUSTOM-'.date('Ymd_His'),
                'clientThemeCSS' => $this->getClientThemeCSS($client),
            ];

            // Generar PDF
            $pdf = PDF::loadView('documents.prescription', $data);
            $pdf->setPaper('letter', 'portrait');

            // Nombre del archivo
            $fileName = 'receta_medica_personalizada_'.date('Ymd_His').'.pdf';

            // Retornar el PDF para descarga
            return $pdf->download($fileName);

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
            // Buscar los service requests
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

            // Obtener el encounter principal (del primer servicio)
            $encounter = $services->first()->encounter;
            $client = $encounter?->appointment?->client ?? $services->first()->practitioner?->user?->clients?->first();

            // Preparar datos para la vista
            $data = [
                'encounter' => $encounter,
                'patient' => $services->first()->patient,
                'practitioner' => $services->first()->practitioner,
                'serviceRequests' => $services,
                'diagnoses' => $encounter ? $encounter->diagnoses : collect([]),
                'date' => Carbon::now(),
                'orderNumber' => 'OM-CUSTOM-'.date('Ymd_His'),
                'clientThemeCSS' => $this->getClientThemeCSS($client),
                'client' => $client,
                'doctorProfile' => RecepyDoctorProfile::whereUserId($services->first()->practitioner->user_id)->first(),
                'pdfService' => new PrescriptionPdfService,
                'serviceType' => $request->input('service_type'),
            ];

            // Generar PDF
            $pdf = PDF::loadView('documents.medical-order', $data);
            $pdf->setPaper('letter', 'portrait');

            // Nombre del archivo
            $fileName = 'orden_medica_personalizada_'.date('Ymd_His').'.pdf';

            // Retornar el PDF para descarga
            return $pdf->download($fileName);

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
                'appointment',
            ])->findOrFail($encounterId);

            if ($encounter->medicationRequests->isEmpty()) {
                abort(400, 'Este encuentro no tiene medicamentos prescritos.');
            }

            $client = $encounter->appointment->client ?? $encounter->practitioner->user->clients->first();

            $data = [
                'encounter' => $encounter,
                'patient' => $encounter->patient,
                'practitioner' => $encounter->practitioner,
                'medications' => $encounter->medicationRequests,
                'diagnoses' => $encounter->diagnoses,
                'date' => Carbon::now(),
                'prescriptionNumber' => 'RX-'.str_pad($encounterId, 6, '0', STR_PAD_LEFT).'-'.date('Ymd'),
                'clientThemeCSS' => $this->getClientThemeCSS($client),
            ];

            return view('documents.prescription', $data);

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
                'appointment',
            ])->findOrFail($encounterId);

            if ($encounter->serviceRequests->isEmpty()) {
                abort(400, 'Este encuentro no tiene servicios solicitados.');
            }

            $client = $encounter->appointment->client ?? $encounter->practitioner->user->clients->first();

            $data = [
                'encounter' => $encounter,
                'patient' => $encounter->patient,
                'practitioner' => $encounter->practitioner,
                'serviceRequests' => $encounter->serviceRequests,
                'diagnoses' => $encounter->diagnoses,
                'date' => Carbon::now(),
                'orderNumber' => 'OM-'.str_pad($encounterId, 6, '0', STR_PAD_LEFT).'-'.date('Ymd'),
                'clientThemeCSS' => $this->getClientThemeCSS($client),
                'client' => $client,
                'doctorProfile' => RecepyDoctorProfile::whereUserId($encounter->practitioner->user_id)->first(),
                'pdfService' => new PrescriptionPdfService,
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
    public function downloadMedicalLeavePdf(Request $request,$id)
    {
        try {
            // Buscar la licencia médica con sus relaciones
            $medicalLeave = MedicalLeave::with(['patient', 'practitioner', 'condition'])
                ->findOrFail($id);

            // Verificar permisos (usuario debe ser el médico que la emitió o tener permisos de admin)
            $user = auth()->user();
            if (! $user->hasRole('admin') &&
                ! $user->hasRole('recepcionista') &&
                (! $user->practitioner || $user->practitioner->id !== $medicalLeave->practitioner_id)) {
                abort(403, 'No tiene permisos para descargar esta licencia médica.');
            }

            if($request->has('view')) return view('pdf.medical-leave',compact('medicalLeave'));

            // Generar el PDF usando la vista
            $pdf = Pdf::loadView('pdf.medical-leave', [
                'medicalLeave' => $medicalLeave,
            ]);

            // Nombre del archivo
            $filename = 'licencia-medica-'.$medicalLeave->identifier.'.pdf';

            // Retornar el PDF como descarga
            return $pdf->stream($filename);

        } catch (\Exception $e) {
            \Log::error('Error al descargar licencia médica: '.$e->getMessage(), [
                'id' => $id,
                'exception' => $e,
            ]);
            abort(500, 'Error al generar el PDF de la incapacidad médica: '.$e->getMessage());
        }
    }

    /**
     * Obtener CSS del tema del cliente para PDFs
     */
    private function getClientThemeCSS($client): string
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
}
