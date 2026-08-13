<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NightwatchExceptionSolutionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public array $issueData,
        public string $aiSolution
    ) {
        $this->onQueue('emails');
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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $exceptionClass = class_basename($this->issueData['exception_class']);
        $priority = ucfirst($this->issueData['priority'] ?? 'medium');

        return (new MailMessage)
            ->subject("🤖 Solución AI: {$exceptionClass} [{$priority}]")
            ->markdown('emails.nightwatch-exception-solution', [
                'issueData' => $this->issueData,
                'aiSolution' => $this->aiSolution,
                'exceptionClass' => $exceptionClass,
                'priority' => $priority,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'issue_id' => $this->issueData['id'],
            'exception_class' => $this->issueData['exception_class'],
            'message' => $this->issueData['message'],
            'file' => $this->issueData['file'],
            'line' => $this->issueData['line'],
            'priority' => $this->issueData['priority'],
            'occurrence_count' => $this->issueData['occurrence_count'],
            'ai_solution_length' => strlen($this->aiSolution),
        ];
    }

    /**
     * Handle a notification failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Falló el envío de solución AI de excepción', [
            'issue_id' => $this->issueData['id'],
            'error' => $exception->getMessage(),
        ]);
    }
}
