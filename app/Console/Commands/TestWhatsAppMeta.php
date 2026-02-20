<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestWhatsAppMeta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:test-meta {phone? : Phone number with country code}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test WhatsApp Meta API direct connection';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Testing WhatsApp Meta API Connection...');
        $this->newLine();

        // Check configuration
        $phoneNumberId = config('services.meta.whatsapp_phone_number_id');
        $accessToken = config('services.meta.whatsapp_access_token');

        if (! $phoneNumberId || ! $accessToken) {
            $this->error('❌ Meta credentials not configured!');
            $this->newLine();
            $this->warn('Please set these environment variables:');
            $this->line('  META_WHATSAPP_PHONE_NUMBER_ID');
            $this->line('  META_WHATSAPP_ACCESS_TOKEN');
            $this->newLine();

            return Command::FAILURE;
        }

        $this->line("✅ Phone Number ID: {$phoneNumberId}");
        $this->line('✅ Access Token: '.str_repeat('*', strlen($accessToken) - 8).substr($accessToken, -8));
        $this->newLine();

        // Get recipient phone
        $phone = $this->argument('phone') ?? $this->ask('Enter recipient phone number (with country code, e.g., +50766948409)');

        if (! $phone) {
            $this->error('❌ Phone number is required!');

            return Command::FAILURE;
        }

        // Normalize phone (remove + and spaces)
        $cleanPhone = ltrim(preg_replace('/[^\d+]/', '', $phone), '+');

        $this->info("📞 Sending test message to: {$cleanPhone}");
        $this->newLine();

        try {
            // Prepare message payload
            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $cleanPhone,
                'type' => 'text',
                'text' => [
                    'preview_url' => false,
                    'body' => "✅ Test message from Meditech2\n\nTime: ".now()->format('Y-m-d H:i:s')."\n\nYour WhatsApp Meta API is working correctly!",
                ],
            ];

            // Send message
            $response = Http::timeout(30)
                ->withToken($accessToken)
                ->post("https://graph.facebook.com/v21.0/{$phoneNumberId}/messages", $payload);

            if ($response->successful()) {
                $data = $response->json();
                $messageId = $data['messages'][0]['id'] ?? 'unknown';

                $this->info('✅ Message sent successfully!');
                $this->line("📨 Message ID: {$messageId}");
                $this->newLine();

                // Show full response
                if ($this->option('verbose')) {
                    $this->line('Full Response:');
                    $this->line(json_encode($data, JSON_PRETTY_PRINT));
                    $this->newLine();
                }

                $this->info('🎉 WhatsApp Meta API is working correctly!');

                return Command::SUCCESS;
            } else {
                $this->error('❌ Failed to send message');
                $this->newLine();

                $errorData = $response->json();
                $errorMessage = $errorData['error']['message'] ?? 'Unknown error';
                $errorCode = $errorData['error']['code'] ?? 'N/A';

                $this->error("Error Code: {$errorCode}");
                $this->error("Error Message: {$errorMessage}");
                $this->newLine();

                if ($this->option('verbose')) {
                    $this->line('Full Error Response:');
                    $this->line($response->body());
                    $this->newLine();
                }

                // Provide helpful hints
                if (str_contains($errorMessage, 'access token')) {
                    $this->warn('💡 Hint: Your access token might be expired or invalid');
                    $this->line('   Generate a new token from Meta Business Manager');
                } elseif (str_contains($errorMessage, 'phone number')) {
                    $this->warn('💡 Hint: Check that the phone number is in the correct format');
                    $this->line('   It should be without + sign: 50766948409');
                } elseif (str_contains($errorMessage, 'template')) {
                    $this->warn('💡 Hint: You might need to use a message template');
                    $this->line('   First time messages may require approved templates');
                }

                return Command::FAILURE;
            }

        } catch (\Exception $e) {
            $this->error('❌ Exception occurred: '.$e->getMessage());
            $this->newLine();

            if ($this->option('verbose')) {
                $this->line($e->getTraceAsString());
            }

            return Command::FAILURE;
        }
    }
}
