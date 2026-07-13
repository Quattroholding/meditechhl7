<?php

namespace App\Livewire\ServiceRequest;

use App\Models\ServiceRequest;
use App\Models\ServiceRequestResult;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadResult extends Component
{
    use WithFileUploads;

    public $showModal = false;

    public $serviceRequestId;

    public $serviceRequest;

    #[Validate('required')]
    public $result_type = '';

    #[Validate('nullable')]
    public $code = '';

    #[Validate('nullable')]
    public $code_display = '';

    #[Validate('required')]
    public $result_date;

    #[Validate('required|file|max:10240')] // Max 10MB
    public $file;

    #[Validate('nullable')]
    public $observations = '';

    #[Validate('nullable')]
    public $notes = '';

    #[Validate('required')]
    public $status = 'final';

    #[Validate('nullable')]
    public $interpretation = '';

    #[Validate('nullable')]
    public $reference_range = '';

    public function mount()
    {
        $this->result_date = now()->format('Y-m-d\TH:i');
    }

    #[On('openUploadResultModal')]
    public function openModal($serviceRequestId)
    {
        $this->serviceRequestId = $serviceRequestId;
        $this->serviceRequest = ServiceRequest::with(['patient', 'practitioner'])->find($serviceRequestId);

        if (! $this->serviceRequest) {
            session()->flash('error', __('service_request.not_found'));

            return;
        }
        $this->code = $this->serviceRequest->code;
        $this->code_display = $this->serviceRequest->cpt?->description_es;

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['file', 'result_type', 'code', 'code_display', 'observations', 'notes', 'interpretation', 'reference_range']);
        $this->result_date = now()->format('Y-m-d\TH:i');
        $this->status = 'final';
    }

    public function save()
    {
        $this->validate();

        try {
            // Subir el archivo
            $fileName = $this->file->getClientOriginalName();
            $fileSize = $this->file->getSize();
            $fileType = $this->file->getMimeType();
            $fileHash = hash_file('sha256', $this->file->getRealPath());

            // Verificar duplicados por hash
            $existingResult = ServiceRequestResult::where('file_hash', $fileHash)
                ->where('service_request_id', $this->serviceRequestId)
                ->first();

            if ($existingResult) {
                session()->flash('error', __('service_request_result.file_already_exists'));

                return;
            }

            // Guardar archivo
            $filePath = $this->file->store('service_request_results/'.$this->serviceRequestId, 'public');

            // Crear el resultado
            ServiceRequestResult::create([
                'fhir_id' => 'SRR-'.uniqid(),
                'service_request_id' => $this->serviceRequestId,
                'patient_id' => $this->serviceRequest->patient_id,
                'practitioner_id' => Auth::user()->practitioner?->id ?? $this->serviceRequest->practitioner_id,
                'status' => $this->status,
                'result_type' => $this->result_type,
                'code' => $this->code,
                'code_display' => $this->code_display,
                'file_path' => $filePath,
                'file_name' => $fileName,
                'file_type' => $fileType,
                'file_size' => $fileSize,
                'file_hash' => $fileHash,
                'result_date' => $this->result_date,
                'uploaded_at' => now(),
                'observations' => $this->observations,
                'notes' => $this->notes,
                'interpretation' => $this->interpretation ?: 'normal',
                'reference_range' => $this->reference_range,
                'effective_date' => $this->result_date,
                'issued_date' => now(),
            ]);

            // Intentar cambio automático de estado
            try {
                $this->serviceRequest->autoChangeStatus(
                    __('service_request.auto_completed_reason'),
                    Auth::id()
                );
            } catch (\Exception $e) {
                // Si falla el cambio automático, continuar sin error
                \Log::warning('Auto status change failed: '.$e->getMessage());
            }

            $this->dispatch('showToastr',
                type: 'success',
                message: __('service_request_result.uploaded_successfully'),
            );
            $this->dispatch('refreshServiceRequests');
            $this->closeModal();

        } catch (\Exception $e) {
            // session()->flash('error', __('service_request_result.upload_failed') . ': ' . $e->getMessage());
            dd($e->getMessage());
            $this->dispatch('showToastr',
                type: 'error',
                message: __('service_request_result.upload_failed').': '.$e->getMessage(),
            );
        }
    }

    public function render()
    {
        return view('livewire.service-request.upload-result');
    }
}
