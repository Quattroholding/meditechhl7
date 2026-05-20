<?php

namespace App\Http\Controllers\Webhooks;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\ClientInvoicePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NeoPaymentsWebhookController extends Controller
{
    /**
     * Handle NeoPayments webhook notifications (3DS Challenge results)
     */
    public function handle(Request $request)
    {
        // Log incoming webhook for debugging
        Log::info('NeoPayments 3DS Webhook Received', [
            'payload' => $request->all(),
            'headers' => $request->headers->all(),
        ]);

        try {
            // NeoPayments sends data in 'payload' field, fallback to 'data' for compatibility
            $data = $request->input('payload') ?? $request->input('data');

            if (! $data) {
                Log::error('NeoPayments webhook: No payload or data field in request', [
                    'all_input' => $request->all(),
                ]);

                return response()->json(['status' => 'error', 'message' => 'No payload or data field'], 400);
            }

            $transactionId = $data['id'] ?? $data['identifier'] ?? null;
            $status = $data['status'] ?? null;
            $responseCode = $data['response_code'] ?? null;
            $authorizationNumber = $data['authorization_number'] ?? null;

            if (! $transactionId) {
                Log::error('NeoPayments webhook: No transaction ID in data');

                return response()->json(['status' => 'error', 'message' => 'No transaction ID'], 400);
            }

            // Find the payment by NeoPayments transaction ID
            // Search in gateway_transaction_id and network_transaction_id (actual DB columns)
            $payment = ClientInvoicePayment::where('gateway_transaction_id', $transactionId)
                ->orWhere('network_transaction_id', $transactionId)
                ->first();

            if (! $payment) {
                Log::warning('NeoPayments webhook: Payment not found', [
                    'transaction_id' => $transactionId,
                ]);

                return response()->json(['status' => 'error', 'message' => 'Payment not found'], 404);
            }

            // Ensure invoice relationship is loaded
            $payment->load('invoice');

            Log::info('NeoPayments webhook: Processing payment', [
                'payment_id' => $payment->id,
                'invoice_id' => $payment->invoice_id,
                'transaction_id' => $transactionId,
                'status' => $status,
                'response_code' => $responseCode,
            ]);

            // Update payment based on transaction status
            if ($status === 'authorized') {
                // Payment successful after 3DS challenge
                // Use actual DB column names: processor_response, auth_code, network_transaction_id
                $payment->processor_response = $responseCode;
                $payment->auth_code = $authorizationNumber;
                $payment->network_transaction_id = $transactionId;

                // Mark as completed (this will also update invoice status)
                $payment->markAsCompleted();

                Log::info('NeoPayments webhook: Payment marked as completed', [
                    'payment_id' => $payment->id,
                    'invoice_id' => $payment->invoice_id,
                ]);
            } elseif (in_array($status, ['denied', 'refused'])) {
                // Payment failed after 3DS challenge
                $payment->status = PaymentStatus::FAILED;
                $payment->processor_response = $responseCode;
                $payment->notes = 'Payment failed after 3DS authentication: '.$status;
                $payment->save();

                Log::info('NeoPayments webhook: Payment marked as failed', [
                    'payment_id' => $payment->id,
                    'invoice_id' => $payment->invoice_id,
                    'status' => $status,
                    'processor_response' => $responseCode,
                ]);
            } else {
                // Unknown status
                Log::warning('NeoPayments webhook: Unknown transaction status', [
                    'payment_id' => $payment->id,
                    'status' => $status,
                ]);
            }

            return response()->json(['status' => 'ok'], 200);
        } catch (\Exception $e) {
            Log::error('NeoPayments webhook: Exception occurred', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }
}
