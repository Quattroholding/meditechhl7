<?php

namespace App\Livewire\Finance\AccountsPayable;

use App\Models\Finance\SupplierInvoice;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierInvoiceDataTable extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $supplier = '';

    public bool $showModal = false;

    public ?SupplierInvoice $editingInvoice = null;

    public function mount(): void
    {
        $this->authorize('payables.invoices.view');
    }

    public function getRows(): Collection
    {
        return SupplierInvoice::query()
            ->with(['supplier', 'costCenter'])
            ->when($this->search, function ($query) {
                $query->where('invoice_number', 'like', "%{$this->search}%")
                    ->orWhereHas('supplier', fn ($q) => $q->where('legal_name', 'like', "%{$this->search}%"));
            })
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->when($this->supplier, fn ($query) => $query->where('supplier_id', $this->supplier))
            ->orderByDesc('invoice_date')
            ->paginate(15);
    }

    public function openModal(?SupplierInvoice $invoice = null): void
    {
        $this->authorize('payables.invoices.create');
        $this->editingInvoice = $invoice;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingInvoice = null;
    }

    public function approve(SupplierInvoice $invoice): void
    {
        $this->authorize('payables.invoices.approve');

        if ($invoice->status !== 'draft') {
            $this->dispatch('error', message: 'Solo se pueden aprobar facturas en borrador');

            return;
        }

        $invoice->update(['status' => 'approved', 'approved_at' => now(), 'approved_by' => auth()->id()]);
        $this->dispatch('invoice-updated');
    }

    public function cancel(SupplierInvoice $invoice): void
    {
        $this->authorize('payables.invoices.create');

        $invoice->update(['status' => 'cancelled']);
        $this->dispatch('invoice-updated');
    }

    public function render()
    {
        return view('livewire.finance.accounts-payable.supplier-invoice-data-table', [
            'invoices' => $this->getRows(),
        ]);
    }
}
