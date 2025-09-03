<?php

namespace App\Http\Controllers;

use App\Models\ApiToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ApiTokenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tokens = ApiToken::orderBy('created_at', 'desc')->paginate(15);

        return view('api-tokens.index', compact('tokens'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('api-tokens.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = $this->validateTokenRequest($request);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Process IPs
            $allowedIps = $this->processIpList($request->allowed_ips);

            // Process scopes
            $scopes = $this->processScopes($request->scopes);

            // Generate token
            $token = ApiToken::generateToken();

            // Create the token
            $apiToken = ApiToken::create([
                'name' => $request->name,
                'token' => $token,
                'allowed_ips' => $allowedIps,
                'scopes' => $scopes,
                'expires_at' => $request->expires_at,
                'description' => $request->description,
                'created_by' => Auth::user()->name,
                'active' => true,
            ]);

            return redirect()->route('api-tokens.show', $apiToken)
                ->with('success', 'Token API creado exitosamente')
                ->with('new_token', $token);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withError('Error creando el token: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ApiToken $apiToken)
    {
        return view('api-tokens.show', compact('apiToken'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ApiToken $apiToken)
    {
        return view('api-tokens.edit', compact('apiToken'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ApiToken $apiToken)
    {
        $validator = $this->validateTokenRequest($request, $apiToken->id);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Process IPs
            $allowedIps = $this->processIpList($request->allowed_ips);

            // Process scopes
            $scopes = $this->processScopes($request->scopes);

            // Update the token (without changing the actual token string)
            $apiToken->update([
                'name' => $request->name,
                'allowed_ips' => $allowedIps,
                'scopes' => $scopes,
                'expires_at' => $request->expires_at,
                'description' => $request->description,
                'active' => $request->has('active'),
            ]);

            return redirect()->route('api-tokens.show', $apiToken)
                ->with('success', 'Token API actualizado exitosamente');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withError('Error actualizando el token: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApiToken $apiToken)
    {
        try {
            $apiToken->delete();

            return redirect()->route('api-tokens.index')
                ->with('success', 'Token API eliminado exitosamente');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withError('Error eliminando el token: '.$e->getMessage());
        }
    }

    /**
     * Toggle token active status
     */
    public function toggle(ApiToken $apiToken)
    {
        $apiToken->update(['active' => ! $apiToken->active]);

        $status = $apiToken->active ? 'activado' : 'desactivado';

        return redirect()->back()
            ->with('success', "Token {$status} exitosamente");
    }

    /**
     * Regenerate token
     */
    public function regenerate(ApiToken $apiToken)
    {
        $newToken = ApiToken::generateToken();
        $apiToken->update(['token' => $newToken]);

        return redirect()->route('api-tokens.show', $apiToken)
            ->with('success', 'Token regenerado exitosamente')
            ->with('new_token', $newToken);
    }

    /**
     * Validate token request
     */
    private function validateTokenRequest(Request $request, $tokenId = null)
    {
        return Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('api_tokens', 'name')->ignore($tokenId),
            ],
            'allowed_ips' => 'required|string',
            'scopes' => 'required|string',
            'expires_at' => 'nullable|date|after:now',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'El nombre del token es obligatorio',
            'name.unique' => 'Ya existe un token con este nombre',
            'allowed_ips.required' => 'Debe especificar al menos una IP permitida',
            'scopes.required' => 'Debe especificar al menos un scope',
            'expires_at.after' => 'La fecha de expiración debe ser futura',
        ]);
    }

    /**
     * Process IP list from string
     */
    private function processIpList(string $ipString): array
    {
        $ips = array_map('trim', explode(',', $ipString));

        foreach ($ips as $ip) {
            if ($ip !== '*' && ! $this->isValidIpOrCidr($ip)) {
                throw new \InvalidArgumentException("IP inválida: {$ip}");
            }
        }

        return $ips;
    }

    /**
     * Process scopes from string
     */
    private function processScopes(string $scopeString): array
    {
        return array_map('trim', explode(',', $scopeString));
    }

    /**
     * Validate IP or CIDR
     */
    private function isValidIpOrCidr(string $ip): bool
    {
        if (strpos($ip, '/') !== false) {
            [$subnet, $bits] = explode('/', $ip);

            return filter_var($subnet, FILTER_VALIDATE_IP) &&
                   is_numeric($bits) &&
                   $bits >= 0 &&
                   $bits <= 32;
        }

        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }
}
