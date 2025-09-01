<?php

namespace App\Livewire\ServiceRequest;

use App\Models\ServiceRequest;
use App\Models\StatusHistoryLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class StatusManager extends Component
{
    public $showModal = false;
    public $serviceRequestId;
    public $serviceRequest;

    #[Validate('required')]
    public $newStatus = '';

    #[Validate('nullable|max:500')]
    public $statusReason = '';

    public $statusHistory = [];

    // Estados FHIR válidos y sus transiciones permitidas
    public $validStatuses = [
        'draft' => ['active', 'revoked', 'entered-in-error'],
        'active' => ['on-hold', 'completed', 'revoked', 'entered-in-error'],
        'on-hold' => ['active', 'revoked', 'entered-in-error'],
        'revoked' => ['entered-in-error'],
        'completed' => ['entered-in-error'],
        'entered-in-error' => [],
        'unknown' => ['draft', 'active', 'entered-in-error']
    ];

    #[On('openStatusManagerModal')]
    public function openModal($serviceRequestId)
    {
        $this->serviceRequestId = $serviceRequestId;
        $this->serviceRequest = ServiceRequest::find($serviceRequestId);

        if (!$this->serviceRequest) {
            session()->flash('error', __('service_request.not_found'));
            return;
        }

        $this->loadStatusHistory();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['newStatus', 'statusReason', 'serviceRequestId', 'serviceRequest', 'statusHistory']);
    }

    public function loadStatusHistory()
    {
        $this->statusHistory = StatusHistoryLog::where('model_name', ServiceRequest::class)
            ->where('record_id', $this->serviceRequestId)
            //->where('new_status', 'status')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getAvailableStatuses()
    {
        $currentStatus = $this->serviceRequest->status ?? 'draft';
        return $this->validStatuses[$currentStatus] ?? [];
    }

    public function changeStatus()
    {
        $this->validate();

        if (!$this->serviceRequest) {
            session()->flash('error', __('service_request.not_found'));
            return;
        }

        $currentStatus = $this->serviceRequest->status;

        // Validar transición
        if (!$this->isValidTransition($currentStatus, $this->newStatus)) {
            session()->flash('error', __('service_request.invalid_status_transition'));
            return;
        }

        try {
            // Registrar cambio en el historial antes de actualizar
            StatusHistoryLog::create([
                'table_name'=>'service_requests',
                'model_name'=> 'App\Models\ServiceRequest',
                'record_id'=>$this->serviceRequest->id,
                'user_id'=>Auth::user()->id,
                'old_status' => $currentStatus,
                'new_status' => $this->newStatus,
                'observation'=> $this->statusReason ?: __('service_request.manual_status_change'),
                'change_type' => 'manual',
            ]);

            // Actualizar el estado
            $this->serviceRequest->update([
                'status' => $this->newStatus,
                'last_updated' => now(),
            ]);

            session()->flash('message.success', __('service_request.status_changed_successfully'));
            $this->dispatch('showToastrStatusManager',
                type: 'success',
                message:__('service_request.status_changed_successfully'),
            );
            $this->dispatch('refreshServiceRequests');
            $this->closeModal();

        } catch (\Exception $e) {
            session()->flash('message.error', __('service_request.status_change_failed') . ': ' . $e->getMessage());
            $this->dispatch('showToastrStatusManager',
                type: 'error',
                message:  __('service_request.status_change_failed') . ': ' . $e->getMessage(),
            );
        }
    }

    public function isValidTransition($fromStatus, $toStatus)
    {
        if (!$fromStatus) {
            return in_array($toStatus, ['draft', 'active']);
        }

        return in_array($toStatus, $this->validStatuses[$fromStatus] ?? []);
    }

    public function getStatusColor($status)
    {
        return match($status) {
            'draft' => 'secondary',
            'active' => 'primary',
            'on-hold' => 'warning',
            'revoked' => 'danger',
            'completed' => 'success',
            'entered-in-error' => 'dark',
            'unknown' => 'light',
            default => 'secondary'
        };
    }

    public function render()
    {
        return view('livewire.service-request.status-manager', [
            'availableStatuses' => $this->getAvailableStatuses()
        ]);
    }
}
