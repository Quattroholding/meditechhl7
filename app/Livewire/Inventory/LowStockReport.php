<?php

namespace App\Livewire\Inventory;

use App\Models\InventoryItem;
use Livewire\Component;

class LowStockReport extends Component
{
    public function render()
    {
        $lowStockItems = InventoryItem::whereNotNull('reorder_point')
            ->with(['inventoryReports' => function ($q) {
                $q->where('status', 'active');
            }])
            ->get()
            ->map(function ($item) {
                $item->total_stock = $item->inventoryReports->sum('quantity_on_hand');
                $item->total_available = $item->total_stock - $item->inventoryReports->sum('quantity_reserved');

                return $item;
            })
            ->filter(fn ($item) => $item->total_available <= $item->reorder_point)
            ->sortBy('total_available');

        return view('livewire.inventory.low-stock-report', [
            'lowStockItems' => $lowStockItems,
        ]);
    }
}
