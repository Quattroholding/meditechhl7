<?php

namespace App\Services\Consultation;

use App\Models\Account;
use App\Models\ChargeItem;
use App\Models\Encounter;
use App\Models\InventoryItem;
use App\Models\Invoice;
use App\Models\InvoiceLineItem;
use App\Models\Patient;
use App\Models\Practitioner;
use App\Models\ServiceCatalog;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ConsultationInvoiceService
{
    /**
     * Generar factura para el encounter (retorna null si no hay items cobrables)
     *
     * @throws \Exception si falla creación de Account o Invoice
     */
    public function generateInvoiceForEncounter(Encounter $encounter, int $clientId): ?Invoice
    {
        // Obtener ChargeItems cobrables para este encounter
        $chargeItems = ChargeItem::where('encounter_id', $encounter->id)
            ->where('status', 'billable')
            ->get();

        if ($chargeItems->isEmpty()) {
            return null;
        }

        // Encontrar o crear Account del paciente
        $account = $this->findOrCreatePatientAccount($encounter, $clientId);

        // Crear Invoice
        $invoice = $this->createInvoice($encounter, $account, $clientId);

        // Crear líneas de factura desde ChargeItems
        $this->createInvoiceLineItems($invoice, $chargeItems);

        // Calcular totales de factura
        $this->calculateInvoiceTotals($invoice);

        return $invoice;
    }

    /**
     * Encontrar o crear cuenta del paciente
     */
    protected function findOrCreatePatientAccount(Encounter $encounter, int $clientId): Account
    {
        $patient = Patient::find($encounter->patient_id);

        if (! $patient) {
            throw new \Exception("Patient {$encounter->patient_id} not found for encounter {$encounter->id}");
        }

        // Buscar cuenta existente
        $account = Account::where('patient_id', $patient->id)
            ->where('client_id', $clientId)
            ->active()
            ->first();

        Log::info('Creating invoice for patient', [
            'appointment_patient_id' => $encounter->appointment?->patient_id,
            'patient_found_id' => $patient->id,
            'account_id' => $account?->id,
        ]);

        if ($account) {
            return $account;
        }

        // Crear nueva cuenta si no existe
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
                'client_id' => $clientId,
                'created_by' => auth()->id(),
            ]);

            // Verificar que la cuenta fue creada correctamente
            if (! $account || ! $account->id) {
                throw new \Exception('Account object is null or missing ID after creation');
            }

            Log::info('Account created successfully', [
                'account_id' => $account->id,
                'patient_id' => $patient->id,
                'client_id' => $clientId,
            ]);

            return $account;
        } catch (\Exception $e) {
            throw new \Exception('Error creating patient account: '.$e->getMessage());
        }
    }

    /**
     * Crear Invoice
     */
    protected function createInvoice(Encounter $encounter, Account $account, int $clientId): Invoice
    {
        // Generar identificador único
        $identifier = 'INV-'.now()->format('Ymd').'-'.str_pad($encounter->id, 6, '0', STR_PAD_LEFT);

        // Verificar que el practitioner existe
        $appointment = $encounter->appointment;
        if (! $appointment) {
            throw new \Exception("Appointment not found for encounter {$encounter->id}");
        }

        $practitioner = Practitioner::find($appointment->practitioner_id);
        if (! $practitioner) {
            throw new \Exception("Practitioner {$appointment->practitioner_id} not found");
        }

        // Obtener branch_id de la sala de consulta
        $branchId = $appointment->consultingRoom?->branch_id;

        // Crear factura
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
                'due_date' => now()->addDays(30), // 30 días de plazo de pago
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
            ]);

            return $invoice;
        } catch (QueryException $e) {
            Log::error('Failed to create invoice', [
                'error' => $e->getMessage(),
                'account_id' => $account->id,
                'patient_id' => $encounter->patient_id,
                'practitioner_id' => $encounter->practitioner_id,
                'encounter_id' => $encounter->id,
                'client_id' => $clientId,
            ]);
            throw new \Exception('Error creating invoice: '.$e->getMessage());
        }
    }

    /**
     * Crear líneas de factura desde ChargeItems
     */
    protected function createInvoiceLineItems(Invoice $invoice, $chargeItems): void
    {
        $lineItemNumber = 1;

        foreach ($chargeItems as $chargeItem) {
            // Determinar descripción del servicio
            $serviceDescription = $this->getServiceDescription($chargeItem);
            $serviceCode = $this->getServiceCode($chargeItem);

            InvoiceLineItem::create([
                'invoice_id' => $invoice->id,
                'charge_item_id' => $chargeItem->id,
                'sequence' => $lineItemNumber++,
                'service_description' => $serviceDescription,
                'cpt_code' => $chargeItem->cpt_code,
                'service_code' => $serviceCode,
                'quantity' => $chargeItem->quantity,
                'unit_price' => $chargeItem->unit_price_value,
                'line_total_gross' => $chargeItem->total_price,
                'service_date' => $chargeItem->occurrence_date_time ?? now(),
                'modifier_codes' => $chargeItem->modifier_codes,
                'client_id' => $chargeItem->client_id,
            ]);

            // Marcar ChargeItem como facturado
            $chargeItem->markAsBilled();
        }
    }

    /**
     * Obtener descripción del servicio desde ChargeItem
     */
    protected function getServiceDescription(ChargeItem $chargeItem): string
    {
        $serviceDescription = 'Servicio médico';

        // Intentar desde ServiceCatalog
        if ($chargeItem->service_catalog_id) {
            $serviceCatalog = ServiceCatalog::find($chargeItem->service_catalog_id);
            if ($serviceCatalog) {
                $serviceDescription = $serviceCatalog->description ?? $serviceCatalog->name;

                return $serviceDescription;
            }
        }

        // Intentar desde producto/inventario si es suministro
        if ($chargeItem->product_reference && is_array($chargeItem->product_reference)) {
            if (isset($chargeItem->product_reference['reference'])) {
                $reference = $chargeItem->product_reference['reference'];
                if (str_contains($reference, 'InventoryItem/')) {
                    $fhirId = str_replace('InventoryItem/', '', $reference);
                    $inventoryItem = InventoryItem::where('fhir_id', $fhirId)->first();
                    if ($inventoryItem && ! empty($inventoryItem->name)) {
                        return $inventoryItem->name;
                    }
                }
            }
        }

        // Intentar desde definition
        if (! empty($chargeItem->definition)) {
            return $chargeItem->definition;
        }

        return $serviceDescription;
    }

    /**
     * Obtener código del servicio desde ChargeItem
     */
    protected function getServiceCode(ChargeItem $chargeItem): string
    {
        // Intentar desde ServiceCatalog
        if ($chargeItem->service_catalog_id) {
            $serviceCatalog = ServiceCatalog::find($chargeItem->service_catalog_id);
            if ($serviceCatalog && $serviceCatalog->code) {
                return $serviceCatalog->code;
            }
        }

        // Intentar desde InventoryItem (para suministros)
        if ($chargeItem->product_reference && is_array($chargeItem->product_reference)) {
            if (isset($chargeItem->product_reference['reference'])) {
                $reference = $chargeItem->product_reference['reference'];
                if (str_contains($reference, 'InventoryItem/')) {
                    $fhirId = str_replace('InventoryItem/', '', $reference);
                    $inventoryItem = InventoryItem::where('fhir_id', $fhirId)->first();
                    if ($inventoryItem && $inventoryItem->sku) {
                        return $inventoryItem->sku;
                    }
                }
            }
        }

        return 'N/A';
    }

    /**
     * Calcular totales de factura
     */
    protected function calculateInvoiceTotals(Invoice $invoice): void
    {
        $subtotal = 0;

        // Calcular subtotal desde líneas de factura
        foreach ($invoice->lineItems as $lineItem) {
            $subtotal += $lineItem->line_total_gross;
        }

        // Calcular impuestos
        $taxEnabled = config('billing.tax_enabled', false);
        $taxRate = config('billing.tax_rate', 0.07); // 7% ITBMS para Panamá por defecto
        $taxAmount = $taxEnabled ? ($subtotal * $taxRate) : 0;
        $totalAmount = $subtotal + $taxAmount;

        // Actualizar totales de factura
        $invoice->update([
            'subtotal_amount' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_tax' => $taxAmount,
            'total_amount' => $totalAmount,
            'total_net' => $totalAmount,
            'amount_due' => $totalAmount,
        ]);
    }
}
