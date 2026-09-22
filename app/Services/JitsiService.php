<?php

namespace App\Services;

use App\Models\Appointment;
use Firebase\JWT\JWT;
use Illuminate\Support\Str;

class JitsiService
{
    protected string $domain;

    protected ?string $appId;

    protected ?string $appSecret;

    protected ?string $keyId;

    public function __construct()
    {
        $this->domain = config('services.jitsi.domain', 'meet.jit.si');
        $this->appId = config('services.jitsi.app_id');
        $this->appSecret = config('services.jitsi.app_secret');
        $this->keyId = config('services.jitsi.key_id');
    }

    /**
     * Create a Jitsi room for an appointment
     */
    public function createRoom(Appointment $appointment): array
    {
        $roomName = $this->generateRoomName($appointment);

        $appointment->update([
            'virtual_room_id' => $roomName,
            'virtual_room_url' => $this->getRoomUrl($roomName),
        ]);

        return [
            'room_name' => $roomName,
            'room_url' => $this->getRoomUrl($roomName),
        ];
    }

    /**
     * Generate a unique room name for the appointment
     */
    protected function generateRoomName(Appointment $appointment): string
    {
        // Format: meditech2_consultation_123_v2_random
        // The 'v2_' prefix helps avoid cached rooms that had membersOnly issues
        return sprintf(
            'meditech_c%d_v2_%s',
            $appointment->id,
            Str::random(8)
        );
    }

    /**
     * Get the full URL for a Jitsi room
     */
    public function getRoomUrl(string $roomName): string
    {
        return sprintf('https://%s/%s', $this->domain, $roomName);
    }

    /**
     * Generate a JWT token for authenticated access (JaaS with JWT)
     */
    public function generateToken(string $roomName, array $userInfo): ?string
    {
        // Only generate token if app_id and key are configured
        if (! $this->appId || ! $this->keyId) {
            return null;
        }

        $isModerator = $userInfo['is_moderator'] ?? false;

        \Log::info('Jitsi getJitsiConfig called', [
            'userInfo' => $userInfo,
            'isModerator' => $isModerator,
            'is_moderator_from_userInfo' => $userInfo['is_moderator'] ?? 'NOT_SET',
        ]);

        // For JaaS (8x8.vc), the payload structure is specific
        $payload = [
            'iss' => 'chat',
            'aud' => 'jitsi',
            'exp' => time() + 7200, // 2 hours
            'nbf' => time() - 10, // Not before: 10 seconds ago
            'sub' => $this->appId,
            'room' => strtolower($roomName), // Jitsi room names must be lowercase
            'context' => [
                'user' => [
                    'name' => $userInfo['name'],
                    'email' => $userInfo['email'] ?? '',
                    'id' => (string) $userInfo['id'],
                    'moderator' => $isModerator,  // Use boolean, not string
                    'avatar' => $userInfo['avatar'] ?? '',
                ],
                'features' => [
                    'recording' => $isModerator,
                    'livestreaming' => false,
                    'screen-sharing' => true,
                    'outbound-call' => false,
                ],
            ],
            'moderator' => $isModerator,
        ];

        // For JaaS, include the Key ID (kid) in the JWT header
        $headers = ['kid' => $this->keyId];

        // Load the private key from environment or file
        $privateKey = config('services.jitsi.app_secret');

        // If not in config, try loading from file (more reliable for multiline content)
        if (! $privateKey) {
            $keyPath = storage_path('app/private/local.meditecpty.com.pk');
            if (file_exists($keyPath)) {
                $privateKey = file_get_contents($keyPath);
            }
        }

        if (! $privateKey) {
            \Log::error('Jitsi private key not found in config or file');

            return null;
        }

        \Log::info('Jitsi JWT generation started', [
            'key_length' => strlen($privateKey),
            'key_id' => $this->keyId,
            'room_name' => $roomName,
            'app_id' => $this->appId,
        ]);

        try {
            $token = JWT::encode($payload, $privateKey, 'RS256', null, $headers);

            \Log::info('Jitsi JWT generated successfully', [
                'key_id' => $this->keyId,
                'room' => $roomName,
                'token_length' => strlen($token),
                'payload_exp' => $payload['exp'],
                'payload_nbf' => $payload['nbf'],
            ]);

            return $token;
        } catch (\Exception $e) {
            \Log::error('Failed to generate Jitsi JWT', [
                'error' => $e->getMessage(),
                'key_id' => $this->keyId,
                'room' => $roomName,
                'exception_class' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Get Jitsi configuration for the frontend
     */
    public function getJitsiConfig(Appointment $appointment, array $userInfo): array
    {
        $roomName = $appointment->virtual_room_id ?? $this->generateRoomName($appointment);

        // Use configured domain (8x8.vc with JWT if auth is enabled)
        // Only fall back to meet.jit.si if no authentication is configured
        $domain = $this->isAuthenticationEnabled() ? '8x8.vc' : 'meet.jit.si';
        $fullRoomName = $roomName;

        $config = [
            'domain' => $domain,
            'roomName' => $fullRoomName,
            'configOverwrite' => [
                'startWithAudioMuted' => true,
                'startWithVideoMuted' => true,
                'enableWelcomePage' => false,
                'prejoinPageEnabled' => false,
                'disableDeepLinking' => true,
                'enableClosePage' => false,
                'defaultLanguage' => 'es',
                // Deshabilitar petición inicial de permisos para evitar errores de navegador
                'disableInitialGUMRequest' => true,
                'startScreenSharing' => false,
                // Configuración simple para máxima compatibilidad
                'requireDisplayName' => false,
                'enableInsecureRoomNameWarning' => false,
                'enableNoAudioDetection' => false,
                'enableNoisyMicDetection' => false,
                // IMPORTANTE: Con JWT en 8x8.vc, la autenticación previene issues de lobby
                // Sin JWT (meet.jit.si), estos parámetros no se respetan a nivel servidor
                'membersOnly' => false,
                'enableLobbyChat' => false,
                'lobbyMode' => false,
                'disableInviteFunctions' => true,
                // Permitir entrada sin moderador en la sala (evita el error "moderators not yet arrived")
                'requireModeratorApproval' => false,
                'startVideoMuted' => true,
                'startAudioMuted' => true,
                // Configuraciones específicas para 8x8.vc
                'enforcedVideoConstraints' => [
                    'height' => [
                        'ideal' => 480,
                        'max' => 720,
                        'min' => 240,
                    ],
                ],
                // Desactivar P2P para mejor compatibilidad con navegadores
                'p2p' => [
                    'enabled' => false,
                ],
            ],
            'interfaceConfigOverwrite' => [
                'TOOLBAR_BUTTONS' => [
                    'microphone',
                    'camera',
                    'desktop',
                    'fullscreen',
                    'hangup',
                    'settings',
                ],
                'SHOW_POWERED_BY' => false,
                'APP_NAME' => 'Meditech2',
                'PROVIDER_NAME' => 'Meditech2',
            ],
            'userInfo' => [
                'displayName' => $userInfo['name'],
                'email' => $userInfo['email'] ?? null,
            ],
        ];

        // Add JWT token when using 8x8.vc with complete credentials
        // JWT authentication prevents members-only lobbying issues on JaaS
        // For public meet.jit.si, no JWT is needed or supported
        $isAuthEnabled = $this->isAuthenticationEnabled();
        \Log::info('Jitsi config authentication check', [
            'auth_enabled' => $isAuthEnabled,
            'app_id' => $this->appId,
            'key_id' => $this->keyId,
            'key_file_exists' => file_exists(storage_path('app/private/jitsi_private_key.pem')),
        ]);

        if ($isAuthEnabled) {
            $token = $this->generateToken($roomName, $userInfo);
            if ($token) {
                $config['jwt'] = $token;
                \Log::info('JWT token added to Jitsi config');
            } else {
                \Log::warning('JWT token generation returned null');
            }
        }

        return $config;
    }

    /**
     * Check if Jitsi is configured with JWT authentication
     */
    public function isAuthenticationEnabled(): bool
    {
        // Check if we have the required JWT credentials
        if (empty($this->appId) || empty($this->keyId)) {
            return false;
        }

        // Check if the private key is configured in the environment
        if (! empty(config('services.jitsi.app_secret'))) {
            return true;
        }

        // Fallback: check if the private key file exists
        $keyPath = storage_path('app/private/local.meditecpty.com.pk');

        return file_exists($keyPath);
    }

    /**
     * Get recording information (if using Jibri for recording)
     */
    public function getRecordingInfo(Appointment $appointment): array
    {
        // Note: Recording with Jitsi requires Jibri setup on self-hosted instance
        // For now, we'll store recording info in the appointment metadata

        $metadata = $appointment->virtual_session_metadata ?? [];

        return [
            'enabled' => $metadata['recording_enabled'] ?? false,
            'url' => $metadata['recording_url'] ?? null,
            'status' => $metadata['recording_status'] ?? 'not_started',
        ];
    }

    /**
     * Save recording information
     */
    public function saveRecordingInfo(Appointment $appointment, array $recordingInfo): void
    {
        $metadata = $appointment->virtual_session_metadata ?? [];
        $metadata['recording_url'] = $recordingInfo['url'] ?? null;
        $metadata['recording_status'] = $recordingInfo['status'] ?? 'completed';
        $metadata['recording_enabled'] = true;

        $appointment->update([
            'virtual_session_metadata' => $metadata,
        ]);
    }
}
