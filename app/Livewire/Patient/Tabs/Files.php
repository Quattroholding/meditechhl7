<?php

namespace App\Livewire\Patient\Tabs;

use App\Models\File;
use App\Models\Patient;
use App\Services\FileService;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Files extends Component
{
    use WithFileUploads;

    public $patient_id;

    public $patient;

    public $archivos = [];

    public $files = [];

    protected $rules = [
        'archivos.*' => 'required|file|max:1024', // 1MB max por archivo
    ];

    protected $messages = [
        'archivos.*.required' => 'Debe seleccionar al menos un archivo.',
        'archivos.*.file' => 'El archivo debe ser válido.',
        'archivos.*.max' => 'El archivo no puede ser mayor a 1MB.',
    ];

    public function mount($patient_id)
    {
        $this->patient_id = $patient_id;
        $this->loadPatient();
        $this->loadFiles();
    }

    public function loadPatient()
    {
        $this->patient = Patient::findOrFail($this->patient_id);
    }

    public function loadFiles()
    {
        $this->files = File::where('table_name', 'patients')
            ->where('record_id', $this->patient_id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function uploadFiles()
    {
        $this->validate();

        $fileService = new FileService;
        $data = [
            'folder' => 'patients',
            'record_id' => $this->patient_id,
            'type' => 'document',
        ];

        $fileService->guardarArchivos($this->archivos, $data);

        // Limpiar archivos temporales
        $this->archivos = [];

        // Recargar archivos
        $this->loadFiles();

        $this->dispatch('showToastrPatientShow', type: 'success', message: 'Archivo(s) subido(s) exitosamente.');
    }

    public function deleteFile($fileId)
    {
        $file = File::findOrFail($fileId);

        // Verificar que el archivo pertenezca a este paciente
        if ($file->table_name !== 'patients' || $file->record_id != $this->patient_id) {
            session()->flash('error', 'No tiene permisos para eliminar este archivo.');
            $this->dispatch('showToastrPatientShow', type: 'error', message: 'No tiene permisos para eliminar este archivo.');

            return;
        }

        // Eliminar archivo físico
        if (Storage::disk('public')->exists($file->path)) {
            Storage::disk('public')->delete($file->path);
        }

        // Eliminar registro de la base de datos
        $file->delete();

        // Recargar archivos
        $this->loadFiles();

        session()->flash('message', 'Archivo eliminado exitosamente.');
        $this->dispatch('showToastrPatientShow', type: 'success', message: 'Archivo eliminado exitosamente.');
        $this->dispatch('fileDeleted');
    }

    public function downloadFile($fileId)
    {
        $file = File::findOrFail($fileId);

        // Verificar que el archivo pertenezca a este paciente
        if ($file->table_name !== 'patients' || $file->record_id != $this->patient_id) {
            session()->flash('error', 'No tiene permisos para descargar este archivo.');
            $this->dispatch('showToastrPatientShow', type: 'error', message: 'No tiene permisos para descargar este archivo.');

            return;
        }

        $filePath = storage_path('app/public/'.$file->path);

        if (file_exists($filePath)) {
            return response()->download($filePath, $file->name);
        }

        session()->flash('error', 'Archivo no encontrado.');
        $this->dispatch('showToastrPatientShow', type: 'error', message: 'Archivo no encontrado.');
    }

    public function render()
    {
        return view('livewire.patient.tabs.files');
    }
}
