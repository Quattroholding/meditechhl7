<?php

namespace App\Livewire\Finance\Supplier;

use App\Models\Finance\Supplier;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierDataTable extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    public bool $showModal = false;

    public ?Supplier $editingSupplier = null;

    public function mount(): void
    {
        $this->authorize('payables.suppliers.view');
    }

    public function getRows(): Collection
    {
        return Supplier::query()
            ->when($this->search, function ($query) {
                $query->where('legal_name', 'like', "%{$this->search}%")
                    ->orWhere('ruc', 'like', "%{$this->search}%")
                    ->orWhere('commercial_name', 'like', "%{$this->search}%");
            })
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->orderBy('legal_name')
            ->paginate(15);
    }

    public function openModal(?Supplier $supplier = null): void
    {
        $this->authorize('payables.suppliers.manage');
        $this->editingSupplier = $supplier;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingSupplier = null;
    }

    public function toggleStatus(Supplier $supplier): void
    {
        $this->authorize('payables.suppliers.manage');
        $supplier->update([
            'status' => $supplier->status === 'active' ? 'inactive' : 'active',
        ]);
        $this->dispatch('supplier-updated');
    }

    public function render()
    {
        return view('livewire.finance.supplier.supplier-data-table', [
            'suppliers' => $this->getRows(),
        ]);
    }
}
