<?php

namespace App\Livewire\Inventory;

use App\Enums\InventoryTransactionType;
use App\Models\Branch;
use App\Models\InventoryItem;
use App\Models\InventoryReport;
use App\Models\InventoryTransaction;
use App\Models\Practitioner;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StockManagement extends Component
{
    public $selectedItemId;

    public $selectedItem;

    public $operation = 'receive'; // receive, adjust, transfer, dispose

    public $locationId;

    public $locationType = 'branch'; // branch or practitioner

    public $quantity;

    public $unitCost;

    public $lotNumber;

    public $expirationDate;

    public $reason;

    public $transferToLocationId;

    public $transferToLocationType = 'branch';

    public function mount($item = null)
    {
        if ($item) {
            $this->selectedItemId = $item;
            $this->loadItem();
        }
    }

    public function updatedSelectedItemId()
    {
        $this->loadItem();
    }

    protected $listeners = ['item-selected' => 'handleItemSelected', 'item-cleared' => 'handleItemCleared'];

    public function handleItemSelected($itemId)
    {
        $this->selectedItemId = $itemId;
        $this->loadItem();
    }

    public function handleItemCleared()
    {
        $this->selectedItemId = null;
        $this->selectedItem = null;
        $this->reset(['quantity', 'unitCost', 'lotNumber', 'expirationDate', 'reason', 'transferToLocationId']);
    }

    protected function loadItem()
    {
        if ($this->selectedItemId) {
            $this->selectedItem = InventoryItem::with(['inventoryReports' => function ($q) {
                $q->where('status', 'active')
                    ->with(['branch', 'practitioner']);
            }])->find($this->selectedItemId);
        }
    }

    protected function rules()
    {
        $rules = [
            'selectedItemId' => 'required|exists:inventory_items,id',
            'locationId' => 'required',
            'quantity' => 'required|numeric|min:0.01',
        ];

        if ($this->operation === 'receive') {
            $rules['unitCost'] = 'nullable|numeric|min:0';
        }

        if ($this->operation === 'transfer') {
            $rules['transferToLocationId'] = 'required';
        }

        if (in_array($this->operation, ['adjust', 'dispose'])) {
            $rules['reason'] = 'required|string|min:5';
        }

        return $rules;
    }

    public function executeOperation()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                switch ($this->operation) {
                    case 'receive':
                        $this->receiveStock();
                        break;
                    case 'adjust':
                        $this->adjustStock();
                        break;
                    case 'transfer':
                        $this->transferStock();
                        break;
                    case 'dispose':
                        $this->disposeStock();
                        break;
                }
            });

            session()->flash('success', 'Operación completada exitosamente.');
            $this->reset(['quantity', 'unitCost', 'lotNumber', 'expirationDate', 'reason', 'transferToLocationId']);
            $this->loadItem();
        } catch (\Exception $e) {
            session()->flash('error', 'Error: '.$e->getMessage());
        }
    }

    protected function receiveStock()
    {
        $inventoryReport = $this->getOrCreateInventoryReport();

        $quantityBefore = $inventoryReport->quantity_on_hand;
        $inventoryReport->increment('quantity_on_hand', $this->quantity);
        $inventoryReport->refresh();

        if ($this->lotNumber) {
            $inventoryReport->lot_number = $this->lotNumber;
        }
        if ($this->expirationDate) {
            $inventoryReport->expiration_date = $this->expirationDate;
        }
        $inventoryReport->save();

        InventoryTransaction::create([
            'transaction_type' => InventoryTransactionType::PURCHASE,
            'transaction_date' => now(),
            'inventory_item_id' => $this->selectedItemId,
            'quantity_change' => $this->quantity,
            'unit_of_measure' => $this->selectedItem->unit_of_measure,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $inventoryReport->quantity_on_hand,
            'to_location_client_id' => auth()->user()->clients()->first()->id,
            'to_location_branch_id' => $this->locationType === 'branch' ? $this->locationId : null,
            'to_location_practitioner_id' => $this->locationType === 'practitioner' ? $this->locationId : null,
            'lot_number' => $this->lotNumber,
            'expiration_date' => $this->expirationDate,
            'unit_cost' => $this->unitCost,
            'total_cost' => $this->unitCost ? ($this->unitCost * $this->quantity) : null,
            'performed_by_user_id' => auth()->id(),
            'reason' => 'Recepción de stock',
            'client_id' => auth()->user()->clients()->first()->id,
        ]);
    }

    protected function adjustStock()
    {
        $inventoryReport = $this->getInventoryReport();

        if (! $inventoryReport) {
            throw new \Exception('No se encontró inventario en esta ubicación.');
        }

        $quantityBefore = $inventoryReport->quantity_on_hand;
        $difference = $this->quantity - $quantityBefore;

        $inventoryReport->quantity_on_hand = $this->quantity;
        $inventoryReport->save();

        InventoryTransaction::create([
            'transaction_type' => InventoryTransactionType::ADJUSTMENT,
            'transaction_date' => now(),
            'inventory_item_id' => $this->selectedItemId,
            'quantity_change' => $difference,
            'unit_of_measure' => $this->selectedItem->unit_of_measure,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $this->quantity,
            'to_location_client_id' => auth()->user()->clients()->first()->id,
            'to_location_branch_id' => $this->locationType === 'branch' ? $this->locationId : null,
            'to_location_practitioner_id' => $this->locationType === 'practitioner' ? $this->locationId : null,
            'performed_by_user_id' => auth()->id(),
            'reason' => $this->reason,
            'client_id' => auth()->user()->clients()->first()->id,
        ]);
    }

    protected function transferStock()
    {
        $fromReport = $this->getInventoryReport();

        if (! $fromReport || $fromReport->quantity_available < $this->quantity) {
            throw new \Exception('Stock insuficiente en ubicación de origen.');
        }

        // Deduct from origin
        $quantityBefore = $fromReport->quantity_on_hand;
        $fromReport->decrement('quantity_on_hand', $this->quantity);
        $fromReport->refresh();

        // Add to destination
        $toReport = $this->getOrCreateInventoryReport($this->transferToLocationId, $this->transferToLocationType);
        $toReport->increment('quantity_on_hand', $this->quantity);

        InventoryTransaction::create([
            'transaction_type' => InventoryTransactionType::TRANSFER,
            'transaction_date' => now(),
            'inventory_item_id' => $this->selectedItemId,
            'quantity_change' => 0, // Net zero, it's a movement
            'unit_of_measure' => $this->selectedItem->unit_of_measure,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $fromReport->quantity_on_hand,
            'from_location_client_id' => auth()->user()->clients()->first()->id,
            'from_location_branch_id' => $this->locationType === 'branch' ? $this->locationId : null,
            'from_location_practitioner_id' => $this->locationType === 'practitioner' ? $this->locationId : null,
            'to_location_client_id' => auth()->user()->clients()->first()->id,
            'to_location_branch_id' => $this->transferToLocationType === 'branch' ? $this->transferToLocationId : null,
            'to_location_practitioner_id' => $this->transferToLocationType === 'practitioner' ? $this->transferToLocationId : null,
            'performed_by_user_id' => auth()->id(),
            'reason' => 'Transferencia de stock',
            'client_id' => auth()->user()->clients()->first()->id,
        ]);
    }

    protected function disposeStock()
    {
        $inventoryReport = $this->getInventoryReport();

        if (! $inventoryReport || $inventoryReport->quantity_available < $this->quantity) {
            throw new \Exception('Stock insuficiente para dar de baja.');
        }

        $quantityBefore = $inventoryReport->quantity_on_hand;
        $inventoryReport->decrement('quantity_on_hand', $this->quantity);
        $inventoryReport->refresh();

        InventoryTransaction::create([
            'transaction_type' => InventoryTransactionType::DISPOSAL,
            'transaction_date' => now(),
            'inventory_item_id' => $this->selectedItemId,
            'quantity_change' => -$this->quantity,
            'unit_of_measure' => $this->selectedItem->unit_of_measure,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $inventoryReport->quantity_on_hand,
            'from_location_client_id' => auth()->user()->clients()->first()->id,
            'from_location_branch_id' => $this->locationType === 'branch' ? $this->locationId : null,
            'from_location_practitioner_id' => $this->locationType === 'practitioner' ? $this->locationId : null,
            'performed_by_user_id' => auth()->id(),
            'reason' => $this->reason,
            'client_id' => auth()->user()->clients()->first()->id,
        ]);
    }

    protected function getInventoryReport()
    {
        return InventoryReport::where('inventory_item_id', $this->selectedItemId)
            ->where('status', 'active')
            ->where('client_id', auth()->user()->clients()->first()->id)
            ->when($this->locationType === 'branch', fn ($q) => $q->where('branch_id', $this->locationId)->whereNull('practitioner_id'))
            ->when($this->locationType === 'practitioner', fn ($q) => $q->where('practitioner_id', $this->locationId))
            ->first();
    }

    protected function getOrCreateInventoryReport($locationId = null, $locationType = null)
    {
        $locationId = $locationId ?? $this->locationId;
        $locationType = $locationType ?? $this->locationType;

        $report = InventoryReport::where('inventory_item_id', $this->selectedItemId)
            ->where('status', 'active')
            ->where('client_id', auth()->user()->clients()->first()->id)
            ->when($locationType === 'branch', fn ($q) => $q->where('branch_id', $locationId)->whereNull('practitioner_id'))
            ->when($locationType === 'practitioner', fn ($q) => $q->where('practitioner_id', $locationId))
            ->first();

        if (! $report) {
            $report = InventoryReport::create([
                'inventory_item_id' => $this->selectedItemId,
                'status' => 'active',
                'client_id' => auth()->user()->clients()->first()->id,
                'branch_id' => $locationType === 'branch' ? $locationId : null,
                'practitioner_id' => $locationType === 'practitioner' ? $locationId : null,
                'quantity_on_hand' => 0,
                'quantity_reserved' => 0,
                'reported_datetime' => now(),
            ]);
        }

        return $report;
    }

    public function render()
    {
        $user = auth()->user();
        $items = InventoryItem::where('status', 'active')->orderBy('name')->get();

        // Filter branches based on user role
        $branches = Branch::orderBy('name')->get();
        $practitioners = Practitioner::where('active', true)->orderBy('name')->get();

        return view('livewire.inventory.stock-management', [
            'items' => $items,
            'branches' => $branches,
            'practitioners' => $practitioners,
        ]);
    }
}
