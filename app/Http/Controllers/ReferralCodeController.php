<?php

namespace App\Http\Controllers;

use App\Models\Client;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Barryvdh\DomPDF\Facade\Pdf;

class ReferralCodeController extends Controller
{
    public function downloadPdf(Client $client)
    {
        // Verify user has access to this client
        if (! auth()->user()->clients->contains($client->id)) {
            abort(403, 'No autorizado para acceder a este cliente.');
        }

        $referralCode = $client->referralCode;

        if (! $referralCode) {
            abort(404, 'Código de referido no encontrado.');
        }

        // Generate registration URL with referral code
        $shareUrl = route('public.register', ['ref' => $referralCode->code]);

        // Generate QR code using BaconQrCode directly
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd
        );
        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($shareUrl);

        // Encode for embedding in PDF
        $qrCode = base64_encode($qrCodeSvg);

        // Load PDF view
        $pdf = Pdf::loadView('pdf.referral-code', [
            'client' => $client,
            'referralCode' => $referralCode,
            'shareUrl' => $shareUrl,
            'qrCode' => $qrCode,
        ]);

        // Download PDF
        return $pdf->stream('codigo-referido-'.$referralCode->code.'.pdf');
    }
}
