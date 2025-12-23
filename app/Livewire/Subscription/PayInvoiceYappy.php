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
        \Log::info('YAPPY crearOrdenYappy()', [
            'invoice_id' => $this->invoice->id
        ]);

        try {

            // 1️⃣ Validar comercio
            $validate = Http::post(config('services.yappy.base_url') . '/payments/validate/merchant', [
                'merchantId' => config('services.yappy.merchant_id'),
                'urlDomain'  => config('app.url'),
            ]);

            if (!$validate->successful()) {
                throw new \Exception('Error validando comercio Yappy');
            }

            $token = data_get($validate->json(), 'body.token');

            $config = [
                'merchantId'  => config('services.yappy.merchant_id'),
                'orderId'     => 'INV-'.$this->invoice->id.'-'.time(),
                'domain'      => parse_url(config('app.url'), PHP_URL_HOST),
                'paymentDate' => time(),
                'subtotal'    => number_format($this->invoice->subtotal ?? 0, 2, '.', ''),
                'taxes'       => number_format($this->invoice->taxes ?? 0, 2, '.', ''),
                'discount'    => '0.00',
                'total'       => number_format($this->invoice->total ?? 0, 2, '.', ''),
                'ipnUrl'      => 'https://meditecpty.com/subscriptions/payments/yappy-ipn',
                'aliasYappy'  => '12091',
                // CAMPOS ADICIONALES REQUERIDOS
                'reference'   => $this->invoice->id,
                'description' => "Pago factura #{$this->invoice->id}",
            ];

            \Log::info('Config Yappy',$config);

            // 2️⃣ Crear orden
            $order = Http::withHeaders([
                'Authorization' => $token,
                'Content-Type'  => 'application/json',
            ])->post(config('services.yappy.base_url') . '/payments/payment-wc',$config);

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

            \Log::info('YAPPY orden creada', $body);

            // 3️⃣ Disparar evento Livewire 3
            $this->dispatch('yappy-ready', [
                'invoiceId'     => $this->invoice->id,
                'transactionId' => $body['transactionId'],
                'token'         => $body['token'],
                'documentName'  => $body['documentName'],
            ]);

        } catch (\Throwable $e) {

            \Log::error('YAPPY error', [
                'message' => $e->getMessage()
            ]);

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
