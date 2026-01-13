<?php

namespace App\Notifications;

use App\Models\ClientSubscription;
use App\Notifications\Concerns\ValidatesEmailChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionActivatedNotification extends Notification implements ShouldQueue
{
    use Queueable, ValidatesEmailChannel;

    public $tries = 3;

    public $backoff = [60, 300, 600];

    public function __construct(
        public ClientSubscription $subscription
    ) {
        $this->onQueue('emails');
    }

    public function via($notifiable)
    {
        return array_filter([
            'database',
            $this->getMailChannelIfValid($notifiable->email),
        ]);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $client = $this->subscription->client;
        $package = $this->subscription->package;

        $subject = '¡Suscripción Activada! - '.$client->name.' - SAMI';

        // Detectar pasos pendientes de configuración
        $pendingSteps = $this->getPendingSetupSteps($client);

        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->view('emails.subscription-activated', [
                'subscription' => $this->subscription,
                'client' => $client,
                'package' => $package,
                'pendingSteps' => $pendingSteps,
            ]);

        $mailMessage->bcc('business@meditecpty.com');

        return $mailMessage;
    }

    /**
     * Detectar los pasos pendientes de configuración inicial
     */
    protected function getPendingSetupSteps($client): array
    {
        $steps = [];

        // 1. Crear sucursal (obligatorio)
        if (! $client->branches()->exists()) {
            $steps[] = [
                'title' => 'Crear su primera sucursal',
                'description' => 'Registre al menos una sucursal para que tus pacientes puedan ubicarte',
                'route' => 'client.branch.create',
                'icon' => '🏢',
                'required' => true,
            ];
        }

        // 2. Registrar consultorio (obligatorio)
        //if ($client->branches()->exists()) {
            $hasConsultingRoom = \App\Models\ConsultingRoom::whereHas('branch', function ($query) use ($client) {
                $query->where('client_id', $client->id);
            })->exists();

            if (! $hasConsultingRoom) {
                $steps[] = [
                    'title' => 'Registrar al menos un consultorio',
                    'description' => 'Agregue consultorios en sus sucursales para agendar citas',
                    'route' => 'client.room.create',
                    'icon' => '🏥',
                    'required' => true,
                ];
            }
        //}

        // 3. Registrar paciente (obligatorio)
        if (! $client->patients()->exists()) {
            $steps[] = [
                'title' => 'Registrar su primer paciente',
                'description' => 'Agregue pacientes a su cuenta para comenzar a agendar citas',
                'route' => 'patients.create',
                'icon' => '👤',
                'required' => true,
            ];
        }

        // 4. Catálogo de servicios (recomendado)
        $hasServices = \App\Models\ServiceCatalog::where('client_id', $client->id)->exists();
        if (! $hasServices) {
            $steps[] = [
                'title' => 'Configurar catálogo de servicios',
                'description' => 'Defina sus servicios médicos para cargarlos rápidamente en consultas y facturar a sus pacientes por sus servicios',
                'route' => 'setting.create_cpt_user',
                'icon' => '📋',
                'required' => false,
            ];
        }

        // 5. Horarios de trabajo (recomendado)
        //if ($client->branches()->exists()) {
            $hasWorkingHours = \App\Models\UserWorkingHour::whereHas('branch', function ($query) use ($client) {
                $query->where('client_id', $client->id);
            })->exists();

            if (! $hasWorkingHours) {
                $steps[] = [
                    'title' => 'Configurar horarios de trabajo',
                    'description' => 'Defina los horarios de atención en cada sucursal para optimizar control del agendamiento',
                    'route' => 'setting.create_working_hour_user',
                    'icon' => '⏰',
                    'required' => false,
                ];
            }
        //}

        return $steps;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $client = $this->subscription->client;
        $package = $this->subscription->package;

        return [
            // Standard notification fields
            'title' => '¡Suscripción Activada!',
            'message' => 'Su pago ha sido verificado y su suscripción al plan '.$package->name.' está ahora activa. ¡Gracias por confiar en nosotros!',
            'steps' => [
                'Plan: '.$package->name,
                'Estado: Activa',
                'Período: '.$this->subscription->current_period_starts_at->format('d/m/Y').' - '.$this->subscription->current_period_ends_at->format('d/m/Y'),
                'Próxima facturación: '.$this->subscription->next_billing_date->format('d/m/Y'),
            ],
            'action' => [
                'text' => 'Ver Suscripción',
                'url' => route('suscriptions.show', $this->subscription->id),
            ],
            'priority' => 'high',
            'icon' => 'fas fa-check-circle',

            // Legacy/specific fields (for backwards compatibility)
            'type' => 'subscription_activated',
            'subscription_id' => $this->subscription->id,
            'client_id' => $client->id,
            'client_name' => $client->name,
            'package_id' => $package->id,
            'package_name' => $package->name,
            'status' => $this->subscription->status->value,
            'current_period_starts_at' => $this->subscription->current_period_starts_at->format('Y-m-d'),
            'current_period_ends_at' => $this->subscription->current_period_ends_at->format('Y-m-d'),
            'next_billing_date' => $this->subscription->next_billing_date->format('Y-m-d'),
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
            'subscription_id' => $this->subscription->id,
            'client_id' => $this->subscription->client_id,
            'error' => $errorMessage,
        ];

        // Check if it's an RFC 2606 reserved domain error
        if (str_contains($errorMessage, 'Recipient address reserved by RFC 2606') ||
            str_contains($errorMessage, 'code "501"')) {
            \Log::warning('Intento de envío a dirección reservada RFC 2606', $context);

            return;
        }

        // Log other errors as errors
        \Log::error('Falló el envío de notificación de suscripción activada', $context);
    }
}
