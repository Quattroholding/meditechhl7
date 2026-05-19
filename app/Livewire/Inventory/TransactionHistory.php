<?php

namespace App\Livewire\Inventory;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionHistory extends Component
{
    use WithPagination;

    public $itemFilter;

    public $typeFilter;

    public $dateFrom;

    public $dateTo;

    public $perPage = 20;

    protected $queryString = ['itemFilter', 'typeFilter', 'dateFrom', 'dateTo'];

    public function updatingItemFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = InventoryTransaction::with(['inventoryItem', 'performedByUser', 'patient'])
            ->orderBy('transaction_date', 'desc');

        if ($this->itemFilter) {
            $query->where('inventory_item_id', $this->itemFilter);
        }

        if ($this->typeFilter) {
            $query->where('transaction_type', $this->typeFilter);
        }

        if ($this->dateFrom) {
            $query->whereDate('transaction_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('transaction_date', '<=', $this->dateTo);
        }

        $transactions = $query->paginate($this->perPage);
        $items = InventoryItem::orderBy('name')->get();

        return view('livewire.inventory.transaction-history', [
            'transactions' => $transactions,
            'items' => $items,
        ]);
    }
}
