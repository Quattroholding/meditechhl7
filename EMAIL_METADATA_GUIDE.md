# Guía de Metadatos en Correos - Tracking con Contexto

## 📋 Descripción

Los correos enviados por la aplicación pueden incluir **metadatos personalizados** (headers custom) que permiten identificar el contexto del correo: cita, paciente, doctor, factura, etc.

Estos metadatos aparecen automáticamente en:
- `/admin/email/message-trace` - Columna "Contexto"
- Modal de detalles - Sección "Información del Contexto"

---

## 🔧 Cómo Agregar Metadatos al Enviar Correos

### Método 1: Usando `Mail::send()` con `withSwiftMessage()`

```php
use Illuminate\Support\Facades\Mail;

Mail::send('emails.appointment-reminder', $data, function ($message) use ($appointment, $patient) {
    $message->to($patient->email, $patient->full_name)
            ->subject('Recordatorio de Cita')
            ->withSwiftMessage(function ($swiftMessage) use ($appointment, $patient) {
                $headers = $swiftMessage->getHeaders();

                // Agregar headers personalizados con prefijo X-
                $headers->addTextHeader('X-Appointment-ID', $appointment->id);
                $headers->addTextHeader('X-Patient-ID', $patient->id);
                $headers->addTextHeader('X-Patient-Name', $patient->full_name);
                $headers->addTextHeader('X-Doctor-ID', $appointment->practitioner_id);
                $headers->addTextHeader('X-Type', 'appointment-reminder');
                $headers->addTextHeader('X-Branch', $appointment->branch->name);
            });
});
```

### Método 2: Usando Mailable con `withSwiftMessage()`

```php
namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentReminder extends Mailable
{
    use SerializesModels;

    public $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function build()
    {
        return $this->subject('Recordatorio de Cita')
                    ->view('emails.appointment-reminder')
                    ->withSwiftMessage(function ($message) {
                        $headers = $message->getHeaders();

                        $headers->addTextHeader('X-Appointment-ID', $this->appointment->id);
                        $headers->addTextHeader('X-Patient-ID', $this->appointment->patient_id);
                        $headers->addTextHeader('X-Patient-Name', $this->appointment->patient->full_name);
                        $headers->addTextHeader('X-Doctor-ID', $this->appointment->practitioner_id);
                        $headers->addTextHeader('X-Appointment-Date', $this->appointment->start_time->format('Y-m-d H:i'));
                        $headers->addTextHeader('X-Type', 'appointment-reminder');
                    });
    }
}

// Uso:
Mail::to($patient->email)->send(new AppointmentReminder($appointment));
```

### Método 3: Usando Notificaciones

```php
namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentReminderNotification extends Notification
{
    protected $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Recordatorio de Cita')
                    ->line('Tiene una cita programada.')
                    ->action('Ver Cita', url('/appointments/'.$this->appointment->id))
                    ->withSwiftMessage(function ($message) {
                        $headers = $message->getHeaders();

                        $headers->addTextHeader('X-Appointment-ID', $this->appointment->id);
                        $headers->addTextHeader('X-Patient-ID', $this->appointment->patient_id);
                        $headers->addTextHeader('X-Type', 'appointment-reminder');
                    });
    }
}

// Uso:
$patient->notify(new AppointmentReminderNotification($appointment));
```

---

## 🏷️ Headers Recomendados por Tipo de Correo

### Recordatorio de Citas

```php
$headers->addTextHeader('X-Type', 'appointment-reminder');
$headers->addTextHeader('X-Appointment-ID', $appointment->id);
$headers->addTextHeader('X-Patient-ID', $patient->id);
$headers->addTextHeader('X-Patient-Name', $patient->full_name);
$headers->addTextHeader('X-Doctor-ID', $practitioner->id);
$headers->addTextHeader('X-Doctor-Name', $practitioner->full_name);
$headers->addTextHeader('X-Appointment-Date', $appointment->start_time->format('Y-m-d H:i'));
$headers->addTextHeader('X-Branch', $branch->name);
```

### Confirmación de Citas

```php
$headers->addTextHeader('X-Type', 'appointment-confirmation');
$headers->addTextHeader('X-Appointment-ID', $appointment->id);
$headers->addTextHeader('X-Patient-ID', $patient->id);
$headers->addTextHeader('X-Status', $appointment->status);
```

### Facturas/Invoices

```php
$headers->addTextHeader('X-Type', 'invoice');
$headers->addTextHeader('X-Invoice-ID', $invoice->id);
$headers->addTextHeader('X-Invoice-Number', $invoice->invoice_number);
$headers->addTextHeader('X-Patient-ID', $patient->id);
$headers->addTextHeader('X-Amount', $invoice->total);
$headers->addTextHeader('X-Payment-Status', $invoice->payment_status);
```

### Resultados de Laboratorio

```php
$headers->addTextHeader('X-Type', 'lab-results');
$headers->addTextHeader('X-Patient-ID', $patient->id);
$headers->addTextHeader('X-Test-ID', $labTest->id);
$headers->addTextHeader('X-Test-Type', $labTest->type);
```

### Prescripciones

```php
$headers->addTextHeader('X-Type', 'prescription');
$headers->addTextHeader('X-Prescription-ID', $prescription->id);
$headers->addTextHeader('X-Patient-ID', $patient->id);
$headers->addTextHeader('X-Doctor-ID', $doctor->id);
$headers->addTextHeader('X-Encounter-ID', $encounter->id);
```

---

## 📊 Visualización en la Interfaz

### En la Tabla de Message Trace

Los metadatos aparecen como **badges azules** en la columna "Contexto":

```
┌──────────────────────────────────────────────────────────┐
│ Contexto                                                 │
├──────────────────────────────────────────────────────────┤
│ 🏷️ Type: appointment-reminder                           │
│ 🏷️ Appointment ID: 123                                  │
│ 🏷️ Patient Name: Juan Pérez                             │
│ 🏷️ Doctor Name: Dr. García                              │
└──────────────────────────────────────────────────────────┘
```

### En el Modal de Detalles

Se muestra una sección especial "📋 Información del Contexto" con todos los metadatos en formato tabla:

```
┌────────────────────────────────────────┐
│ 📋 Información del Contexto            │
├────────────────────────────────────────┤
│ Type:            appointment-reminder  │
│ Appointment ID:  123                   │
│ Patient ID:      456                   │
│ Patient Name:    Juan Pérez            │
│ Doctor ID:       789                   │
│ Doctor Name:     Dr. García            │
│ Appointment Date: 2026-08-15 10:30     │
│ Branch:          Sucursal Centro       │
└────────────────────────────────────────┘
```

---

## ⚙️ Configuración Técnica

### Cómo Funciona

1. **Al enviar correo**: Laravel dispara evento `MessageSent`
2. **Listener intercepta**: `SaveEmailToSentItems` captura el correo
3. **Se guardan headers**: Los headers X-* se preservan en Sent Items
4. **Graph API lee headers**: El servicio solicita campo `internetMessageHeaders`
5. **Se extraen metadatos**: `extractCustomHeaders()` parsea headers X-*
6. **Se muestran en UI**: Blade templates renderizan los metadatos

### Limitaciones

- ✅ Funciona con **todos** los correos enviados desde la aplicación
- ✅ Headers se preservan en Exchange/Office 365
- ✅ No afecta la entrega del correo
- ⚠️ Solo headers que comiencen con `X-` se consideran metadatos
- ⚠️ Máximo ~1KB de datos en headers personalizados (límite de SMTP)

---

## 🧪 Ejemplo Completo de Prueba

```php
// En tinker o un controlador de prueba:
use Illuminate\Support\Facades\Mail;

Mail::raw('Este es un correo de prueba con metadatos', function ($message) {
    $message->to('test@ejemplo.com')
            ->subject('Prueba de Metadatos')
            ->withSwiftMessage(function ($swiftMessage) {
                $headers = $swiftMessage->getHeaders();

                $headers->addTextHeader('X-Type', 'test-email');
                $headers->addTextHeader('X-Test-ID', '12345');
                $headers->addTextHeader('X-User', 'Admin');
                $headers->addTextHeader('X-Environment', config('app.env'));
                $headers->addTextHeader('X-Timestamp', now()->toDateTimeString());
            });
});
```

Luego verifica en `/admin/email/message-trace`:
1. Busca por remitente `notificaciones@meditecpty.com`
2. Deberías ver el correo con badges de metadatos
3. Haz clic en 👁️ para ver todos los detalles

---

## 🎯 Casos de Uso Recomendados

### 1. **Auditoría**: Rastrear qué correos se enviaron para cada cita/paciente
```php
$headers->addTextHeader('X-Appointment-ID', $appointment->id);
```
Luego puedes buscar todos los correos de esa cita.

### 2. **Debugging**: Identificar errores en envíos masivos
```php
$headers->addTextHeader('X-Batch-ID', $batchId);
$headers->addTextHeader('X-Sent-By-Job', $jobClass);
```

### 3. **Reportes**: Categorizar correos por tipo
```php
$headers->addTextHeader('X-Category', 'marketing');
$headers->addTextHeader('X-Campaign-ID', $campaign->id);
```

### 4. **Soporte**: Ayudar a usuarios a encontrar correos específicos
```php
$headers->addTextHeader('X-Support-Ticket', $ticket->number);
$headers->addTextHeader('X-Customer-ID', $customer->id);
```

---

## 🔍 Búsqueda y Filtrado

Actualmente, los metadatos **se muestran pero no se filtran** en la búsqueda.

Si necesitas buscar por metadatos específicos, tendrías que:
1. Modificar `ExchangeMessageTraceService` para filtrar después de cargar
2. O usar el asunto/destinatario para búsqueda

**Ejemplo de búsqueda manual** (futuro enhancement):
```php
// Filtrar correos con X-Appointment-ID específico
$messages = collect($result['messages'])->filter(function ($msg) use ($appointmentId) {
    return isset($msg['Metadata']['Appointment-ID'])
           && $msg['Metadata']['Appointment-ID'] == $appointmentId;
});
```

---

## ✅ Checklist de Implementación

Cuando implementes metadatos en tus correos:

- [ ] Identifica el tipo de correo (appointment, invoice, etc.)
- [ ] Define los headers X-* necesarios
- [ ] Usa `withSwiftMessage()` para agregar headers
- [ ] Prueba el envío en desarrollo
- [ ] Verifica en `/admin/email/message-trace` que aparezcan los metadatos
- [ ] Documenta los headers usados en tu código

---

## 💡 Tips y Mejores Prácticas

1. **Prefijo X- obligatorio**: Solo headers que empiezan con `X-` se consideran metadatos
2. **Nombres descriptivos**: Usa nombres claros como `X-Patient-Name`, no `X-PN`
3. **Valores simples**: Evita JSON o datos complejos, usa valores de texto simples
4. **No información sensible**: No pongas contraseñas, tokens, o datos médicos detallados
5. **Consistencia**: Usa los mismos nombres de headers en todos tus correos del mismo tipo

---

## 🆘 Troubleshooting

### Los metadatos no aparecen

**Posibles causas:**
1. No usaste el prefijo `X-` en el nombre del header
2. El correo no se guardó en Sent Items (queue worker no corriendo)
3. Cache de Graph API (espera 1 hora o limpia cache)

**Solución:**
```bash
# Verificar queue worker
php artisan queue:work

# Limpiar cache
php artisan cache:clear

# Verificar headers en tinker
$headers->getAll(); // Dentro de withSwiftMessage()
```

### Headers con caracteres especiales

Evita caracteres especiales en valores de headers. Usa solo ASCII:

```php
// ❌ Mal
$headers->addTextHeader('X-Patient-Name', 'José María Ñoño');

// ✅ Bien
$headers->addTextHeader('X-Patient-Name', 'Jose Maria Nono');
// O usa encoding:
$headers->addTextHeader('X-Patient-Name', base64_encode('José María Ñoño'));
```

---

## 📚 Referencias

- [RFC 2822 - Internet Message Format](https://www.ietf.org/rfc/rfc2822.txt)
- [Microsoft Graph API - Message Resource](https://learn.microsoft.com/en-us/graph/api/resources/message)
- [Laravel Mail - Custom Headers](https://laravel.com/docs/12.x/mail#customizing-the-swiftmailer-message)
