<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Notifications\Concerns\ValidatesEmailChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    public function __construct(
        public Ticket $ticket
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

        return (new MailMessage)
            ->subject('Nuevo Ticket de Soporte Creado - '.$clinicName)
            ->bcc('business@meditecpty.com')
            ->greeting('¡Hola '.$notifiable->full_name.'!')
            ->line('Se ha creado un nuevo ticket de soporte que requiere su atención.')
            ->line('**Código:** '.$this->ticket->identifier)
            ->line('**Título:** '.$this->ticket->title)
            ->line('**Prioridad:** '.$this->ticket->priority_label)
            ->line('**Categoría:** '.$this->ticket->category_label)
            ->line('**Creado por:** '.$this->ticket->user->full_name)
            ->line('**Descripción:**')
            ->line($this->ticket->description)
            ->action('Ver Ticket', route('tickets.index'))
            ->line('Gracias por usar '.$clinicName);
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
            'title' => 'Nuevo Ticket de Soporte: '.$this->ticket->identifier,
            'message' => 'Se ha creado un nuevo ticket de soporte por '.$this->ticket->user->full_name,
            'steps' => array_filter([
                '🎫 Código: '.$this->ticket->identifier,
                '📌 Prioridad: '.$this->ticket->priority_label,
                '📂 Categoría: '.$this->ticket->category_label,
                '👤 Creado por: '.$this->ticket->user->full_name,
            ]),
            'action' => [
                'text' => 'Ver Ticket',
                'url' => route('tickets.index'),
            ],
            'priority' => $this->ticket->priority === 'critical' ? 'high' : 'normal',
            'icon' => 'fas fa-ticket-alt',

            // Legacy/specific fields
            'type' => 'ticket_created',
            'ticket_id' => $this->ticket->id,
            'ticket_identifier' => $this->ticket->identifier,
            'ticket_title' => $this->ticket->title,
            'ticket_priority' => $this->ticket->priority,
            'ticket_category' => $this->ticket->category,
            'ticket_status' => $this->ticket->status,
            'created_by_user_id' => $this->ticket->user_id,
            'created_by_user_name' => $this->ticket->user->full_name,
            'created_at' => $this->ticket->created_at,
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
            'user_id' => $this->ticket->user_id,
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
        \Log::error('Falló el envío de notificación de ticket creado', $context);
    }
}
