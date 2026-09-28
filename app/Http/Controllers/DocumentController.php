<?php

namespace App\Http\Controllers;

use App\Models\DocumentUpload;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Display document management dashboard
     */
    public function index()
    {
        return view('documents.index');
    }

    /**
     * View PDF inline in browser
     */
    public function view($documentId)
    {
        // Load the document
        $document = DocumentUpload::findOrFail($documentId);

        // Verify access
        $this->authorize('view', $document);

        $disk = Storage::disk('local');

        if (! $disk->exists($document->file_path)) {
            abort(404, 'Archivo no encontrado');
        }

        return response()->file($disk->path($document->file_path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$document->original_filename.'"',
        ]);
    }

    /**
     * Download original PDF
     */
    public function download(DocumentUpload $document)
    {
        // Verify access
        $this->authorize('view', $document);

        $disk = Storage::disk('local');

        if (! $disk->exists($document->file_path)) {
            abort(404, 'Archivo no encontrado');
        }

        return $disk->download($document->file_path, $document->original_filename);
    }

    /**
     * Show document details
     */
    public function show(DocumentUpload $document)
    {
        $this->authorize('view', $document);

        return view('documents.show', ['document' => $document]);
    }

    /**
     * Delete document
     */
    public function destroy(DocumentUpload $document)
    {
        $this->authorize('delete', $document);

        // Only allow deletion if not processed
        if ($document->status->value === 'processed') {
            return back()->withErrors(['error' => 'No se puede eliminar un documento ya procesado']);
        }

        // Delete file
        Storage::disk('local')->delete($document->file_path);

        // Soft delete record
        $document->delete();

        return redirect()->route('documents.index')->with('success', 'Documento eliminado correctamente');
    }
}
