<?php

namespace App\Livewire\Finance\AccountsPayable;

use App\Models\CostCenter;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Services\Finance\AccountsPayableService;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class SupplierInvoiceModal extends Component
{
    use WithFileUploads;

    public bool $isModal = true;

    public ?SupplierInvoice $invoice = null;

    #[Validate]
    public int $supplier_id = 0;

    #[Validate]
    public string $invoice_number = '';

    #[Validate]
    public string $invoice_date = '';

    #[Validate]
    public string $received_date = '';

    #[Validate]
    public string $due_date = '';

    #[Validate]
    public string $currency = 'PAB';

    #[Validate]
    public float $subtotal = 0;

    #[Validate]
    public float $tax_amount = 0;

    #[Validate]
    public float $total_amount = 0;

    #[Validate]
    public ?int $cost_center_id = null;

    #[Validate]
    public string $notes = '';

    #[Validate]
    public mixed $invoice_file = null;

    public array $distributions = [];

    public bool $showDistributions = false;

    public function mount(?SupplierInvoice $invoice = null): void
    {
        if ($invoice) {
            $this->invoice = $invoice;
            $this->supplier_id = $invoice->supplier_id;
            $this->invoice_number = $invoice->invoice_number;
            $this->invoice_date = $invoice->invoice_date->toDateString();
            $this->received_date = $invoice->received_date->toDateString();
            $this->due_date = $invoice->due_date->toDateString();
            $this->currency = $invoice->currency;
            $this->subtotal = $invoice->subtotal;
            $this->tax_amount = $invoice->tax_amount;
            $this->total_amount = $invoice->total_amount;
            $this->cost_center_id = $invoice->cost_center_id;
            $this->notes = $invoice->notes ?? '';

            $this->distributions = $invoice->costDistributions->map(fn ($d) => [
                'cost_center_id' => $d->cost_center_id,
                'percentage' => $d->percentage,
            ])->toArray();
        } else {
            $this->received_date = now()->toDateString();
            $this->invoice_date = now()->toDateString();
        }
    }

    public function rules(): array
    {
        return [
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_number' => 'required|string|max:100',
            'invoice_date' => 'required|date',
            'received_date' => 'required|date',
            'due_date' => 'required|date|after:invoice_date',
            'currency' => 'required|in:PAB,USD',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'cost_center_id' => 'nullable|exists:cost_centers,id',
            'notes' => 'nullable|string|max:1000',
            'invoice_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];
    }

    public function updatedTax(): void
    {
        $this->total_amount = $this->subtotal + $this->tax_amount;
    }

    public function updatedSubtotal(): void
    {
        $this->total_amount = $this->subtotal + $this->tax_amount;
    }

    public function save(): void
    {
        $this->authorize('payables.invoices.create');
        $this->validate();

        $clientId = auth()->user()->getCurrentClient()->id;
        $service = app(AccountsPayableService::class);
        $isCreating = ! $this->invoice;

        if ($this->invoice) {
            $this->invoice->update([
                'supplier_id' => $this->supplier_id,
                'invoice_number' => $this->invoice_number,
                'invoice_date' => $this->invoice_date,
                'received_date' => $this->received_date,
                'due_date' => $this->due_date,
                'currency' => $this->currency,
                'subtotal' => $this->subtotal,
                'tax_amount' => $this->tax_amount,
                'total_amount' => $this->total_amount,
                'cost_center_id' => $this->cost_center_id,
                'notes' => $this->notes,
                'updated_by' => auth()->id(),
            ]);
        } else {
            $this->invoice = $service->createSupplierInvoice($clientId, [
                'supplier_id' => $this->supplier_id,
                'invoice_number' => $this->invoice_number,
                'invoice_date' => $this->invoice_date,
                'received_date' => $this->received_date,
                'due_date' => $this->due_date,
                'currency' => $this->currency,
                'subtotal' => $this->subtotal,
                'tax_amount' => $this->tax_amount,
                'total_amount' => $this->total_amount,
                'cost_center_id' => $this->cost_center_id,
                'notes' => $this->notes,
            ]);
        }

        // Guardar archivo si se proporcionó
        if ($this->invoice_file) {
            $storagePath = $this->invoice_file->store(
                "supplier-invoices/{$clientId}",
                'private'
            );

            // Crear o actualizar registro de archivo
            $this->invoice->update([
                'document_path' => $storagePath,
                'document_filename' => $this->invoice_file->getClientOriginalName(),
            ]);
        }

        // Guardar distribuciones si hay
        if (! empty($this->distributions)) {
            $service->distributeByCenter($this->invoice, $this->distributions);
        }

        $message = $isCreating ? 'Factura de proveedor creada exitosamente.' : 'Factura de proveedor actualizada exitosamente.';
        session()->flash('message.success', $message);

        if ($this->isModal) {
            $this->dispatch('invoice-saved');
            $this->dispatch('closeModal');
        } else {
            $this->redirect(route('finance.payables.invoices.index'));
        }
    }

    public function addDistribution(): void
    {
        $this->distributions[] = ['cost_center_id' => 0, 'percentage' => 0];
    }

    public function removeDistribution(int $index): void
    {
        unset($this->distributions[$index]);
        $this->distributions = array_values($this->distributions);
    }

    public function getSuppliersProperty()
    {
        return Supplier::where('client_id', auth()->user()->getCurrentClient()->id)
            ->where('status', 'active')
            ->orderBy('legal_name')
            ->get();
    }

    public function getCostCentersProperty()
    {
        return CostCenter::where('client_id', auth()->user()->getCurrentClient()->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.finance.accounts-payable.supplier-invoice-modal', [
            'suppliers' => $this->suppliers,
            'costCenters' => $this->costCenters,
        ]);
    }
}
