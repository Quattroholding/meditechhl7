<?php

namespace App\Livewire\Accounting\Period;

use App\Models\Accounting\AccountingPeriod;
use Carbon\Carbon;
use Livewire\Attributes\Validate;
use Livewire\Component;

class PeriodCreateModal extends Component
{
    #[Validate('required|string|max:100')]
    public string $name = '';

    #[Validate('required|integer|min:2020')]
    public int $fiscal_year = 0;

    #[Validate('required|integer|min:1|max:12')]
    public int $period_number = 0;

    #[Validate('required|date')]
    public string $start_date = '';

    #[Validate('required|date|after_or_equal:start_date')]
    public string $end_date = '';

    public function mount(): void
    {
        $this->fiscal_year = now()->year;
        $this->period_number = now()->month;
        $this->start_date = now()->startOfMonth()->toDateString();
        $this->end_date = now()->endOfMonth()->toDateString();
    }

    public function save(): void
    {
        $this->validate();

        try {
            $clientId = auth()->user()->getCurrentClient()->id;

            AccountingPeriod::create([
                'client_id' => $clientId,
                'name' => $this->name,
                'fiscal_year' => $this->fiscal_year,
                'period_number' => $this->period_number,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'status' => 'open',
                'created_by' => auth()->id(),
            ]);

            session()->flash('message.success', 'Período contable creado exitosamente');
            $this->dispatch('period-created');
            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('message.error', 'Error al crear el período: '.$e->getMessage());
        }
    }

    public function updatedFiscalYear(): void
    {
        $this->updateDateRange();
    }

    public function updatedPeriodNumber(): void
    {
        $this->updateDateRange();
    }

    private function updateDateRange(): void
    {
        if ($this->fiscal_year && $this->period_number) {
            $date = Carbon::createFromDate($this->fiscal_year, $this->period_number, 1);
            $this->start_date = $date->startOfMonth()->toDateString();
            $this->end_date = $date->endOfMonth()->toDateString();
            $this->name = $date->locale('es')->translatedFormat('F Y');
        }
    }

    private function resetForm(): void
    {
        $this->name = '';
        $this->fiscal_year = now()->year;
        $this->period_number = now()->month;
        $this->start_date = now()->startOfMonth()->toDateString();
        $this->end_date = now()->endOfMonth()->toDateString();
    }

    public function render()
    {
        return view('livewire.accounting.period.period-create-modal');
    }
}
