<?php

namespace App\Livewire\Doctor;

use App\Models\File;
use App\Models\Practitioner;
use App\Services\FileService;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class SignatureManager extends Component
{
    use WithFileUploads;

    public $practitioner_id;

    public $practitioner;

    public $signature_file;

    public $seal_file;

    public $signature_preview;

    public $seal_preview;

    public $existing_signature;

    public $existing_seal;

    public $uploading_signature = false;

    public $uploading_seal = false;

    protected $rules = [
        'signature_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'seal_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ];

    protected $messages = [
        'signature_file.image' => 'El archivo de firma debe ser una imagen',
        'signature_file.mimes' => 'La firma debe ser un archivo: jpeg, png, jpg, gif, svg',
        'signature_file.max' => 'La firma no puede ser mayor a 2MB',
        'seal_file.image' => 'El archivo de sello debe ser una imagen',
        'seal_file.mimes' => 'El sello debe ser un archivo: jpeg, png, jpg, gif, svg',
        'seal_file.max' => 'El sello no puede ser mayor a 2MB',
    ];

    public function mount($practitioner_id)
    {
        $this->practitioner_id = $practitioner_id;
        $this->practitioner = Practitioner::find($practitioner_id);
        // No cargar archivos en mount, esperar la llamada explícita a loadData()
    }

    public function loadData()
    {
        $this->loadExistingFiles();
    }

    public function loadExistingFiles()
    {
        // Cargar firma existente
        $this->existing_signature = File::where('table_name', 'practitioners')
            ->where('record_id', $this->practitioner_id)
            ->where('type', 'signature')
            ->first();

        // Cargar sello existente
        $this->existing_seal = File::where('table_name', 'practitioners')
            ->where('record_id', $this->practitioner_id)
            ->where('type', 'seal')
            ->first();
    }

    public function updatedSignatureFile()
    {
        $this->validateOnly('signature_file');

        if ($this->signature_file) {
            $this->signature_preview = $this->signature_file->temporaryUrl();
        }
    }

    public function updatedSealFile()
    {
        $this->validateOnly('seal_file');

        if ($this->seal_file) {
            $this->seal_preview = $this->seal_file->temporaryUrl();
        }
    }

    public function uploadSignature()
    {
        $this->validate(['signature_file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048']);

        try {
            $this->uploading_signature = true;

            // Eliminar firma anterior si existe
            if ($this->existing_signature) {
                $this->deleteFile($this->existing_signature);
            }

            // Subir nueva firma
            $fileService = new FileService;
            $data = [
                'record_id' => $this->practitioner_id,
                'folder' => 'practitioners',
                'type' => 'signature',
                'name' => 'signature_'.$this->practitioner_id.'_'.time(),
            ];

            $fileService->guardarArchivos([$this->signature_file], $data, true);

            // Recargar archivos existentes
            $this->loadExistingFiles();

            // Limpiar campos
            $this->signature_file = null;
            $this->signature_preview = null;
            $this->uploading_signature = false;

            session()->flash('message', 'Firma subida exitosamente.');
            $this->dispatch('signature-uploaded');
            // Dispatch event to refresh setup reminders
            $this->dispatch('refreshSetupReminders');

        } catch (\Exception $e) {
            $this->uploading_signature = false;
            session()->flash('error', 'Error al subir la firma: '.$e->getMessage());
        }
    }

    public function uploadSeal()
    {
        $this->validate(['seal_file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048']);

        try {
            $this->uploading_seal = true;

            // Eliminar sello anterior si existe
            if ($this->existing_seal) {
                $this->deleteFile($this->existing_seal);
            }

            // Subir nuevo sello
            $fileService = new FileService;
            $data = [
                'record_id' => $this->practitioner_id,
                'folder' => 'practitioners',
                'type' => 'seal',
                'name' => 'seal_'.$this->practitioner_id.'_'.time(),
            ];

            $fileService->guardarArchivos([$this->seal_file], $data, true);

            // Recargar archivos existentes
            $this->loadExistingFiles();

            // Limpiar campos
            $this->seal_file = null;
            $this->seal_preview = null;
            $this->uploading_seal = false;

            session()->flash('message', 'Sello subido exitosamente.');
            $this->dispatch('seal-uploaded');
            // Dispatch event to refresh setup reminders
            $this->dispatch('refreshSetupReminders');

        } catch (\Exception $e) {
            $this->uploading_seal = false;
            session()->flash('error', 'Error al subir el sello: '.$e->getMessage());
        }
    }

    public function deleteSignature()
    {
        if ($this->existing_signature) {
            try {
                $this->deleteFile($this->existing_signature);
                $this->existing_signature = null;
                session()->flash('message', 'Firma eliminada exitosamente.');
            } catch (\Exception $e) {
                session()->flash('error', 'Error al eliminar la firma: '.$e->getMessage());
            }
        }
    }

    public function deleteSeal()
    {
        if ($this->existing_seal) {
            try {
                $this->deleteFile($this->existing_seal);
                $this->existing_seal = null;
                session()->flash('message', 'Sello eliminado exitosamente.');
            } catch (\Exception $e) {
                session()->flash('error', 'Error al eliminar el sello: '.$e->getMessage());
            }
        }
    }

    private function deleteFile($file)
    {
        // Eliminar archivo físico de almacenamiento privado
        if (Storage::disk('local')->exists($file->path)) {
            Storage::disk('local')->delete($file->path);
        }

        // Eliminar registro de la base de datos
        $file->delete();
    }

    public function cancelSignatureUpload()
    {
        $this->signature_file = null;
        $this->signature_preview = null;
        $this->uploading_signature = false;
        $this->resetErrorBag('signature_file');
    }

    public function cancelSealUpload()
    {
        $this->seal_file = null;
        $this->seal_preview = null;
        $this->uploading_seal = false;
        $this->resetErrorBag('seal_file');
    }

    public function render()
    {
        return view('livewire.doctor.signature-manager');
    }
}
