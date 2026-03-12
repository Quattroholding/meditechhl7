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
                //'token' => $token,
                'token_length' => strlen($token ?? ''),
            ]);

            $aliasYappy =  preg_replace('/^\+?507/', '', $this->invoice->client->whatsapp ?? '');
            $config = [
                'merchantId'  => $merchantId,
                'orderId'     => $this->invoice->id,
                'domain'      => 'https://meditecpty.com',
                'paymentDate' => time(),
                'subtotal'    => number_format($this->invoice->total ?? 0, 2, '.', ''),
                'taxes'       => '0.00',
                'discount'    => '0.00',
                'total'       => number_format($this->invoice->total ?? 0, 2, '.', ''),
                'ipnUrl'      => 'https://webhooks.meditecpty.com/subscriptions/payments/yappy-ipn',
                //'aliasYappy'  =>$aliasYappy,
                'aliasYappy'  => '60800131',
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
               // 'token' => $body['token'] ?? null,
                'documentName' => $body['documentName'] ?? null,
                'full_body' => $body,
            ]);

            // 3️⃣ Disparar evento Livewire 3
            /*$this->dispatch('yappy-ready',
                invoiceId:  $this->invoice->id,
                transactionId: $body['transactionId'],
                token: $body['token'],
                documentName:$body['documentName'],
            );*/

            $this->dispatch('showToastrYappy',
                type: 'success',
                message: 'Se a enviado una petición de pago por yappy al número de teléfono '.$aliasYappy.'. Una vez recibida la confirmación del pago su suscripción sera activada.',
            );

            \Log::info('========== YAPPY REQUEST DEBUG END (SUCCESS) ==========');

        } catch (\Throwable $e) {
            \Log::error('YAPPY error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            \Log::info('========== YAPPY REQUEST DEBUG END (ERROR) ==========');

            $this->dispatch('refreshSetupReminders');

            $this->dispatch('showToastrYappy',
                type: 'error',
                message: $e->getMessage(),
            );
        }
    }

    public function render()
    {
        return view('livewire.subscription.pay-invoice-yappy');
    }
}
