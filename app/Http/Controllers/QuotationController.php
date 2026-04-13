<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Services\QuotationPdfService;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    public function __construct(
        protected QuotationPdfService $pdfService
    ) {}

    public function index()
    {
        return view('quotations.index');
    }

    public function serviceTypes()
    {
        return view('quotations.service-types');
    }

    public function downloadPdf($id)
    {
        $quotation = Quotation::with('items.serviceType')->findOrFail($id);

        return $this->pdfService->downloadPdf($quotation);
    }

    public function streamPdf($id)
    {
        $quotation = Quotation::with('items.serviceType')->findOrFail($id);

        return $this->pdfService->streamPdf($quotation);
    }

    public function changeStatus(Request $request, $id)
    {
        $quotation = Quotation::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:sent,accepted,rejected,cancelled',
        ]);

        switch ($validated['status']) {
            case 'sent':
                $quotation->markAsSent();
                break;
            case 'accepted':
                $quotation->markAsAccepted();
                break;
            case 'rejected':
                $quotation->markAsRejected();
                break;
            case 'cancelled':
                $quotation->cancel();
                break;
        }

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado exitosamente',
        ]);
    }
}
