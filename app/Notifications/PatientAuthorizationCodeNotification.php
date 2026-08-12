<?php

namespace App\Notifications;

use App\Models\AuthorizationCode;
use App\Models\Practitioner;
use App\Notifications\Concerns\ValidatesEmailChannel;
use App\Notifications\Concerns\WithEmailMetadata;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PatientAuthorizationCodeNotification extends Notification implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel, WithEmailMetadata;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    public $deleteWhenMissingModels = true;

    public function __construct(
        public AuthorizationCode $authorizationCode,
        public Practitioner $practitioner
    ) {
        $this->onQueue('emails');
    }

    public function via($notifiable)
    {
        $channels = ['database'];

        // Only add mail channel if email is valid and not reserved
        if ($this->isValidEmail($notifiable->email)) {
            $channels[] = 'mail';
        }

        // Add WhatsApp channel if user has WhatsApp phone number
        // Temporarily disabled while waiting for Meta template approval
        // if ($notifiable->whatsapp_phone || $notifiable->phone) {
        //     $channels[] = \App\Channels\WhatsAppMetaChannel::class;
        // }

        return $channels;
    }

    /**
     * Define metadatos personalizados para tracking del correo
     */
    protected function emailMetadata(): array
    {
        return [
            'Type' => 'patient-authorization-code',
            'Authorization-Code-ID' => $this->authorizationCode->id,
            'Patient-ID' => $this->authorizationCode->patient_id,
            'Patient-Name' => $this->authorizationCode->patient->full_name ?? 'N/A',
            'Practitioner-ID' => $this->practitioner->id,
            'Practitioner-Name' => $this->practitioner->name,
            'Code' => $this->authorizationCode->code,
            'Expires-At' => $this->authorizationCode->expires_at->format('Y-m-d H:i'),
        ];
    }

    public function toMail($notifiable)
    {
        $clinicName = config('app.name');
        $expiresAt = $this->authorizationCode->expires_at;

        return (new MailMessage)
            ->subject('Código de Autorización de Acceso a Historial Clínico - '.$clinicName)
            ->greeting('¡Hola '.$notifiable->name.'!')
            ->line('El Dr. **'.$this->practitioner->name.'** ha solicitado acceso a su historial clínico completo.')
            ->line('Para autorizar este acceso, proporcione el siguiente código de 4 dígitos al médico:')
            ->line('## **'.$this->authorizationCode->code.'**')
            ->line('⚠️ **Este código expira el '.$expiresAt->format('d/m/Y').' a las '.$expiresAt->format('H:i').'** (válido por 1 hora)')
            ->line('Una vez que el médico ingrese este código correctamente, tendrá acceso permanente a su historial clínico.')
            ->line('Si usted no solicitó esto o no desea autorizar el acceso, simplemente ignore este mensaje.')
            ->salutation('Atentamente, Equipo de '.$clinicName)
            ->withSymfonyMessage(function ($message) {
                $this->applyEmailMetadata($message);
            });
    }

    /**
     * Get the WhatsApp representation of the notification.
     * Uses WhatsApp UTILITY template for authorization code delivery.
     */
    public function toWhatsApp(object $notifiable): array
    {
        $clinicName = config('app.name');
        $expiresAt = $this->authorizationCode->expires_at;
        $expirationText = $expiresAt->format('d/m/Y').' a las '.$expiresAt->format('H:i');

        // Build components for utility template
        $components = [];

        // Body component with variables
        // {{1}} = Patient name
        // {{2}} = Practitioner name
        // {{3}} = Authorization code
        // {{4}} = Expiration date/time
        // {{5}} = Clinic name
        $components[] = [
            'type' => 'body',
            'parameters' => [
                ['type' => 'text', 'text' => $notifiable->name],
                ['type' => 'text', 'text' => $this->practitioner->name],
                ['type' => 'text', 'text' => $this->authorizationCode->code],
                ['type' => 'text', 'text' => $expirationText],
                ['type' => 'text', 'text' => $clinicName],
            ],
        ];

        return [
            'use_template' => true,
            'template_name' => 'authorization_code_delivery',
            'language_code' => 'es',
            'components' => $components,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            // Standard notification fields
            'title' => 'Código de Autorización de Acceso',
            'message' => 'El Dr. '.$this->practitioner->name.' solicita acceso a su historial clínico.',
            'steps' => [
                '🔑 Su código de autorización: **'.$this->authorizationCode->code.'**',
                '⏰ Válido hasta: '.$this->authorizationCode->expires_at->format('d/m/Y H:i'),
                '✅ Proporcione este código al médico para autorizar el acceso',
            ],
            'action' => null,
            'priority' => 'high',
            'icon' => 'fas fa-key',

            // Legacy/specific fields (for backwards compatibility)
            'type' => 'authorization_code',
            'authorization_code_id' => $this->authorizationCode->id,
            'practitioner_name' => $this->practitioner->name,
            'practitioner_id' => $this->practitioner->id,
            'code' => $this->authorizationCode->code,
            'expires_at' => $this->authorizationCode->expires_at->format('Y-m-d H:i:s'),
            'sent_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $errorMessage = $exception->getMessage();
        $context = [
            'authorization_code_id' => $this->authorizationCode->id,
            'patient_id' => $this->authorizationCode->patient_id,
            'practitioner_id' => $this->authorizationCode->practitioner_id,
            'error' => $errorMessage,
        ];

        // Check if it's an RFC 2606 reserved domain error
        if (str_contains($errorMessage, 'Recipient address reserved by RFC 2606') ||
            str_contains($errorMessage, 'code "501"')) {
            \Log::warning('Intento de envío a dirección reservada RFC 2606', array_merge($context, [
                'patient_email' => $this->authorizationCode->patient->email ?? 'N/A',
                'note' => 'El email del paciente usa un dominio reservado (example.com, test.com, etc.)',
            ]));

            return;
        }

        // Log other errors as errors
        \Log::error('Falló el envío de código de autorización', $context);
    }
}
