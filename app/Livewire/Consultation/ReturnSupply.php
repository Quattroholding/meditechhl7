<?php

namespace App\Livewire\Consultation;

use App\Enums\SupplyReturnReason;
use App\Models\SupplyDelivery;
use Livewire\Component;

class ReturnSupply extends Component
{
    public ?int $deliveryId = null;

    public ?SupplyDelivery $delivery = null;

    // Form fields
    public float $quantityToReturn = 0;

    public string $reason = '';

    public string $notes = '';

    // Modal state
    public bool $showModal = false;

    // Listeners
    protected $listeners = ['openReturnModal' => 'openModal'];

    // Available quantities
    public float $quantityDispensed = 0;

    public float $quantityAlreadyReturned = 0;

    public float $quantityAvailableToReturn = 0;

    protected function rules()
    {
        return [
            'quantityToReturn' => [
                'required',
                'numeric',
                'min:0.01',
                'max:'.$this->quantityAvailableToReturn,
            ],
            'reason' => 'required|in:'.implode(',', array_column(SupplyReturnReason::cases(), 'value')),
            'notes' => 'nullable|string|max:1000',
        ];
    }

    protected $messages = [
        'quantityToReturn.required' => 'La cantidad es requerida',
        'quantityToReturn.min' => 'La cantidad debe ser mayor a 0',
        'quantityToReturn.max' => 'No puedes devolver más de lo disponible',
        'reason.required' => 'La razón es requerida',
    ];

    public function openModal($deliveryId)
    {
        $this->deliveryId = $deliveryId;
        $this->delivery = SupplyDelivery::with(['inventoryItem', 'supplyReturns'])->findOrFail($deliveryId);

        // Calculate quantities
        $this->quantityDispensed = $this->delivery->supplied_quantity;
        $this->quantityAlreadyReturned = $this->delivery->getTotalQuantityReturned();
        $this->quantityAvailableToReturn = $this->quantityDispensed - $this->quantityAlreadyReturned;

        // Set default quantity to full available
        $this->quantityToReturn = $this->quantityAvailableToReturn;

        // Reset form
        $this->reason = '';
        $this->notes = '';

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['deliveryId', 'delivery', 'quantityToReturn', 'reason', 'notes']);
        $this->resetValidation();
    }

    public function processReturn()
    {
        $this->validate();

        try {
            $reasonEnum = SupplyReturnReason::from($this->reason);

            $this->delivery->returnSupply(
                $this->quantityToReturn,
                $reasonEnum,
                $this->notes
            );

            $this->dispatch('showToastr',
                type: 'success',
                message: 'Suministro devuelto correctamente. El stock ha sido restaurado.'
            );

            $this->dispatch('supply-returned');
            $this->closeModal();
        } catch (\Exception $e) {
            \Log::error('Error al devolver suministro: '.$e->getMessage(), [
                'delivery_id' => $this->deliveryId,
                'quantity' => $this->quantityToReturn,
                'reason' => $this->reason,
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('showToastr',
                type: 'error',
                message: 'Error al devolver suministro: '.$e->getMessage()
            );
        }
    }

    public function render()
    {
        return view('livewire.consultation.return-supply', [
            'returnReasons' => SupplyReturnReason::options(),
        ]);
    }
}
