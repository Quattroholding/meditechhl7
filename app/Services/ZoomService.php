<?php

namespace App\Services;

use App\Models\Appointment;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ZoomService
{
    protected Client $httpClient;

    protected ?string $accountId;

    protected ?string $clientId;

    protected ?string $clientSecret;

    protected ?string $hostUserId;

    protected ?string $webhookSecret;

    protected string $baseUrl;

    protected string $dataCenter;

    protected bool $sandboxMode;

    public function __construct()
    {
        $this->accountId = config('services.zoom.account_id');
        $this->clientId = config('services.zoom.client_id');
        $this->clientSecret = config('services.zoom.client_secret');
        $this->hostUserId = config('services.zoom.host_user_id');
        $this->webhookSecret = config('services.zoom.webhook_secret');
        $this->baseUrl = config('services.zoom.api_base_url', 'https://zoom.us/v2');
        $this->dataCenter = config('services.zoom.data_center', 'US');

        // Sandbox mode: simular Zoom sin credenciales reales
        $this->sandboxMode = config('services.zoom.sandbox_mode', false)
                            || ! $this->isConfigured();

        $this->httpClient = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
        ]);
    }

    /**
     * Get OAuth 2.0 access token (cached for 1 hour)
     * In sandbox mode, returns a mock token
     */
    public function getAccessToken(): string
    {
        // Sandbox mode: return mock token
        if ($this->sandboxMode) {
            return Cache::remember('zoom_sandbox_token', 3600, function () {
                Log::info('Zoom Sandbox Mode: Using mock access token');

                return 'zoom_sandbox_token_'.hash('sha256', 'sandbox');
            });
        }

        $cacheKey = 'zoom_access_token_'.$this->accountId;

        return Cache::remember($cacheKey, 3600, function () {
            try {
                $response = $this->httpClient->post('/oauth/token', [
                    'auth' => [$this->clientId, $this->clientSecret],
                    'form_params' => [
                        'grant_type' => 'account_credentials',
                        'account_id' => $this->accountId,
                    ],
                ]);

                $data = json_decode($response->getBody(), true);

                return $data['access_token'] ?? '';
            } catch (\Exception $e) {
                Log::error('Zoom OAuth token error', ['error' => $e->getMessage()]);

                return '';
            }
        });
    }

    /**
     * Create a Zoom meeting for an appointment
     * In sandbox mode, simulates meeting creation
     */
    public function createMeeting(Appointment $appointment): array
    {
        try {
            $accessToken = $this->getAccessToken();

            if (! $accessToken) {
                throw new \Exception('Unable to obtain Zoom access token');
            }

            // SANDBOX MODE: Simulate meeting creation
            if ($this->sandboxMode) {
                $meetingId = (int) (time() % 1000000000000);
                $password = $this->generateMeetingPassword();
                $meetingData = [
                    'id' => $meetingId,
                    'uuid' => 'sandbox_uuid_'.hash('sha256', $meetingId),
                    'join_url' => "https://zoom.us/j/{$meetingId}",
                    'password' => $password,
                    'start_time' => $appointment->start->format('Y-m-d\TH:i:s'),
                ];

                $appointment->update([
                    'virtual_room_id' => (string) $meetingData['id'],
                    'virtual_room_url' => $meetingData['join_url'],
                    'virtual_session_metadata' => [
                        'zoom_uuid' => $meetingData['uuid'],
                        'meeting_password' => $password,
                        'created_at' => now()->toIso8601String(),
                        'provider' => 'zoom',
                        'sandbox_mode' => true,
                    ],
                ]);

                Log::info('Zoom meeting created (SANDBOX)', [
                    'appointment_id' => $appointment->id,
                    'meeting_id' => $meetingData['id'],
                    'sandbox_mode' => true,
                ]);

                return [
                    'meeting_id' => (string) $meetingData['id'],
                    'join_url' => $meetingData['join_url'],
                    'start_time' => $meetingData['start_time'],
                    'password' => $password,
                ];
            }

            // PRODUCTION MODE: Real Zoom API call
            $payload = [
                'topic' => 'Consulta Médica - '.$appointment->patient->name,
                'type' => 2,
                'start_time' => $appointment->start->format('Y-m-d\TH:i:s'),
                'duration' => $appointment->minutes_duration ?? 30,
                'timezone' => config('app.timezone', 'UTC'),
                'password' => $this->generateMeetingPassword(),
                'agenda' => 'Consulta Médica Virtual',
                'settings' => [
                    'host_video' => true,
                    'participant_video' => true,
                    'cn_meeting' => false,
                    'in_meeting' => false,
                    'join_before_host' => false,
                    'jbh_time' => 0,
                    'waiting_room' => false,
                    'mute_upon_entry' => false,
                    'auto_recording' => 'cloud',
                    'alternative_hosts' => '',
                    'close_registration' => false,
                    'show_share_button' => true,
                    'allow_multiple_devices' => false,
                    'registrants_confirmation_email' => false,
                    'meeting_authentication' => false,
                    'encryption_type' => 'enhanced',
                    'approved_or_denied_countries_restriction' => [
                        'enable' => false,
                    ],
                ],
            ];

            $response = $this->httpClient->post(
                "/users/{$this->hostUserId}/meetings",
                [
                    'headers' => [
                        'Authorization' => 'Bearer '.$accessToken,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => $payload,
                ]
            );

            $meetingData = json_decode($response->getBody(), true);

            $appointment->update([
                'virtual_room_id' => (string) $meetingData['id'],
                'virtual_room_url' => $meetingData['join_url'],
                'virtual_session_metadata' => [
                    'zoom_uuid' => $meetingData['uuid'] ?? null,
                    'meeting_password' => $meetingData['password'] ?? null,
                    'created_at' => now()->toIso8601String(),
                    'provider' => 'zoom',
                ],
            ]);

            Log::info('Zoom meeting created (PRODUCTION)', [
                'appointment_id' => $appointment->id,
                'meeting_id' => $meetingData['id'],
            ]);

            return [
                'meeting_id' => (string) $meetingData['id'],
                'join_url' => $meetingData['join_url'],
                'start_time' => $meetingData['start_time'],
                'password' => $meetingData['password'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Error creating Zoom meeting', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Get meeting details from Zoom API
     */
    public function getMeetingDetails(string $meetingId): array
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = $this->httpClient->get(
                "/meetings/{$meetingId}",
                [
                    'headers' => [
                        'Authorization' => 'Bearer '.$accessToken,
                    ],
                ]
            );

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Error getting Zoom meeting details', [
                'meeting_id' => $meetingId,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Delete a Zoom meeting
     */
    public function deleteMeeting(string $meetingId): bool
    {
        try {
            $accessToken = $this->getAccessToken();

            $this->httpClient->delete(
                "/meetings/{$meetingId}",
                [
                    'headers' => [
                        'Authorization' => 'Bearer '.$accessToken,
                    ],
                ]
            );

            Log::info('Zoom meeting deleted', ['meeting_id' => $meetingId]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error deleting Zoom meeting', [
                'meeting_id' => $meetingId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Generate SDK signature for Zoom Meeting SDK
     * Used to authenticate users joining via the SDK
     */
    public function generateSDKSignature(string $meetingNumber, int $role = 0): string
    {
        $timestamp = time() * 1000; // milliseconds
        $msg = base64_encode($this->clientId.intval($meetingNumber).$timestamp.$role);

        $signature = hash_hmac(
            'sha256',
            $msg,
            $this->clientSecret,
            true
        );

        $encodedSignature = base64_encode($signature);

        return "{$this->clientId}.{intval($meetingNumber)}.{$timestamp}.{$role}.{$encodedSignature}";
    }

    /**
     * Get Zoom Meeting SDK configuration for frontend
     */
    public function getZoomConfig(Appointment $appointment, array $userInfo): array
    {
        $meetingId = $appointment->virtual_room_id;

        if (! $meetingId) {
            throw new \Exception('Meeting ID not found for appointment');
        }

        $signature = $this->generateSDKSignature(
            $meetingId,
            $userInfo['is_moderator'] ? 1 : 0
        );

        return [
            'sdkKey' => $this->clientId,
            'signature' => $signature,
            'meetingNumber' => (int) $meetingId,
            'userName' => $userInfo['name'],
            'userEmail' => $userInfo['email'] ?? '',
            'passWord' => $appointment->virtual_session_metadata['meeting_password'] ?? '',
            'role' => $userInfo['is_moderator'] ? 1 : 0,
            'leaveUrl' => 'about:blank',
            'token' => '',
            'zak' => '',
        ];
    }

    /**
     * Get recordings for a meeting
     */
    public function getRecordings(string $meetingId): array
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = $this->httpClient->get(
                "/meetings/{$meetingId}/recordings",
                [
                    'headers' => [
                        'Authorization' => 'Bearer '.$accessToken,
                    ],
                ]
            );

            $data = json_decode($response->getBody(), true);

            return $data['recording_files'] ?? [];
        } catch (\Exception $e) {
            Log::error('Error getting Zoom recordings', [
                'meeting_id' => $meetingId,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Download a recording file
     */
    public function downloadRecording(string $downloadUrl, string $appointmentId): ?string
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = $this->httpClient->get($downloadUrl, [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                ],
                'stream' => true,
            ]);

            // Save to storage
            $filename = "recording_{$appointmentId}_".time().'.mp4';
            $path = "recordings/{$filename}";

            \Storage::disk('private')->put(
                $path,
                $response->getBody()
            );

            Log::info('Zoom recording downloaded', [
                'appointment_id' => $appointmentId,
                'path' => $path,
            ]);

            return $path;
        } catch (\Exception $e) {
            Log::error('Error downloading Zoom recording', [
                'download_url' => $downloadUrl,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Delete a recording
     */
    public function deleteRecording(string $recordingId): bool
    {
        try {
            $accessToken = $this->getAccessToken();

            $this->httpClient->delete(
                "/recordings/{$recordingId}",
                [
                    'headers' => [
                        'Authorization' => 'Bearer '.$accessToken,
                    ],
                ]
            );

            Log::info('Zoom recording deleted', ['recording_id' => $recordingId]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error deleting Zoom recording', [
                'recording_id' => $recordingId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Validate webhook signature
     */
    public function validateWebhook(string $body, string $signature): bool
    {
        if (! $this->webhookSecret) {
            Log::warning('Zoom webhook secret not configured');

            return false;
        }

        $timestamp = request()->header('x-zm-request-timestamp');
        $message = "{$timestamp}:{$body}";

        $expectedSignature = 'v0='.hash_hmac(
            'sha256',
            $message,
            $this->webhookSecret
        );

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Handle webhook events
     */
    public function handleWebhookEvent(array $event): void
    {
        try {
            $eventType = $event['event'] ?? null;

            match ($eventType) {
                'meeting.started' => $this->handleMeetingStarted($event),
                'meeting.ended' => $this->handleMeetingEnded($event),
                'recording.completed' => $this->handleRecordingCompleted($event),
                default => Log::info('Unhandled Zoom webhook event', ['type' => $eventType]),
            };
        } catch (\Exception $e) {
            Log::error('Error handling Zoom webhook event', [
                'error' => $e->getMessage(),
                'event' => $event,
            ]);
        }
    }

    /**
     * Handle meeting.started event
     */
    protected function handleMeetingStarted(array $event): void
    {
        $meetingId = $event['payload']['object']['id'] ?? null;

        if (! $meetingId) {
            return;
        }

        $appointment = Appointment::where('virtual_room_id', (string) $meetingId)->first();

        if ($appointment && ! $appointment->virtual_session_started_at) {
            $appointment->update([
                'virtual_session_started_at' => now(),
            ]);

            Log::info('Meeting started event processed', [
                'appointment_id' => $appointment->id,
                'meeting_id' => $meetingId,
            ]);
        }
    }

    /**
     * Handle meeting.ended event
     */
    protected function handleMeetingEnded(array $event): void
    {
        $meetingId = $event['payload']['object']['id'] ?? null;

        if (! $meetingId) {
            return;
        }

        $appointment = Appointment::where('virtual_room_id', (string) $meetingId)->first();

        if ($appointment && ! $appointment->virtual_session_ended_at) {
            $metadata = $appointment->virtual_session_metadata ?? [];
            $metadata['participant_count'] = $event['payload']['object']['participant_count'] ?? 0;

            $appointment->update([
                'virtual_session_ended_at' => now(),
                'virtual_session_metadata' => $metadata,
            ]);

            Log::info('Meeting ended event processed', [
                'appointment_id' => $appointment->id,
                'meeting_id' => $meetingId,
            ]);
        }
    }

    /**
     * Handle recording.completed event
     */
    protected function handleRecordingCompleted(array $event): void
    {
        $meetingId = $event['payload']['object']['id'] ?? null;

        if (! $meetingId) {
            return;
        }

        $appointment = Appointment::where('virtual_room_id', (string) $meetingId)->first();

        if (! $appointment) {
            return;
        }

        try {
            $recordings = $this->getRecordings((string) $meetingId);

            if (! empty($recordings)) {
                $recording = $recordings[0]; // Get first recording file
                $downloadUrl = $recording['download_url'] ?? null;

                if ($downloadUrl) {
                    $path = $this->downloadRecording($downloadUrl, $appointment->id);

                    if ($path) {
                        $metadata = $appointment->virtual_session_metadata ?? [];
                        $metadata['recording_id'] = $recording['id'] ?? null;
                        $metadata['recording_path'] = $path;
                        $metadata['recording_url'] = $recording['play_url'] ?? null;
                        $metadata['recording_size'] = $recording['file_size'] ?? null;

                        $appointment->update([
                            'virtual_session_metadata' => $metadata,
                        ]);

                        Log::info('Recording downloaded and stored', [
                            'appointment_id' => $appointment->id,
                            'recording_id' => $recording['id'],
                            'path' => $path,
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error processing recording completion', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate a random meeting password
     */
    protected function generateMeetingPassword(): string
    {
        return strtoupper(substr(sha1(random_bytes(16)), 0, 6));
    }

    /**
     * Check if Zoom is properly configured
     */
    public function isConfigured(): bool
    {
        return ! empty($this->accountId)
            && ! empty($this->clientId)
            && ! empty($this->clientSecret)
            && ! empty($this->hostUserId);
    }

    /**
     * Check if running in sandbox mode (testing without real credentials)
     */
    public function isSandboxMode(): bool
    {
        return $this->sandboxMode;
    }

    /**
     * Get current mode as string for logging/debugging
     */
    public function getMode(): string
    {
        return $this->sandboxMode ? 'SANDBOX' : 'PRODUCTION';
    }
}
