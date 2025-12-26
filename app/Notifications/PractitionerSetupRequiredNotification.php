<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PractitionerSetupRequiredNotification extends Notification
{
    use Queueable;

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
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Configuración inicial requerida',
            'message' => 'Para comenzar a agendar citas, es necesario que complete la configuración de su cuenta creando:',
            'steps' => [
                'Una sucursal  en el módulo de Sedes',
                'Al menos un consultorio asociado a la sucursal',
            ],
            'action' => [
                'text' => 'Ir al módulo de Sedes',
                'url' => route('client.branch.index'),
            ],
            'priority' => 'high',
            'icon' => 'fas fa-building',
        ];
    }
}
