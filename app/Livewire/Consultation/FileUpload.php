<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use App\Models\Media;
use App\Services\MediaStorageService;
use Livewire\Component;
use Livewire\WithFileUploads;

class FileUpload extends Component
{
    use WithFileUploads;

    public int $encounter_id;

    public ?Encounter $encounter = null;

    public $files = [];

    public $uploadedMedia = [];

    public string $fileCategory = 'clinical_photo';

    public string $fileNote = '';

    public bool $hasExternalStorage = false;

    public bool $viewOnly = false;

    public array $categories = [
        'clinical_photo' => 'Fotografía Clínica',
        'diagnostic_image' => 'Imagen Diagnóstica',
        'lab_result' => 'Resultado de Laboratorio',
        'prescription' => 'Prescripción',
        'clinical_document' => 'Documento Clínico',
        'referral' => 'Documento de Referencia',
    ];

    protected function rules(): array
    {
        return [
            'files.*' => 'required|file|max:10240|mimes:jpg,jpeg,png,gif',
            'fileCategory' => 'required|string',
            'fileNote' => 'nullable|string|max:500',
        ];
    }

    protected function messages(): array
    {
        return [
            'files.*.required' => 'Debe seleccionar al menos un archivo',
            'files.*.file' => 'El archivo no es válido',
            'files.*.max' => 'El archivo no debe ser mayor a 10MB',
            'files.*.mimes' => 'Solo se permiten archivos de imagen (JPG, PNG, GIF)',
            'fileCategory.required' => 'Debe seleccionar una categoría',
        ];
    }

    public function mount(): void
    {
        $this->encounter = Encounter::with('appointment')->findOrFail($this->encounter_id);
        $this->loadUploadedFiles();
        $this->checkExternalStorageConfig();
    }

    protected function checkExternalStorageConfig(): void
    {
        $clientId = $this->encounter->appointment?->client_id ?? auth()->user()?->default_client_id;

        if ($clientId) {
            $provider = \App\Services\Storage\StorageProviderFactory::make($clientId);
            $this->hasExternalStorage = $provider !== null;
        }
    }

    public function loadUploadedFiles(): void
    {
        $this->uploadedMedia = $this->encounter
            ->consultationFiles()
            ->with('encounter.appointment')
            ->whereNull('deleted_at')  // Asegurar que no incluye soft deleted
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($media) {
                $mediaArray = $media->toArray();
                $mediaArray['url'] = $media->isStoredExternally()
                    ? $media->accessible_url
                    : $media->url;
                $mediaArray['file_size_human'] = $media->file_size_human;

                // Usar getRawOriginal para saltarnos el mutador del BaseModel
                // que formatea created_at a dd-mm-yyyy (sin hora)
                $mediaArray['created_at'] = $media->getRawOriginal('created_at');

                return $mediaArray;
            })
            ->toArray();
    }

    public function uploadFiles(): void
    {
        $this->validate();

        try {
            // Reload encounter with appointment relationship
            $encounter = Encounter::with('appointment')->findOrFail($this->encounter_id);

            $service = app(MediaStorageService::class);

            foreach ($this->files as $file) {
                $service->storeConsultationMedia(
                    $file,
                    $encounter,
                    [
                        'type' => 'photo',
                        'modality' => $this->fileCategory,
                        'note' => $this->fileNote,
                    ]
                );
            }

            $this->files = [];
            $this->fileNote = '';

            $this->loadUploadedFiles();

            $this->dispatch('notify',
                type: 'success',
                message: 'Archivos subidos exitosamente'
            );

            $this->dispatch('files-uploaded');
        } catch (\Exception $e) {
            \Log::error('Error uploading consultation files', [
                'encounter_id' => $this->encounter_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('notify',
                type: 'error',
                message: 'Error al subir archivos: ' . $e->getMessage()
            );
        }
    }

    public function deleteFile(int $mediaId): void
    {
        try {
            $media = Media::with('encounter.appointment')->findOrFail($mediaId);

            if ($media->encounter_id !== $this->encounter_id) {
                $this->dispatch('notify',
                    type: 'error',
                    message: 'No tiene permisos para eliminar este archivo'
                );

                return;
            }

            $service = app(MediaStorageService::class);
            $service->deleteMedia($media);

            $this->loadUploadedFiles();

            $this->dispatch('notify',
                type: 'success',
                message: 'Archivo eliminado exitosamente'
            );

            $this->dispatch('file-deleted');
        } catch (\Exception $e) {
            \Log::error('Error deleting consultation file', [
                'media_id' => $mediaId,
                'error' => $e->getMessage()
            ]);

            $this->dispatch('notify',
                type: 'error',
                message: 'Error al eliminar archivo: ' . $e->getMessage()
            );
        }
    }

    public function render()
    {
        return view('livewire.consultation.file-upload');
    }
}
