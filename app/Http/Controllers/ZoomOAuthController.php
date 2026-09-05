<?php

namespace App\Http\Controllers;

use App\Models\PractitionerZoomProfile;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ZoomOAuthController extends Controller
{
    protected Client $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client;
    }

    /**
     * Redirigir al usuario a Zoom para autorización
     */
    public function authorize()
    {
        $clientId = config('services.zoom.client_id');
        $redirectUri = route('zoom.callback');
        $stateToken = session()->token();

        $params = [
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => 'meeting:write:meeting user:read:user',
            'state' => $stateToken,
        ];

        $zoomAuthUrl = 'https://zoom.us/oauth/authorize?'.http_build_query($params);

        // Log detallado de los parámetros que se están armando
        Log::info('=== ZOOM OAUTH AUTHORIZE STEP 1 ===');
        Log::info('Parámetros individuales que se envían a Zoom:', [
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => $params['scope'],
            'state' => substr($stateToken, 0, 10).'...',
        ]);
        Log::info('URL COMPLETA generada:', [
            'authorization_url' => $zoomAuthUrl,
            'url_length' => strlen($zoomAuthUrl),
        ]);
        Log::info('Usuario autenticado:', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'user_name' => Auth::user()->name,
        ]);
        Log::info('Configuración de la aplicación:', [
            'app_url' => config('app.url'),
            'app_env' => config('app.env'),
            'zoom_api_base_url' => config('services.zoom.api_base_url'),
        ]);
        Log::info('=== Redirigiendo a Zoom... ===');

        return redirect($zoomAuthUrl);
    }

    /**
     * Callback después de que Zoom autoriza
     * Nota: Zoom puede enviar solicitudes de validación de webhook a esta URL
     * Respondemos igual que el webhook para pasar la validación de Zoom
     */
    public function callback(Request $request)
    {
        Log::info('=== ZOOM OAUTH CALLBACK STEP 2 ===');
        Log::info('Datos recibidos del callback de Zoom:', $request->all());
        Log::info('Headers de la request:', $request->headers->all());

        // Zoom envía validación de webhook a TODAS las URLs registradas
        // Responder como lo hace el webhook endpoint
        if (($request->input('event') ?? null) === 'endpoint.url_validation') {
            $plainToken = $request->input('payload.plainToken') ?? null;
            if ($plainToken) {
                $webhookSecret = config('services.zoom.webhook_secret');
                $encryptedToken = hash_hmac('sha256', $plainToken, $webhookSecret);

                Log::info('=== Zoom webhook URL validation recibida en OAuth callback ===');
                Log::info('Webhook validation details:', [
                    'plainToken' => substr($plainToken, 0, 10).'...',
                    'encryptedToken' => substr($encryptedToken, 0, 10).'...',
                ]);

                return response()->json([
                    'plainToken' => $plainToken,
                    'encryptedToken' => $encryptedToken,
                ]);
            }
        }

        // Si no es validación de webhook, procesar OAuth
        Log::info('Validando state token (CSRF protection):', [
            'session_state' => substr(session()->token(), 0, 10).'...',
            'request_state' => substr($request->state ?? '', 0, 10).'...',
            'states_match' => $request->state === session()->token(),
        ]);

        if ($request->state !== session()->token()) {
            Log::warning('=== ERROR: State token mismatch (CSRF attack?) ===', [
                'user_id' => Auth::id(),
                'session_state' => substr(session()->token(), 0, 10).'...',
                'request_state' => substr($request->state ?? '', 0, 10).'...',
            ]);

            return redirect()->back()->with('error', 'Error de seguridad en OAuth. Intenta de nuevo.');
        }

        $code = $request->code;

        Log::info('=== Código de autorización recibido ===', [
            'code_received' => ! empty($code),
            'code_preview' => $code ? substr($code, 0, 10).'...' : 'NO RECIBIDO',
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
        ]);

        if (! $code) {
            Log::warning('=== ERROR: No se recibió código de Zoom ===', [
                'request_data' => $request->all(),
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()->with('error', 'No se recibió código de Zoom.');
        }

        try {
            // Obtener access token
            $clientId = config('services.zoom.client_id');
            $clientSecret = config('services.zoom.client_secret');
            $redirectUri = route('zoom.callback');

            Log::info('=== ZOOM OAUTH STEP 3: Solicitando access token ===');
            Log::info('Parámetros que se envían a zoom.us/oauth/token:', [
                'client_id' => $clientId,
                'client_secret' => substr($clientSecret, 0, 5).'... (ocultado)',
                'grant_type' => 'authorization_code',
                'code' => substr($code, 0, 10).'... (ocultado)',
                'redirect_uri' => $redirectUri,
            ]);

            $response = $this->httpClient->post('https://zoom.us/oauth/token', [
                'auth' => [$clientId, $clientSecret],
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => $redirectUri,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            Log::info('=== Respuesta de Zoom recibida ===');
            Log::info('Token response details:', [
                'status_code' => $response->getStatusCode(),
                'has_access_token' => isset($data['access_token']),
                'access_token_preview' => isset($data['access_token']) ? substr($data['access_token'], 0, 10).'...' : 'NO PRESENTE',
                'token_type' => $data['token_type'] ?? 'NO PRESENTE',
                'expires_in' => $data['expires_in'] ?? 'NO PRESENTE',
                'has_refresh_token' => isset($data['refresh_token']),
                'refresh_token_preview' => isset($data['refresh_token']) ? substr($data['refresh_token'], 0, 10).'...' : 'NO PRESENTE',
                'user_id' => Auth::id(),
            ]);

            if (! isset($data['access_token'])) {
                Log::error('=== ERROR: No se recibió access_token de Zoom ===', [
                    'response_status' => $response->getStatusCode(),
                    'response_body' => json_encode($data),
                    'user_id' => Auth::id(),
                ]);

                throw new \Exception('No access token received from Zoom');
            }

            // Obtener información del usuario de Zoom
            Log::info('=== ZOOM OAUTH STEP 4: Obteniendo info del usuario ===');
            Log::info('Llamando a GET /v2/users/me con token:', [
                'access_token_preview' => substr($data['access_token'], 0, 10).'...',
                'user_id' => Auth::id(),
            ]);

            $userResponse = $this->httpClient->get('https://zoom.us/v2/users/me', [
                'headers' => [
                    'Authorization' => 'Bearer '.$data['access_token'],
                ],
            ]);

            $userData = json_decode($userResponse->getBody(), true);

            Log::info('=== Información del usuario Zoom recibida ===');
            Log::info('Zoom user details:', [
                'zoom_user_id' => $userData['id'] ?? null,
                'zoom_email' => $userData['email'] ?? null,
                'zoom_user_type' => $userData['user_type'] ?? null,
                'zoom_first_name' => $userData['first_name'] ?? null,
                'zoom_last_name' => $userData['last_name'] ?? null,
                'zoom_pic_url' => $userData['pic_url'] ?? null,
                'zoom_timezone' => $userData['timezone'] ?? null,
                'app_user_id' => Auth::id(),
            ]);

            // Obtener practitioner del usuario autenticado
            Log::info('=== ZOOM OAUTH STEP 5: Buscando practitioner ===');
            $practitioner = Auth::user()->practitioner;

            if (! $practitioner) {
                Log::error('=== ERROR: Practitioner no encontrado ===', [
                    'user_id' => Auth::id(),
                    'user_email' => Auth::user()->email,
                    'user_model' => Auth::user()::class,
                ]);

                return redirect()->back()->with('error', 'No se encontró perfil de médico.');
            }

            Log::info('Practitioner encontrado:', [
                'practitioner_id' => $practitioner->id,
                'practitioner_name' => $practitioner->name ?? $practitioner->first_name.' '.$practitioner->last_name,
                'app_user_id' => Auth::id(),
            ]);

            // Guardar o actualizar zoom profile
            Log::info('=== ZOOM OAUTH STEP 6: Guardando/actualizando zoom profile ===');
            Log::info('Datos que se guardarán en practitioner_zoom_profiles:', [
                'practitioner_id' => $practitioner->id,
                'zoom_user_id' => $userData['id'],
                'zoom_email' => $userData['email'],
                'access_token' => substr($data['access_token'], 0, 10).'... (ocultado)',
                'refresh_token' => isset($data['refresh_token']) ? substr($data['refresh_token'], 0, 10).'... (ocultado)' : 'null',
                'expires_in' => $data['expires_in'].' segundos',
                'token_expires_at' => now()->addSeconds($data['expires_in'] ?? 3600)->format('Y-m-d H:i:s'),
            ]);

            PractitionerZoomProfile::updateOrCreate(
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

            Log::info('=== ✅ Zoom profile conectado exitosamente ===', [
                'practitioner_id' => $practitioner->id,
                'zoom_user_id' => $userData['id'],
                'zoom_email' => $userData['email'],
                'app_user_id' => Auth::id(),
                'connection_time' => now()->format('Y-m-d H:i:s'),
            ]);

            return redirect()->route('practitioner.settings.zoom')->with('success', 'Cuenta de Zoom conectada correctamente.');
        } catch (ClientException $e) {
            $errorBody = $e->getResponse()->getBody()->getContents();
            $errorData = json_decode($errorBody, true);

            Log::error('=== ❌ ERROR: Zoom API ClientException (4xx) ===', [
                'status_code' => $e->getResponse()->getStatusCode(),
                'error' => $errorData['reason'] ?? $errorData['message'] ?? 'Unknown error',
                'error_description' => $errorData['error_description'] ?? null,
                'error_code' => $errorData['error'] ?? null,
                'full_response' => $errorBody,
                'request_url' => $e->getRequest()->getUri(),
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()->with('error', 'Error de Zoom: '.($errorData['reason'] ?? $errorData['error_description'] ?? 'Error desconocido'));
        } catch (ServerException $e) {
            $errorBody = $e->getResponse()->getBody()->getContents();

            Log::error('=== ❌ ERROR: Zoom API ServerException (5xx) ===', [
                'status_code' => $e->getResponse()->getStatusCode(),
                'error_body' => $errorBody,
                'request_url' => $e->getRequest()->getUri(),
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()->with('error', 'Error en servidor de Zoom. Por favor intenta de nuevo.');
        } catch (\Exception $e) {
            Log::error('=== ❌ ERROR: Zoom OAuth error (excepción general) ===', [
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_type' => $e::class,
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Error al conectar con Zoom: '.$e->getMessage());
        }
    }

    /**
     * Desconectar cuenta de Zoom
     */
    public function disconnect()
    {
        Log::info('=== ZOOM DISCONNECT INITIATED ===');
        Log::info('Usuario intentando desconectar:', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
        ]);

        $practitioner = Auth::user()->practitioner;

        if (! $practitioner) {
            Log::warning('No practitioner found para disconnect', [
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()->with('error', 'No se encontró perfil de médico.');
        }

        if ($practitioner->zoomProfile) {
            Log::info('Zoom profile encontrado, procediendo con eliminación:', [
                'practitioner_id' => $practitioner->id,
                'zoom_user_id' => $practitioner->zoomProfile->zoom_user_id,
                'zoom_email' => $practitioner->zoomProfile->zoom_email,
            ]);

            $practitioner->zoomProfile()->delete();

            Log::info('=== ✅ Zoom profile desconectado exitosamente ===', [
                'practitioner_id' => $practitioner->id,
                'user_id' => Auth::id(),
                'disconnection_time' => now()->format('Y-m-d H:i:s'),
            ]);

            return redirect()->route('practitioner.settings.zoom')->with('success', 'Cuenta de Zoom desconectada.');
        }

        Log::warning('=== No hay Zoom profile para desconectar ===', [
            'practitioner_id' => $practitioner->id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('error', 'No hay cuenta de Zoom conectada.');
    }
}
