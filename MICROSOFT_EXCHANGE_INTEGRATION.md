# Integración con Microsoft Exchange - Seguimiento de Correos

## 📋 Descripción

Sistema de seguimiento y sincronización de correos enviados con Microsoft Exchange Office 365 mediante Microsoft Graph API.

### ✨ Funcionalidades

1. **Sincronización Automática a Sent Items**
   - Cada correo enviado se guarda automáticamente en "Sent Items"
   - Funciona con correos enviados por SMTP relay
   - Ejecución asíncrona mediante queues

2. **Interfaz de Búsqueda (Message Trace)**
   - Búsqueda por remitente, destinatario, asunto, fechas
   - Ver detalles completos de cada correo
   - Estado de entrega automático (detecta rebotes)
   - Paginación de resultados

3. **Metadatos Personalizados (Context Tracking)**
   - Agrega información de contexto a los correos (cita, paciente, etc.)
   - Headers personalizados (X-Headers) se preservan en Exchange
   - Visualización de metadatos en la interfaz de búsqueda
   - Ver guía completa en `EMAIL_METADATA_GUIDE.md`

---

## 🔧 Configuración de Azure AD

### Permisos Requeridos

Tu aplicación en Azure AD debe tener estos permisos **Application** (NO Delegated):

| Permiso | Tipo | Propósito |
|---------|------|-----------|
| Mail.Read | Application | Leer correos del buzón |
| Mail.ReadBasic.All | Application | Lectura básica de todos los buzones |
| Mail.ReadWrite | Application | Escribir mensajes en carpetas (Sent Items) |

### Pasos de Configuración

1. **Azure Portal** → Azure Active Directory → App registrations
2. Selecciona tu aplicación
3. **API permissions** → Add a permission → Microsoft Graph
4. Selecciona **Application permissions** (importante)
5. Marca los 3 permisos listados arriba
6. **Grant admin consent for [organización]** ⚠️ CRÍTICO

---

## ⚙️ Variables de Entorno

Agrega al archivo `.env`:

```bash
# Microsoft Graph API
MICROSOFT_TENANT_ID=tu-tenant-id
MICROSOFT_CLIENT_ID=tu-client-id
MICROSOFT_CLIENT_SECRET=tu-client-secret
MICROSOFT_MAILBOX_EMAIL=notificaciones@meditecpty.com
MICROSOFT_SAVE_TO_SENT_ITEMS=true
```

### Obtener Credenciales

- **Tenant ID**: Azure Portal → Azure AD → Overview → Directory (tenant) ID
- **Client ID**: Azure Portal → Tu App → Overview → Application (client) ID
- **Client Secret**: Azure Portal → Tu App → Certificates & secrets → New client secret

---

## 🚀 Componentes del Sistema

### Servicios

1. **`MicrosoftGraphService`**
   - Conexión con Microsoft Graph API
   - Lectura de correos desde Sent Items
   - Autenticación OAuth 2.0

2. **`ExchangeMessageTraceService`**
   - Búsqueda de correos con filtros
   - Verificación de entrega
   - Paginación de resultados

3. **`SendItemsSyncService`**
   - Guarda copias de correos en Sent Items
   - Sincronización automática

### Listeners

**`SaveEmailToSentItems`**
- Intercepta evento `MessageSent` de Laravel
- Guarda copia automática en Sent Items
- Ejecución asíncrona via queue

### Componentes Livewire

1. **`Email/OutboxDataTable`** (`/admin/email/outbox`)
   - Vista de correos en Sent Items
   - Búsqueda y ordenamiento

2. **`Email/MessageTraceTable`** (`/admin/email/message-trace`)
   - Búsqueda avanzada con filtros
   - Verificación de entrega

---

## 📡 Rutas Disponibles

### Para Administradores

```php
/admin/email/outbox         // Vista de Sent Items
/admin/email/message-trace  // Búsqueda avanzada
```

**Middleware**: `auth`, `verified`, `role:admin`

---

## 🔄 Funcionamiento Automático

### Flujo de Sincronización

1. La aplicación envía un correo (SMTP normal)
2. Laravel dispara evento `MessageSent`
3. `SaveEmailToSentItems` listener intercepta el evento
4. Se encola el job en queue
5. Queue worker guarda copia en Sent Items
6. El correo aparece en `/admin/email/message-trace`

### Queue Worker (Producción)

**Importante**: Debes tener el queue worker corriendo:

```bash
# Opción 1: Modo desarrollo
php artisan queue:work --tries=3

# Opción 2: Modo producción con Supervisor
php artisan queue:work --tries=3 --timeout=60
```

### Configurar Supervisor (Recomendado)

```ini
[program:meditech-queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/meditech2/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/html/meditech2/storage/logs/queue-worker.log
stopwaitsecs=3600
```

Activar:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start meditech-queue-worker:*
```

---

## 🧪 Pruebas

### Verificar Configuración

```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear

# Enviar correo de prueba
php artisan tinker
```

En tinker:
```php
Mail::raw('Prueba', function ($m) {
    $m->to('test@ejemplo.com')->subject('Test');
});
```

### Verificar en la Interfaz

1. Ve a `/admin/email/message-trace`
2. Configura filtros:
   - **Remitente**: notificaciones@meditecpty.com
   - **Fecha**: Hoy
3. Buscar
4. Deberías ver el correo de prueba

---

## 📝 Logs

### Ver Logs de Sincronización

```bash
tail -f storage/logs/laravel*.log | grep "Sent Items"
```

### Logs Exitosos

```
[timestamp] local.INFO: Correo guardado en Sent Items {"message_id":"AAMk...", "subject":"...", "recipients":1}
[timestamp] local.INFO: Correo sincronizado a Sent Items {"subject":"...", "recipients_count":1}
```

### Logs de Error

```
[timestamp] local.ERROR: Error guardando correo en Sent Items: ... {"subject":"...", "exception":"..."}
```

---

## ⚠️ Troubleshooting

### Error: "Access is denied" (403)

**Causa**: Permisos insuficientes o no otorgados

**Solución**:
1. Verifica que los permisos sean de tipo **Application**
2. **Grant admin consent** en Azure Portal
3. Espera 5 minutos (propagación)
4. `php artisan cache:clear`

### Los Correos No Aparecen

**Causa**: Queue worker no está corriendo

**Solución**:
```bash
php artisan queue:work
```

O verifica Supervisor:
```bash
sudo supervisorctl status meditech-queue-worker:*
```

### Token Expirado

**Causa**: El token de acceso se cachea por 1 hora

**Solución**:
```bash
php artisan cache:clear
```

---

## 🔒 Seguridad

- ✅ Client Secret debe estar en `.env` (nunca en código)
- ✅ Solo permisos Application (no Delegated)
- ✅ Acceso restringido a rol admin
- ✅ Tokens cacheados con expiración
- ✅ Logs de todas las operaciones

---

## 📊 Limitaciones

### Correos Históricos

- ❌ Solo correos **desde la fecha de implementación**
- ❌ Correos anteriores no se sincronizan retroactivamente
- ✅ Para correos históricos: usar Exchange Admin Center

### API de Microsoft Graph

- ⚠️ Límite de 999 mensajes por consulta
- ⚠️ Paginación requerida para más resultados
- ⚠️ Tokens expiran cada 1 hora (se renuevan automáticamente)

---

## 💡 Notas de Producción

1. **Queue Worker**: DEBE estar corriendo (usar Supervisor)
2. **Caché**: Los tokens se cachean por rendimiento
3. **Logs**: Revisar regularmente `storage/logs/`
4. **Permisos**: Verificar en Azure AD periódicamente
5. **Rotación de Secrets**: Cambiar client secret cada 12 meses

---

## 📦 Archivos del Sistema

```
app/
├── Services/
│   ├── MicrosoftGraphService.php
│   ├── ExchangeMessageTraceService.php
│   └── SendItemsSyncService.php
├── Listeners/
│   └── SaveEmailToSentItems.php
└── Livewire/Email/
    ├── OutboxDataTable.php
    └── MessageTraceTable.php

config/
└── services.php (configuración de microsoft)

resources/views/
├── email/
│   ├── outbox.blade.php
│   └── message-trace.blade.php
└── livewire/email/
    ├── outbox-data-table.blade.php
    └── message-trace-table.blade.php

routes/web/
└── admin.php (rutas de email)
```

---

## 🆘 Soporte

Para problemas:
1. Revisar logs: `storage/logs/laravel*.log`
2. Verificar permisos en Azure Portal
3. Verificar queue worker: `sudo supervisorctl status`
4. Limpiar caché: `php artisan cache:clear`

---

## ✅ Checklist de Producción

- [ ] Permisos configurados en Azure AD
- [ ] Admin consent otorgado
- [ ] Variables en `.env` configuradas
- [ ] Queue worker corriendo (Supervisor)
- [ ] Logs monitoreados
- [ ] Prueba de envío realizada
- [ ] Interfaz web accesible (`/admin/email/message-trace`)
