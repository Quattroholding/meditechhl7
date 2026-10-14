<?php

namespace App\Jobs;

use App\Enums\SupplierInvoiceStatus;
use App\Models\DocumentUpload;
use App\Models\Finance\Supplier;
use App\Models\Finance\SupplierInvoice;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GeneratePeriodicContractInvoiceJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 60;

    public int $tries = 3;

    public array $backoff = [10, 30, 60];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $documentId,
        public int $supplierId,
        public int $invoiceSequence,
        public Carbon $invoiceDate,
        public array $contractData,
    ) {
        $this->queue = 'document-processing';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $document = DocumentUpload::find($this->documentId);

        if (! $document) {
            Log::error('GeneratePeriodicContractInvoiceJob: Document not found', ['id' => $this->documentId]);

            return;
        }

        try {
            $supplier = Supplier::find($this->supplierId);

            if (! $supplier) {
                throw new \RuntimeException('Supplier not found');
            }

            $paymentAmount = $this->contractData['payment_amount'];
            $currency = $this->contractData['currency'];
            $invoiceDueDate = $this->invoiceDate->copy()->addDay();

            $invoice = SupplierInvoice::create([
                'uuid' => Str::uuid(),
                'client_id' => $document->client_id,
                'supplier_id' => $supplier->id,
                'invoice_number' => $supplier->id.'-'.$document->id.'-'.str_pad($this->invoiceSequence, 4, '0', STR_PAD_LEFT),
                'invoice_date' => $this->invoiceDate->toDateString(),
                'received_date' => $this->invoiceDate->toDateString(),
                'due_date' => $invoiceDueDate->toDateString(),
                'currency' => $currency,
                'subtotal' => $paymentAmount,
                'tax_amount' => 0,
                'total_amount' => $paymentAmount,
                'paid_amount' => 0,
                'balance' => $paymentAmount,
                'status' => SupplierInvoiceStatus::REGISTERED->value,
                'document_path' => $document->file_path,
                'document_filename' => $document->original_filename,
                'notes' => $this->contractData['contract_title'] ?? 'Contract Invoice',
                'created_by' => auth()->id() ?? 1,
                'updated_by' => auth()->id() ?? 1,
            ]);

            // Load client relation before approving (Observer needs it)
            $invoice->load('client');

            // Approve the invoice to trigger Observer and generate journal entry
            $invoice->update([
                'status' => SupplierInvoiceStatus::APPROVED->value,
                'approved_at' => now(),
                'approved_by' => auth()->id() ?? 1,
            ]);

            Log::info('GeneratePeriodicContractInvoiceJob: Invoice created', [
                'document_id' => $document->id,
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'invoice_date' => $this->invoiceDate->toDateString(),
                'sequence' => $this->invoiceSequence,
            ]);

        } catch (\Exception $e) {
            Log::error('GeneratePeriodicContractInvoiceJob: Error', [
                'document_id' => $this->documentId,
                'invoice_sequence' => $this->invoiceSequence,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
