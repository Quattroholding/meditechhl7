<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Client;
use App\Models\ClientInvoice;
use App\Models\ClientInvoicePayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SuscriptionPaymentController extends Controller
{
    public function index()
    {
        return view('subscriptions.payments.index');
    }

    public function settings()
    {
        return view('subscriptions.payments.settings');
    }

    public function show($id)
    {
        $payment = ClientInvoicePayment::with([
            'invoice.client',
            'invoice.subscription.package',
            'processedBy',
        ])->findOrFail($id);

        return view('subscriptions.payments.show', compact('payment'));
    }

    public function download($id)
    {
        $payment = ClientInvoicePayment::with([
            'invoice.client',
            'invoice.subscription.package',
            'processedBy',
        ])->findOrFail($id);

        // Get company data (Cliente ID 1 - Soluciones Meditec)
        $company = Client::find(1);

        if (! $company) {
            return redirect()->back()->with('error', 'No se encontró la información de la empresa.');
        }

        // Generate PDF
        $pdf = Pdf::loadView('subscriptions.payments.pdf.receipt', [
            'payment' => $payment,
            'company' => $company,
        ]);

        // Set paper size and orientation
        $pdf->setPaper('letter', 'portrait');

        // Download the PDF
        $reference = $payment->payment_reference ?? $payment->id;
        $filename = 'Recibo_Pago_'.$reference.'_'.date('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }

    public function verify($id)
    {
        $payment = ClientInvoicePayment::findOrFail($id);

        // Only allow verification of pending payments
        if ($payment->status->value !== 'pending') {
            session()->flash('message.error','Solo se pueden verificar pagos pendientes.');

            return redirect()
                ->back()
                ->with('error', 'Solo se pueden verificar pagos pendientes.');
        }

        $payment->markAsCompleted(auth()->id());

        session()->flash('message.success','Pago verificado y aprobado exitosamente.');

        return redirect()
            ->route('suscriptions.payments.index')
            ->with('success', 'Pago verificado y aprobado exitosamente.');
    }

    public function reject($id)
    {
        $payment = ClientInvoicePayment::findOrFail($id);

        // Only allow rejection of pending payments
        if ($payment->status->value !== 'pending') {
            return redirect()
                ->back()
                ->with('error', 'Solo se pueden rechazar pagos pendientes.');
        }

        $reason = request()->input('reason', 'Pago rechazado por contabilidad');
        $payment->markAsFailed($reason, auth()->id());

        return redirect()
            ->route('suscriptions.payments.index')
            ->with('success', 'Pago rechazado exitosamente.');
    }

    public function downloadReceipt($id)
    {
        $payment = ClientInvoicePayment::findOrFail($id);

        // Check if receipt file exists
        if (! $payment->receipt_file_path) {
            return redirect()
                ->back()
                ->with('error', 'Este pago no tiene un comprobante adjunto.');
        }

        // Check if file exists in storage
        if (! Storage::exists($payment->receipt_file_path)) {
            return redirect()
                ->back()
                ->with('error', 'El archivo de comprobante no se encuentra en el sistema.');
        }

        // Download the file
        $filename = 'Comprobante_Pago_'.($payment->payment_reference ?? $payment->id).'.'.pathinfo($payment->receipt_file_path, PATHINFO_EXTENSION);

        return Storage::download($payment->receipt_file_path, $filename);
    }

    public function edit($id)
    {
        $payment = ClientInvoicePayment::with([
            'invoice.client',
            'invoice.subscription.package',
        ])->findOrFail($id);

        return view('subscriptions.payments.edit', compact('payment'));
    }

    public function destroy($id)
    {
        $payment = ClientInvoicePayment::findOrFail($id);

        // Only allow deletion of pending payments
        if ($payment->status->value !== 'pending') {
            return redirect()
                ->back()
                ->with('error', 'Solo se pueden eliminar pagos pendientes.');
        }

        $payment->delete();

        return redirect()
            ->route('suscriptions.payments.index')
            ->with('success', 'Pago eliminado exitosamente.');
    }

    public function yappyIPN(Request $request){
        // Yappy enviará: orderId, hash, status, domain

        $orderId = $request->query('orderId');
        $status  = $request->query('status');
        $hash    = $request->query('hash');
        $domain = $_GET['domain'];
        $invoice = ClientInvoice::whereInvoiceNumber($orderId)->first();

        // TODO: validar hash con tu clave secreta
        Log::info("Yappy IPN: {$invoice->id} => {$status}");

        if (isset($orderId) && isset($status) && isset($domain) && isset($hash)) {
            header('Content-Type: application/json');
            $success = validateHash();
            if ($success && $status=='E' && $invoice) {
                // LÓGICA DE NEGOCIOS
                // Actualizar modelo de factura según el estado
                // E = Ejecuto, R = Rechazado, C = Cancelado, X = Expirado
                $statusPayment = PaymentStatus::COMPLETED;
                // Create payment record
                $payment = ClientInvoicePayment::create([
                    'client_invoice_id' => $invoice->id,
                    'amount' => $invoice->total,
                    'payment_date' => now(),
                    'payment_method' => PaymentMethod::YAPPY,
                    'payment_reference' => 'YAPPY-'.$invoice->invoice_number,
                    'status' => $statusPayment,
                ]);

                // Update invoice payment status
                $invoice->updatePaymentStatus();
            }

            return response()->json(['success' => true]);
        }

    }


    function validateHash()
    {
        try {
            $secretKey = env('YAPPY_SECRET_KEY');
            $orderId = $_GET['orderId'];
            $status = $_GET['status'];
            $hash = $_GET['hash'];
            $domain = $_GET['domain'];
            $values = base64_decode($secretKey);
            $secrete = explode('.', $values);
            $signature = hash_hmac('sha256', $orderId . $status . $domain, $secrete[0]);
            $success = strcmp($hash, $signature) === 0;
        } catch (\Throwable $th) {
            $success = false;
        }
        return $success;
    }


}
