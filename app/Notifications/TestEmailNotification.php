<?php

namespace App\Notifications;

use App\Notifications\Concerns\WithEmailMetadata;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TestEmailNotification extends Notification
{
    use Queueable, WithEmailMetadata;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Define metadatos personalizados para tracking del correo de prueba
     */
    protected function emailMetadata(): array
    {
        return [
            'Type' => 'test-email',
            'Test-Category' => 'email-metadata-verification',
            'Test-ID' => 'TEST-'.now()->format('YmdHis'),
            'Environment' => config('app.env'),
            'App-Version' => '2.0.0',
            'Sent-From' => 'TestEmailNotification',
            'IP-Address' => request()->ip() ?? '127.0.0.1',
            'Timestamp' => now()->format('Y-m-d H:i:s'),
            'User-Agent' => request()->userAgent() ?? 'CLI',
            'Test-Data-1' => 'Valor de prueba 1',
            'Test-Data-2' => 'Valor de prueba 2',
            'Test-Number' => rand(1000, 9999),
            'Test-Boolean' => 'true',
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🧪 Correo de Prueba - Verificación de Metadata - '.now()->format('Y-m-d H:i:s'))
            ->greeting('¡Hola!')
            ->line('Este es un correo de prueba para verificar que los metadatos personalizados se estén enviando correctamente.')
            ->line('**Metadata incluida en este correo:**')
            ->line('✓ Type: test-email')
            ->line('✓ Test-Category: email-metadata-verification')
            ->line('✓ Test-ID: TEST-'.now()->format('YmdHis'))
            ->line('✓ Environment: '.config('app.env'))
            ->line('✓ IP-Address: '.(request()->ip() ?? '127.0.0.1'))
            ->line('✓ Timestamp: '.now()->format('Y-m-d H:i:s'))
            ->line('✓ Y otros 7 campos de metadata adicionales...')
            ->line('Si ves estos datos en el Message Trace de Office 365, significa que el sistema de metadata está funcionando correctamente.')
            ->action('Ver Message Trace', url('/email/message-trace'))
            ->line('Busca este correo en el Message Trace y verifica que todos los headers X-* estén presentes.')
            ->salutation('Saludos, Sistema de Pruebas SAMI')
            ->withSymfonyMessage(function ($message) {
                $this->applyEmailMetadata($message);
            });
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'test_sent_at' => now(),
            'test_id' => 'TEST-'.now()->format('YmdHis'),
        ];
    }
}
