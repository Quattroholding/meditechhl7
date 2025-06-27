<?php

namespace App\Livewire\Payment;

use App\Models\Payment;
use Livewire\Component;
use Livewire\WithPagination;

class DataTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'payment_date';
    public $sortDirection = 'desc';
    public $paymentMethod = '';
    public $status = '';
    public $dateFrom = '';
    public $dateTo = '';

    protected $queryString = ['search', 'paymentMethod', 'status', 'dateFrom', 'dateTo'];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPaymentMethod()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function clickedDateTo(){
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function getPaymentsProperty()
    {
        return Payment::with(['invoice', 'patient', 'client'])
            ->when($this->search, function ($query) {
                return $query->where('payment_number', 'like', '%' . $this->search . '%')
                       ->orWhere('reference_number', 'like', '%' . $this->search . '%')
                       ->orWhere('transaction_id', 'like', '%' . $this->search . '%')
                       ->orWhereHas('patient', function ($q) {
                           $q->where('name', 'like', '%' . $this->search . '%');
                       })
                       ->orWhereHas('invoice', function ($q) {
                           $q->where('invoice_number', 'like', '%' . $this->search . '%');
                       });
            })
            ->when($this->paymentMethod, function ($query) {
                return $query->where('payment_method', $this->paymentMethod);
            })
            ->when($this->status, function ($query) {
                return $query->where('status', $this->status);
            })
            ->when($this->dateFrom, function ($query) {
                return $query->where('payment_date', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                return $query->where('payment_date', '<=', $this->dateTo);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function getPaymentMethodsProperty()
    {
        return [
            '' => 'Todos los métodos',
            'cash' => 'Efectivo',
            'credit_card' => 'Tarjeta de Crédito',
            'debit_card' => 'Tarjeta de Débito',
            'bank_transfer' => 'Transferencia Bancaria',
            'check' => 'Cheque',
            'online' => 'Pago Online',
            'insurance' => 'Seguro',
            'other' => 'Otro',
        ];
    }

    public function getStatusOptionsProperty()
    {
        return [
            '' => 'Todos los estados',
            'pending' => 'Pendiente',
            'completed' => 'Completado',
            'failed' => 'Fallido',
            'cancelled' => 'Cancelado',
            'refunded' => 'Reembolsado',
        ];
    }

    public function clearFilters()
    {
        $this->reset(['search', 'paymentMethod', 'status', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.payment.data-table');
    }
}
