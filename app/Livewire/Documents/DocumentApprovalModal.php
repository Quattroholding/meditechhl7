<?php

namespace App\Livewire\Documents;

use App\Jobs\ProcessApprovedDocumentJob;
use App\Models\DocumentApproval;
use App\Models\DocumentUpload;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class DocumentApprovalModal extends Component
{
    public ?int $documentUploadId = null;

    public ?DocumentUpload $document = null;

    public array $editedItems = [];

    public array $selectedItems = [];

    public ?string $notes = null;

    public ?string $rejectionReason = null;

    public bool $isEditing = false;

    public bool $showModal = false;

    public bool $isRejecting = false;

    #[On('openApprovalModal')]
    public function open(int $documentId): void
    {
        try {
            $this->documentUploadId = $documentId;
            $this->document = DocumentUpload::findOrFail($documentId);

            // Verify user has access to this client
            $currentClient = auth()->user()->getCurrentClient();
            if (! $currentClient || $currentClient->id !== $this->document->client_id) {
                session()->flash('error', 'No tienes acceso a este documento');

                return;
            }

            // Verify document has been parsed
            if (! $this->document->parseResult) {
                session()->flash('error', 'El documento aún no ha sido procesado. Por favor, espera a que termine el procesamiento.');

                return;
            }

            // Decode extracted_data and initialize selected items
            $extractedData = $this->document->parseResult->extracted_data;
            if (is_string($extractedData)) {
                $extractedData = json_decode($extractedData, true);
            }
            $items = $extractedData['items'] ?? [];
            $this->selectedItems = array_fill(0, count($items), true);

            $this->showModal = true;

        } catch (\Exception $e) {
            Log::error('Error opening approval modal', [
                'document_id' => $documentId,
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', 'Error al abrir el documento: '.$e->getMessage());
        }
    }

    public function updateItemField(int $itemIndex, string $field, mixed $value): void
    {
        $this->editedItems[$itemIndex][$field] = $value;
    }

    public function toggleItemSelection(int $itemIndex): void
    {
        $this->selectedItems[$itemIndex] = ! ($this->selectedItems[$itemIndex] ?? false);
    }

    public function addItem(): void
    {
        if (! $this->document) {
            return;
        }

        try {
            // Get current items
            $extractedData = $this->document->parseResult->extracted_data;
            if (is_string($extractedData)) {
                $extractedData = json_decode($extractedData, true);
            }

            $items = $extractedData['items'] ?? [];

            // Add empty item
            $items[] = [
                'sku' => '',
                'name' => '',
                'quantity' => 1,
                'unit_cost' => 0.0,
                'unit' => 'und',
            ];

            // Update parse result
            $this->document->parseResult->update([
                'extracted_data' => json_encode([
                    'items' => $items,
                    'confidence' => $extractedData['confidence'] ?? 0.9,
                ]),
                'manually_edited' => true,
                'edited_by_user_id' => auth()->id(),
                'edited_at' => now(),
            ]);

            // Refresh modal
            $this->document = DocumentUpload::findOrFail($this->documentUploadId);
            $this->editedItems = [];

            // Reopen modal to refresh
            $this->open($this->documentUploadId);

        } catch (\Exception $e) {
            session()->flash('error', 'Error al agregar item: '.$e->getMessage());
        }
    }

    public function removeItem(int $itemIndex): void
    {
        if (! $this->document) {
            return;
        }

        try {
            // Get current items
            $extractedData = $this->document->parseResult->extracted_data;
            if (is_string($extractedData)) {
                $extractedData = json_decode($extractedData, true);
            }

            $items = $extractedData['items'] ?? [];

            // Remove item at index
            if (isset($items[$itemIndex])) {
                unset($items[$itemIndex]);
                $items = array_values($items); // Re-index
            }

            // Update parse result
            $this->document->parseResult->update([
                'extracted_data' => json_encode([
                    'items' => $items,
                    'confidence' => $extractedData['confidence'] ?? 0.9,
                ]),
                'manually_edited' => true,
                'edited_by_user_id' => auth()->id(),
                'edited_at' => now(),
            ]);

            // Refresh modal
            $this->document = DocumentUpload::findOrFail($this->documentUploadId);
            $this->editedItems = [];

            // Reopen modal to refresh
            $this->open($this->documentUploadId);

        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar item: '.$e->getMessage());
        }
    }

    public function approve(): void
    {
        if (! $this->document) {
            return;
        }

        // Validate at least one item is selected
        if (! in_array(true, $this->selectedItems)) {
            session()->flash('error', 'Debes seleccionar al menos un item');

            return;
        }

        try {
            // Decode extracted data
            $extractedData = $this->document->parseResult->extracted_data;
            if (is_string($extractedData)) {
                $extractedData = json_decode($extractedData, true);
            }

            // Update extracted data with selected items and edited fields
            $items = $extractedData['items'] ?? [];
            $filteredItems = [];

            foreach ($items as $index => $item) {
                if ($this->selectedItems[$index] ?? false) {
                    // Apply edits if any
                    if (isset($this->editedItems[$index])) {
                        $item = array_merge($item, $this->editedItems[$index]);
                    }
                    $filteredItems[] = $item;
                }
            }

            // Update parse result
            $this->document->parseResult->update([
                'extracted_data' => json_encode([
                    'items' => $filteredItems,
                    'confidence' => $extractedData['confidence'] ?? 0.9,
                ]),
                'manually_edited' => ! empty($this->editedItems),
                'edited_by_user_id' => auth()->id(),
                'edited_at' => now(),
            ]);

            // Create approval record
            DocumentApproval::create([
                'document_upload_id' => $this->document->id,
                'approved_by_user_id' => auth()->id(),
                'approved_at' => now(),
                'notes' => $this->notes,
            ]);

            // Mark as approved
            $this->document->markAsApproved();

            // Dispatch processing job
            ProcessApprovedDocumentJob::dispatch($this->document->id);

            session()->flash('success', 'Documento aprobado. El procesamiento ha comenzado.');
            $this->dispatch('refreshDocumentList');
            $this->closeModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Error al aprobar documento: '.$e->getMessage());
        }
    }

    public function startRejecting(): void
    {
        $this->isRejecting = true;
    }

    public function cancelRejection(): void
    {
        $this->isRejecting = false;
        $this->rejectionReason = null;
    }

    public function confirmReject(): void
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:10',
        ]);

        if (! $this->document) {
            return;
        }

        try {
            // Create rejection record
            DocumentApproval::create([
                'document_upload_id' => $this->document->id,
                'rejected_by_user_id' => auth()->id(),
                'rejected_at' => now(),
                'rejection_reason' => $this->rejectionReason,
            ]);

            // Mark as rejected
            $this->document->markAsRejected($this->rejectionReason);

            session()->flash('success', 'Documento rechazado correctamente.');
            $this->dispatch('refreshDocumentList');
            $this->closeModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Error al rechazar documento: '.$e->getMessage());
        }
    }

    public function closeModal(): void
    {
        $this->reset();
        $this->showModal = false;
    }

    public function render()
    {
        $parseResult = $this->document?->parseResult;

        // Decode extracted_data if stored as JSON string
        $extractedData = [];
        if ($parseResult?->extracted_data) {
            $data = $parseResult->extracted_data;
            if (is_string($data)) {
                $data = json_decode($data, true);
            }
            $extractedData = is_array($data) ? $data : [];
        }

        $items = $extractedData['items'] ?? [];

        // Decode validation messages if stored as JSON string
        $validationMessages = [];
        if ($parseResult?->validation_messages) {
            $messages = $parseResult->validation_messages;
            if (is_string($messages)) {
                $messages = json_decode($messages, true);
            }
            $validationMessages = is_array($messages) ? $messages : [];
        }

        // Generate PDF view URL
        $pdfUrl = $this->document ? route('documents.view', $this->document) : null;

        return view('livewire.documents.document-approval-modal', [
            'items' => $items,
            'validationMessages' => $validationMessages,
            'confidenceScore' => $parseResult?->confidence_score ?? 0,
            'pdfUrl' => $pdfUrl,
        ]);
    }
}
