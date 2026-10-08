<?php

namespace App\Services\Finance;

use App\Models\Accounting\AccountingAccount;
use App\Models\Finance\CostDistribution;
use App\Models\DocumentParseResult;
use App\Models\DocumentUpload;
use App\Models\Finance\Supplier;
use App\Models\Finance\SupplierInvoice;
use App\Services\Accounting\AccountingEngineService;
use Illuminate\Support\Facades\DB;

class DocumentFinancialProcessor
{
    protected AccountingEngineService $accountingEngine;

    public function __construct(AccountingEngineService $accountingEngine)
    {
        $this->accountingEngine = $accountingEngine;
    }

    /**
     * Procesa un documento de factura de proveedor y crea SupplierInvoice automáticamente
     */
    public function processSupplierInvoiceDocument(
        DocumentUpload $document,
        DocumentParseResult $parseResult
    ): SupplierInvoice {
        return DB::transaction(function () use ($document, $parseResult) {
            // 1. Encontrar o crear proveedor
            $supplier = $this->findOrCreateSupplier(
                $parseResult->detected_supplier,
                $document->client_id
            );

            // 2. Crear SupplierInvoice
            $invoice = SupplierInvoice::create([
                'client_id' => $document->client_id,
                'supplier_id' => $supplier->id,
                'invoice_number' => $parseResult->extracted_data['invoice_number'] ?? $document->invoice_number,
                'invoice_date' => $parseResult->extracted_data['invoice_date'] ?? $document->invoice_date,
                'received_date' => now(),
                'due_date' => $this->calculateDueDate(
                    $parseResult->extracted_data['invoice_date'] ?? $document->invoice_date,
                    $supplier->credit_days
                ),
                'subtotal' => $parseResult->extracted_data['subtotal'] ?? $document->subtotal,
                'tax_amount' => $parseResult->extracted_data['tax_amount'] ?? $document->total_tax,
                'total_amount' => $parseResult->extracted_data['total'] ?? $document->total,
                'cost_center_id' => $document->cost_center_id,
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                'created_by' => auth()->id(),
            ]);

            // 3. Vincular documento a factura
            $document->update([
                'supplier_id' => $supplier->id,
                'supplier_invoice_id' => $invoice->id,
            ]);

            // 4. Generar asiento contable automático
            $this->accountingEngine->processSupplierInvoiceCreated($invoice);

            return $invoice;
        });
    }

    /**
     * Encuentra o crea un proveedor basado en el nombre detectado
     */
    private function findOrCreateSupplier(string $supplierName, int $clientId): Supplier
    {
        // Buscar por nombre comercial o razón social
        $supplier = Supplier::where('client_id', $clientId)
            ->where(function ($query) use ($supplierName) {
                $query->where('legal_name', $supplierName)
                    ->orWhere('commercial_name', $supplierName);
            })
            ->first();

        if ($supplier) {
            return $supplier;
        }

        // Crear nuevo proveedor
        return Supplier::create([
            'client_id' => $clientId,
            'ruc' => 'TEMP-'.uniqid(),
            'legal_name' => $supplierName,
            'status' => 'active',
            'accounting_account_id' => $this->getDefaultCxPAccount($clientId)->id,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Calcula la fecha de vencimiento basada en días de crédito
     */
    private function calculateDueDate($invoiceDate, int $creditDays)
    {
        return now()->parse($invoiceDate)->addDays($creditDays);
    }

    /**
     * Obtiene la cuenta contable default para CxP
     */
    private function getDefaultCxPAccount(int $clientId)
    {
        return AccountingAccount::where('client_id', $clientId)
            ->where('code', '2101') // Cuentas por Pagar
            ->firstOrFail();
    }

    /**
     * Procesa distribución de costos
     */
    public function addCostDistribution(
        SupplierInvoice $invoice,
        array $distributions
    ): void {
        if (! CostDistribution::validateDistributions($distributions)) {
            throw new \Exception('Las distribuciones deben sumar 100%');
        }

        foreach ($distributions as $distribution) {
            CostDistribution::updateOrCreate(
                [
                    'supplier_invoice_id' => $invoice->id,
                    'cost_center_id' => $distribution['cost_center_id'],
                ],
                [
                    'percentage' => $distribution['percentage'],
                    'amount' => $invoice->total_amount * ($distribution['percentage'] / 100),
                ]
            );
        }
    }
}
