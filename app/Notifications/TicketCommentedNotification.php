<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Notifications\Concerns\ValidatesEmailChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCommentedNotification extends Notification implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    public function __construct(
        public Ticket $ticket,
        public TicketComment $comment
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

        return $channels;
    }

    public function toMail($notifiable)
    {
        $clinicName = $this->ticket->client->name ?? config('app.name');

        $mailMessage = (new MailMessage)
            ->subject('Nuevo Comentario en Ticket '.$this->ticket->identifier.' - '.$clinicName)
            ->greeting('¡Hola '.$notifiable->full_name.'!')
            ->line('Se ha agregado un nuevo comentario en el ticket de soporte.')
            ->line('**Código:** '.$this->ticket->identifier)
            ->line('**Título:** '.$this->ticket->title)
            ->line('**Comentario de:** '.$this->comment->user->full_name)
            ->line('**Comentario:**')
            ->line($this->comment->comment);

        if ($this->comment->marks_as_resolved) {
            $mailMessage->line('🎉 **Este comentario marca el ticket como resuelto.**');
        }

        $mailMessage->action('Ver Ticket', route('tickets.index'))
            ->line('Gracias por usar '.$clinicName);

        return $mailMessage;
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
            'title' => 'Nuevo Comentario: '.$this->ticket->identifier,
            'message' => $this->comment->user->full_name.' comentó en el ticket',
            'steps' => array_filter([
                '🎫 Código: '.$this->ticket->identifier,
                '💬 Comentario de: '.$this->comment->user->full_name,
                $this->comment->marks_as_resolved ? '✅ Marca como resuelto' : null,
            ]),
            'action' => [
                'text' => 'Ver Ticket',
                'url' => route('tickets.index'),
            ],
            'priority' => $this->comment->marks_as_resolved ? 'high' : 'normal',
            'icon' => 'fas fa-comment',

            // Legacy/specific fields
            'type' => 'ticket_commented',
            'ticket_id' => $this->ticket->id,
            'ticket_identifier' => $this->ticket->identifier,
            'ticket_title' => $this->ticket->title,
            'comment_id' => $this->comment->id,
            'comment_text' => $this->comment->comment,
            'commented_by_user_id' => $this->comment->user_id,
            'commented_by_user_name' => $this->comment->user->full_name,
            'is_internal' => $this->comment->is_internal,
            'marks_as_resolved' => $this->comment->marks_as_resolved,
            'created_at' => $this->comment->created_at,
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
            'ticket_id' => $this->ticket->id,
            'ticket_identifier' => $this->ticket->identifier,
            'comment_id' => $this->comment->id,
            'user_id' => $this->comment->user_id,
            'error' => $errorMessage,
        ];

        // Check if it's an RFC 2606 reserved domain error
        if (str_contains($errorMessage, 'Recipient address reserved by RFC 2606') ||
            str_contains($errorMessage, 'code "501"')) {
            \Log::warning('Intento de envío a dirección reservada RFC 2606', array_merge($context, [
                'note' => 'El email usa un dominio reservado (example.com, test.com, etc.)',
            ]));

            return;
        }

        // Log other errors as errors
        \Log::error('Falló el envío de notificación de comentario en ticket', $context);
    }
}
