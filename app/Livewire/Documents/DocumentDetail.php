<?php

namespace App\Livewire\Documents;

use App\Enums\DocumentType;
use App\Models\DocumentUpload;
use App\Services\DocumentApprovalService;
use Livewire\Component;

class DocumentDetail extends Component
{
    public ?int $documentId = null;

    public ?DocumentUpload $document = null;

    public array $editedItems = [];

    public array $selectedItems = [];

    public ?string $notes = null;

    public ?string $rejectionReason = null;

    public bool $isRejecting = false;

    public array $itemTotals = [];

    public float $subtotal = 0.0;

    public float $totalTax = 0.0;

    public float $totalInvoice = 0.0;

    public bool $isApproving = false;

    // Electricity bill properties
    public array $billData = [];

    // Generic document properties
    public array $genericData = [];

    public array $editableFields = [];

    public ?string $selectedAction = null;

    public array $availableActions = [
        'register_electricity_bill' => 'Registrar factura de electricidad',
        'register_water_bill' => 'Registrar factura de agua',
        'register_gas_bill' => 'Registrar factura de gas',
        'save_for_reference' => 'Solo guardar para referencia',
        'discard' => 'Descartar documento',
    ];

    public function mount(DocumentUpload $document): void
    {
        try {
            $this->documentId = $document->id;
            $this->document = $document;

            // Verify user has access to this client
            $currentClient = auth()->user()->getCurrentClient();
            if (! $currentClient || $currentClient->id !== $this->document->client_id) {
                abort(403, 'No tienes acceso a este documento');
            }

            // Verify document has been parsed
            if (! $this->document->parseResult) {
                $this->dispatch('showToastr',
                    type: 'error',
                    message: 'El documento aún no ha sido procesado. Por favor, espera a que termine el procesamiento.',
                );
                session()->flash('error', 'El documento aún no ha sido procesado. Por favor, espera a que termine el procesamiento.');

                return;
            }

            // Decode extracted_data
            $extractedData = $this->document->parseResult->extracted_data;
            if (is_string($extractedData)) {
                $extractedData = json_decode($extractedData, true);
            }

            // Handle different document types
            if ($this->document->document_type === DocumentType::INVENTORY) {
                // For inventory documents, process items
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

                // Calculate totals
                $this->calculateTotals($items);
            } elseif (in_array($this->document->document_type->value, ['ensa', 'idaan', 'naturgy'])) {
                // For utility bills (electricity, water, gas), store the bill data
                $this->billData = $extractedData;
            } else {
                // For generic documents, prepare editable fields
                $this->genericData = $extractedData;
                $this->editableFields = $this->buildEditableFields($extractedData);
            }

        } catch (\Exception $e) {
            $this->dispatch('showToastr',
                type: 'error',
                message: 'Error al cargar el documento: '.$e->getMessage(),
            );
            abort(500, 'Error al cargar el documento: '.$e->getMessage());
        }
    }

    private function isGenericDocument(): bool
    {
        return $this->document
            && ! in_array(
                $this->document->document_type->value,
                [
                    DocumentType::INVENTORY->value,
                    'ensa',
                    'idaan',
                    'naturgy',
                ]
            );
    }

    private function buildEditableFields(array $extractedData): array
    {
        return [
            'key_value_pairs' => $extractedData['key_value_pairs'] ?? [],
            'numeric_fields' => $extractedData['numeric_fields'] ?? [],
            'tables' => $this->formatTablesForEditing($extractedData['tables'] ?? []),
            'full_text' => $extractedData['full_text'] ?? '',
        ];
    }

    private function formatTablesForEditing(array $tables): array
    {
        $formatted = [];

        foreach ($tables as $tableIndex => $table) {
            $formatted[$tableIndex] = $table['rows'] ?? [];
        }

        return $formatted;
    }

    private function persistGenericFields(): void
    {
        try {
            $parseResult = $this->document->parseResult;
            $extractedData = json_decode($parseResult->extracted_data, true) ?? [];

            // Update with edited fields and selected action
            $extractedData['edited_fields'] = $this->editableFields;
            $extractedData['selected_action'] = $this->selectedAction;

            $parseResult->update([
                'extracted_data' => json_encode($extractedData),
                'manually_edited' => true,
                'edited_by_user_id' => auth()->id(),
                'edited_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to persist generic fields', ['error' => $e->getMessage()]);
        }
    }

    public function updateItemField(int $itemIndex, string $field, mixed $value): void
    {
        // Prevent editing already processed documents
        if (in_array($this->document->status->value, ['processed', 'rejected', 'approved', 'processing'])) {
            $this->dispatch('showToastr',
                type: 'error',
                message: 'No se pueden editar items en un documento ya procesado.',
            );

            return;
        }

        $this->editedItems[$itemIndex][$field] = $value;

        // Auto-save change to database
        $this->persistEditedItems();

        // Recalculate totals when any field is edited
        $this->recalculateTotalsAfterEdit();
    }

    private function persistEditedItems(): void
    {
        try {
            // Get current items
            $extractedData = $this->document->parseResult->extracted_data;
            if (is_string($extractedData)) {
                $extractedData = json_decode($extractedData, true);
            }

            $items = $extractedData['items'] ?? [];

            // Apply all edits to items
            foreach ($items as $index => &$item) {
                if (isset($this->editedItems[$index])) {
                    $item = array_merge($item, $this->editedItems[$index]);
                }
            }

            // Ensure all items have required fields
            foreach ($items as &$item) {
                if (! isset($item['base_price']) || empty($item['base_price'])) {
                    $item['base_price'] = $item['unit_cost'] ?? 0;
                }
                // Ensure unit_type is present
                if (! isset($item['unit_type'])) {
                    $item['unit_type'] = 'internal';
                }
                // Ensure internal_units_per_presentation is present
                if (! isset($item['internal_units_per_presentation'])) {
                    $item['internal_units_per_presentation'] = 1;
                }
            }

            // Save to database
            $this->document->parseResult->update([
                'extracted_data' => json_encode([
                    'items' => $items,
                    'confidence' => $extractedData['confidence'] ?? 0.9,
                    'subtotal' => $extractedData['subtotal'] ?? 0,
                    'total_tax' => $extractedData['total_tax'] ?? 0,
                    'total' => $extractedData['total'] ?? 0,
                    'invoice_number' => $extractedData['invoice_number'] ?? null,
                    'invoice_date' => $extractedData['invoice_date'] ?? null,
                ]),
                'manually_edited' => true,
                'edited_by_user_id' => auth()->id(),
                'edited_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to persist edited items', ['error' => $e->getMessage()]);
        }
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

            // Total = (quantity * unit_cost) - (quantity * discount per unit)
            $itemTotal = ($quantity * $unitCost) - ($quantity * $discount);
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
        // Prevent toggling selection on already processed documents
        if (in_array($this->document->status->value, ['processed', 'rejected', 'approved', 'processing'])) {
            return;
        }

        $this->selectedItems[$itemIndex] = ! ($this->selectedItems[$itemIndex] ?? false);
    }

    public function addItem(): void
    {
        if (! $this->document) {
            return;
        }

        // Prevent adding items to already processed documents
        if (in_array($this->document->status->value, ['processed', 'rejected', 'approved', 'processing'])) {
            $this->dispatch('showToastr',
                type: 'error',
                message: 'No se pueden agregar items a un documento ya procesado.',
            );
            session()->flash('error', 'No se pueden agregar items a un documento ya procesado.');

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
            $this->document = DocumentUpload::findOrFail($this->documentId);

            // Apply edits before calculating totals (preserve previous edits)
            foreach ($items as $index => &$item) {
                if (isset($this->editedItems[$index])) {
                    $item = array_merge($item, $this->editedItems[$index]);
                }
            }

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

        // Prevent removing items from already processed documents
        if (in_array($this->document->status->value, ['processed', 'rejected', 'approved', 'processing'])) {
            $this->dispatch('showToastr',
                type: 'error',
                message: 'No se pueden eliminar items de un documento ya procesado.',
            );
            session()->flash('error', 'No se pueden eliminar items de un documento ya procesado.');

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

                // Also re-index editedItems to match
                $newEditedItems = [];
                $skipped = false;
                foreach ($this->editedItems as $idx => $edit) {
                    if ($idx === $itemIndex) {
                        $skipped = true;
                    } else {
                        $newIdx = $skipped && $idx > $itemIndex ? $idx - 1 : $idx;
                        $newEditedItems[$newIdx] = $edit;
                    }
                }
                $this->editedItems = $newEditedItems;
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
            $this->document = DocumentUpload::findOrFail($this->documentId);

            // Apply edits before calculating totals
            foreach ($items as $index => &$item) {
                if (isset($this->editedItems[$index])) {
                    $item = array_merge($item, $this->editedItems[$index]);
                }
            }

            $this->calculateTotals($items);

        } catch (\Exception $e) {
            $this->dispatch('showToastr',
                type: 'error',
                message: $e->getMessage(),
            );
            session()->flash('error', 'Error al eliminar item: '.$e->getMessage());
        }
    }

    public function approve(): void
    {
        if (! $this->document) {
            return;
        }

        // Prevent approving already processed documents
        if (in_array($this->document->status->value, ['processed', 'rejected', 'approved', 'processing'])) {
            $this->dispatch('showToastr',
                type: 'error',
                message: 'No se puede aprobar este documento. Ya fue procesado.',
            );
            session()->flash('error', 'No se puede aprobar este documento. Ya fue procesado.');

            return;
        }

        // Validate generic documents have action selected
        if ($this->isGenericDocument() && empty($this->selectedAction)) {
            $this->dispatch('showToastr',
                type: 'error',
                message: 'Debes seleccionar una acción para procesar el documento',
            );
            session()->flash('error', 'Debes seleccionar una acción para procesar el documento');

            return;
        }

        // Validate at least one item is selected (only for inventory documents)
        if ($this->document->document_type === DocumentType::INVENTORY && ! in_array(true, $this->selectedItems)) {
            $this->dispatch('showToastr',
                type: 'error',
                message: 'Debes seleccionar al menos un item',
            );
            session()->flash('error', 'Debes seleccionar al menos un item');

            return;
        }

        // Set loading state
        $this->isApproving = true;

        // Get selected indices
        $selectedIndices = array_keys(array_filter($this->selectedItems));

        // Persist generic fields if applicable
        if ($this->isGenericDocument()) {
            $this->persistGenericFields();
        }

        // Refresh document from database to ensure we have the latest parsed data
        $this->document->refresh();
        $this->document->load('parseResult');

        // Log the data being sent to approval service
        \Log::info('DocumentDetail::approve() - Data being sent', [
            'document_id' => $this->document->id,
            'selected_indices' => $selectedIndices,
            'edited_items' => $this->editedItems,
            'selected_action' => $this->selectedAction,
            'parse_result_extracted_data' => $this->document->parseResult?->extracted_data,
        ]);

        // Use service to approve document
        $service = new DocumentApprovalService;
        $result = $service->approve(
            $this->document,
            $selectedIndices,
            $this->editedItems,
            $this->notes,
            $this->selectedAction
        );

        if ($result['success']) {
            // Don't redirect yet, let polling detect when job completes
            $message = match ($this->document->document_type) {
                DocumentType::ELECTRICITY_BILL => 'Factura aprobada. Procesando...',
                default => 'Documento aprobado. Procesando...',
            };

            $this->dispatch('showToastr',
                type: 'success',
                message: $message,
            );
        } else {
            // Clear loading state on error
            $this->isApproving = false;
            $this->dispatch('showToastr',
                type: 'error',
                message: $result['message'],
            );
            session()->flash('error', 'Error al aprobar documento: '.$result['message']);
        }
    }

    public function checkApprovalStatus(): void
    {
        if (! $this->isApproving || ! $this->document) {
            return;
        }

        // Refresh document from database
        $this->document = DocumentUpload::findOrFail($this->documentId);

        // Check if processing completed
        if (in_array($this->document->status->value, ['processed', 'rejected', 'parsing_failed'])) {
            $this->isApproving = false;

            if ($this->document->status->value === 'processed') {
                $this->dispatch('showToastr',
                    type: 'success',
                    message: 'Inventario procesado exitosamente',
                );
                session()->flash('success', 'Inventario procesado exitosamente');
                $this->redirect(route('documents.index'));
            } elseif ($this->document->status->value === 'rejected') {
                $this->dispatch('showToastr',
                    type: 'error',
                    message: 'El documento fue rechazado durante el procesamiento',
                );
            } else {
                $this->dispatch('showToastr',
                    type: 'error',
                    message: 'Error al procesar el inventario',
                );
            }
        }
    }

    public function startRejecting(): void
    {
        // Prevent rejecting already processed documents
        if (in_array($this->document->status->value, ['processed', 'rejected', 'approved', 'processing'])) {
            $this->dispatch('showToastr',
                type: 'error',
                message: 'No se puede rechazar este documento. Ya fue procesado.',
            );
            session()->flash('error', 'No se puede rechazar este documento. Ya fue procesado.');

            return;
        }

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
            $this->dispatch('showToastr',
                type: 'success',
                message: $result['message'],
            );
            session()->flash('success', $result['message']);
            $this->redirect(route('documents.index'));
        } else {
            $this->dispatch('showToastr',
                type: 'error',
                message: $result['message'],
            );
            session()->flash('error', 'Error al rechazar documento: '.$result['message']);
        }
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

        return view('livewire.documents.document-detail', [
            'items' => $items,
            'billData' => $this->billData,
            'genericData' => $this->genericData,
            'editableFields' => $this->editableFields,
            'availableActions' => $this->availableActions,
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
