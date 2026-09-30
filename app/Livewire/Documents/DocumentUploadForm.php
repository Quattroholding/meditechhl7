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

    #[Validate('required|string|in:inventory,electricity-bill,water-bill,gas-bill')]
    public ?string $document_type = null;

    #[Validate('required|file|mimes:pdf|max:10240')]
    public $file = null;

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

    public function uploadDocument(): void
    {
        $this->errorMessage = null;
        $this->successMessage = null;

        try {
            $this->validate();

            // Verificar que el cliente existe
            $client = Client::find($this->client_id);
            if (! $client) {
                throw new \Exception('Cliente no válido');
            }

            // Store the file and get the path
            $diskName = config('filesystems.default', 'local');
            $filePath = $this->file->store("documents/{$this->client_id}", $diskName);

            if (! $filePath) {
                throw new \Exception('No se pudo guardar el archivo. Verifica los permisos de almacenamiento.');
            }

            Log::info('Document file stored', [
                'path' => $filePath,
                'disk' => $diskName,
                'size' => $this->file->getSize(),
                'name' => $this->file->getClientOriginalName(),
            ]);

            $documentUpload = DocumentUpload::create([
                'client_id' => $this->client_id,
                'branch_id' => $this->branch_id,
                'document_type' => DocumentType::tryFrom($this->document_type),
                'status' => 'pending',
                'file_path' => $filePath,
                'original_filename' => $this->file->getClientOriginalName(),
                'file_size' => $this->file->getSize(),
                'mime_type' => $this->file->getMimeType(),
                'uploaded_by_user_id' => auth()->id(),
            ]);

            ParseDocumentJob::dispatch($documentUpload->id);

            $this->reset();
            $this->successMessage = 'Documento subido correctamente. El procesamiento ha comenzado.';
            $this->dispatch('documentUploaded');

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
