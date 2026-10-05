<?php

namespace App\Livewire\Documents;

use App\Enums\DocumentType;
use App\Jobs\ParseDocumentJob;
use App\Models\Branch;
use App\Models\Client;
use App\Models\DocumentUpload;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentUploadForm extends Component
{
    use WithFileUploads;

    #[Validate('required|exists:clients,id')]
    public ?int $client_id = null;

    #[Validate('required|exists:branches,id')]
    public ?int $branch_id = null;

    #[Validate('required|array|min:1|max:10')]
    public array $files = [];

    // Document type for each file (parallel array to $files)
    public array $fileTypes = [];

    public ?string $successMessage = null;

    public ?string $errorMessage = null;

    public function mount(): void
    {
        // Intenta obtener cliente del usuario
        $userClients = auth()->user()->clients;
        if ($userClients->isNotEmpty()) {
            $this->client_id = $userClients->first()->id;
        } else {
            // Si no tiene clientes asignados, usa el primer cliente disponible
            $firstClient = Client::first();
            if ($firstClient) {
                $this->client_id = $firstClient->id;
            }
        }

        // Obtener primera branch del cliente seleccionado
        $this->selectDefaultBranch();
    }

    public function updatedClientId(): void
    {
        // Resetear branch cuando cambia el cliente
        $this->selectDefaultBranch();
    }

    private function selectDefaultBranch(): void
    {
        if ($this->client_id) {
            $firstBranch = Branch::where('client_id', $this->client_id)->first();
            $this->branch_id = $firstBranch?->id;
        }
    }

    public function removeFile(int $index): void
    {
        unset($this->files[$index]);
        unset($this->fileTypes[$index]);
        // Reindex arrays
        $this->files = array_values($this->files);
        $this->fileTypes = array_values($this->fileTypes);
    }

    public function uploadDocument(): void
    {
        $this->errorMessage = null;
        $this->successMessage = null;

        try {
            $this->validate();

            // Validar que cada archivo tenga un tipo de documento asignado
            foreach ($this->files as $index => $file) {
                if (empty($this->fileTypes[$index])) {
                    throw new \Exception("El documento '{$file->getClientOriginalName()}' no tiene tipo seleccionado.");
                }

                // Validar que sea un tipo válido
                $validTypes = array_map(fn ($case) => $case->value, DocumentType::cases());
                if (! in_array($this->fileTypes[$index], $validTypes)) {
                    throw new \Exception("Tipo de documento inválido para '{$file->getClientOriginalName()}'.");
                }
            }

            // Verificar que el cliente existe
            $client = Client::find($this->client_id);
            if (! $client) {
                throw new \Exception('Cliente no válido');
            }

            // Process each file
            $diskName = config('filesystems.default', 'local');
            $uploadedCount = 0;
            $delaySeconds = 0; // Delay progresivo: 0s para el primero, 120s para el segundo, etc.
            $delayIncrement = 5; // 2 minutos entre cada job

            foreach ($this->files as $index => $file) {
                try {
                    // Store the file and get the path
                    $filePath = $file->store("documents/{$this->client_id}", $diskName);

                    if (! $filePath) {
                        throw new \Exception('No se pudo guardar el archivo. Verifica los permisos de almacenamiento.');
                    }

                    Log::info('Document file stored', [
                        'path' => $filePath,
                        'disk' => $diskName,
                        'size' => $file->getSize(),
                        'name' => $file->getClientOriginalName(),
                        'type' => $this->fileTypes[$index],
                    ]);

                    $documentUpload = DocumentUpload::create([
                        'client_id' => $this->client_id,
                        'branch_id' => $this->branch_id,
                        'document_type' => DocumentType::tryFrom($this->fileTypes[$index]),
                        'status' => 'pending',
                        'file_path' => $filePath,
                        'original_filename' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_by_user_id' => auth()->id(),
                    ]);

                    // Dispatch parse job with progressive delay (2 minutes between jobs)
                    ParseDocumentJob::dispatch($documentUpload->id)
                        ->delay(now()->addSeconds($delaySeconds));

                    Log::info('Parse job dispatched', [
                        'document_id' => $documentUpload->id,
                        'delay_seconds' => $delaySeconds,
                        'filename' => $file->getClientOriginalName(),
                    ]);

                    $uploadedCount++;
                    $delaySeconds += $delayIncrement;

                } catch (\Throwable $fileError) {
                    Log::error('Error processing single document', [
                        'filename' => $file->getClientOriginalName(),
                        'error' => $fileError->getMessage(),
                    ]);

                    // Continue with next file
                    continue;
                }
            }

            // Reset form
            $this->reset();

            // Show success message with count
            if ($uploadedCount > 0) {
                $this->successMessage = $uploadedCount === 1
                    ? 'Documento subido correctamente. El procesamiento ha comenzado.'
                    : "{$uploadedCount} documentos subidos correctamente. El procesamiento comenzará en intervalos de 2 minutos.";
                $this->dispatch('documentUploaded');
            } else {
                throw new \Exception('No se pudieron procesar los archivos.');
            }

        } catch (\Throwable $e) {
            Log::error('Document upload error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render()
    {
        $branches = $this->client_id
            ? Branch::where('client_id', $this->client_id)->get()
            : collect();

        return view('livewire.documents.document-upload-form', [
            'clients' => Client::all(),
            'branches' => $branches,
            'documentTypes' => DocumentType::cases(),
        ]);
    }
}
