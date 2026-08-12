<?php

namespace App\Notifications;

use App\Models\HistoryDownload;
use App\Notifications\Concerns\WithEmailMetadata;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PatientHistoryReadyNotification extends Notification
{
    use Queueable, WithEmailMetadata;

    public function __construct(
        public HistoryDownload $historyDownload
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Define metadatos personalizados para tracking del correo
     */
    protected function emailMetadata(): array
    {
        return [
            'Type' => 'patient-history-ready',
            'History-Download-ID' => $this->historyDownload->id,
            'Patient-ID' => $this->historyDownload->patient_id,
            'Patient-Name' => $this->historyDownload->patient->full_name ?? 'N/A',
            'Total-Encounters' => $this->historyDownload->total_encounters,
            'Expires-At' => $this->historyDownload->expires_at?->format('Y-m-d H:i') ?? 'N/A',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Historial Médico Listo',
            'message' => "El historial médico de {$this->historyDownload->patient->full_name} está listo para descargar.",
            'patient_name' => $this->historyDownload->patient->full_name,
            'total_encounters' => $this->historyDownload->total_encounters,
            'download_url' => $this->historyDownload->getDownloadUrl(),
            'expires_at' => $this->historyDownload->expires_at?->format('d/m/Y H:i'),
            'history_download_id' => $this->historyDownload->id,
            'icon' => '📋',
            'type' => 'success',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Historial Médico Listo para Descargar')
            ->line("El historial médico de {$this->historyDownload->patient->full_name} está listo.")
            ->line("Total de consultas: {$this->historyDownload->total_encounters}")
            ->action('Descargar Historial', $this->historyDownload->getDownloadUrl())
            ->line('Este enlace expirará en 24 horas.')
            ->withSwiftMessage(function ($message) {
                $this->applyEmailMetadata($message);
            });
    }
}
