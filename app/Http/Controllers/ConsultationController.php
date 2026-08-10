<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Encounter;
use App\Models\File;
use App\Models\Patient;
use App\Models\PatientPractitionerAuthorization;
use App\Models\PresentIllnesType;
use App\Models\Scopes\EncouterScope;
use App\Notifications\EncounterPrescriptionNotification;
use App\Services\Consultation\ConsultationInvoiceService;
use App\Services\Consultation\ConsultationValidator;
use App\Services\Consultation\EncounterFinalizationService;
use App\Services\Consultation\ServiceRequestProcessor;
use App\Services\Consultation\SupplyRequestProcessor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ConsultationController extends Controller
{
    public function index()
    {
        return view('consultations.index');
    }

    public function show(Request $request, $appointment_id)
    {

        $template = [];
        $client = auth()->user()->clients()->first();

        if (! $client) {
            abort(403, 'Usuario no tiene acceso a editar esta consulta.');
        }

        $client_id = $client->id;
        $appointment = Appointment::findOrFail($appointment_id);
        $patient = Patient::findOrFail($appointment->patient_id);

        $consultation = Encounter::whereAppointmentId($appointment_id)->first();

        $type = '4525004'; // consulta de medicina general
        if ($appointment->medical_speciality_id != '58') {
            $type = '26172008';
        } // consulta de especialidad

        if (! $consultation) {
            $consultation = Encounter::create(['fhir_id' => 'encounter-'.Str::uuid(),
                'patient_id' => $appointment->patient_id,
                'practitioner_id' => $appointment->practitioner_id,
                'assisted_by' => $appointment->assisted_by,
                'appointment_id' => $appointment->id,
                'identifier' => 'ENC-'.strtoupper(Str::random(7)),
                'status' => 'in-progress',
                'class' => 'SS',
                'type' => $type,
                'priority' => 'routine',
                'start' => now(),
                'medical_speciality_id' => $appointment->medical_speciality_id,
                'end' => now()]);
        }

        return view('consultations.create', compact('consultation', 'appointment', 'patient'));
    }

    public function finished(Request $request, $appointment_id)
    {
        try {
            // 1. Validación y autorización
            $validator = app(ConsultationValidator::class);
            $validated = $validator->validateConsultationFinalization($appointment_id);

            $appointment = $validated['appointment'];
            $encounter = $validated['encounter'];
            $clientId = $validated['client_id'];
            $isAuthorized = $validator->isUserAuthorizedToFinalize($appointment);

            // 2. Procesar Service Requests
            app(ServiceRequestProcessor::class)->processServiceRequests($encounter);

            // 3. Procesar Supply Requests (transacción atómica)
            app(SupplyRequestProcessor::class)->processSupplyRequests($encounter);

            // 4. Generar factura si hay items cobrables
            $invoice = app(ConsultationInvoiceService::class)
                ->generateInvoiceForEncounter($encounter, $clientId);

            // 5. Verificar notificación de prescripción (antes de que observer se dispare)
            $finalizationService = app(EncounterFinalizationService::class);
            $prescriptionInfo = $finalizationService->checkPrescriptionNotification($encounter);

            // 6. Finalizar Encounter y Appointment
            $finalizationService->finalizeConsultation($encounter, $appointment, $isAuthorized);

            // 7. Construir y mostrar mensaje de éxito
            $messages = $this->buildSuccessMessage($invoice, $prescriptionInfo);
            session()->flash('message.success', implode(' ', $messages));

            return redirect(route('consultation.view', $encounter->id));

        } catch (\Exception $e) {
            Log::error('Error al finalizar consulta', [
                'appointment_id' => $appointment_id ?? null,
                'user_id' => auth()->id(),
                'client_id' => auth()->user()?->getCurrentClient()?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            session()->flash('message.error', 'Error al finalizar consulta: '.$e->getMessage());

            return back()->withInput();
        }
    }

    public function view($encounter_id)
    {
        $encounter = Encounter::with([
            'patient',
            'practitioner',
            'appointment',
            'vitalSigns.observationType',
            'presentIllnesses',
            'medicationRequests',
            'serviceRequests',
            'referrals',
            'physicalExams',
            'diagnoses.condition.icd10Code',
        ])->findOrFail($encounter_id);

        return view('consultations.view', compact('encounter'));
    }

    /**
     * Construir mensaje de éxito con información de factura y prescripciones
     */
    protected function buildSuccessMessage($invoice, array $prescriptionInfo): array
    {
        $messages = ['¡Consulta finalizada con éxito!'];

        if ($invoice) {
            $downloadUrl = route('invoice.download', $invoice->id);
            $messages[] = 'Factura generada: '.$invoice->identifier.' - <a href="'.$downloadUrl.'" target="_blank" class="btn btn-sm btn-primary">Descargar PDF</a>';
        } else {
            $messages[] = '(Sin servicios facturables)';
        }

        if ($prescriptionInfo['will_send']) {
            $sentItems = [];
            if ($prescriptionInfo['has_medications']) {
                $sentItems[] = 'recetas médicas';
            }
            if ($prescriptionInfo['has_service_requests']) {
                $sentItems[] = 'órdenes de laboratorio/imágenes';
            }
            $messages[] = '<br><i class="fa fa-envelope text-success"></i> Se enviaron '.implode(' y ', $sentItems).' al correo del paciente: <strong>'.$prescriptionInfo['patient_email'].'</strong>';
        }

        return $messages;
    }

    public function downloadResumen(Request $request, $appointment_id)
    {

        try {
            $appointment = Appointment::find($appointment_id);
            $auth = false;
            if (auth()->user()->hasRole('doctor') && auth()->user()->pranctitioner && PatientPractitionerAuthorization::wherePatientId($appointment->patient_id)
                ->wherePrantitionerId(auth()->user()->pranctitioner->id)->first()) {
                $auth = true;
            }
            $data = Encounter::when($auth, function ($query) {
                $query->withoutGlobalScope(EncouterScope::class);
            })
                ->whereAppointmentId($appointment_id)->first();
            $data['consultation-list'] = PresentIllnesType::get();
            $consultation_disabilities = [];
            $lang = 'esp';
            $home_visit = false;

            // Buscar firma y sello del practitioner
            $sello = File::where('table_name', 'practitioners')
                ->where('record_id', $data->practitioner_id)
                ->where('type', 'seal')
                ->first()?->path ?? '';

            $firma = File::where('table_name', 'practitioners')
                ->where('record_id', $data->practitioner_id)
                ->where('type', 'signature')
                ->first()?->path ?? '';

            $mode = 'full';
            foreach ($data->diagnoses()->get() as $d) {
                if ($d->condition) {
                    if ($d->condition->icd10Code) {
                        array_push($consultation_disabilities, '<td>'.$d->condition->code.'</td><td>'.$d->condition->icd10Code->description_es.'</td>');
                    } else {
                        array_push($consultation_disabilities, '<td>'.$d->condition->code.'</td><td>'.$d->condition->onset_info.'</td>');
                    }
                }

            }

            if ($request->has('html')) {
                return view('consultations.consultation_report.index', compact('data', 'lang', 'home_visit', 'sello', 'firma', 'mode', 'consultation_disabilities'));
            }

            $pdf = Pdf::loadView('consultations.consultation_report.index', compact('data', 'lang', 'home_visit', 'sello', 'firma', 'mode', 'consultation_disabilities'));

            return $pdf->stream($data->identifier.'.pdf');
        } catch (\Exception $e) {
            Log::error('Error al descargar resumen de consulta', [
                'appointment_id' => $appointment_id ?? null,
                'user_id' => auth()->id(),
                'client_id' => auth()->user()?->getCurrentClient()?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            session()->flash('message.error', 'Error al descargar el resumen: '.$e->getMessage());

            return back();
        }

    }

    /**
     * Resend prescriptions via WhatsApp manually
     */
    public function resendPrescriptionsWhatsApp($encounter_id)
    {
        try {
            $encounter = Encounter::with([
                'patient',
                'practitioner',
                'medicationRequests',
                'serviceRequests',
            ])->findOrFail($encounter_id);

            // Check if there are unsent prescriptions
            $hasUnsentMedications = $encounter->medicationRequests()
                ->whereNull('notification_sent_at')
                ->exists();

            $hasUnsentServiceRequests = $encounter->serviceRequests()
                ->whereNull('notification_sent_at')
                ->exists();

            if (! $hasUnsentMedications && ! $hasUnsentServiceRequests) {
                return redirect()->back()->with('message.warning', 'No hay recetas pendientes de envío para este encounter.');
            }

            // Verify patient has phone
            $patient = $encounter->patient;
            if (! $patient || ! ($patient->whatsapp_phone || $patient->phone)) {
                return redirect()->back()->with('message.error', 'El paciente no tiene número de WhatsApp configurado.');
            }

            // Send notification
            $patient->notify(new EncounterPrescriptionNotification(
                $encounter,
                $hasUnsentMedications,
                $hasUnsentServiceRequests
            ));

            return redirect()->back()->with('message.success', 'Prescripciones enviadas por WhatsApp exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al reenviar prescripciones por WhatsApp', [
                'encounter_id' => $encounter_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('message.error', 'Error al enviar prescripciones: '.$e->getMessage());
        }
    }
}
