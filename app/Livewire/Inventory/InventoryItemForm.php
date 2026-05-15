<?php

namespace App\Livewire\Inventory;

use App\Enums\InventoryItemType;
use App\Enums\UnitOfMeasure;
use App\Models\InventoryItem;
use Livewire\Component;

class InventoryItemForm extends Component
{
    public ?InventoryItem $item = null;

    public $name;

    public $description;

    public $sku;

    public $barcode;

    public $item_type = 'supply';

    public $base_cost;

    public $base_price;

    public $currency = 'USD';

    public $unit_of_measure = 'unit';

    public $requires_prescription = false;

    public $track_by_lot = false;

    public $track_by_serial = false;

    public $expiration_tracking = false;

    public $reorder_point;

    public $reorder_quantity;

    public $status = 'active';

    public function mount($itemId = null)
    {
        if ($itemId) {
            $this->item = InventoryItem::findOrFail($itemId);
            $this->fill([
                'name' => $this->item->name,
                'description' => $this->item->description,
                'sku' => $this->item->sku,
                'barcode' => $this->item->barcode,
                'item_type' => $this->item->item_type->value,
                'base_cost' => $this->item->base_cost,
                'base_price' => $this->item->base_price,
                'currency' => $this->item->currency,
                'unit_of_measure' => $this->item->unit_of_measure->value,
                'requires_prescription' => $this->item->requires_prescription,
                'track_by_lot' => $this->item->track_by_lot,
                'track_by_serial' => $this->item->track_by_serial,
                'expiration_tracking' => $this->item->expiration_tracking,
                'reorder_point' => $this->item->reorder_point,
                'reorder_quantity' => $this->item->reorder_quantity,
                'status' => $this->item->status->value,
            ]);
        }
    }

    protected function rules()
    {
        $itemId = $this->item?->id;
        $itemTypes = implode(',', array_keys(InventoryItemType::options()));
        $unitOfMeasures = implode(',', array_keys(UnitOfMeasure::options()));

        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => ['required', 'string', 'max:100', 'unique:inventory_items,sku,'.($itemId ?? 'NULL')],
            'barcode' => 'nullable|string|max:100',
            'item_type' => 'required|in:'.$itemTypes,
            'base_cost' => 'nullable|numeric|min:0',
            'base_price' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:10',
            'unit_of_measure' => 'required|in:'.$unitOfMeasures,
            'requires_prescription' => 'boolean',
            'track_by_lot' => 'boolean',
            'track_by_serial' => 'boolean',
            'expiration_tracking' => 'boolean',
            'reorder_point' => 'nullable|integer|min:0',
            'reorder_quantity' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'item_type' => $this->item_type,
            'base_cost' => $this->base_cost,
            'base_price' => $this->base_price,
            'currency' => $this->currency,
            'unit_of_measure' => $this->unit_of_measure,
            'requires_prescription' => $this->requires_prescription,
            'track_by_lot' => $this->track_by_lot,
            'track_by_serial' => $this->track_by_serial,
            'expiration_tracking' => $this->expiration_tracking,
            'reorder_point' => $this->reorder_point,
            'reorder_quantity' => $this->reorder_quantity,
            'status' => $this->status,
            'category' => [[
                'coding' => [[
                    'system' => 'http://terminology.hl7.org/CodeSystem/supply-item-type',
                    'code' => $this->item_type,
                    'display' => ucfirst($this->item_type),
                ]],
                'text' => ucfirst($this->item_type),
            ]],
            'code' => [[
                'coding' => [[
                    'system' => 'urn:meditech:inventory',
                    'code' => $this->sku,
                ]],
            ]],
        ];

        if ($this->item) {
            $this->item->update($data);
            $message = 'Item actualizado exitosamente.';
        } else {
            $data['client_id'] = auth()->user()->clients()->first()->id;
            InventoryItem::create($data);
            $message = 'Item creado exitosamente.';
        }

        session()->flash('success', $message);

        return redirect()->route('inventory.items.index');
    }

    public function render()
    {
        return view('livewire.inventory.inventory-item-form');
    }
}
