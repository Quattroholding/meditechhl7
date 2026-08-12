# Guía Rápida: Agregar Metadatos a Notificaciones

Esta guía explica cómo agregar metadatos de tracking a las notificaciones existentes de Laravel.

---

## ✅ Notificaciones Ya Actualizadas

Las siguientes notificaciones **ya incluyen metadatos**:

- ✅ `AppointmentReminderNotification` - Recordatorios de citas
- ✅ `AppointmentConfirmedNotification` - Confirmaciones de citas
- ✅ `InvoiceGeneratedNotification` - Facturas generadas
- ✅ `EncounterPrescriptionNotification` - Prescripciones médicas

---

## 📝 Cómo Actualizar Una Notificación (3 Pasos)

### Paso 1: Importar y Usar el Trait

Agrega el import y el trait `WithEmailMetadata`:

```php
// Al inicio del archivo
use App\Notifications\Concerns\WithEmailMetadata;

// En la clase
class MiNotificacion extends Notification implements ShouldQueue
{
    use Queueable, WithEmailMetadata; // <-- Agregar aquí
```

### Paso 2: Definir Metadatos

Implementa el método `emailMetadata()` **antes** del método `toMail()`:

```php
/**
 * Define metadatos personalizados para tracking del correo
 */
protected function emailMetadata(): array
{
    return [
        'Type' => 'nombre-del-tipo',
        'Appointment-ID' => $this->appointment->id,
        'Patient-ID' => $this->patient->id,
        'Patient-Name' => $this->patient->full_name,
        // ... agregar más metadatos relevantes
    ];
}
```

### Paso 3: Aplicar Metadatos en toMail()

Agrega `->withSwiftMessage()` al final del `return` en `toMail()`:

```php
public function toMail($notifiable): MailMessage
{
    return (new MailMessage)
        ->subject('Asunto del correo')
        ->view('emails.mi-vista', [
            // ... datos para la vista
        ])
        ->withSwiftMessage(function ($message) {
            $this->applyEmailMetadata($message);
        });
}
```

---

## 🎯 Metadatos Recomendados por Tipo de Notificación

### Notificaciones de Citas (Appointment)

```php
protected function emailMetadata(): array
{
    return [
        'Type' => 'appointment-[accion]', // reminder, confirmed, cancelled, rescheduled
        'Appointment-ID' => $this->appointment->id,
        'Patient-ID' => $this->appointment->patient_id,
        'Patient-Name' => $this->appointment->patient->full_name,
        'Doctor-ID' => $this->appointment->practitioner_id,
        'Doctor-Name' => $this->appointment->practitioner->name,
        'Appointment-Date' => $this->appointment->start->format('Y-m-d H:i'),
        'Branch-Name' => $this->appointment->consultingRoom->branch->name ?? 'N/A',
        'Specialty' => $this->appointment->medicalSpeciality->name ?? 'N/A',
        'Client-ID' => $this->appointment->client_id,
    ];
}
```

### Notificaciones de Facturas/Pagos (Invoice/Payment)

```php
protected function emailMetadata(): array
{
    return [
        'Type' => 'invoice-[accion]', // generated, payment-registered, payment-failed
        'Invoice-ID' => $this->invoice->id,
        'Invoice-Number' => $this->invoice->invoice_number,
        'Client-ID' => $this->invoice->client_id,
        'Client-Name' => $this->invoice->client->name,
        'Total-Amount' => number_format($this->invoice->total, 2),
        'Payment-Status' => $this->invoice->status->value,
        'Due-Date' => $this->invoice->due_date->format('Y-m-d'),
    ];
}
```

### Notificaciones de Usuario (Authentication/Account)

```php
protected function emailMetadata(): array
{
    return [
        'Type' => 'user-[accion]', // registration, password-reset, 2fa-enabled
        'User-ID' => $this->user->id,
        'User-Email' => $this->user->email,
        'User-Role' => $this->user->roles->pluck('name')->join(','),
        'Client-ID' => $this->user->client_id ?? null,
    ];
}
```

### Notificaciones de Waitlist

```php
protected function emailMetadata(): array
{
    return [
        'Type' => 'waitlist-[accion]', // added, slot-available, cancelled, expired
        'Waitlist-ID' => $this->waitlistEntry->id,
        'Patient-ID' => $this->waitlistEntry->patient_id,
        'Patient-Name' => $this->waitlistEntry->patient->full_name,
        'Doctor-ID' => $this->waitlistEntry->practitioner_id,
        'Priority' => $this->waitlistEntry->priority,
    ];
}
```

### Notificaciones de Suscripción

```php
protected function emailMetadata(): array
{
    return [
        'Type' => 'subscription-[accion]', // activated, cancelled, expired
        'Subscription-ID' => $this->subscription->id,
        'Client-ID' => $this->subscription->client_id,
        'Package-Name' => $this->subscription->package->name,
        'Status' => $this->subscription->status->value,
    ];
}
```

---

## 📋 Checklist: Notificaciones Pendientes de Actualizar

Marca las que vayas actualizando:

### Citas (Appointments)
- [ ] `AppointmentCancelledNotification`
- [ ] `AppointmentRescheduledNotification`
- [ ] `AppointmentRejectedNotification`
- [ ] `AppointmentBookedNotification`
- [ ] `AppointmentProposedNotification`
- [ ] `AppointmentBookedForPractitionerNotification`
- [ ] `AppointmentRescheduledForPractitionerNotification`

### Waitlist
- [ ] `AppointmentAddedToWaitlistNotification`
- [ ] `WaitlistSlotAvailableNotification`
- [ ] `WaitlistEntryCancelledNotification`
- [ ] `WaitlistEntryExpiredNotification`

### Pagos
- [ ] `PaymentRegisteredNotification`
- [ ] `PaymentFailedNotification`
- [ ] `PaymentRejectedNotification`

### Usuarios/Autenticación
- [ ] `ResetPasswordNotification`
- [ ] `AccountValidationNotification`
- [ ] `PractitionerCredentialsNotification`
- [ ] `PractitionerSetupRequiredNotification`
- [ ] `NewUserRegistrationNotification`
- [ ] `TwoFactorEnabledNotification`
- [ ] `TwoFactorDisabledNotification`
- [ ] `TwoFactorBackupCodeNotification`
- [ ] `RecoveryCodeUsedNotification`

### Suscripciones
- [ ] `SubscriptionActivatedNotification`

### Otros
- [ ] `PatientHistoryReadyNotification`
- [ ] `NewEnterpriseLeadNotification`
- [ ] `HemoScreenResultReceivedNotification`
- [ ] `PatientAuthorizationCodeNotification`
- [ ] `TicketCreatedNotification`
- [ ] `TicketCommentedNotification`
- [ ] `SendPatientSatisfactionSurvey`

---

## 🧪 Cómo Probar

Después de actualizar una notificación:

1. **Dispara la notificación** (en desarrollo):
   ```php
   // Ejemplo con AppointmentReminder
   $patient = Patient::first();
   $appointment = $patient->appointments()->first();

   $patient->notify(new AppointmentReminderNotification($appointment));
   ```

2. **Verifica en Message Trace**:
   - Ve a `/admin/email/message-trace`
   - Busca el correo recién enviado
   - Verifica que aparezcan los metadatos en la columna "Contexto"
   - Haz clic en 👁️ para ver los detalles completos

3. **Verifica en logs** (opcional):
   ```bash
   tail -f storage/logs/laravel.log | grep "Correo sincronizado"
   ```

---

## 💡 Tips

1. **Nombres de Metadatos**: Usa nombres descriptivos en formato `Kebab-Case`
   - ✅ `Patient-Name`, `Appointment-ID`, `Invoice-Number`
   - ❌ `pn`, `apt_id`, `invoiceNum`

2. **Valores Vacíos**: El trait automáticamente filtra valores `null` o vacíos

3. **Type Field**: Siempre incluye un campo `Type` descriptivo del tipo de notificación

4. **Fechas**: Formatea fechas como `Y-m-d H:i` para consistencia

5. **IDs**: Incluye siempre los IDs principales (paciente, cita, doctor, etc.)

6. **No Información Sensible**: Evita contraseñas, tokens, datos médicos detallados

---

## ⚠️ Errores Comunes

### Error: "Call to undefined method"

**Causa**: Olvidaste importar el trait

**Solución**:
```php
use App\Notifications\Concerns\WithEmailMetadata;

class MiNotificacion extends Notification
{
    use WithEmailMetadata; // <-- Agregar
```

### Los metadatos no aparecen

**Causa**: Olvidaste agregar `withSwiftMessage()`

**Solución**:
```php
return (new MailMessage)
    ->subject('...')
    ->view('...')
    ->withSwiftMessage(function ($message) {
        $this->applyEmailMetadata($message);
    });
```

### Error: "Trying to get property of non-object"

**Causa**: Un campo en `emailMetadata()` es null

**Solución**: Usa el operador null-safe `??`:
```php
'Doctor-Name' => $this->appointment->practitioner->name ?? 'N/A',
```

---

## 📚 Documentación Relacionada

- **Guía completa de metadatos**: `EMAIL_METADATA_GUIDE.md`
- **Integración Exchange**: `MICROSOFT_EXCHANGE_INTEGRATION.md`
- **Trait fuente**: `app/Notifications/Concerns/WithEmailMetadata.php`

---

## 🎯 Próximos Pasos

1. Actualiza las notificaciones más importantes primero (citas, facturas, prescripciones) ✅
2. Actualiza las notificaciones de autenticación y usuario
3. Actualiza las notificaciones de waitlist y suscripciones
4. Actualiza el resto de notificaciones secundarias
5. Prueba cada una en desarrollo antes de subirlas a producción
