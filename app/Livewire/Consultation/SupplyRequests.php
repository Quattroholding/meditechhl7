<?php

namespace App\Livewire\Consultation;

use App\Enums\SupplyRequestStatus;
use App\Models\Encounter;
use App\Models\InventoryItem;
use App\Models\SupplyRequest;
use Livewire\Component;

class SupplyRequests extends Component
{
    public $query = '';

    public $results = [];

    public $encounter_id;

    public $encounter;

    public $selectedSupplies = [];

    public $quantities = [];

    public $customPrices = [];

    public $isFree = [];

    public $section_id = 17; // Section for supplies

    public $perPage = 20;

    public $totalResults = 0;

    public $hasMoreResults = false;

    public function mount()
    {
        $this->encounter = Encounter::find($this->encounter_id);
        $this->getSupplyRequestsProperty();
    }

    public function getSupplyRequestsProperty()
    {
        $this->selectedSupplies = $this->encounter->supplyRequests()
            ->with('inventoryItem')
            ->where('status', SupplyRequestStatus::DRAFT)
            ->orderBy('id', 'ASC')
            ->get();

        foreach ($this->selectedSupplies as $supply) {
            $this->quantities[$supply->id] = $supply->quantity;
            $this->customPrices[$supply->id] = $supply->custom_price;
            $this->isFree[$supply->id] = $supply->is_free;
        }
    }

    public function updatedQuery()
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            $this->totalResults = 0;
            $this->hasMoreResults = false;
            $this->perPage = 20;

            return;
        }

        $this->perPage = 20;
        $this->searchInventoryItems();
    }

    public function loadMore()
    {
        $this->perPage += 20;
        $this->searchInventoryItems();
    }

    private function searchInventoryItems()
    {
        $searchQuery = $this->query;

        $query = InventoryItem::query()
            ->where('status', 'active')
            ->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', '%'.$searchQuery.'%')
                    ->orWhere('sku', 'like', '%'.$searchQuery.'%')
                    ->orWhere('barcode', 'like', '%'.$searchQuery.'%')
                    ->orWhere('description', 'like', '%'.$searchQuery.'%');
            });

        $this->totalResults = $query->count();

        $results = $query->orderBy('name', 'ASC')
            ->take($this->perPage)
            ->get();

        $this->hasMoreResults = $this->totalResults > $this->perPage;

        // Get stock availability for each item
        $practitioner = $this->encounter->practitioner;

        // Get branch_id from appointment, or from consulting_room if not directly available
        $branchId = $this->encounter->appointment->branch_id
            ?? $this->encounter->appointment->consultingRoom->branch_id
            ?? null;

        $this->results = $results->map(function ($item) use ($practitioner, $branchId) {
            // Only check practitioner inventory if they have individual inventory enabled
            $practitionerId = ($practitioner && $practitioner->has_individual_inventory) ? $practitioner->id : null;
            $stock = $item->getStockLevel($branchId, $practitionerId);

            return [
                'id' => $item->id,
                'name' => $item->name,
                'sku' => $item->sku,
                'description' => $item->description,
                'unit_of_measure' => $item->unit_of_measure,
                'base_price' => $item->base_price,
                'currency' => $item->currency,
                'stock' => $stock,
            ];
        })->toArray();
    }

    public function selectOption($itemData)
    {
        $itemId = $itemData['id'];

        // Check if already selected
        $exists = $this->encounter->supplyRequests()
            ->where('inventory_item_id', $itemId)
            ->where('status', SupplyRequestStatus::DRAFT)
            ->exists();

        if ($exists) {
            session()->flash('supply-error', 'Este suministro ya está agregado.');

            return;
        }

        // Check stock availability
        if ($itemData['stock'] <= 0) {
            session()->flash('supply-error', 'No hay stock disponible de este suministro.');

            return;
        }

        // Get branch_id from appointment, or from consulting_room if not directly available
        $branchId = $this->encounter->appointment->branch_id
            ?? $this->encounter->appointment->consultingRoom->branch_id
            ?? null;

        // Create supply request
        $supplyRequest = SupplyRequest::create([
            'status' => SupplyRequestStatus::DRAFT,
            'intent' => 'order',
            'priority' => 'routine',
            'inventory_item_id' => $itemId,
            'quantity' => 1, // Default quantity
            'unit_of_measure' => $itemData['unit_of_measure'],
            'patient_id' => $this->encounter->patient_id,
            'encounter_id' => $this->encounter_id,
            'practitioner_id' => $this->encounter->practitioner_id,
            'is_billable' => true,
            'is_free' => false,
            'custom_price' => null, // Use base price
            'occurrence_datetime' => now(),
            'client_id' => auth()->user()->clients()->first()->id,
            'branch_id' => $branchId,
            'consulting_room_id' => $this->encounter->appointment->consulting_room_id ?? null,
        ]);

        // Add to local state
        $this->quantities[$supplyRequest->id] = 1;
        $this->customPrices[$supplyRequest->id] = null;
        $this->isFree[$supplyRequest->id] = false;

        // Clear search
        $this->query = '';
        $this->results = [];

        // Refresh list
        $this->getSupplyRequestsProperty();

        session()->flash('supply-success', 'Suministro agregado exitosamente.');
    }

    public function delete($supplyRequestId)
    {
        $supplyRequest = SupplyRequest::find($supplyRequestId);

        if ($supplyRequest && $supplyRequest->encounter_id == $this->encounter_id) {
            $supplyRequest->delete();

            // Remove from local state
            unset($this->quantities[$supplyRequestId]);
            unset($this->customPrices[$supplyRequestId]);
            unset($this->isFree[$supplyRequestId]);

            // Refresh list
            $this->getSupplyRequestsProperty();

            session()->flash('supply-success', 'Suministro eliminado.');
        }
    }

    public function updatedQuantities($value, $id)
    {
        $supplyRequest = SupplyRequest::find($id);

        if ($supplyRequest && $value > 0) {
            // Validate stock availability
            $practitioner = $this->encounter->practitioner;

            // Get branch_id from appointment, or from consulting_room if not directly available
            $branchId = $this->encounter->appointment->branch_id
                ?? $this->encounter->appointment->consultingRoom->branch_id
                ?? null;

            // Only check practitioner inventory if they have individual inventory enabled
            $practitionerId = ($practitioner && $practitioner->has_individual_inventory) ? $practitioner->id : null;
            $stock = $supplyRequest->inventoryItem->getStockLevel($branchId, $practitionerId);

            if ($value > $stock) {
                session()->flash('supply-error', "Stock insuficiente. Disponible: {$stock}");
                $this->quantities[$id] = $supplyRequest->quantity;

                return;
            }

            $supplyRequest->update(['quantity' => $value]);
        }
    }

    public function toggleFree($id)
    {
        $supplyRequest = SupplyRequest::find($id);

        if ($supplyRequest) {
            $newIsFree = ! $supplyRequest->is_free;

            $supplyRequest->update([
                'is_free' => $newIsFree,
                'is_billable' => ! $newIsFree, // If free, not billable
            ]);

            $this->isFree[$id] = $newIsFree;
        }
    }

    public function updatedCustomPrices($value, $id)
    {
        $supplyRequest = SupplyRequest::find($id);

        if ($supplyRequest) {
            $supplyRequest->update(['custom_price' => $value ?: null]);
        }
    }

    public function render()
    {
        return view('livewire.consultation.supply-requests');
    }
}
