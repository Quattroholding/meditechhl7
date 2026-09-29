<?php

namespace App\Livewire\Documents;

use App\Models\DocumentUpload;
use App\Services\DocumentApprovalService;
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

    public array $itemTotals = []; // Calculated totals per item

    public float $subtotal = 0.0;

    public float $totalTax = 0.0;

    public float $totalInvoice = 0.0;

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

            // Ensure all items have required fields
            foreach ($items as &$item) {
                if (! isset($item['unit_type'])) {
                    $item['unit_type'] = 'internal';
                }
                if (! isset($item['internal_units_per_presentation'])) {
                    $item['internal_units_per_presentation'] = 1;
                }
                if (! isset($item['base_price'])) {
                    $item['base_price'] = $item['unit_cost'] ?? 0;
                }
            }

            $this->selectedItems = array_fill(0, count($items), true);

            // Load totals from OCR if available, otherwise calculate
            if (isset($extractedData['subtotal']) && isset($extractedData['total_tax']) && isset($extractedData['total'])) {
                $this->subtotal = (float) $extractedData['subtotal'];
                $this->totalTax = (float) $extractedData['total_tax'];
                $this->totalInvoice = (float) $extractedData['total'];
            } else {
                // Fallback: calculate totals from items
                $this->calculateTotals($items);
            }

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

        // Recalculate totals when any field is edited
        $this->recalculateTotalsAfterEdit();
    }

    private function calculateTotals(array $items): void
    {
        $this->itemTotals = [];
        $this->subtotal = 0.0;
        $this->totalTax = 0.0;

        foreach ($items as $index => $item) {
            $quantity = (float) ($item['quantity'] ?? 0);
            $unitCost = (float) ($item['unit_cost'] ?? 0);
            $discount = (float) ($item['discount'] ?? 0);
            $tax = (float) ($item['tax'] ?? 0);

            // Total = (quantity * unit_cost) - discount
            $itemTotal = ($quantity * $unitCost) - $discount;
            $this->itemTotals[$index] = $itemTotal;
            $this->subtotal += $itemTotal;
            $this->totalTax += $tax;
        }

        $this->totalInvoice = $this->subtotal + $this->totalTax;
    }

    private function recalculateTotalsAfterEdit(): void
    {
        // Get current items
        $extractedData = $this->document->parseResult->extracted_data;
        if (is_string($extractedData)) {
            $extractedData = json_decode($extractedData, true);
        }

        $items = $extractedData['items'] ?? [];

        // Apply any edits
        foreach ($items as $index => &$item) {
            if (isset($this->editedItems[$index])) {
                $item = array_merge($item, $this->editedItems[$index]);
            }
        }

        // Recalculate
        $this->calculateTotals($items);
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
                'base_price' => 0.0,
                'discount' => 0.0,
                'tax' => 0.0,
                'unit' => 'und',
                'unit_type' => 'internal',
                'internal_units_per_presentation' => 1,
            ];

            // Update parse result
            $this->document->parseResult->update([
                'extracted_data' => json_encode([
                    'items' => $items,
                    'confidence' => $extractedData['confidence'] ?? 0.9,
                    'subtotal' => $extractedData['subtotal'] ?? 0,
                    'total_tax' => $extractedData['total_tax'] ?? 0,
                    'total' => $extractedData['total'] ?? 0,
                ]),
                'manually_edited' => true,
                'edited_by_user_id' => auth()->id(),
                'edited_at' => now(),
            ]);

            // Refresh modal
            $this->document = DocumentUpload::findOrFail($this->documentUploadId);
            $this->editedItems = [];
            $this->calculateTotals($items);

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
                    'subtotal' => $extractedData['subtotal'] ?? 0,
                    'total_tax' => $extractedData['total_tax'] ?? 0,
                    'total' => $extractedData['total'] ?? 0,
                ]),
                'manually_edited' => true,
                'edited_by_user_id' => auth()->id(),
                'edited_at' => now(),
            ]);

            // Refresh modal
            $this->document = DocumentUpload::findOrFail($this->documentUploadId);
            $this->editedItems = [];
            $this->calculateTotals($items);

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

        // Get selected indices
        $selectedIndices = array_keys(array_filter($this->selectedItems));

        // Use service to approve document
        $service = new DocumentApprovalService;
        $result = $service->approve(
            $this->document,
            $selectedIndices,
            $this->editedItems,
            $this->notes
        );

        if ($result['success']) {
            session()->flash('success', $result['message']);
            $this->dispatch('refreshDocumentList');
            $this->closeModal();
        } else {
            session()->flash('error', 'Error al aprobar documento: '.$result['message']);
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

        // Use service to reject document
        $service = new DocumentApprovalService;
        $result = $service->reject($this->document, $this->rejectionReason);

        if ($result['success']) {
            session()->flash('success', $result['message']);
            $this->dispatch('refreshDocumentList');
            $this->closeModal();
        } else {
            session()->flash('error', 'Error al rechazar documento: '.$result['message']);
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
            'itemTotals' => $this->itemTotals,
            'subtotal' => $this->subtotal,
            'totalTax' => $this->totalTax,
            'totalInvoice' => $this->totalInvoice,
        ]);
    }
}
