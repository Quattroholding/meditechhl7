<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        return view('Invoice.index');
    }

    public function show($id)
    {
        $invoice = Invoice::with([
            'patient',
            'encounter',
            'account',
            'performerPractitioner.user',
            'issuerOrganization',
            'client',
            'lineItems.chargeItem',
        ])->findOrFail($id);

        // Verify user has access to this invoice
        // $userClientIds = auth()->user()->clients()->pluck('client_id')->toArray();
        // if (!in_array($invoice->client_id, $userClientIds)) {
        //    abort(403, __('invoice.errors.permission_denied'));
        // }

        return view('Invoice.show', compact('invoice'));
    }

    public function download(Request $request, $invoice_id)
    {
        try {

            $invoice = Invoice::with([
                'patient',
                'encounter',
                'account',
                'performerPractitioner',
                'issuerOrganization',
                'lineItems.chargeItem',
            ])->findOrFail($invoice_id);

            // Verify user has access to this invoice
            // $userClientIds = auth()->user()->clients()->pluck('client_id')->toArray();
            // if (! in_array($invoice->client_id, $userClientIds)) {
            //    abort(403, 'No tiene permisos para acceder a esta factura.');
            // }

            $data = [
                'invoice' => $invoice,
                'patient' => $invoice->patient,
                'encounter' => $invoice->encounter,
                'account' => $invoice->account,
                'practitioner' => $invoice->performerPractitioner,
                'organization' => $invoice->issuerOrganization,
                'lineItems' => $invoice->lineItems,
                'subtotal' => $invoice->subtotal_amount,
                'tax' => $invoice->tax_amount,
                'total' => $invoice->total_amount,
                'generateDate' => now()->format('d/m/Y H:i:s'),
                'template' => $request->get('template', null),
            ];

            // Determine which template to use
            $templateNumber = $request->get('template', null);
            $templateView = 'Invoice.invoice_template'; // default template

            if ($templateNumber && view()->exists("Invoice.templates.template_{$templateNumber}")) {
                $templateView = "Invoice.templates.template_{$templateNumber}";
            }

            // If HTML preview is requested
            if ($request->has('html')) {
                return view($templateView, $data);
            }

            // Generate PDF}
            $customPaper = [0, 0, 700, 850];
            $pdf = Pdf::loadView($templateView, $data)
            // $pdf = Pdf::loadView('Invoice.templates.master_template', $data)
                ->setPaper('letter', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'defaultFont' => 'Arial',
                ]);

            $fileName = 'factura_'.$invoice->identifier.'.pdf';

            return $pdf->stream($fileName);

        } catch (\Exception $e) {
            session()->flash('message.error', 'Error al generar la factura: '.$e->getMessage());

            return back();
        }
    }
}
