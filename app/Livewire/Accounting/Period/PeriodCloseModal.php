<?php

namespace App\Livewire\Accounting\Period;

use App\Models\Accounting\AccountingPeriod;
use Livewire\Attributes\On;
use Livewire\Component;

class PeriodCloseModal extends Component
{
    public ?AccountingPeriod $period = null;

    #[On('edit-period')]
    public function editPeriod(int $id): void
    {
        $this->period = AccountingPeriod::findOrFail($id);
    }

    public function close(): void
    {
        if (! $this->period || ! $this->period->isOpen()) {
            session()->flash('message.error', 'No se puede cerrar este período');

            return;
        }

        try {
            $this->period->close(auth()->user());
            session()->flash('message.success', 'Período cerrado exitosamente');
            $this->dispatch('period-closed');
            $this->period = null;
        } catch (\Exception $e) {
            session()->flash('message.error', 'Error al cerrar el período: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.accounting.period.period-close-modal');
    }
}
