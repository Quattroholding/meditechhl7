<?php

namespace App\Services;

use App\Jobs\ProcessApprovedDocumentJob;
use App\Models\DocumentApproval;
use App\Models\DocumentUpload;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DocumentApprovalService
{
    /**
     * Approve a document with items and totals.
     *
     * @param  array  $selectedItemIndices  Array of selected item indices
     * @param  array  $editedItems  Array of edited items
     * @param  ?string  $notes  Optional approval notes
     * @param  ?string  $action  Optional action for generic documents
     * @return array Result with status and message
     */
    public function approve(
        DocumentUpload $document,
        array $selectedItemIndices,
        array $editedItems = [],
        ?string $notes = null,
        ?string $action = null
    ): array {
        try {
            // Decode extracted data
            $extractedData = $document->parseResult->extracted_data;
            if (is_string($extractedData)) {
                $extractedData = json_decode($extractedData, true);
            }

            // Filter items based on selection
            $items = $extractedData['items'] ?? [];
            $filteredItems = [];

            Log::info('DocumentApprovalService::approve - Processing items', [
                'document_id' => $document->id,
                'total_items_in_data' => count($items),
                'selected_indices' => $selectedItemIndices,
                'edited_items_count' => count($editedItems),
            ]);

            foreach ($items as $index => $item) {
                if (in_array($index, $selectedItemIndices)) {
                    if (isset($editedItems[$index])) {
                        $item = array_merge($item, $editedItems[$index]);
                    }

                    Log::debug('DocumentApprovalService::approve - Item after processing', [
                        'item_index' => $index,
                        'item' => $item,
                    ]);

                    $filteredItems[] = $item;
                }
            }

            // Calculate totals if not available from OCR
            $subtotal = (float) ($extractedData['subtotal'] ?? 0);
            $totalTax = (float) ($extractedData['total_tax'] ?? 0);
            $totalInvoice = (float) ($extractedData['total'] ?? 0);

            // Update parse result with totals
            $document->parseResult->update([
                'extracted_data' => json_encode([
                    'items' => $filteredItems,
                    'confidence' => $extractedData['confidence'] ?? 0.9,
                    'subtotal' => $subtotal,
                    'total_tax' => $totalTax,
                    'total' => $totalInvoice,
                    'invoice_number' => $extractedData['invoice_number'] ?? null,
                    'invoice_date' => $extractedData['invoice_date'] ?? null,
                ]),
                'manually_edited' => ! empty($editedItems),
                'edited_by_user_id' => auth()->id(),
                'edited_at' => now(),
            ]);

            // Update document with invoice details from OCR using direct SQL
            Log::info('Approving document - updating with OCR data', [
                'document_id' => $document->id,
                'invoice_number' => $extractedData['invoice_number'] ?? null,
                'subtotal' => $subtotal,
                'total_tax' => $totalTax,
                'total' => $totalInvoice,
            ]);

            $updateResult = DB::table('document_uploads')
                ->where('id', $document->id)
                ->update([
                    'invoice_number' => $extractedData['invoice_number'] ?? null,
                    'invoice_date' => $extractedData['invoice_date'] ?? null,
                    'subtotal' => $subtotal,
                    'total_tax' => $totalTax,
                    'total' => $totalInvoice,
                    'updated_at' => now(),
                ]);

            Log::info('Document update result', [
                'document_id' => $document->id,
                'rows_affected' => $updateResult,
            ]);

            // Create or update approval record
            DocumentApproval::updateOrCreate(
                ['document_upload_id' => $document->id],
                [
                    'approved_by_user_id' => auth()->id(),
                    'approved_at' => now(),
                    'notes' => $notes,
                    'selected_action' => $action,
                    'rejected_by_user_id' => null,
                    'rejected_at' => null,
                    'rejection_reason' => null,
                ]
            );

            // Mark as approved
            $document->markAsApproved();

            // Dispatch processing job
            ProcessApprovedDocumentJob::dispatch($document->id);

            Log::info('Document approved successfully', [
                'document_id' => $document->id,
                'items_count' => count($filteredItems),
                'user_id' => auth()->id(),
            ]);

            return [
                'success' => true,
                'message' => 'Documento aprobado. El procesamiento ha comenzado.',
                'document_id' => $document->id,
            ];

        } catch (\Exception $e) {
            Log::error('Error approving document', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'document_id' => $document->id,
            ];
        }
    }

    /**
     * Reject a document with a reason.
     *
     * @return array Result with status and message
     */
    public function reject(DocumentUpload $document, string $rejectionReason): array
    {
        try {
            // Create or update rejection record
            DocumentApproval::updateOrCreate(
                ['document_upload_id' => $document->id],
                [
                    'rejected_by_user_id' => auth()->id(),
                    'rejected_at' => now(),
                    'rejection_reason' => $rejectionReason,
                    'approved_by_user_id' => null,
                    'approved_at' => null,
                    'notes' => null,
                ]
            );

            // Mark as rejected
            $document->markAsRejected($rejectionReason);

            Log::info('Document rejected successfully', [
                'document_id' => $document->id,
                'user_id' => auth()->id(),
            ]);

            return [
                'success' => true,
                'message' => 'Documento rechazado correctamente.',
                'document_id' => $document->id,
            ];

        } catch (\Exception $e) {
            Log::error('Error rejecting document', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'document_id' => $document->id,
            ];
        }
    }
}
