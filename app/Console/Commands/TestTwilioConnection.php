<?php

namespace App\Console\Commands;

use App\Services\TwilioService;
use Illuminate\Console\Command;
use Twilio\Exceptions\RestException;

class TestTwilioConnection extends Command
{
    protected $signature = 'twilio:test-connection {phone}';

    protected $description = 'Test Twilio WhatsApp connection and send a simple message';

    public function handle(TwilioService $twilioService)
    {
        $phone = $this->argument('phone');

        $this->info('Testing Twilio WhatsApp Connection...');
        $this->info('================================');
        $this->newLine();

        // Display configuration
        $this->info('Configuration:');
        $this->line('  SID: '.config('services.twilio.sid'));
        $this->line('  Token: '.str_repeat('*', strlen(config('services.twilio.token')) - 4).substr(config('services.twilio.token'), -4));
        $this->line('  From: '.config('services.twilio.whatsapp_from'));
        $this->line('  To: whatsapp:'.$phone);
        $this->newLine();

        try {
            $message = "🧪 Test de conexión Twilio\n\nEste es un mensaje de prueba desde Meditech2.\n\nFecha: ".now()->format('d/m/Y H:i:s');

            $this->info('Sending test message...');

            $result = $twilioService->sendWhatsAppMessage($phone, $message);

            $this->newLine();
            $this->info('✅ Message sent successfully!');
            $this->newLine();
            $this->line('Message SID: '.$result->sid);
            $this->line('Status: '.$result->status);
            $this->line('To: '.$result->to);
            $this->line('From: '.$result->from);
            $this->line('Date created: '.$result->dateCreated->format('Y-m-d H:i:s'));

            if ($result->errorCode) {
                $this->error('Error Code: '.$result->errorCode);
                $this->error('Error Message: '.$result->errorMessage);
            }

            return 0;
        } catch (RestException $e) {
            $this->newLine();
            $this->error('❌ Twilio API Error:');
            $this->error('Code: '.$e->getCode());
            $this->error('Message: '.$e->getMessage());
            $this->newLine();

            if ($e->getCode() == 21408) {
                $this->warn('⚠️  WhatsApp Sandbox Not Configured');
                $this->newLine();
                $this->info('To use Twilio WhatsApp Sandbox, you need to:');
                $this->line('1. Go to: https://console.twilio.com/us1/develop/sms/try-it-out/whatsapp-learn');
                $this->line('2. Send this message to +1 415 523 8886:');
                $this->line('   join <your-sandbox-keyword>');
                $this->line('3. Once joined, try this command again');
            }

            return 1;
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ Unexpected Error:');
            $this->error($e->getMessage());
            $this->newLine();
            $this->line('Stack trace:');
            $this->line($e->getTraceAsString());

            return 1;
        }
    }
}
