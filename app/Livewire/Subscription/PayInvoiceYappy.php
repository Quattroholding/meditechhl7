<?php

namespace App\Livewire\Subscription;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class PayInvoiceYappy extends Component
{

    public $invoice;

    public function mount($invoice)
    {
        $this->invoice = $invoice;
    }

    public function crearOrdenYappy()
    {
        \Log::info('========== YAPPY REQUEST DEBUG START ==========');
        \Log::info('YAPPY crearOrdenYappy()', [
            'invoice_id' => $this->invoice->id,
            'invoice_total' => $this->invoice->total,
        ]);

        try {
            // Log configuración
            $baseUrl = config('services.yappy.base_url');
            $merchantId = config('services.yappy.merchant_id');
            $appUrl = config('app.url');

            \Log::info('YAPPY Config', [
                'base_url' => $baseUrl,
                'merchant_id' => $merchantId,
                'app_url' => $appUrl,
            ]);

            // 1️⃣ Validar comercio
            $validateUrl = $baseUrl . '/payments/validate/merchant';
            $validatePayload = [
                'merchantId' => $merchantId,
                'urlDomain'  => $appUrl,
            ];

            \Log::info('YAPPY Step 1: Validate Merchant REQUEST', [
                'url' => $validateUrl,
                'method' => 'POST',
                'payload' => $validatePayload,
            ]);

            $validate = Http::post($validateUrl, $validatePayload);

            \Log::info('YAPPY Step 1: Validate Merchant RESPONSE', [
                'status' => $validate->status(),
                'successful' => $validate->successful(),
                'headers' => $validate->headers(),
                'body' => $validate->json(),
                'raw_body' => $validate->body(),
            ]);

            if (!$validate->successful()) {
                throw new \Exception('Error validando comercio Yappy: ' . $validate->body());
            }

            $token = data_get($validate->json(), 'body.token');

            \Log::info('YAPPY Token received', [
                'token' => $token,
                'token_length' => strlen($token ?? ''),
            ]);

            $config = [
                'merchantId'  => $merchantId,
                'orderId'     => 'INV-'.$this->invoice->id.'-'.time(),
                'domain'      => parse_url($appUrl, PHP_URL_HOST),
                'paymentDate' => time(),
                'subtotal'    => number_format($this->invoice->subtotal ?? 0, 2, '.', ''),
                'taxes'       => number_format($this->invoice->taxes ?? 0, 2, '.', ''),
                'discount'    => '0.00',
                'total'       => number_format($this->invoice->total ?? 0, 2, '.', ''),
                'ipnUrl'      => 'https://meditecpty.com/subscriptions/payments/yappy-ipn',
                'aliasYappy'  => '12091',
                'reference'   => $this->invoice->id,
                'description' => "Pago factura #{$this->invoice->id}",
            ];

            // 2️⃣ Crear orden
            $orderUrl = $baseUrl . '/payments/payment-wc';
            $orderHeaders = [
                'Authorization' => $token,
                'Content-Type'  => 'application/json',
            ];

            \Log::info('YAPPY Step 2: Create Order REQUEST', [
                'url' => $orderUrl,
                'method' => 'POST',
                'headers' => $orderHeaders,
                'payload' => $config,
            ]);

            $order = Http::withHeaders($orderHeaders)->post($orderUrl, $config);

            \Log::info('YAPPY Step 2: Create Order RESPONSE', [
                'status' => $order->status(),
                'successful' => $order->successful(),
                'headers' => $order->headers(),
                'body' => $order->json(),
                'raw_body' => $order->body(),
            ]);

            if (!$order->successful()) {
                \Log::error('YAPPY response error', [
                    'status' => $order->status(),
                    'body'   => $order->json(),
                    'raw'    => $order->body(),
                ]);

                throw new \Exception(
                    data_get($order->json(), 'message')
                    ?? 'Error creando orden Yappy'
                );
            }

            $body = $order->json('body');

            \Log::info('YAPPY orden creada exitosamente', [
                'transactionId' => $body['transactionId'] ?? null,
                'token' => $body['token'] ?? null,
                'documentName' => $body['documentName'] ?? null,
                'full_body' => $body,
            ]);

            // 3️⃣ Disparar evento Livewire 3
            $this->dispatch('yappy-ready', [
                'invoiceId'     => $this->invoice->id,
                'transactionId' => $body['transactionId'],
                'token'         => $body['token'],
                'documentName'  => $body['documentName'],
            ]);

            \Log::info('========== YAPPY REQUEST DEBUG END (SUCCESS) ==========');

        } catch (\Throwable $e) {
            \Log::error('YAPPY error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            \Log::info('========== YAPPY REQUEST DEBUG END (ERROR) ==========');

            $this->dispatch('yappy-error', [
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.subscription.pay-invoice-yappy');
    }
}
