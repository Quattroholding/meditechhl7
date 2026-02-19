<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WhatsAppService
{
    protected string $apiVersion = 'v21.0';

    /**
     * Send a text message via WhatsApp Meta API
     */
    public function sendMessage(string $to, string $message): bool
    {
        $payload = $this->buildTextMessagePayload($to, $message);

        return $this->sendToMetaApi($payload);
    }

    /**
     * Send a document via WhatsApp Meta API
     *
     * @param  string  $to  Phone number (will be normalized)
     * @param  string  $documentUrl  Public URL to the document
     * @param  string|null  $filename  Optional filename
     * @param  string|null  $caption  Optional caption
     */
    public function sendDocument(string $to, string $documentUrl, ?string $filename = null, ?string $caption = null): bool
    {
        $payload = $this->buildDocumentMessagePayload($to, $documentUrl, $filename, $caption);

        return $this->sendToMetaApi($payload);
    }

    /**
     * Send a message with media (image, video, audio, document)
     */
    public function sendMedia(string $to, string $mediaUrl, string $mediaType = 'document', ?string $caption = null): bool
    {
        $payload = $this->buildMediaMessagePayload($to, $mediaUrl, $mediaType, $caption);

        return $this->sendToMetaApi($payload);
    }

    /**
     * Store a PDF temporarily and return a public URL
     * The file will be stored in public disk for WhatsApp access
     *
     * @param  string  $pdfContent  Binary PDF content
     * @param  string  $filename  Filename for the PDF
     * @return string|null Public URL to the PDF, or null on failure
     */
    public function storePdfTemporarily(string $pdfContent, string $filename): ?string
    {
        try {
            // Generate a unique path with timestamp for auto-cleanup
            $path = 'temp/whatsapp/'.date('Y-m-d').'/'.Str::uuid().'_'.$filename;

            // Store in public disk
            Storage::disk('public')->put($path, $pdfContent);

            // Generate public URL
            $url = Storage::disk('public')->url($path);

            // Make it an absolute URL if it's relative
            if (! str_starts_with($url, 'http')) {
                $url = url($url);
            }

            Log::info('PDF almacenado temporalmente para WhatsApp', [
                'path' => $path,
                'url' => $url,
                'filename' => $filename,
            ]);

            return $url;
        } catch (\Exception $e) {
            Log::error('Error al almacenar PDF temporalmente', [
                'filename' => $filename,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Clean up old temporary WhatsApp files (older than 24 hours)
     */
    public function cleanupOldTempFiles(): int
    {
        try {
            $deletedCount = 0;
            $directories = Storage::disk('public')->directories('temp/whatsapp');

            foreach ($directories as $directory) {
                $files = Storage::disk('public')->files($directory);

                foreach ($files as $file) {
                    // Check if file is older than 24 hours
                    $lastModified = Storage::disk('public')->lastModified($file);
                    if (now()->timestamp - $lastModified > 86400) {
                        Storage::disk('public')->delete($file);
                        $deletedCount++;
                    }
                }
            }

            if ($deletedCount > 0) {
                Log::info('Archivos temporales de WhatsApp limpiados', [
                    'deleted_count' => $deletedCount,
                ]);
            }

            return $deletedCount;
        } catch (\Exception $e) {
            Log::error('Error al limpiar archivos temporales de WhatsApp', [
                'error' => $e->getMessage(),
            ]);

            return 0;
        }
    }

    /**
     * Get normalized phone number from notifiable
     */
    public function getPhoneNumber($notifiable): ?string
    {
        Log::info('WhatsApp Service obtiene numero de telefono', [
            'app.env' => config('app.env'),
            'testing_mode' => config('services.meta.testing_mode'),
        ]);

        // If in testing mode, use specific testing phone
        if (config('app.env') === 'local' || config('services.meta.testing_mode')) {
            $testingPhone = config('services.meta.testing_phone');
            if ($testingPhone) {
                return $this->normalizePhoneNumber($testingPhone);
            }
        }

        // Check for whatsapp_phone first, then fall back to phone
        $phone = $notifiable->whatsapp_phone ?? $notifiable->phone ?? null;

        if (! $phone || $phone === '0' || trim($phone) === '') {
            return null;
        }

        return $this->normalizePhoneNumber($phone);
    }

    /**
     * Normalize phone number to international format
     * Handles various formats like: +507 6694-8409, (507) 66266088, 50769121653, etc.
     */
    public function normalizePhoneNumber(?string $phone): ?string
    {
        if (! $phone || $phone === '0' || trim($phone) === '') {
            return null;
        }

        // Remove all non-digit characters except + at the start
        $cleaned = preg_replace('/[^\d+]/', '', trim($phone));

        // If it's empty after cleaning, return null
        if (empty($cleaned) || $cleaned === '0') {
            return null;
        }

        // If already has + at the beginning, keep it
        if (str_starts_with($cleaned, '+')) {
            return $cleaned;
        }

        // If starts with 507 (Panama) but no +, add it
        if (str_starts_with($cleaned, '507') && strlen($cleaned) >= 11) {
            return '+'.$cleaned;
        }

        // If starts with 1 (USA/Canada) and has 11 digits, add +
        if (str_starts_with($cleaned, '1') && strlen($cleaned) === 11) {
            return '+'.$cleaned;
        }

        // If starts with 57 (Colombia) and has 12 digits, add +
        if (str_starts_with($cleaned, '57') && strlen($cleaned) >= 12) {
            return '+'.$cleaned;
        }

        // If starts with 593 (Ecuador) and has appropriate length, add +
        if (str_starts_with($cleaned, '593') && strlen($cleaned) >= 12) {
            return '+'.$cleaned;
        }

        // If starts with 506 (Costa Rica) and has 11 digits, add +
        if (str_starts_with($cleaned, '506') && strlen($cleaned) === 11) {
            return '+'.$cleaned;
        }

        // If it's 8 digits starting with 6, assume it's Panama without country code
        if (strlen($cleaned) === 8 && str_starts_with($cleaned, '6')) {
            return '+507'.$cleaned;
        }

        // If it's 7 digits, assume it's Panama without country code (landline format)
        if (strlen($cleaned) === 7) {
            return '+507'.$cleaned;
        }

        // Default: assume it's Panama if it's not already international format
        return '+507'.ltrim($cleaned, '0');
    }

    /**
     * Detect media type from URL
     */
    public function detectMediaType(string $url): string
    {
        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));

        return match ($extension) {
            'jpg', 'jpeg', 'png' => 'image',
            'mp4', 'avi', 'mov' => 'video',
            'mp3', 'ogg', 'amr' => 'audio',
            'pdf', 'doc', 'docx', 'xls', 'xlsx' => 'document',
            default => 'document',
        };
    }

    // ========================================
    // Protected Helper Methods
    // ========================================

    /**
     * Build text message payload for Meta API
     */
    protected function buildTextMessagePayload(string $to, string $message): array
    {
        // Remove + from phone number as Meta API expects it without
        $cleanPhone = ltrim($this->normalizePhoneNumber($to) ?? $to, '+');

        return [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $cleanPhone,
            'type' => 'text',
            'text' => [
                'preview_url' => false,
                'body' => $message,
            ],
        ];
    }

    /**
     * Build document message payload for Meta API
     */
    protected function buildDocumentMessagePayload(string $to, string $documentUrl, ?string $filename = null, ?string $caption = null): array
    {
        // Remove + from phone number as Meta API expects it without
        $cleanPhone = ltrim($this->normalizePhoneNumber($to) ?? $to, '+');

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $cleanPhone,
            'type' => 'document',
            'document' => [
                'link' => $documentUrl,
            ],
        ];

        if ($filename) {
            $payload['document']['filename'] = $filename;
        }

        if ($caption) {
            $payload['document']['caption'] = $caption;
        }

        return $payload;
    }

    /**
     * Build media message payload for Meta API
     */
    protected function buildMediaMessagePayload(string $to, string $mediaUrl, string $mediaType, ?string $caption = null): array
    {
        // Remove + from phone number as Meta API expects it without
        $cleanPhone = ltrim($this->normalizePhoneNumber($to) ?? $to, '+');

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $cleanPhone,
            'type' => $mediaType,
            $mediaType => [
                'link' => $mediaUrl,
            ],
        ];

        // Add caption if it's not a document
        if ($mediaType !== 'document' && $caption) {
            $payload[$mediaType]['caption'] = $caption;
        }

        return $payload;
    }

    /**
     * Send payload to Meta WhatsApp Cloud API
     */
    protected function sendToMetaApi(array $payload): bool
    {
        $phoneNumberId = config('services.meta.whatsapp_phone_number_id');
        $accessToken = config('services.meta.whatsapp_access_token');

        if (! $phoneNumberId || ! $accessToken) {
            Log::error('WhatsApp Meta API: Credenciales no configuradas', [
                'has_phone_id' => ! empty($phoneNumberId),
                'has_token' => ! empty($accessToken),
            ]);

            return false;
        }

        try {
            $response = Http::timeout(30)
                ->withToken($accessToken)
                ->post("https://graph.facebook.com/{$this->apiVersion}/{$phoneNumberId}/messages", $payload);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('WhatsApp Meta API: Mensaje enviado exitosamente', [
                    'to' => $payload['to'],
                    'type' => $payload['type'],
                    'message_id' => $data['messages'][0]['id'] ?? null,
                ]);

                return true;
            } else {
                Log::error('WhatsApp Meta API: Error HTTP', [
                    'to' => $payload['to'],
                    'type' => $payload['type'],
                    'status_code' => $response->status(),
                    'response_body' => $response->body(),
                    'error' => $response->json('error'),
                ]);

                return false;
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp Meta API: Excepcion', [
                'to' => $payload['to'] ?? null,
                'type' => $payload['type'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }
}
