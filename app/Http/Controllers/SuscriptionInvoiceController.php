<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientInvoice;
use Barryvdh\DomPDF\Facade\Pdf;

class SuscriptionInvoiceController extends Controller
{
    public function index()
    {
        return view('subscriptions.invoices.index');
    }

    public function pending()
    {
        return view('subscriptions.invoices.pending');
    }

    public function show($id)
    {
        $invoice = ClientInvoice::with([
            'client',
            'subscription.package',
            'items',
            'payments',
        ])->findOrFail($id);

        return view('subscriptions.invoices.show', compact('invoice'));
    }

    public function download($id)
    {
        $invoice = ClientInvoice::with([
            'client',
            'subscription.package',
            'items',
            'payments',
        ])->findOrFail($id);

        // Get company data (Cliente ID 1 - Soluciones Meditec)
        $company = Client::find(1);

        if (! $company) {
            return redirect()->back()->with('error', 'No se encontró la información de la empresa.');
        }

        // Generate PDF
        $pdf = Pdf::loadView('subscriptions.invoices.pdf.invoice', [
            'invoice' => $invoice,
            'company' => $company,
        ]);

        // Set paper size and orientation
        $pdf->setPaper('letter', 'portrait');

        // Download the PDF
        $filename = 'Factura_'.$invoice->invoice_number.'_'.date('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }

    public function edit($id)
    {
        $invoice = ClientInvoice::with([
            'client',
            'subscription.package',
            'items',
        ])->findOrFail($id);

        return view('subscriptions.invoices.edit', compact('invoice'));
    }

    public function destroy($id)
    {
        $invoice = ClientInvoice::findOrFail($id);

        // Only allow cancellation of pending invoices
        if (! in_array($invoice->status->value, ['pending', 'overdue'])) {
            return redirect()
                ->back()
                ->with('error', 'Solo se pueden cancelar facturas pendientes o vencidas.');
        }

        $invoice->cancel('Cancelada por el usuario');

        return redirect()
            ->route('suscriptions.invoices.index')
            ->with('success', 'Factura cancelada exitosamente.');
    }
}
