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
        try {
            $item = InventoryItem::find($itemId);

            \Log::info('InventoryItemList::delete() - Attempting to delete item', [
                'item_id' => $itemId,
                'item_found' => $item ? true : false,
                'item_sku' => $item?->sku,
            ]);

            if (! $item) {
                \Log::warning('InventoryItemList::delete() - Item not found', ['item_id' => $itemId]);
                $this->dispatch('showToastrItemList',
                    type: 'error',
                    message: 'No se encontró el item a eliminar.',
                );

                return;
            }

            // Check if item has stock (active reports with quantity > 0)
            $hasStock = $item->inventoryReports()
                ->where('status', 'active')
                ->where(function ($query) {
                    $query->where('quantity_on_hand', '>', 0)
                        ->orWhere('internal_units_on_hand', '>', 0);
                })
                ->exists();

            \Log::info('InventoryItemList::delete() - Stock check', [
                'item_id' => $itemId,
                'has_active_stock_with_quantity' => $hasStock,
                'total_inventory_reports' => $item->inventoryReports()->count(),
                'active_reports_detail' => $item->inventoryReports()
                    ->where('status', 'active')
                    ->get(['id', 'quantity_on_hand', 'internal_units_on_hand', 'status'])
                    ->toArray(),
            ]);

            if ($hasStock) {
                $this->dispatch('showToastrItemList',
                    type: 'error',
                    message: 'No se puede eliminar este item porque tiene stock activo en inventario.',
                );

                return;
            }

            $item->delete();
            \Log::info('InventoryItemList::delete() - Item deleted successfully', ['item_id' => $itemId]);
            $this->dispatch('showToastrItemList',
                type: 'success',
                message: 'Item eliminado exitosamente.',
            );
        } catch (\Exception $e) {
            \Log::error('InventoryItemList::delete() - Error deleting item', [
                'item_id' => $itemId,
                'error' => $e->getMessage(),
            ]);
            $this->dispatch('showToastrItemList',
                type: 'error',
                message: 'Error al eliminar item: '.$e->getMessage(),
            );
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
            $item->total_internal_units = $item->inventoryReports->sum('internal_units_on_hand');

            return $item;
        });

        return view('livewire.inventory.inventory-item-list', [
            'items' => $items,
        ]);
    }
}
