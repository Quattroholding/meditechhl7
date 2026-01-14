<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\ConsultingRoom;
use App\Models\PatientClient;
use App\Models\RapidAccess;
use App\Models\ServiceCatalog;
use App\Models\SetupReminderConfig;
use Livewire\Component;

class SetupReminderPanel extends Component
{
    public bool $showPanel = false;

    public ?string $currentReminder = null;

    public ?string $reminderTitle = null;

    public ?string $reminderMessage = null;

    public ?string $reminderActionUrl = null;

    public ?string $reminderActionText = null;

    public bool $isRequired = true;

    protected $listeners = ['refreshSetupReminders' => 'refreshReminders'];

    /**
     * Define all setup checks in order of priority
     * Each check should return null if passed, or an array with reminder details if failed
     * 'is_required' => true: REQUERIMIENTOS (auto-abren el panel)
     * 'is_required' => false: SUGERENCIAS (solo muestran icono, usuario debe abrir)
     */
    protected function getSetupChecks(): array
    {
        return [
            'subscription' => function () {
                $user = auth()->user();
                $subscription = $user->getActiveSubscription();

                // Si no hay sistema de suscripción, no mostrar recordatorio
                if (! $subscription) {
                    return null;
                }

                // Verificar estados problemáticos de la suscripción
                if ($user->hasSubscriptionPendingActivation()) {
                    // Verificar si la factura tiene un pago pendiente registrado
                    $hasPendingPayment = $subscription->invoices()
                        ->whereIn('status', ['pending', 'overdue'])
                        ->get()
                        ->contains(fn ($invoice) => $invoice->hasPendingPaymentForFullAmount());

                    // Si ya tiene un pago registrado (aunque sea pending), no mostrar recordatorio
                    if ($hasPendingPayment) {
                        return null;
                    }

                    $invoice = $subscription->invoices()->whereIn('status', ['pending', 'overdue'])->first();

                    return [
                        'key' => 'subscription',
                        'title' => 'Activa tu Suscripción',
                        'message' => 'Tu suscripción está pendiente de activación. Completa el pago de tu primera factura para comenzar a usar todas las funcionalidades del sistema.',
                        'action_url' => route('suscriptions.invoices.show', $invoice->id),
                        'action_text' => 'Ver Facturas',
                        'is_required' => true,
                    ];
                }

                if ($user->hasSubscriptionPastDue()) {
                    // Verificar si las facturas vencidas tienen pagos pendientes registrados
                    $hasPendingPayment = $subscription->invoices()
                        ->whereIn('status', ['pending', 'overdue'])
                        ->get()
                        ->contains(fn ($invoice) => $invoice->hasPendingPaymentForFullAmount());

                    // Si ya tiene un pago registrado (aunque sea pending), no mostrar recordatorio
                    if ($hasPendingPayment) {
                        return null;
                    }

                    $daysRemaining = $user->getDaysUntilSubscriptionAction();
                    $daysText = $daysRemaining > 0 ? " Tienes {$daysRemaining} días antes de la suspensión." : '';

                    return [
                        'key' => 'subscription',
                        'title' => 'Pago Pendiente',
                        'message' => "Tienes facturas vencidas.{$daysText} Realiza el pago para evitar la suspensión del servicio.",
                        'action_url' => route('suscriptions.invoices.index'),
                        'action_text' => 'Pagar Facturas',
                        'is_required' => true,
                    ];
                }

                if ($user->hasSubscriptionSuspended()) {
                    // Verificar si las facturas tienen pagos pendientes registrados
                    $hasPendingPayment = $subscription->invoices()
                        ->whereIn('status', ['pending', 'overdue'])
                        ->get()
                        ->contains(fn ($invoice) => $invoice->hasPendingPaymentForFullAmount());

                    // Si ya tiene un pago registrado (aunque sea pending), no mostrar recordatorio
                    if ($hasPendingPayment) {
                        return null;
                    }

                    $daysRemaining = $user->getDaysUntilSubscriptionAction();
                    $daysText = $daysRemaining > 0 ? " Tienes {$daysRemaining} días para regularizar." : '';

                    return [
                        'key' => 'subscription',
                        'title' => 'Suscripción Suspendida',
                        'message' => "Tu suscripción está suspendida por falta de pago.{$daysText} El agendamiento de citas está deshabilitado hasta que regularices tu situación.",
                        'action_url' => route('suscriptions.invoices.index'),
                        'action_text' => 'Ver Facturas',
                        'is_required' => true,
                    ];
                }

                if ($user->hasSubscriptionExpired()) {
                    return [
                        'key' => 'subscription',
                        'title' => 'Suscripción Expirada',
                        'message' => 'Tu suscripción ha expirado. Contacta con soporte para reactivar el servicio y continuar usando el sistema.',
                        'action_url' => route('suscriptions.show'),
                        'action_text' => 'Ver Suscripción',
                        'is_required' => true,
                    ];
                }

                // Verificar límite de citas alcanzado
                if ($user->hasReachedAppointmentsLimit()) {
                    $limit = $user->getAppointmentsLimit();
                    $currentCount = $user->getAppointmentsCountInCurrentPeriod();

                    return [
                        'key' => 'subscription',
                        'title' => 'Límite de Citas Alcanzado',
                        'message' => "Has alcanzado el límite de {$limit} citas de tu plan actual ({$currentCount}/{$limit} citas este período). Actualiza tu plan para continuar agendando.",
                        'action_url' => route('suscriptions.show'),
                        'action_text' => 'Actualizar Plan',
                        'is_required' => true,
                    ];
                }

                return null;
            },
            'branch' => function () {
                $hasBranch = Branch::exists();
                if (! $hasBranch) {
                    return [
                        'key' => 'branch',
                        'title' => '¡Hola! Configura tu <br/>primera sucursal',
                        'message' => 'Para comenzar a usar el sistema y agendar citas , necesitas crear al menos una sucursal donde atenderás a tus pacientes.',
                        'action_url' => route('client.branch.create'),
                        'action_text' => 'Crear Sucursal',
                        'is_required' => true,
                    ];
                }

                return null;
            },
            'consulting_room' => function () {
                $hasConsultingRoom = ConsultingRoom::exists();
                if (! $hasConsultingRoom) {
                    return [
                        'key' => 'consulting_room',
                        'title' => 'Configura tu consultorio',
                        'message' => 'Ya tienes tu sucursal creada. Ahora necesitas configurar al menos un consultorio para poder agendar citas.',
                        'action_url' => route('client.room.create'),
                        'action_text' => 'Crear Consultorio',
                        'is_required' => true,
                    ];
                }

                return null;
            },
            'patient' => function () {
                $client = auth()->user()->getCurrentClient();
                if (! $client) {
                    return null;
                }

                $hasPatients = PatientClient::where('client_id', $client->id)->exists();
                if (! $hasPatients) {
                    return [
                        'key' => 'patient',
                        'title' => 'Registra tus pacientes',
                        'message' => '¡Excelente! Ya tienes tu sucursal y consultorio configurados. Ahora puedes registrar tus pacientes para comenzar a agendar citas.',
                        'action_url' => route('patient.create'),
                        'action_text' => 'Registrar Paciente',
                        'is_required' => true,
                    ];
                }

                return null;
            },
            'service_catalog' => function () {
                $client = auth()->user()->getCurrentClient();
                if (! $client) {
                    return null;
                }

                $hasServiceCatalog = ServiceCatalog::where('client_id', $client->id)
                    ->where('is_active', true)
                    ->exists();

                if (! $hasServiceCatalog) {
                    return [
                        'key' => 'service_catalog',
                        'title' => 'Configura tu <br/> Catálogo de Servicios',
                        'message' => '¡Perfecto! Ya tienes tu estructura básica. Ahora configura los servicios médicos que ofreces con sus precios para poder facturar correctamente.',
                        'action_url' => route('setting.create_user_procedures'),
                        'action_text' => 'Crear Servicios',
                        'is_required' => false,
                    ];
                }

                return null;
            },
            'working_hours' => function () {
                $user = auth()->user();
                $hasWorkingHours = $user->workingHours()->exists();

                if (! $hasWorkingHours) {
                    return [
                        'key' => 'working_hours',
                        'title' => 'Configura tu <br/> Horario de Atención',
                        'message' => 'Define tus horarios de trabajo para que los pacientes sepan cuándo estás disponible para agendar citas.',
                        'action_url' => route('setting.create_working_hour_user'),
                        'action_text' => 'Configurar Horario',
                        'is_required' => false,
                    ];
                }

                return null;
            },
            'rapid_access' => function () {
                $user = auth()->user();
                $client = $user->getCurrentClient();

                if (! $client) {
                    return null;
                }

                $hasRapidAccess = RapidAccess::where('client_id', $client->id)
                    ->where('active', true)
                    ->exists();

                if (! $hasRapidAccess) {
                    return [
                        'key' => 'rapid_access',
                        'title' => 'Configura Accesos <br/> Rápidos',
                        'message' => 'Configura tus accesos rápidos de laboratorios, imágenes y procedimientos para agilizar el llenado de consultas médicas.',
                        'action_url' => route('setting.create_rapid_access'),
                        'action_text' => 'Configurar Accesos',
                        'is_required' => false,
                    ];
                }

                return null;
            },
            // Add more checks here in the future
            // 'new_feature' => function() { ... },
        ];
    }

    public function mount(): void
    {
        // Only show for doctors
        /*if (! auth()->user()->hasRole('doctor') or ! auth()->user()->hasRole('admin client')) {
            return;
        }*/

        // Check if user has already dismissed in this session
        $checks = $this->getSetupChecks();

        foreach ($checks as $key => $checkFunction) {
            // ⚠️ NUEVO: Verificar si el recordatorio está activo en la configuración
            if (! SetupReminderConfig::isReminderActive($key)) {
                continue;
            }

            // Skip if this check was already dismissed in this session
            if (session()->has("setup_reminder_dismissed_{$key}")) {
                continue;
            }

            // Execute the check
            $result = $checkFunction();

            // If check failed, show this reminder
            if ($result !== null) {
                $this->currentReminder = $result['key'];
                $this->reminderTitle = $result['title'];
                $this->reminderMessage = $result['message'];
                $this->reminderActionUrl = $result['action_url'] ?? null;
                $this->reminderActionText = $result['action_text'] ?? null;
                // ⚠️ NUEVO: Obtener is_required desde la configuración de la base de datos
                $this->isRequired = SetupReminderConfig::isReminderRequired($key);

                // ⚠️ NUEVO: No abrir el panel si el usuario ya está en la página de la acción
                $isOnActionPage = $this->reminderActionUrl && url()->current() === $this->reminderActionUrl;

                // Only auto-open panel if it's required AND user is not already on the action page
                $this->showPanel = $this->isRequired && ! $isOnActionPage;
                break;
            }
        }
    }

    public function togglePanel(): void
    {
        $this->showPanel = ! $this->showPanel;
    }

    public function closePanel(): void
    {
        $this->showPanel = false;
    }

    public function dismiss(): void
    {
        // Mark this specific reminder as dismissed for the session
        if ($this->currentReminder) {
            session()->put("setup_reminder_dismissed_{$this->currentReminder}", true);
        }

        $this->showPanel = false;

        // Re-check for next reminder
        $this->recheckReminders();
    }

    protected function recheckReminders(): void
    {
        $checks = $this->getSetupChecks();

        // Reset current reminder
        $this->currentReminder = null;
        $this->reminderTitle = null;
        $this->reminderMessage = null;
        $this->reminderActionUrl = null;
        $this->reminderActionText = null;
        $this->isRequired = true;

        foreach ($checks as $key => $checkFunction) {
            // ⚠️ NUEVO: Verificar si el recordatorio está activo en la configuración
            if (! SetupReminderConfig::isReminderActive($key)) {
                continue;
            }

            // Skip if this check was already dismissed in this session
            if (session()->has("setup_reminder_dismissed_{$key}")) {
                continue;
            }

            // Execute the check
            $result = $checkFunction();

            // If check failed, set this as the next reminder
            if ($result !== null) {
                $this->currentReminder = $result['key'];
                $this->reminderTitle = $result['title'];
                $this->reminderMessage = $result['message'];
                $this->reminderActionUrl = $result['action_url'] ?? null;
                $this->reminderActionText = $result['action_text'] ?? null;
                // ⚠️ NUEVO: Obtener is_required desde la configuración de la base de datos
                $this->isRequired = SetupReminderConfig::isReminderRequired($key);

                // ⚠️ NUEVO: No abrir el panel si el usuario ya está en la página de la acción
                $isOnActionPage = $this->reminderActionUrl && url()->current() === $this->reminderActionUrl;

                // Only auto-open panel if it's required AND user is not already on the action page
                $this->showPanel = $this->isRequired && ! $isOnActionPage;
                break;
            }
        }
    }

    /**
     * Refresh reminders when called from other components
     */
    public function refreshReminders(): void
    {
        $this->recheckReminders();
    }

    public function render()
    {
        return view('livewire.setup-reminder-panel');
    }
}
