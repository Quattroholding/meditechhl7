<?php

namespace App\Livewire\Components;

use App\Models\InventoryItem;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class InventoryItemSelector extends Component
{
    #[Modelable]
    public $selectedItemId = '';

    public $searchTerm = '';

    public $showDropdown = false;

    public $items = [];

    public $selectedItemName = '';

    public $placeholder = 'Buscar item por nombre, SKU o código...';

    public $required = false;

    public function mount()
    {
        $this->loadSelectedItem();
    }

    public function updatedSelectedItemId()
    {
        $this->loadSelectedItem();
    }

    protected function loadSelectedItem()
    {
        if ($this->selectedItemId) {
            $item = InventoryItem::find($this->selectedItemId);
            if ($item) {
                $this->selectedItemName = $item->name;
                $this->searchTerm = $item->name;
            }
        }
    }

    public function updatedSearchTerm()
    {
        if (strlen($this->searchTerm) >= 2) {
            $this->showDropdown = true;
            $this->searchItems();
        } else {
            $this->showDropdown = false;
            $this->items = [];
        }

        // Clear selection if search term changes
        if ($this->searchTerm !== $this->selectedItemName) {
            $this->selectedItemId = '';
            $this->selectedItemName = '';
        }
    }

    public function searchItems()
    {
        $this->items = InventoryItem::where('status', 'active')
            ->where(function ($query) {
                $query->where('name', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('sku', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('barcode', 'like', '%'.$this->searchTerm.'%');
            })
            ->with('inventoryReports')
            ->limit(10)
            ->get();
    }

    public function selectItem($itemId)
    {
        $item = InventoryItem::find($itemId);
        if ($item) {
            $this->selectedItemId = $itemId;
            $this->selectedItemName = $item->name;
            $this->searchTerm = $item->name;
            $this->showDropdown = false;
            $this->items = [];

            // Dispatch event to notify parent component
            $this->dispatch('item-selected', itemId: $itemId);
        }
    }

    public function clearSelection()
    {
        $this->selectedItemId = '';
        $this->selectedItemName = '';
        $this->searchTerm = '';
        $this->showDropdown = false;
        $this->items = [];

        // Dispatch event to notify parent component
        $this->dispatch('item-cleared');
    }

    public function hideDropdown()
    {
        // Delay hiding to allow for click events
        $this->dispatch('hide-dropdown-after-delay');
    }

    public function render()
    {
        return view('livewire.components.inventory-item-selector');
    }
}
