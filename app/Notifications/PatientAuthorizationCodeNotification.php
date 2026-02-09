<?php

namespace App\Notifications;

use App\Models\AuthorizationCode;
use App\Models\Practitioner;
use App\Notifications\Concerns\ValidatesEmailChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PatientAuthorizationCodeNotification extends Notification implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel;

    public $tries = 3;

    public $backoff = [60, 300, 600];

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
        if ($notifiable->whatsapp_phone || $notifiable->phone) {
            $channels[] = \App\Channels\WhatsAppMetaChannel::class;
        }

        return $channels;
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
            ->salutation('Atentamente, Equipo de '.$clinicName);
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): string
    {
        $clinicName = config('app.name');
        $expiresAt = $this->authorizationCode->expires_at;

        $message = "🔐 *Código de Autorización de Acceso*\n\n";
        $message .= "Hola {$notifiable->name},\n\n";
        $message .= "El Dr. *{$this->practitioner->name}* ha solicitado acceso a su historial clínico completo.\n\n";
        $message .= "Para autorizar este acceso, proporcione el siguiente código de 4 dígitos al médico:\n\n";
        $message .= "🔑 *{$this->authorizationCode->code}*\n\n";
        $message .= "⚠️ Este código expira el {$expiresAt->format('d/m/Y')} a las {$expiresAt->format('H:i')} (válido por 1 hora)\n\n";
        $message .= "Una vez que el médico ingrese este código correctamente, tendrá acceso permanente a su historial clínico.\n\n";
        $message .= "Si usted no solicitó esto o no desea autorizar el acceso, simplemente ignore este mensaje.\n\n";
        $message .= "Atentamente,\nEquipo de {$clinicName}";

        return $message;
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
