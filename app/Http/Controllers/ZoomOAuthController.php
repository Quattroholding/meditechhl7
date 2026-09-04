<?php

namespace App\Http\Controllers;

use App\Models\PractitionerZoomProfile;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ZoomOAuthController extends Controller
{
    protected Client $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client();
    }

    /**
     * Redirigir al usuario a Zoom para autorización
     */
    public function authorize()
    {
        $clientId = config('services.zoom.client_id');
        $redirectUri = route('zoom.callback');

        $zoomAuthUrl = 'https://zoom.us/oauth/authorize?' . http_build_query([
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'state' => session()->token(),
        ]);

        return redirect($zoomAuthUrl);
    }

    /**
     * Callback después de que Zoom autoriza
     * Nota: Zoom puede enviar solicitudes de validación de webhook a esta URL
     * Respondemos igual que el webhook para pasar la validación de Zoom
     */
    public function callback(Request $request)
    {
        // Zoom envía validación de webhook a TODAS las URLs registradas
        // Responder como lo hace el webhook endpoint
        if (($request->input('event') ?? null) === 'endpoint.url_validation') {
            $plainToken = $request->input('payload.plainToken') ?? null;
            if ($plainToken) {
                $webhookSecret = config('services.zoom.webhook_secret');
                $encryptedToken = hash_hmac('sha256', $plainToken, $webhookSecret);

                Log::info('Zoom webhook URL validation received on OAuth callback', [
                    'plainToken' => substr($plainToken, 0, 10).'...',
                ]);

                return response()->json([
                    'plainToken' => $plainToken,
                    'encryptedToken' => $encryptedToken,
                ]);
            }
        }

        // Si no es validación de webhook, procesar OAuth
        // Validar state token
        if ($request->state !== session()->token()) {
            Log::warning('Zoom OAuth state mismatch', [
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()->with('error', 'Error de seguridad en OAuth. Intenta de nuevo.');
        }

        $code = $request->code;

        if (!$code) {
            return redirect()->back()->with('error', 'No se recibió código de Zoom.');
        }

        try {
            // Obtener access token
            $response = $this->httpClient->post('https://zoom.us/oauth/token', [
                'auth' => [
                    config('services.zoom.client_id'),
                    config('services.zoom.client_secret'),
                ],
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => route('zoom.callback'),
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if (!isset($data['access_token'])) {
                throw new \Exception('No access token received');
            }

            // Obtener información del usuario de Zoom
            $userResponse = $this->httpClient->get('https://zoom.us/v2/users/me', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $data['access_token'],
                ],
            ]);

            $userData = json_decode($userResponse->getBody(), true);

            // Obtener practitioner del usuario autenticado
            $practitioner = Auth::user()->practitioner;

            if (!$practitioner) {
                return redirect()->back()->with('error', 'No se encontró perfil de médico.');
            }

            // Guardar o actualizar zoom profile
            $zoomProfile = PractitionerZoomProfile::updateOrCreate(
                ['practitioner_id' => $practitioner->id],
                [
                    'zoom_user_id' => $userData['id'],
                    'zoom_email' => $userData['email'],
                    'access_token' => $data['access_token'],
                    'refresh_token' => $data['refresh_token'] ?? null,
                    'token_expires_at' => now()->addSeconds($data['expires_in'] ?? 3600),
                    'verified_at' => now(),
                ]
            );

            Log::info('Zoom profile connected', [
                'practitioner_id' => $practitioner->id,
                'zoom_user_id' => $userData['id'],
            ]);

            return redirect()->route('practitioner.settings.zoom')->with('success', 'Cuenta de Zoom conectada correctamente.');
        } catch (\Exception $e) {
            Log::error('Zoom OAuth error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()->with('error', 'Error al conectar con Zoom: ' . $e->getMessage());
        }
    }

    /**
     * Desconectar cuenta de Zoom
     */
    public function disconnect()
    {
        $practitioner = Auth::user()->practitioner;

        if ($practitioner && $practitioner->zoomProfile) {
            $practitioner->zoomProfile()->delete();

            Log::info('Zoom profile disconnected', [
                'practitioner_id' => $practitioner->id,
            ]);

            return redirect()->route('practitioner.settings.zoom')->with('success', 'Cuenta de Zoom desconectada.');
        }

        return redirect()->back()->with('error', 'No hay cuenta de Zoom conectada.');
    }
}
