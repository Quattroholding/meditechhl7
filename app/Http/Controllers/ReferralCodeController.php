<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

        // Generate QR code as SVG for better quality in PDF
        $qrCode = base64_encode(QrCode::format('svg')
            ->size(200)
            ->errorCorrection('H')
            ->generate($shareUrl));

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
