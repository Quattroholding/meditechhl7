<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientPreference;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DropboxOAuthController extends Controller
{
    /**
     * Redirect user to Dropbox OAuth authorization page.
     */
    public function redirect(Request $request): RedirectResponse
    {
        $client = auth()->user()->getCurrentClient();

        if (! $client) {
            return redirect()
                ->route('setting.external_storage')
                ->with('error', 'No se ha seleccionado un cliente');
        }

        // Store client_id in session to retrieve after callback
        session(['dropbox_oauth_client_id' => $client->id]);

        $params = http_build_query([
            'client_id' => config('services.dropbox.app_key'),
            'redirect_uri' => route('admin.dropbox.callback'),
            'response_type' => 'code',
            'token_access_type' => 'offline', // Request refresh token
        ]);

        return redirect("https://www.dropbox.com/oauth2/authorize?{$params}");
    }

    /**
     * Handle OAuth callback from Dropbox.
     */
    public function callback(Request $request): RedirectResponse
    {
        if ($request->has('error')) {
            Log::error('Dropbox OAuth error', [
                'error' => $request->error,
                'error_description' => $request->error_description,
            ]);

            return redirect()
                ->route('setting.external_storage')
                ->with('error', 'Error al conectar con Dropbox: '.$request->error_description);
        }

        if (! $request->has('code')) {
            return redirect()
                ->route('setting.external_storage')
                ->with('error', 'No se recibió código de autorización de Dropbox');
        }

        $clientId = session('dropbox_oauth_client_id');

        if (! $clientId) {
            return redirect()
                ->route('setting.external_storage')
                ->with('error', 'Sesión expirada. Por favor intente nuevamente');
        }

        try {
            // Exchange authorization code for access token and refresh token
            $response = Http::asForm()->post('https://api.dropboxapi.com/oauth2/token', [
                'code' => $request->code,
                'grant_type' => 'authorization_code',
                'client_id' => config('services.dropbox.app_key'),
                'client_secret' => config('services.dropbox.app_secret'),
                'redirect_uri' => route('admin.dropbox.callback'),
            ]);

            if (! $response->successful()) {
                throw new \Exception('Failed to exchange authorization code: '.$response->body());
            }

            $data = $response->json();

            // Store tokens in ClientPreference
            $config = [
                'enabled' => true,
                'provider' => 'dropbox',
                'access_token' => Crypt::encryptString($data['access_token']),
                'refresh_token' => Crypt::encryptString($data['refresh_token']),
                'expires_at' => now()->addSeconds($data['expires_in'])->toDateTimeString(),
                'account_id' => $data['account_id'] ?? null,
                'token_type' => $data['token_type'] ?? 'bearer',
            ];

            ClientPreference::setExternalStorageConfig($clientId, $config);

            Log::info('Dropbox OAuth successful', [
                'client_id' => $clientId,
                'account_id' => $data['account_id'] ?? null,
            ]);

            session()->forget('dropbox_oauth_client_id');

            return redirect()
                ->route('setting.external_storage')
                ->with('success', 'Dropbox conectado exitosamente. Los tokens se renovarán automáticamente.');
        } catch (\Exception $e) {
            Log::error('Error in Dropbox OAuth callback', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('setting.external_storage')
                ->with('error', 'Error al completar la autenticación con Dropbox: '.$e->getMessage());
        }
    }

    /**
     * Disconnect Dropbox integration.
     */
    public function disconnect(Request $request): RedirectResponse
    {
        $client = auth()->user()->getCurrentClient();

        if (! $client) {
            return redirect()
                ->route('setting.external_storage')
                ->with('error', 'No se ha seleccionado un cliente');
        }

        try {
            $config = [
                'enabled' => false,
                'provider' => 'dropbox',
            ];

            ClientPreference::setExternalStorageConfig($client->id, $config);

            return redirect()
                ->route('setting.external_storage')
                ->with('success', 'Dropbox desconectado exitosamente');
        } catch (\Exception $e) {
            return redirect()
                ->route('setting.external_storage')
                ->with('error', 'Error al desconectar Dropbox: '.$e->getMessage());
        }
    }
}
