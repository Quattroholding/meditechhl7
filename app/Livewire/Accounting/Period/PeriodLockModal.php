<?php

namespace App\Livewire\Accounting\Period;

use App\Models\Accounting\AccountingPeriod;
use Livewire\Attributes\On;
use Livewire\Component;

class PeriodLockModal extends Component
{
    public ?AccountingPeriod $period = null;

    #[On('edit-period')]
    public function editPeriod(int $id): void
    {
        $this->period = AccountingPeriod::findOrFail($id);
    }

    public function lock(): void
    {
        if (! $this->period || ! $this->period->isClosed()) {
            session()->flash('message.error', 'No se puede bloquear este período');

            return;
        }

        try {
            $this->period->lock();
            session()->flash('message.success', 'Período bloqueado exitosamente');
            $this->dispatch('period-locked');
            $this->period = null;
        } catch (\Exception $e) {
            session()->flash('message.error', 'Error al bloquear el período: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.accounting.period.period-lock-modal');
    }
}
