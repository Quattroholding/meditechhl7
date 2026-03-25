<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class TestCyberSourceController extends Controller
{
    public function customers(): JsonResponse
    {
        $merchantId = config('cybersource.merchant_id');
        $keyId = config('cybersource.key_id');
        $secretKey = config('cybersource.secret_key');
        $baseUrl = config('cybersource.base_url');

        $endpoint = '/tms/v2/customers';
        $url = $baseUrl.$endpoint;

        $method = 'GET';
        $date = gmdate('D, d M Y H:i:s T');

        $signatureString =
            'host: '.parse_url($baseUrl, PHP_URL_HOST)."\n".
            "date: $date\n".
            '(request-target): '.strtolower($method)." $endpoint\n".
            "v-c-merchant-id: $merchantId";

        $signature = base64_encode(
            hash_hmac(
                'sha256',
                $signatureString,
                base64_decode($secretKey),
                true
            )
        );

        $headers = [
            'Host' => parse_url($baseUrl, PHP_URL_HOST),
            'Date' => $date,
            'v-c-merchant-id' => $merchantId,
            'Signature' => "keyid=\"$keyId\", ".
                'algorithm="HmacSHA256", '.
                'headers="host date (request-target) v-c-merchant-id", '.
                "signature=\"$signature\"",
            'Content-Type' => 'application/json',
        ];

        $response = Http::withHeaders($headers)->get($url);

        return response()->json([
            'status' => $response->status(),
            'success' => $response->successful(),
            'body' => $response->json(),
        ]);
    }
}
