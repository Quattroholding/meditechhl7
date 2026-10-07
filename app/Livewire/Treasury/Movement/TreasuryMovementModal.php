<?php

namespace App\Livewire\Treasury\Movement;

use App\Models\Treasury\Bank;
use App\Models\Treasury\CashRegister;
use App\Models\Treasury\TreasuryMovement;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TreasuryMovementModal extends Component
{
    public bool $isModal = false;

    #[Validate]
    public string $movement_type = '';

    #[Validate]
    public string $movement_date = '';

    #[Validate]
    public float $amount = 0;

    #[Validate]
    public ?int $bank_id = null;

    #[Validate]
    public ?int $cash_register_id = null;

    #[Validate]
    public string $description = '';

    #[Validate]
    public ?string $reference_number = null;

    public function mount(): void
    {
        $this->movement_date = now()->toDateString();
    }

    public function rules(): array
    {
        return [
            'movement_type' => 'required|in:deposit,withdrawal,transfer,adjustment',
            'movement_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'bank_id' => 'nullable|exists:banks,id',
            'cash_register_id' => 'nullable|exists:cash_registers,id',
            'description' => 'required|string|max:500',
            'reference_number' => 'nullable|string|max:100',
        ];
    }

    protected $messages = [
        'movement_type.required' => 'El tipo de movimiento es obligatorio.',
        'movement_type.in' => 'El tipo de movimiento no es válido.',
        'movement_date.required' => 'La fecha del movimiento es obligatoria.',
        'movement_date.date' => 'La fecha debe ser una fecha válida.',
        'amount.required' => 'El monto es obligatorio.',
        'amount.numeric' => 'El monto debe ser un número.',
        'amount.min' => 'El monto debe ser mayor a 0.',
        'bank_id.exists' => 'El banco seleccionado no es válido.',
        'cash_register_id.exists' => 'La caja seleccionada no es válida.',
        'description.required' => 'La descripción es obligatoria.',
    ];

    public function updatedMovementType(): void
    {
        // Reset bank and cash selections when movement type changes
        $this->bank_id = null;
        $this->cash_register_id = null;
    }

    public function save(): void
    {
        $this->authorize('treasury.movements.create');
        $this->validate();

        // Validate that at least one destination is selected based on movement type
        if ($this->movement_type === 'transfer') {
            if (! $this->bank_id || ! $this->cash_register_id) {
                $this->dispatch('swal:alert', [
                    'type' => 'error',
                    'title' => 'Error',
                    'text' => 'Para una transferencia, debe seleccionar tanto un banco como una caja.',
                ]);

                return;
            }
        } else {
            if (! $this->bank_id && ! $this->cash_register_id) {
                $this->dispatch('swal:alert', [
                    'type' => 'error',
                    'title' => 'Error',
                    'text' => 'Debe seleccionar un banco o una caja.',
                ]);

                return;
            }
        }

        $clientId = auth()->user()->getCurrentClient()->id;
        $movementNumber = $this->generateMovementNumber($clientId);

        TreasuryMovement::create([
            'client_id' => $clientId,
            'movement_number' => $movementNumber,
            'movement_date' => $this->movement_date,
            'movement_type' => $this->movement_type,
            'bank_id' => $this->bank_id,
            'cash_register_id' => $this->cash_register_id,
            'amount' => $this->amount,
            'description' => $this->description,
            'reference_number' => $this->reference_number,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $this->dispatch('swal:alert', [
            'type' => 'success',
            'title' => 'Éxito',
            'text' => 'Movimiento de tesorería registrado exitosamente.',
        ]);

        if ($this->isModal) {
            $this->dispatch('closeModal');
        } else {
            $this->redirect(route('treasury.movements.index'));
        }
    }

    private function generateMovementNumber(int $clientId): string
    {
        $year = now()->year;
        $lastMovement = TreasuryMovement::where('client_id', $clientId)
            ->whereYear('created_at', $year)
            ->latest('id')
            ->first();

        $sequence = $lastMovement ? (int) substr($lastMovement->movement_number, -6) + 1 : 1;

        return sprintf('TM-%d-%06d', $year, $sequence);
    }

    public function getBanksProperty()
    {
        return Bank::where('client_id', auth()->user()->getCurrentClient()->id)
            ->where('status', 'active')
            ->orderBy('bank_name')
            ->get();
    }

    public function getCashRegistersProperty()
    {
        return CashRegister::where('client_id', auth()->user()->getCurrentClient()->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.treasury.movement.treasury-movement-modal', [
            'banks' => $this->banks,
            'cashRegisters' => $this->cashRegisters,
        ]);
    }
}
