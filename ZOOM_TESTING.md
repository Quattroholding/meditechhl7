# Zoom Testing Guide - Sandbox Mode ✅

**Para desarrolladores: Prueba todo SIN pagar Zoom Pro**

---

## 🎯 Quick Start (2 minutos)

### 1. Habilitar Sandbox Mode

```bash
# Editar .env
ZOOM_SANDBOX_MODE=true

# Guardar y listo
```

### 2. Crear y Probar Meeting

```bash
# Ver opciones
php artisan zoom:test

# Crear meeting de prueba
php artisan zoom:test create

# Simular eventos webhook
php artisan zoom:test simulate --appointment_id=1

# Ver configuración
php artisan zoom:test config
```

**Eso es todo!** ✅

---

## 📋 3 Modos de Testing

### Modo 1: SANDBOX (Recomendado para desarrollo)

**Qué hace:**
- Simula meetings sin API real
- No requiere credenciales Zoom
- Perfecto para testing local

**Configurar:**
```bash
ZOOM_SANDBOX_MODE=true
```

**Usar:**
```bash
php artisan zoom:test create
php artisan zoom:test simulate
php artisan zoom:test list
```

**Resultado:**
```
✅ Meeting simulado
✅ Webhooks simulados
✅ DB actualizada
✅ SIN gastos
```

---

### Modo 2: DEV ACCOUNT (Zoom gratis con límites)

**Qué hace:**
- Llamadas reales a Zoom API
- Meetings con límite de 40 minutos
- Para testing más realista
- GRATIS (no requiere tarjeta)

**Configurar:**

1. Crear dev account (gratis):
   ```
   https://marketplace.zoom.us
   Login con GitHub/Google (gratis)
   ```

2. Crear OAuth app:
   ```
   Develop → Build App → Server-to-Server OAuth
   Copiar: Account ID, Client ID, Client Secret
   ```

3. Actualizar .env:
   ```bash
   ZOOM_ACCOUNT_ID=tu_account_id
   ZOOM_CLIENT_ID=tu_client_id
   ZOOM_CLIENT_SECRET=tu_client_secret
   ZOOM_HOST_USER_ID=dev@zoom.us
   ZOOM_SANDBOX_MODE=false  # Importante
   ```

4. Crear webhook (opcional):
   ```
   Marketplace → App → Event Subscriptions
   URL: http://localhost:8000/api/webhooks/zoom
   ```

**Usar:**
```bash
php artisan zoom:test create   # Real meeting creado en Zoom
php artisan zoom:test config   # Ver conexión
```

**Resultado:**
```
✅ Real Zoom API calls
✅ Meetings en tu account DEV
⏱️  Límite 40 minutos
✅ SIN gastos
```

---

### Modo 3: PRODUCTION (Zoom Pro real)

**Qué hace:**
- Meetings sin límites (ilimitados)
- Para producción real
- Requiere pagar Zoom Pro ($14.16/mes)

**Configurar:**

1. Comprar Zoom Pro:
   ```
   https://zoom.us/pricing/healthcare
   Plan: Pro
   Precio: $14.16/mes
   ```

2. Crear OAuth app (igual que DEV)

3. Configurar .env:
   ```bash
   ZOOM_ACCOUNT_ID=tu_account_id
   ZOOM_CLIENT_ID=tu_client_id
   ZOOM_CLIENT_SECRET=tu_client_secret
   ZOOM_HOST_USER_ID=admin@tuclínica.com
   ZOOM_SANDBOX_MODE=false
   ```

4. Configurar webhooks:
   ```
   URL: https://tuapp.com/api/webhooks/zoom
   ```

**Usar:**
```bash
php artisan zoom:test create   # Real, sin límites
php artisan zoom:test config   # Ver conexión
```

**Resultado:**
```
✅ Meetings sin límites
✅ Para pacientes reales
💰 $14.16/mes/usuario
```

---

## 🧪 Ejemplos de Testing

### Ejemplo 1: Test Básico (Sandbox)

```bash
# 1. Confirmar sandbox activo
php artisan zoom:test config
# Output: "Mode: SANDBOX"

# 2. Crear meeting
php artisan zoom:test create
# Output:
# ✅ Meeting created successfully!
# Appointment ID: 5
# Meeting ID: 1234567890
# Join URL: https://zoom.us/j/1234567890
# Mode: SANDBOX

# 3. Verificar en DB
php artisan tinker
$appointment = App\Models\Appointment::find(5);
echo $appointment->virtual_room_id;  # 1234567890
echo $appointment->virtual_room_url; # https://zoom.us/j/1234567890
```

### Ejemplo 2: Simular Eventos Completos

```bash
# Crear meeting
php artisan zoom:test create
# Appointment ID: 10

# Simular: doctor inicia sesión
php artisan zoom:test simulate --appointment_id=10
# Output:
# 1️⃣  Simulating: meeting.started
#    ✓ Started at: 2026-09-02 14:30:45
# 2️⃣  Simulating: meeting.ended
#    ✓ Ended at: 2026-09-02 14:35:22
#    ✓ Participants: 2
# ✅ All webhook events simulated!

# Verificar datos en DB
php artisan tinker
$a = App\Models\Appointment::find(10);
$a->virtual_session_started_at;    # 2026-09-02 14:30:45
$a->virtual_session_ended_at;      # 2026-09-02 14:35:22
$a->virtual_session_metadata;      # Incluye participant_count: 2
```

### Ejemplo 3: Test con Tinker (Avanzado)

```bash
php artisan tinker

# 1. Verificar modo
$zoom = app(App\Services\ZoomService::class);
echo $zoom->getMode();              # "SANDBOX"
echo $zoom->isSandboxMode();        # true

# 2. Crear meeting manualmente
$appt = App\Models\Appointment::factory()->create([
    'consultation_type' => 'virtual'
]);
$meeting = $zoom->createMeeting($appt);
dump($meeting);

# 3. Generar SDK signature
$sig = $zoom->generateSDKSignature($appt->virtual_room_id, 1);
echo $sig;  # zoom_sandbox_token_...

# 4. Simular webhooks
$zoom->handleWebhookEvent([
    'event' => 'meeting.started',
    'payload' => ['object' => ['id' => $appt->virtual_room_id]]
]);
$appt->refresh();
echo $appt->virtual_session_started_at;
```

---

## 🔄 Flujo Completo de Testing

```
┌─────────────────────────────────────────────────────────────┐
│ SANDBOX MODE TESTING FLOW                                   │
└─────────────────────────────────────────────────────────────┘

1. Habilitar Sandbox
   ZOOM_SANDBOX_MODE=true
          ↓

2. Crear Meeting
   php artisan zoom:test create
          ↓
   ✅ Appointment + Meeting ID guardados en DB
          ↓

3. Verificar Datos
   php artisan zoom:test list
          ↓
   ✅ Ver meetings listados
          ↓

4. Simular Webhooks
   php artisan zoom:test simulate --appointment_id=X
          ↓
   ✅ Eventos processed
   ✅ Timestamps registrados
          ↓

5. Verificar Logs
   php artisan pail --filter="Zoom"
          ↓
   ✅ Ver transacciones completas
          ↓

6. Testing Completo ✅
   Todo funciona sin pagar nada
```

---

## 📊 Tabla: Comparación de Modos

| Feature | Sandbox | DEV Account | Production |
|---------|---------|------------|-----------|
| **Costo** | $0 | $0 | $14.16/mes |
| **API Real** | ❌ Simulado | ✅ Real | ✅ Real |
| **Meetings en Zoom** | ❌ No | ✅ Sí | ✅ Sí |
| **Duración Max** | - | 40 min | Ilimitado |
| **Recordings** | ❌ Mock | ✅ Real | ✅ Real |
| **Webhooks** | ✅ Simulados | ✅ Reales | ✅ Reales |
| **Para Testing** | ✅ Perfecto | ✅ Realista | ❌ Costoso |
| **Para Producción** | ❌ No | ❌ No | ✅ Sí |

---

## 🛠️ Comandos Disponibles

```bash
# Ver todas las opciones
php artisan zoom:test

# Crear meeting de prueba
php artisan zoom:test create

# Listar appointments virtuales
php artisan zoom:test list

# Simular webhooks para appointment
php artisan zoom:test simulate
php artisan zoom:test simulate --appointment_id=5

# Ver configuración Zoom actual
php artisan zoom:test config
```

---

## ✅ Testing Checklist

### Testing Manual

- [ ] Habilitar `ZOOM_SANDBOX_MODE=true`
- [ ] Ejecutar: `php artisan zoom:test create`
- [ ] Verificar meeting creado en DB
- [ ] Ejecutar: `php artisan zoom:test simulate`
- [ ] Verificar eventos procesados
- [ ] Ver logs: `php artisan pail --filter="Zoom"`

### Automated Testing

```bash
# Ejecutar tests
php artisan test --filter=Zoom --compact

# Resultado esperado:
# ZoomServiceTest ........................ 7/7 ✅
# ZoomWebhookTest ........................ 6/6 ✅
# Total: 13/13 ✅
```

### Antes de Producción

- [ ] Cambiar `ZOOM_SANDBOX_MODE=false`
- [ ] Verificar credenciales reales en .env
- [ ] Ejecutar: `php artisan zoom:test config`
- [ ] Crear meeting real: `php artisan zoom:test create`
- [ ] Verificar en Zoom Dashboard que meeting aparece
- [ ] Configurar webhooks en Zoom Marketplace

---

## 🐛 Troubleshooting

### Problema: "Error creating Zoom meeting"

**En Sandbox:**
```
→ Verificar ZOOM_SANDBOX_MODE=true
→ Verificar DB tiene appointments
→ Ver logs: php artisan pail --filter="Zoom"
```

**En DEV/Production:**
```
→ Verificar credenciales en .env
→ Ejecutar: php artisan zoom:test config
→ Verificar ✅ para cada credential
```

### Problema: "No virtual appointments found"

```bash
# Crear appointment de prueba
php artisan tinker
App\Models\Appointment::factory()->create([
    'consultation_type' => 'virtual'
]);

# O ejecutar:
php artisan zoom:test create
```

### Problema: Webhooks no se simulan

```bash
# Asegurarse que appointment tiene meeting_id
php artisan tinker
$a = App\Models\Appointment::find(5);
echo $a->virtual_room_id;  # Debe tener valor

# Si está vacío, crear meeting primero:
php artisan zoom:test create --appointment_id=5
```

---

## 📚 Referencia Rápida

| Comando | Resultado |
|---------|-----------|
| `php artisan zoom:test` | Mostrar menú opciones |
| `php artisan zoom:test create` | Crear meeting simulado/real |
| `php artisan zoom:test list` | Listar meetings |
| `php artisan zoom:test simulate --appointment_id=5` | Simular webhooks |
| `php artisan zoom:test config` | Ver configuración |

---

## 🚀 Próximos Pasos

### Hoy: Testing Local
```bash
ZOOM_SANDBOX_MODE=true
php artisan zoom:test create
```

### Cuando esté listo: DEV Account
```bash
1. Crear en marketplace.zoom.us (gratis)
2. Copiar credentials a .env
3. ZOOM_SANDBOX_MODE=false
4. php artisan zoom:test create
```

### Para Producción: Zoom Pro
```bash
1. Comprar en zoom.us/pricing/healthcare
2. Copiar credentials reales a .env
3. Configurar webhooks
4. Deploy
```

---

## 💡 Tips

✅ **Siempre comienza en SANDBOX** - Cero costo, testing completo
✅ **DEV Account para realismo** - Sin tarjeta de crédito requerida
✅ **Tests automatizados** - Ejecuta `php artisan test --filter=Zoom`
✅ **Logs son tu amigo** - `php artisan pail --filter="Zoom"`
✅ **Sandbox mode es perfectamente funcional** - Mismo código que producción

---

**Última actualización:** 2026-09-02
**Status:** ✅ Listo para Testing
**Costo de Desarrollo:** $0 (Sandbox completo)
