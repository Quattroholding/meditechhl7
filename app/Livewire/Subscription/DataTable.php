<?php

namespace App\Livewire\Subscription;

use App\Models\ClientSubscription;
use App\Models\StatusHistoryLog;
use App\Services\SubscriptionService;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class DataTable extends Component
{
    use WithPagination;

    public $search = ''; // Búsqueda

    public $statusFilter = ''; // Filtro por estado

    public $sortField = 'created_at'; // Ordenación por defecto

    public $sortDirection = 'desc'; // Dirección de orden

    public $pagination = 15;

    public $showHistoryModal = false;

    public $selectedSubscription;

    public $statusHistory = [];

    public function mount()
    {
        // Inicialización si es necesaria
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
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
        $this->resetPage();
    }

    public function showHistory($subscriptionId)
    {
        $this->selectedSubscription = ClientSubscription::with(['client', 'package'])
            ->find($subscriptionId);

        // Obtener el historial de cambios de estado
        $this->statusHistory = StatusHistoryLog::where('table_name', 'client_subscriptions')
            ->where('record_id', $subscriptionId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $this->showHistoryModal = true;
    }

    public function closeHistoryModal()
    {
        $this->showHistoryModal = false;
        $this->selectedSubscription = null;
        $this->statusHistory = [];
    }

    public function reactivateSubscription($subscriptionId, SubscriptionService $subscriptionService)
    {
        try {
            $subscription = ClientSubscription::findOrFail($subscriptionId);

            if ($subscription->status->value !== 'suspended') {
                $this->dispatch('notify', type: 'error', message: 'Solo se pueden reactivar suscripciones suspendidas');

                return;
            }

            $newInvoice = $subscriptionService->reactivate($subscription);

            if ($newInvoice) {
                $this->dispatch('notify', type: 'success', message: '¡Suscripción reactivada! Se ha generado factura nueva.');
                $this->resetPage();
            } else {
                $this->dispatch('notify', type: 'error', message: 'Error al generar factura durante reactivación');
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Error: '.$e->getMessage());
        }
    }

    public function render()
    {
        $subscriptions = ClientSubscription::with(['client', 'package', 'currentInvoice'])
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->whereHas('client', function ($clientQuery) {
                        $clientQuery->where('name', 'like', '%'.$this->search.'%');
                    })
                        ->orWhereHas('package', function ($packageQuery) {
                            $packageQuery->where('name', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->when($this->statusFilter, function (Builder $query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->pagination);

        return view('livewire.subscription.data-table', [
            'subscriptions' => $subscriptions,
        ]);
    }
}
