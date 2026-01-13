# 📋 Flujo de Estados de Suscripciones

Este documento explica el ciclo de vida completo de una suscripción en el sistema Meditech2, incluyendo todos los estados posibles y las transiciones entre ellos.

---

## 📊 Estados Disponibles

| Estado | Valor | Color | Descripción |
|--------|-------|-------|-------------|
| **PENDING_ACTIVATION** | `pending_activation` | 🟡 Warning | Suscripción creada, esperando primer pago |
| **TRIAL** | `trial` | 🔵 Info | En período de prueba gratuito |
| **ACTIVE** | `active` | 🟢 Success | Suscripción activa y al día con pagos |
| **PAST_DUE** | `past_due` | 🟡 Warning | Factura vencida, en período de gracia |
| **SUSPENDED** | `suspended` | 🔴 Danger | Suspendida por falta de pago |
| **CANCELLED** | `cancelled` | ⚫ Secondary | Cancelada por el cliente |
| **EXPIRED** | `expired` | ⚫ Dark | Expirada permanentemente |

---

## 🔄 Diagrama de Flujo de Estados

```
┌─────────────────────────────────────────────────────────────────────┐
│                     CREACIÓN DE SUSCRIPCIÓN                          │
└─────────────────────────────────────────────────────────────────────┘
                              │
                              ▼
                    ┌─────────────────┐
                    │  Con trial/     │
                    │  meses gratis?  │
                    └─────────────────┘
                       │           │
                 Sí   │           │   No
                      ▼           ▼
            ┌──────────────┐   ┌─────────────────────┐
            │   🔵 TRIAL   │   │ 🟡 PENDING_ACTIVATION│
            └──────────────┘   └─────────────────────┘
                   │                      │
                   │                      │ Se genera factura
                   │                      │ inmediatamente
                   │                      │
                   │                      ▼
                   │           ┌─────────────────────┐
                   │           │   Factura Pagada?   │
                   │           └─────────────────────┘
                   │                │            │
                   │           Sí   │            │ No (queda pendiente)
                   │                │            │
                   │                ▼            ▼
                   │           ┌──────────┐   ┌────────────┐
                   │           │          │   │  Permanece │
                   │           │          │   │  PENDING   │
                   │           │          │   └────────────┘
                   │           │          │
                   │           │          │
    Trial expira  │           │          │
    y se genera   │           │          │
    factura       │           │          │
                   │           │          │
                   └───────────┴──────────┘
                              │
                              ▼
                    ┌──────────────────┐
                    │   🟢 ACTIVE      │ ◄────────┐
                    │  (Suscripción    │          │
                    │   activa al día) │          │ Pago recibido
                    └──────────────────┘          │
                              │                   │
                              │ Fin del período   │
                              │ → Genera factura  │
                              ▼                   │
                    ┌──────────────────┐          │
                    │ Nueva Factura    │          │
                    │    Generada      │          │
                    └──────────────────┘          │
                              │                   │
                              │                   │
                    ┌─────────┴──────────┐        │
                    │                    │        │
              Pagada│                    │No pagada
                    │                    │        │
                    ▼                    ▼        │
            ┌──────────────┐   ┌─────────────────┤
            │ Continúa     │   │ 🟡 PAST_DUE     │
            │   ACTIVE     │   │ (Pago vencido,  │
            └──────────────┘   │  en gracia)     │
                    ▲          └─────────────────┘
                    │                   │
                    └───────────────────┤ Pago recibido
                                        │ dentro del período
                                        │ de gracia (7 días)
                                        │
                                        ▼
                              ┌──────────────────┐
                              │ Período de gracia│
                              │    expirado?     │
                              └──────────────────┘
                                   │        │
                             Sí   │        │   No
                                  │        │   (aún en gracia)
                                  ▼        │
                         ┌──────────────┐ │
                         │ 🔴 SUSPENDED │ │
                         │ (Suspendida) │ │
                         └──────────────┘ │
                                │         │
                                │         └────► Practitioners
                                │                SIGUEN activos
                                │
                                │ 30 días sin pago
                                │
                                ▼
                         ┌──────────────┐
                         │  ⚫ EXPIRED  │
                         │  (Expirada)  │
                         └──────────────┘


CANCELACIÓN MANUAL (puede ocurrir en cualquier momento):
                         ┌──────────────┐
                         │⚫ CANCELLED   │
                         │ (Cancelada)  │
                         └──────────────┘
```

---

## 🎯 Transiciones de Estado Detalladas

### 1. **CREACIÓN → PENDING_ACTIVATION**

**Cuándo:** Al crear una suscripción sin trial ni meses gratis.

**Condiciones:**
- `trial_days = 0`
- `free_months = 0`

**Acciones automáticas:**
- Se genera inmediatamente una factura
- `next_billing_date = now()`

**Código:**
```php
$subscription = SubscriptionService::create($client, $package, [
    'trial_days' => 0,
    'free_months' => 0,
]);
// Estado: PENDING_ACTIVATION
// Se genera factura automáticamente
```

---

### 2. **CREACIÓN → TRIAL**

**Cuándo:** Al crear una suscripción con período de prueba o meses gratis.

**Condiciones:**
- `trial_days > 0` O `free_months > 0`

**Acciones automáticas:**
- NO se genera factura inmediatamente
- `trial_ends_at` se establece
- `next_billing_date = trial_ends_at`

**Código:**
```php
$subscription = SubscriptionService::create($client, $package, [
    'trial_days' => 30,
    'free_months' => 1,
]);
// Estado: TRIAL
// No se genera factura hasta que expire el trial
```

---

### 3. **PENDING_ACTIVATION → ACTIVE**

**Cuándo:** Se paga la primera factura.

**Condiciones:**
- Existe una factura en estado `pending`
- Se registra un pago que cubre la factura
- El pago es aprobado/confirmado

**Acciones automáticas:**
- `status = ACTIVE`
- `current_period_starts_at = now()`
- `current_period_ends_at = ahora + billing_period_days`
- `next_billing_date = current_period_ends_at`

**Código:**
```php
// Al registrar pago en ClientInvoicePayment
$payment = ClientInvoicePayment::create([...]);
if ($payment->status === PaymentStatus::CONFIRMED) {
    // La factura se marca como pagada
    // La suscripción pasa a ACTIVE automáticamente
    $subscription->activate();
}
```

---

### 4. **TRIAL → ACTIVE**

**Cuándo:** Expira el período de prueba.

**Condiciones:**
- `trial_ends_at < now()`
- Comando cron ejecuta `subscriptions:generate-invoices`

**Acciones automáticas:**
- `status = ACTIVE`
- Se genera la primera factura
- Inicia el período de facturación regular

**Comando cron:**
```bash
php artisan subscriptions:generate-invoices
```

**Código interno:**
```php
// En SubscriptionService::processRenewals()
if ($subscription->status === SubscriptionStatus::TRIAL
    && $subscription->trial_ends_at->isPast()) {
    $subscription->status = SubscriptionStatus::ACTIVE;
    $subscription->save();
    $this->invoiceService->generate($subscription);
}
```

---

### 5. **ACTIVE → ACTIVE** (Renovación exitosa)

**Cuándo:** Fin del período de facturación con pago exitoso.

**Condiciones:**
- `next_billing_date <= now()`
- Se genera nueva factura
- El cliente paga la factura

**Acciones automáticas:**
- Permanece en `ACTIVE`
- Se actualiza `current_period_starts_at`
- Se actualiza `current_period_ends_at`
- Se calcula nuevo `next_billing_date`

**Comando cron:**
```bash
php artisan subscriptions:generate-invoices
```

---

### 6. **ACTIVE → PAST_DUE**

**Cuándo:** Se genera factura al final del período pero NO se paga.

**Condiciones:**
- `next_billing_date <= now()`
- Se genera factura
- La factura NO es pagada

**Período de gracia:**
- **7 días por defecto** (configurable en `.env`)
- Durante estos 7 días, los practitioners **SIGUEN activos**
- El cliente puede pagar sin perder acceso

**Variables de entorno:**
```env
SUBSCRIPTION_GRACE_PERIOD_DAYS=7
```

**Disponibilidad de practitioners:**
```php
// Durante PAST_DUE dentro del período de gracia
$doctors = Practitioner::withActiveSubscription()->get();
// ✅ Sigue devolviendo doctores (están disponibles)

// PAST_DUE fuera del período de gracia (> 7 días)
// ❌ Ya no devuelve doctores (no disponibles)
```

---

### 7. **PAST_DUE → ACTIVE**

**Cuándo:** El cliente paga la factura vencida dentro del período de gracia.

**Condiciones:**
- Estado actual: `PAST_DUE`
- Pago recibido y confirmado
- Dentro de los 7 días de gracia

**Acciones automáticas:**
- `status = ACTIVE`
- Se actualiza el período de facturación
- Los practitioners continúan disponibles

---

### 8. **PAST_DUE → SUSPENDED**

**Cuándo:** Expira el período de gracia sin pago (7 días).

**Condiciones:**
- `status = PAST_DUE`
- `current_period_ends_at + grace_period_days < now()`
- Sin pago registrado

**Acciones automáticas:**
- `status = SUSPENDED`
- Los practitioners dejan de estar disponibles
- Se envían notificaciones al cliente

**⚠️ Impacto:**
```php
// Los practitioners ya NO están disponibles
$doctors = Practitioner::withActiveSubscription()->get();
// ❌ Devuelve vacío (no disponibles)
```

---

### 9. **SUSPENDED → ACTIVE**

**Cuándo:** El cliente paga las facturas pendientes después de la suspensión.

**Condiciones:**
- Estado actual: `SUSPENDED`
- Se pagan TODAS las facturas pendientes
- Acción manual por administrador

**Código:**
```php
$subscriptionService->reactivate($subscription);
// Status = ACTIVE
// Practitioners vuelven a estar disponibles
```

---

### 10. **SUSPENDED → EXPIRED**

**Cuándo:** Han pasado 30 días desde la suspensión sin pago.

**Condiciones:**
- `status = SUSPENDED`
- `updated_at < now() - 30 days`
- Comando cron ejecuta `subscriptions:cleanup`

**Comando cron:**
```bash
php artisan subscriptions:cleanup
```

**Acciones automáticas:**
- `status = EXPIRED`
- Suscripción expira permanentemente
- Se requiere crear nueva suscripción para reactivar

**⚠️ Permanente:** No hay forma de reactivar una suscripción EXPIRED, se debe crear una nueva.

---

### 11. **CUALQUIER ESTADO → CANCELLED**

**Cuándo:** El cliente cancela su suscripción manualmente.

**Condiciones:**
- Acción manual del cliente o administrador
- Puede ser inmediata o al final del período

**Cancelación al final del período:**
```php
$subscriptionService->cancel($subscription, 'Cliente solicitó cancelación', false);
// Status permanece igual hasta que expire el período
// cancelled_at se registra
// next_billing_date = null
```

**Cancelación inmediata:**
```php
$subscriptionService->cancel($subscription, 'Violación de términos', true);
// Status = CANCELLED inmediatamente
// Practitioners dejan de estar disponibles
```

---

## ⚙️ Comandos Automáticos (Cron)

### 1. **Generar Facturas** (Diario)

**Comando:**
```bash
php artisan subscriptions:generate-invoices
```

**Frecuencia:** Diario a las 00:00

**Qué hace:**
1. Busca suscripciones con `next_billing_date <= now()`
2. Genera facturas para estas suscripciones
3. Si estaban en TRIAL, las pasa a ACTIVE
4. Actualiza períodos de facturación

**Configuración en cron:**
```bash
0 0 * * * cd /var/www/html/meditech2 && php artisan subscriptions:generate-invoices
```

---

### 2. **Limpiar Suscripciones Expiradas** (Diario)

**Comando:**
```bash
php artisan subscriptions:cleanup
```

**Frecuencia:** Diario a las 01:00

**Qué hace:**
1. **Expira trials vencidos:** TRIAL → EXPIRED (después de grace_period_days)
2. **Expira suscripciones suspendidas:** SUSPENDED → EXPIRED (después de 30 días)
3. Desactiva códigos de referido expirados

**Configuración en cron:**
```bash
0 1 * * * cd /var/www/html/meditech2 && php artisan subscriptions:cleanup
```

---

## 🔐 Período de Gracia

### Configuración

**Archivo:** `config/subscriptions.php`
```php
'grace_period_days' => env('SUBSCRIPTION_GRACE_PERIOD_DAYS', 7),
```

**Variable de entorno:** `.env`
```env
SUBSCRIPTION_GRACE_PERIOD_DAYS=7
```

---

### Comportamiento

| Día | Estado | Factura | Practitioners Disponibles | Cliente puede pagar |
|-----|--------|---------|--------------------------|---------------------|
| 0 | ACTIVE | ✅ Pagada | ✅ SÍ | N/A |
| Fin período | PAST_DUE | ⚠️ Vencida | ✅ SÍ | ✅ SÍ |
| +1 día | PAST_DUE | ⚠️ Vencida | ✅ SÍ | ✅ SÍ |
| +2-6 días | PAST_DUE | ⚠️ Vencida | ✅ SÍ | ✅ SÍ |
| +7 días | PAST_DUE | ⚠️ Vencida | ✅ SÍ (último día) | ✅ SÍ |
| +8 días | SUSPENDED | ❌ Vencida | ❌ NO | ✅ SÍ (pero suspendido) |
| +38 días | EXPIRED | ❌ Vencida | ❌ NO | ❌ Debe crear nueva |

---

### Verificar si está en período de gracia

**Código:**
```php
if ($subscription->isInGracePeriod()) {
    // Aún tiene tiempo para pagar
    echo "Quedan X días para pagar antes de suspensión";
} else {
    // Ya pasó el período de gracia
    echo "Período de gracia expirado";
}
```

---

## 📝 Scope de Eloquent

### `activeOrInGracePeriod()`

Filtra suscripciones que deben considerarse "activas" para efectos de disponibilidad de practitioners.

**Incluye:**
- ✅ ACTIVE
- ✅ TRIAL
- ✅ PAST_DUE (dentro del período de gracia)

**Excluye:**
- ❌ PAST_DUE (fuera del período de gracia)
- ❌ SUSPENDED
- ❌ CANCELLED
- ❌ EXPIRED
- ❌ PENDING_ACTIVATION

**Uso:**
```php
// Obtener suscripciones activas o en período de gracia
$subscriptions = ClientSubscription::activeOrInGracePeriod()->get();

// Practitioners con suscripción activa o en período de gracia
$doctors = Practitioner::withActiveSubscription()->get();
// Internamente usa activeOrInGracePeriod()
```

---

## 📋 Tabla Resumen de Disponibilidad

| Estado | Practitioners Disponibles | Genera Facturas | Puede Pagar | Notas |
|--------|---------------------------|----------------|-------------|-------|
| **PENDING_ACTIVATION** | ❌ NO | ✅ Ya generada | ✅ SÍ | Esperando primer pago |
| **TRIAL** | ✅ SÍ | ❌ NO (aún) | N/A | Período gratuito |
| **ACTIVE** | ✅ SÍ | ✅ SÍ (al final del período) | ✅ SÍ | Todo funcional |
| **PAST_DUE** (0-7 días) | ✅ SÍ | ❌ NO | ✅ SÍ | Período de gracia |
| **PAST_DUE** (7+ días) | ❌ NO | ❌ NO | ✅ SÍ | Gracia expirada |
| **SUSPENDED** | ❌ NO | ❌ NO | ✅ SÍ | Requiere pago + reactivación |
| **CANCELLED** | ❌ NO | ❌ NO | ❌ NO | Cancelada por cliente |
| **EXPIRED** | ❌ NO | ❌ NO | ❌ NO | Requiere nueva suscripción |

---

## 🎯 Ejemplos Prácticos

### Ejemplo 1: Cliente nuevo sin trial

```php
// Día 0: Crear suscripción
$subscription = SubscriptionService::create($client, $package, [
    'trial_days' => 0,
]);
// Estado: PENDING_ACTIVATION
// Se genera factura inmediatamente
// Practitioners: ❌ NO disponibles

// Día 0: Cliente paga
$payment = ClientInvoicePayment::create([...]);
// Estado: ACTIVE
// Practitioners: ✅ SÍ disponibles

// Día 30: Fin del período
// Cron ejecuta: subscriptions:generate-invoices
// Se genera nueva factura
// Estado: ACTIVE (si paga) o PAST_DUE (si no paga)
```

---

### Ejemplo 2: Cliente con trial de 30 días

```php
// Día 0: Crear suscripción con trial
$subscription = SubscriptionService::create($client, $package, [
    'trial_days' => 30,
]);
// Estado: TRIAL
// Practitioners: ✅ SÍ disponibles (gratis)
// NO se genera factura

// Día 30: Expira trial
// Cron ejecuta: subscriptions:generate-invoices
// Estado: ACTIVE
// Se genera primera factura
// Practitioners: ✅ SÍ disponibles

// Día 30: Cliente NO paga
// Estado: PAST_DUE
// Practitioners: ✅ SÍ disponibles (período de gracia)

// Día 37: Expira período de gracia
// Estado: SUSPENDED
// Practitioners: ❌ NO disponibles
```

---

### Ejemplo 3: Cliente con pago atrasado que paga en gracia

```php
// Estado: PAST_DUE (día 33 desde fin de período)
// Practitioners: ✅ SÍ disponibles (aún en gracia)

// Día 35: Cliente paga la factura
$payment = ClientInvoicePayment::create([...]);
// Estado: ACTIVE
// Practitioners: ✅ SÍ disponibles
// Se actualiza next_billing_date
```

---

## 🔔 Notificaciones

### Recordatorios de pago

**Antes del vencimiento:**
- 7 días antes
- 3 días antes
- 1 día antes

**Configuración:** `config/subscriptions.php`
```php
'reminder_days_before' => [7, 3, 1],
```

---

### Notificación de suspensión

**Cuándo:** Al pasar de PAST_DUE a SUSPENDED

**Contenido:**
- Factura(s) pendiente(s)
- Monto total adeudado
- Fecha de suspensión
- Instrucciones para reactivar

---

### Notificación de expiración

**Cuándo:** Al pasar de SUSPENDED a EXPIRED (30 días)

**Contenido:**
- Suscripción expirada permanentemente
- Datos eliminados o archivados
- Necesita crear nueva suscripción

---

## 🛠️ Métodos Útiles

### En ClientSubscription

```php
// Verificar si está activa
$subscription->is_active; // true/false

// Verificar si está en trial
$subscription->is_on_trial; // true/false

// Verificar si está cancelada
$subscription->is_cancelled; // true/false

// Verificar período de gracia
$subscription->isInGracePeriod(); // true/false

// Días hasta renovación
$subscription->days_until_renewal; // int o null

// Activar manualmente
$subscription->activate();

// Suspender
$subscription->suspend();

// Cancelar
$subscription->cancel('Motivo', $immediately = false);

// Reanudar
$subscription->resume();

// Extender trial
$subscription->extendTrial(7); // agregar 7 días
```

---

### En SubscriptionService

```php
// Crear suscripción
$subscriptionService->create($client, $package, $options);

// Activar
$subscriptionService->activate($subscription);

// Suspender
$subscriptionService->suspend($subscription);

// Reactivar
$subscriptionService->reactivate($subscription);

// Cancelar
$subscriptionService->cancel($subscription, 'reason', $immediate);

// Cambiar plan
$subscriptionService->changePlan($subscription, $newPackage, $prorate);

// Actualizar doctores extras
$subscriptionService->updateExtraDoctors($subscription, $count);

// Extender trial
$subscriptionService->extendTrial($subscription, $days);

// Procesar renovaciones (cron)
$subscriptionService->processRenewals();

// Procesar expiradas (cron)
$subscriptionService->processExpired();
```

---

## 📖 Referencias

- **Modelo:** `app/Models/ClientSubscription.php`
- **Enum:** `app/Enums/SubscriptionStatus.php`
- **Servicio:** `app/Services/SubscriptionService.php`
- **Config:** `config/subscriptions.php`
- **Comandos:**
  - `app/Console/Commands/GenerateSubscriptionInvoices.php`
  - `app/Console/Commands/CleanupSubscriptions.php`

---

## 💡 Mejores Prácticas

1. **Siempre usar el SubscriptionService** para cambios de estado en lugar de modificar directamente el modelo
2. **Configurar los cron jobs** para que se ejecuten automáticamente
3. **Monitorear los logs** de transiciones de estado
4. **Notificar al cliente** antes de suspender o expirar
5. **Dar suficiente período de gracia** (7-15 días recomendado)
6. **Probar el flujo completo** en ambiente de staging antes de producción
7. **Documentar las cancelaciones** con un motivo claro
8. **Revisar facturas pendientes** antes de suspender para evitar errores

---

*Última actualización: 2026-01-12*
