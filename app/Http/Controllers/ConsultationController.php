<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Appointment;
use App\Models\ChargeItem;
use App\Models\Encounter;
use App\Models\Invoice;
use App\Models\InvoiceLineItem;
use App\Models\Patient;
use App\Models\PatientPractitionerAuthorization;
use App\Models\PresentIllnesType;
use App\Models\Scopes\EncouterScope;
use App\Models\ServiceCatalog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $client_id = auth()->user()->clients()->first()->id;
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
            // Note: Not using DB::beginTransaction() here because Account creation
            // needs to be committed before Invoice can reference it via foreign key

            $appointment = Appointment::find($appointment_id);
            if (! $appointment) {
                throw new \Exception('Cita no encontrada.');
            }

            // Get client_id from appointment or from authenticated user
            $clientId = $appointment->client_id ?? auth()->user()->getCurrentClient()?->id;

            if (! $clientId) {
                throw new \Exception('No se pudo determinar el client_id de la consulta.');
            }

            $encounter = Encounter::whereAppointmentId($appointment->id)->first();
            if (! $encounter) {
                throw new \Exception('Encounter no encontrado.');
            }

            // CAMBIAR EL ESTATUS DE LOS SERVICE REQUEST A ACTIVE
            $encounter->serviceRequests()->update(['status' => 'active']);

            // Get billable ChargeItems for this encounter
            $chargeItems = ChargeItem::where('encounter_id', $encounter->id)
                ->where('status', 'billable')
                ->get();

            $invoice = null;

            if ($chargeItems->count() > 0) {
                // Find or create patient account - use encounter's patient_id instead of appointment's
                $patient = Patient::find($encounter->patient_id);

                if (! $patient) {
                    throw new \Exception("Patient {$encounter->patient_id} not found for encounter {$encounter->id}");
                }

                // Get client_id from user or appointment
                $currentClientId = $clientId ?? auth()->user()->getCurrentClient()?->id;

                $account = Account::where('patient_id', $patient->id)
                    ->where('client_id', $currentClientId)
                    ->active()
                    ->first();

                \Log::info('Creating invoice for patient', [
                    'appointment_patient_id' => $appointment->patient_id,
                    'patient_found_id' => $patient->id,
                    'account_id' => $account?->id,
                ]);

                if (! $account) {
                    // Create account within the transaction
                    try {
                        $account = Account::create([
                            'fhir_id' => 'account-'.Str::uuid(),
                            'name' => "Patient Account - {$patient->name}",
                            'description' => "Primary account for patient {$patient->name}",
                            'type' => [
                                'coding' => [
                                    [
                                        'system' => 'http://terminology.hl7.org/CodeSystem/account-type',
                                        'code' => 'patient',
                                        'display' => 'Patient Account',
                                    ],
                                ],
                            ],
                            'status' => 'active',
                            'patient_id' => $patient->id,
                            'client_id' => $currentClientId,
                            'created_by' => auth()->id(),
                        ]);

                        // Verify the account was created successfully
                        if (! $account || ! $account->id) {
                            throw new \Exception('Account object is null or missing ID after creation');
                        }

                        \Log::info('Account created successfully', [
                            'account_id' => $account->id,
                            'patient_id' => $patient->id,
                            'client_id' => $currentClientId,
                        ]);
                    } catch (\Exception $e) {
                        throw new \Exception('Error creating patient account: '.$e->getMessage());
                    }
                }

                $identifier = 'INV-'.now()->format('Ymd').'-'.str_pad($encounter->id, 6, '0', STR_PAD_LEFT);

                // Verify all foreign keys exist before creating invoice
                $practitioner = \App\Models\Practitioner::find($appointment->practitioner_id);
                if (! $practitioner) {
                    throw new \Exception("Practitioner {$appointment->practitioner_id} not found");
                }

                // Get branch_id from appointment's consulting_room
                $branchId = $appointment->consultingRoom?->branch_id;

                // Create invoice for this encounter
                try {
                    $invoice = Invoice::create([
                        'fhir_id' => 'invoice-'.Str::uuid(),
                        'identifier' => $identifier,
                        'status' => 'issued',
                        'type' => 'invoice',
                        'patient_id' => $encounter->patient_id,
                        'encounter_id' => $encounter->id,
                        'account_id' => $account->id,
                        'date' => now(),
                        'issue_date' => now(),
                        'due_date' => now()->addDays(30), // 30 days payment term
                        'payment_terms' => '30 días',
                        'currency' => 'USD',
                        'subtotal_amount' => 0,
                        'tax_amount' => 0,
                        'total_amount' => 0,
                        'amount_due' => 0,
                        'payment_status' => 'unpaid',
                        'recipient_patient_id' => $encounter->patient_id,
                        'performer_practitioner_id' => $encounter->practitioner_id,
                        'client_id' => $clientId,
                        'branch_id' => $branchId,
                        'issuer_organization_id' => $clientId,
                        'created_by' => auth()->id(),
                        // Let the model generate invoice_number automatically
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    \Log::error('Failed to create invoice', [
                        'error' => $e->getMessage(),
                        'account_id' => $account->id,
                        'patient_id' => $encounter->patient_id,
                        'practitioner_id' => $encounter->practitioner_id,
                        'encounter_id' => $encounter->id,
                        'client_id' => $clientId,
                    ]);
                    throw new \Exception('Error creating invoice: '.$e->getMessage());
                }

                $subtotal = 0;
                $lineItemNumber = 1;

                // Create invoice line items from ChargeItems
                foreach ($chargeItems as $chargeItem) {
                    $lineTotal = $chargeItem->total_price;
                    $subtotal += $lineTotal;
                    $serviceCatalog = ServiceCatalog::find($chargeItem->service_catalog_id);

                    // Ensure we have a valid service description
                    $serviceDescription = $serviceCatalog?->description ?? $chargeItem->definition ?? 'Servicio médico';
                    $serviceCode = $serviceCatalog?->code ?? 'N/A';

                    InvoiceLineItem::create([
                        'invoice_id' => $invoice->id,
                        'charge_item_id' => $chargeItem->id,
                        'sequence' => $lineItemNumber++,
                        'service_description' => $serviceDescription,
                        'cpt_code' => $chargeItem->cpt_code,
                        'service_code' => $serviceCode,
                        'quantity' => $chargeItem->quantity,
                        'unit_price' => $chargeItem->unit_price_value,
                        'line_total_gross' => $lineTotal,
                        'service_date' => $chargeItem->occurrence_date_time ?? now(),
                        'modifier_codes' => $chargeItem->modifier_codes,
                        'client_id' => $chargeItem->client_id,
                    ]);

                    // Mark ChargeItem as billed
                    $chargeItem->markAsBilled();
                }

                // Calculate tax (if applicable) - assuming 7% tax rate for Panama
                $taxRate = 0.07; // 7% ITBMS for Panama
                $taxAmount = $subtotal * $taxRate;
                $totalAmount = $subtotal + $taxAmount;

                // Update invoice totals
                $invoice->update([
                    'subtotal_amount' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'total_tax' => $taxAmount,
                    'total_amount' => $totalAmount,
                    'total_net' => $totalAmount,
                    'amount_due' => $totalAmount,
                ]);
            }

            // Update appointment status
            if(auth()->user()->id == $appointment->practitioner->user_id)
                $appointment->update(['status' => 'fulfilled']);

            // Check if prescriptions will be sent (before saving encounter triggers observer)
            $prescriptionNotificationInfo = $this->checkPrescriptionNotification($encounter);

            // Update encounter status
            if(auth()->user()->id == $appointment->practitioner->user_id){
                $encounter->status = 'finished';
                $encounter->end = now();
                $encounter->save();
            }


            // Build success message
            $messages = ['¡Consulta finalizada con éxito!'];

            if ($invoice) {
                $downloadUrl = route('invoice.download', $invoice->id);
                $messages[] = 'Factura generada: '.$invoice->identifier.' - <a href="'.$downloadUrl.'" target="_blank" class="btn btn-sm btn-primary">Descargar PDF</a>';
            } else {
                $messages[] = '(Sin servicios facturables)';
            }

            // Add prescription notification message if applicable
            if ($prescriptionNotificationInfo['will_send']) {
                $sentItems = [];
                if ($prescriptionNotificationInfo['has_medications']) {
                    $sentItems[] = 'recetas médicas';
                }
                if ($prescriptionNotificationInfo['has_service_requests']) {
                    $sentItems[] = 'órdenes de laboratorio/imágenes';
                }
                $messages[] = '<br><i class="fa fa-envelope text-success"></i> Se enviaron '.implode(' y ', $sentItems).' al correo del paciente: <strong>'.$prescriptionNotificationInfo['patient_email'].'</strong>';
            }

            session()->flash('message.success', implode(' ', $messages));

            return redirect(route('consultation.view', $encounter->id));

        } catch (\Exception $e) {
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
     * Check if prescription notification will be sent to patient
     * This mirrors the logic in EncounterObserver to predict if email will be sent
     */
    private function checkPrescriptionNotification(Encounter $encounter): array
    {
        $result = [
            'will_send' => false,
            'has_medications' => false,
            'has_service_requests' => false,
            'patient_email' => null,
            'reason' => null,
        ];

        $practitioner = $encounter->practitioner;
        $patient = $encounter->patient;

        // Check if practitioner has signature and seal
        if (! $practitioner || ! $practitioner->signature() || ! $practitioner->seal()) {
            $result['reason'] = 'practitioner_no_signature_seal';

            return $result;
        }

        // Check if patient has email
        if (! $patient || ! $patient->email) {
            $result['reason'] = 'patient_no_email';

            return $result;
        }

        // Check for unsent prescriptions
        $hasUnsentMedications = $encounter->medicationRequests()
            ->whereNull('notification_sent_at')
            ->exists();

        $hasUnsentServiceRequests = $encounter->serviceRequests()
            ->whereNull('notification_sent_at')
            ->exists();

        // If no unsent prescriptions, skip
        if (! $hasUnsentMedications && ! $hasUnsentServiceRequests) {
            $result['reason'] = 'no_unsent_prescriptions';

            return $result;
        }

        $result['will_send'] = true;
        $result['has_medications'] = $hasUnsentMedications;
        $result['has_service_requests'] = $hasUnsentServiceRequests;
        $result['patient_email'] = $patient->email;

        return $result;
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
            $sello = \App\Models\File::where('table_name', 'practitioners')
                ->where('record_id', $data->practitioner_id)
                ->where('type', 'seal')
                ->first()?->path ?? '';

            $firma = \App\Models\File::where('table_name', 'practitioners')
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
            $patient->notify(new \App\Notifications\EncounterPrescriptionNotification(
                $encounter,
                $hasUnsentMedications,
                $hasUnsentServiceRequests
            ));

            return redirect()->back()->with('message.success', 'Prescripciones enviadas por WhatsApp exitosamente.');

        } catch (\Exception $e) {
            \Log::error('Error al reenviar prescripciones por WhatsApp', [
                'encounter_id' => $encounter_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('message.error', 'Error al enviar prescripciones: '.$e->getMessage());
        }
    }
}
