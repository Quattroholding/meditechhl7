<?php

namespace App\Notifications;

use App\Models\EnterpriseLead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewEnterpriseLeadNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public EnterpriseLead $lead)
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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $isUpgradeRequest = $this->lead->source === 'upgrade_request';

        $message = (new MailMessage)
            ->subject($isUpgradeRequest ? '🔔 Nueva Solicitud de Upgrade - Plan Empresarial' : '🔔 Nuevo Lead Empresarial')
            ->greeting('¡Nuevo Lead Empresarial!')
            ->line($isUpgradeRequest
                ? 'Un cliente existente ha solicitado upgrade al Plan Empresarial.'
                : 'Se ha recibido una nueva solicitud de contacto para el Plan Empresarial.'
            );

        // Información del contacto
        $message->line('**Información del Contacto:**')
            ->line('👤 **Nombre:** '.$this->lead->full_name)
            ->line('📧 **Email:** '.$this->lead->email)
            ->line('📱 **Teléfono:** '.$this->lead->phone)
            ->line('🏢 **Empresa/Clínica:** '.$this->lead->company_name);

        // Si hay mensaje, mostrarlo
        if ($this->lead->message) {
            $message->line('')
                ->line('**Mensaje:**')
                ->line($this->lead->message);
        }

        // Información adicional para upgrade requests
        if ($isUpgradeRequest && isset($this->lead->metadata['current_package_id'])) {
            $currentPackage = \App\Models\Package::find($this->lead->metadata['current_package_id']);
            $requestedPackage = \App\Models\Package::find($this->lead->metadata['requested_package_id']);

            $message->line('')
                ->line('**Información de Suscripción:**')
                ->line('📊 **Plan Actual:** '.$currentPackage?->name.' ($'.number_format($currentPackage?->base_price ?? 0, 2).')')
                ->line('🎯 **Plan Solicitado:** '.$requestedPackage?->name.' ($'.number_format($requestedPackage?->base_price ?? 0, 2).')');

            if (isset($this->lead->metadata['client_id'])) {
                $client = \App\Models\Client::find($this->lead->metadata['client_id']);
                $message->line('🆔 **Cliente ID:** '.$client?->id.' - '.$client?->name);
            }
        }

        $message->line('')
            ->line('**Origen:** '.($isUpgradeRequest ? 'Solicitud de Upgrade' : 'Landing Page'))
            ->line('**Fecha:** '.$this->lead->created_at);

        // Acción para ver el lead
        if (isset($this->lead->id)) {
            $message->action('Ver Lead en el Sistema', url('/admin/leads/'.$this->lead->id));
        }

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
            'lead_id' => $this->lead->id,
            'company_name' => $this->lead->company_name,
            'full_name' => $this->lead->full_name,
            'source' => $this->lead->source,
        ];
    }
}
