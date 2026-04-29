<?php

namespace App\Livewire\ServiceRequest;

use App\Models\ServiceRequest;
use App\Models\ServiceRequestResult;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ViewResults extends Component
{
    use WithPagination;

    public $showModal = false;

    public $serviceRequestId;

    public $serviceRequest;

    public $results = [];

    public $selectedResult = null;

    #[On('openViewResultsModal')]
    public function openModal($serviceRequestId)
    {
        $this->serviceRequestId = $serviceRequestId;
        $this->serviceRequest = ServiceRequest::with(['patient', 'practitioner', 'cpt'])->find($serviceRequestId);

        if (! $this->serviceRequest) {
            session()->flash('error', __('service_request.not_found'));

            $this->dispatch('showToastrSrViewResult',
                type: 'error',
                message:  __('service_request.not_found'),
            );

            return;
        }

        $this->loadResults();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedResult = null;
        $this->reset(['serviceRequestId', 'serviceRequest', 'results']);
    }

    public function loadResults()
    {
        $this->results = ServiceRequestResult::where('service_request_id', $this->serviceRequestId)
            ->with(['practitioner'])
            ->orderBy('result_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function viewResult($resultId)
    {
        $this->selectedResult = ServiceRequestResult::with(['practitioner'])->find($resultId);
    }

    public function closeResultDetail()
    {
        $this->selectedResult = null;
    }

    public function downloadFile($resultId)
    {
        $result = ServiceRequestResult::find($resultId);

        if (! $result || ! $result->file_path) {
            session()->flash('error', __('service_request_result.download_failed'));

            $this->dispatch('showToastrSrViewResult',
                type: 'error',
                message:  __('service_request_result.download_failed'),
            );

            return;
        }

        if (! Storage::disk('public')->exists($result->file_path)) {
            session()->flash('error', __('service_request_result.file_not_found'));

            $this->dispatch('showToastrSrViewResult',
                type: 'error',
                message:   __('service_request_result.file_not_found'),
            );

            return;
        }

        return Storage::disk('public')->download($result->file_path, $result->file_name);
    }

    public function deleteResult($resultId)
    {
        $result = ServiceRequestResult::find($resultId);

        if (! $result) {
            session()->flash('error', __('service_request_result.not_found'));

            $this->dispatch('showToastrSrViewResult',
                type: 'error',
                message:  __('service_request_result.not_found'),
            );

            return;
        }

        // Eliminar archivo físico
        if ($result->file_path && Storage::disk('public')->exists($result->file_path)) {
            Storage::disk('public')->delete($result->file_path);
        }

        // Eliminar registro
        $result->delete();

        session()->flash('success', __('service_request_result.deleted_successfully'));

        $this->dispatch('showToastrSrViewResult',
            type: 'success',
            message:  __('service_request_result.deleted_successfully'),
        );

        $this->loadResults(); // Recargar la lista
        $this->closeResultDetail(); // Cerrar detalle si está abierto
    }

    public function openUploadModal()
    {

        $this->dispatch('openUploadResultModal', $this->serviceRequestId);
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.service-request.view-results');
    }
}
