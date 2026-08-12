<?php

namespace App\Notifications;

use App\Notifications\Concerns\ValidatesEmailChannel;
use App\Notifications\Concerns\WithEmailMetadata;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountValidationNotification extends Notification implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel, WithEmailMetadata;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public bool $approved,
        public ?string $rejectionReason = null
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return array_filter([
            $this->getMailChannelIfValid($notifiable->email),
        ]);
    }

    /**
     * Define metadatos personalizados para tracking del correo
     */
    protected function emailMetadata(): array
    {
        return [
            'Type' => $this->approved ? 'account-validated-approved' : 'account-validated-rejected',
            'Approved' => $this->approved ? 'yes' : 'no',
            'Rejection-Reason' => $this->rejectionReason ?? 'N/A',
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        if ($this->approved) {
            return $this->approvedMail($notifiable);
        }

        return $this->rejectedMail($notifiable);
    }

    /**
     * Email for approved account
     */
    private function approvedMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Cuenta Aprobada - Bienvenido a SAMI Recetas')
            ->bcc('business@meditecpty.com')
            ->greeting('¡Felicitaciones '.$notifiable->first_name.'!')
            ->line('Tu cuenta de médico ha sido aprobada exitosamente.')
            ->line('Hemos revisado y validado tus documentos.')
            ->line('Ya puedes iniciar sesión en la aplicación móvil con tus credenciales.')
            ->action('Descargar la App', 'https://samirx.meditecpty.com/')
            ->line('Si tienes alguna pregunta, no dudes en contactarnos.')
            ->salutation('Saludos, El equipo de Soluciones Meditec')
            ->withSymfonyMessage(function ($message) {
                $this->applyEmailMetadata($message);
            });
    }

    /**
     * Email for rejected account
     */
    private function rejectedMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Registro No Aprobado - SAMI Recetas')
            ->greeting('Hola '.$notifiable->first_name.',')
            ->line('Lamentamos informarte que tu registro no ha sido aprobado.')
            ->error();

        if ($this->rejectionReason) {
            $mail->line('Motivo del rechazo:')
                ->line('**'.$this->rejectionReason.'**');
        }

        return $mail->line('Si consideras que esto es un error o deseas más información, por favor contáctanos.')
            ->line('Puedes volver a registrarte corrigiendo la información solicitada.')
            ->salutation('Saludos, El equipo de Soluciones Meditec')
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
            'approved' => $this->approved,
            'rejection_reason' => $this->rejectionReason,
            'message' => $this->approved
                ? 'Tu cuenta ha sido aprobada. Ya puedes iniciar sesión.'
                : 'Tu registro ha sido rechazado. '.$this->rejectionReason,
        ];
    }
}
