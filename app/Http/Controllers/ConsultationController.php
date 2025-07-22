<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Appointment;
use App\Models\ChargeItem;
use App\Models\Encounter;
use App\Models\EncounterSection;
use App\Models\EncounterTemplate;
use App\Models\Invoice;
use App\Models\InvoiceLineItem;
use App\Models\Patient;
use App\Models\PresentIllnesType;
use App\Models\ServiceCatalog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        if ($appointment->medical_speciality_id != '50') {
            $type = '26172008';
        } // consulta de especialidad

        if (! $consultation) {
            $consultation = Encounter::create(['fhir_id' => 'encounter-'.fake()->uuid(),
                'patient_id' => $appointment->patient_id,
                'practitioner_id' => $appointment->practitioner_id,
                'appointment_id' => $appointment->id,
                'identifier' => 'ENC-'.fake()->unique()->numerify('#######'),
                'status' => 'in-progress',
                'class' => 'SS',
                'type' => $type,
                'priority' => 'routine',
                'start' => now(),
                'medical_speciality_id' => $appointment->medical_speciality_id,
                'end' => now()]);
        }

        $encounter_sections_user = EncounterTemplate::whereUserId(Auth::getUser()->id)->get();

        if ($encounter_sections_user->count() > 0) {
            $encounter_sections = EncounterSection::whereIn('id', $encounter_sections_user->pluck('encounter_section_id'))->get();
        } else {
            $encounter_sections = EncounterSection::whereNull('category')->get();
        }

        $secciones = $encounter_sections->pluck('name_esp', 'id');

        return view('consultations.create', compact('consultation', 'appointment', 'patient', 'encounter_sections', 'secciones'));
    }

    public function finished(Request $request, $appointment_id)
    {
        try {
            DB::beginTransaction();

            $appointment = Appointment::find($appointment_id);
            if (! $appointment) {
                throw new \Exception('Cita no encontrada.');
            }

            $clientId =$appointment->client_id;

            $encounter = Encounter::whereAppointmentId($appointment->id)->first();
            if (! $encounter) {
                throw new \Exception('Encounter no encontrado.');
            }

            // Get billable ChargeItems for this encounter
            $chargeItems = ChargeItem::where('encounter_id', $encounter->id)
                ->where('status', 'billable')
                ->get();

            $invoice = null;

            if ($chargeItems->count() > 0) {
                // Find or create patient account
                $patient = Patient::find($appointment->patient_id);
                $account = Account::where('patient_id', $patient->id)
                    ->where('client_id', $encounter->client_id)
                    ->active()
                    ->first();



                if (! $account) {
                    $account = Account::createPatientAccount($patient);
                }

                $identifier = 'INV-'.now()->format('Ymd').'-'.str_pad($encounter->id, 6, '0', STR_PAD_LEFT);

                // Create invoice for this encounter
                $invoice = Invoice::create([
                    'fhir_id' => 'invoice-'.Str::uuid(),
                    'identifier' => $identifier,
                    'status' => 'issued',
                    'type' => 'invoice',
                    'patient_id' => $appointment->patient_id,
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
                    'recipient_patient_id' => $appointment->patient_id,
                    'performer_practitioner_id' => $appointment->practitioner_id,
                    'client_id' => $clientId,
                    'issuer_organization_id' => $clientId,
                    'created_by' => auth()->id(),
                ]);

                $subtotal = 0;
                $lineItemNumber = 1;

                // Create invoice line items from ChargeItems
                foreach ($chargeItems as $chargeItem) {
                    $lineTotal = $chargeItem->total_price;
                    $subtotal += $lineTotal;
                    $serviceCatalog = ServiceCatalog::find($chargeItem->service_catalog_id);

                    InvoiceLineItem::create([
                        'invoice_id' => $invoice->id,
                        'charge_item_id' => $chargeItem->id,
                        'sequence' => $lineItemNumber++,
                        'service_description' => $serviceCatalog->description,
                        'cpt_code' => $chargeItem->cpt_code,
                        'service_code' => $serviceCatalog->code,
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
            $appointment->update(['status' => 'fulfilled']);

            // Update encounter status
            $encounter->status = 'finished';
            $encounter->end = now();
            $encounter->save();

            DB::commit();

            if ($invoice) {
                $downloadUrl = route('invoice.download', $invoice->id);
                session()->flash('message.success', '¡Consulta finalizada con éxito! Factura generada: '.$invoice->identifier.' - <a href="'.$downloadUrl.'" target="_blank" class="btn btn-sm btn-primary">Descargar PDF</a>');
            } else {
                session()->flash('message.success', '¡Consulta finalizada con éxito! (Sin servicios facturables)');
            }

            return redirect(route('consultation.index'));

        } catch (\Exception $e) {
            DB::rollBack();
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
            'physicalExams'
        ])->findOrFail($encounter_id);

        return view('consultations.view', compact('encounter'));
    }

    public function downloadResumen(Request $request, $appointment_id)
    {

        $appointment = Appointment::find($appointment_id);
        $data = Encounter::whereAppointmentId($appointment_id)->first();
        $data['consultation-list'] = PresentIllnesType::get();
        $consultation_disabilities = [];
        $lang = 'esp';
        $home_visit = false;
        $sello = $firma = '';
        $mode = 'full';
        foreach ($data->diagnoses()->get() as $d) {
            array_push($consultation_disabilities, '<td>'.$d->condition->code.'</td><td>'.$d->condition->icd10Code->description_es.'</td>');
        }

        if ($request->has('html')) {
            return view('consultations.consultation_report.index', compact('data', 'lang', 'home_visit', 'sello', 'firma', 'mode', 'consultation_disabilities'));
        }

        $pdf = Pdf::loadView('consultations.consultation_report.index', compact('data', 'lang', 'home_visit', 'sello', 'firma', 'mode', 'consultation_disabilities'));

        return $pdf->stream('resumen.pdf');
    }

}
