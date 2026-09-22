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
        // Format: meditech2_consultation_123_abc123
        return sprintf(
            'meditec_consultation_%d_%s',
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
     * Generate a JWT token for authenticated access (if using self-hosted Jitsi with JWT)
     */
    public function generateToken(string $roomName, array $userInfo): ?string
    {
        // Only generate token if app_id and app_secret are configured
        if (! $this->appId || ! $this->appSecret) {
            return null;
        }

        $isModerator = $userInfo['is_moderator'] ?? false;

        // For JaaS (8x8.vc), the payload structure is specific
        $payload = [
            'iss' => 'chat',
            'aud' => 'jitsi',
            'exp' => time() + 7200, // 2 hours
            'nbf' => time() - 10, // Not before: 10 seconds ago
            'sub' => $this->appId,
            'room' => $roomName, // Specific room name for this session
            'context' => [
                'user' => [
                    'name' => $userInfo['name'],
                    'email' => $userInfo['email'] ?? '',
                    'id' => (string) $userInfo['id'],
                    'moderator' => $isModerator ? 'true' : 'false',
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

        // For JaaS, we need to include the Key ID (kid) in the JWT header
        $headers = [];
        if ($this->keyId) {
            $headers = ['kid' => $this->keyId];
        }

        // Load the private key properly for RS256 signing
        // Check if it's a file path or the actual key content
        $privateKey = $this->appSecret;

        // If it looks like a PEM key in env, normalize newlines
        if (strpos($privateKey, 'BEGIN PRIVATE KEY') !== false) {
            // Replace \n with actual newlines if it's stored as string in env
            $privateKey = str_replace('\n', "\n", $privateKey);
            $privateKey = str_replace('"', '', $privateKey);
        }

        // Try to load from file if exists
        $keyPath = storage_path('app/private/jitsi_private_key.pk');
        if (file_exists($keyPath)) {
            $privateKey = file_get_contents($keyPath);
        }

        return JWT::encode($payload, $privateKey, 'RS256', null, $headers);
    }

    /**
     * Get Jitsi configuration for the frontend
     */
    public function getJitsiConfig(Appointment $appointment, array $userInfo): array
    {
        $roomName = $appointment->virtual_room_id ?? $this->generateRoomName($appointment);

        // Use public meet.jit.si for maximum compatibility and stability
        // 8x8.vc has issues with MediaDevices API in some configurations
        $domain = 'meet.jit.si';
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
                // IMPORTANTE: Deshabilitar membersOnly para permitir que cualquiera entre
                // Sin esto, la sala queda bloqueada si no hay moderador
                'membersOnly' => false,
                'enableLobbyChat' => false,
                'disableInviteFunctions' => true,
                // Desactivar P2P para mejor compatibilidad
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

        // Add JWT token ONLY if using 8x8.vc with complete credentials
        // For public meet.jit.si, JWT causes authentication issues and should not be used
        if ($domain === '8x8.vc' && $this->isAuthenticationEnabled()) {
            $token = $this->generateToken($roomName, $userInfo);
            if ($token) {
                $config['jwt'] = $token;
            }
        }

        return $config;
    }

    /**
     * Check if Jitsi is configured with JWT authentication
     */
    public function isAuthenticationEnabled(): bool
    {
        return ! empty($this->appId) && ! empty($this->appSecret);
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
