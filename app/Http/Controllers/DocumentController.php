<?php

namespace App\Http\Controllers;

use App\Jobs\ParseDocumentJob;
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
     * Show form for uploading new document
     */
    public function create()
    {
        return view('documents.create');
    }

    /**
     * Store uploaded document
     */
    public function store()
    {
        $validated = request()->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
            'document_type' => 'required|in:inventory,electricity_bill,water_bill,gas_bill',
        ]);

        // Handle file upload
        if (request()->hasFile('file')) {
            $file = request()->file('file');
            $fileName = time().'_'.$file->getClientOriginalName();
            $filePath = $file->storeAs('documents', $fileName, 'local');

            // Create document record
            $document = DocumentUpload::create([
                'client_id' => auth()->user()->getCurrentClient()->id,
                'document_type' => $validated['document_type'],
                'status' => 'pending_parsing',
                'file_path' => $filePath,
                'original_filename' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_by_user_id' => auth()->id(),
            ]);

            // Dispatch parsing job
            ParseDocumentJob::dispatch($document->id);

            return redirect()->route('documents.index')->with('success', 'Documento subido correctamente. El procesamiento ha comenzado.');
        }

        return back()->withErrors(['file' => 'Error al procesar el archivo.']);
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
     * Show document detail page for review and editing
     */
    public function detail(DocumentUpload $document)
    {
        $this->authorize('view', $document);

        return view('documents.detail', ['document' => $document]);
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
