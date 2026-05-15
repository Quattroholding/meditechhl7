<?php

namespace App\Livewire\Inventory;

use App\Models\InventoryItem;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryItemList extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $itemTypeFilter = '';

    public $sortField = 'name';

    public $sortDirection = 'asc';

    public $perPage = 15;

    protected $queryString = ['search', 'statusFilter', 'itemTypeFilter', 'sortField', 'sortDirection'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingItemTypeFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function delete($itemId)
    {
        $item = InventoryItem::find($itemId);

        if ($item) {
            // Check if item has active inventory reports
            $hasStock = $item->inventoryReports()->where('status', 'active')->exists();

            if ($hasStock) {
                session()->flash('error', 'No se puede eliminar este item porque tiene stock activo en inventario.');

                return;
            }

            $item->delete();
            session()->flash('success', 'Item eliminado exitosamente.');
        }
    }

    public function render()
    {
        $query = InventoryItem::query()
            ->with(['inventoryReports' => function ($q) {
                $q->where('status', 'active');
            }]);

        // Apply search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('sku', 'like', '%'.$this->search.'%')
                    ->orWhere('barcode', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            });
        }

        // Apply filters
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->itemTypeFilter) {
            $query->where('item_type', $this->itemTypeFilter);
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        $items = $query->paginate($this->perPage);

        // Calculate total stock for each item
        $items->getCollection()->transform(function ($item) {
            $item->total_stock = $item->inventoryReports->sum('quantity_on_hand');
            $item->total_reserved = $item->inventoryReports->sum('quantity_reserved');
            $item->total_available = $item->total_stock - $item->total_reserved;

            return $item;
        });

        return view('livewire.inventory.inventory-item-list', [
            'items' => $items,
        ]);
    }
}
