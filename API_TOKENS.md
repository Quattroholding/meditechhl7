# Sistema de Tokens API - Meditech2

## 🔐 Descripción

El sistema de tokens API de Meditech2 proporciona una forma segura de acceder a la API sin necesidad de usuarios específicos (no son ni pacientes ni practitioners). Estos tokens incluyen restricciones por IP y sistemas de permisos granulares.

## ✨ Características

- **Autenticación sin usuarios**: Tokens independientes no vinculados a usuarios
- **Restricciones por IP**: Control de acceso basado en direcciones IP específicas o rangos CIDR
- **Permisos granulares**: Sistema de scopes para controlar acceso a recursos específicos
- **Expiración configurable**: Tokens con fechas de expiración opcionales
- **Auditoría completa**: Registro de uso y intentos no autorizados
- **Caché inteligente**: Sistema de caché para optimizar rendimiento
- **Múltiples formas de autenticación**: Bearer token, header personalizado o query parameter

## 🚀 Generación de Tokens

### Comando Artisan

```bash
# Token básico con acceso total desde cualquier IP
php artisan api:generate-token "Nombre del Token"

# Token con restricción de IP específica
php artisan api:generate-token "Server Interno" --ips="192.168.1.100"

# Token con rango CIDR
php artisan api:generate-token "Red Local" --ips="192.168.1.0/24"

# Token con múltiples IPs
php artisan api:generate-token "Servidores" --ips="10.0.0.1,10.0.0.2,192.168.1.0/24"

# Token con fecha de expiración
php artisan api:generate-token "Token Temporal" --expires="2025-12-31"

# Token con permisos específicos
php artisan api:generate-token "Solo Lectura" --scopes="read:appointments,read:practitioners"

# Token completo con todas las opciones
php artisan api:generate-token "API Externa" \
  --ips="203.0.113.0/24" \
  --scopes="read:*,write:appointments" \
  --expires="2025-06-30" \
  --description="Token para sistema externo de citas"
```

## 🔗 Uso de la API

### Rutas Disponibles

Los tokens API tienen acceso a las siguientes rutas bajo el prefijo `/api/v1/`:

```
GET  /api/v1/practitioners
GET  /api/v1/practitioners/{id}/availability
GET  /api/v1/practitioners/{id}/consulting-rooms
GET  /api/v1/appointments
POST /api/v1/appointments
PUT  /api/v1/appointments/{id}
DELETE /api/v1/appointments/{id}
GET  /api/v1/medicines
GET  /api/v1/branches
GET  /api/v1/consulting-rooms
GET  /api/v1/medical-specialities
```

### Métodos de Autenticación

#### 1. Bearer Token (Recomendado)
```bash
curl -H "Authorization: Bearer mdt_vB76IWeUsXv6tVuo6uwSRblL1eYXmmAW894CY56mo0vYBYFfFDUmdtrrt4LO" \
     https://api.meditech.com/api/v1/practitioners
```

#### 2. Header Personalizado
```bash
curl -H "X-API-Token: mdt_vB76IWeUsXv6tVuo6uwSRblL1eYXmmAW894CY56mo0vYBYFfFDUmdtrrt4LO" \
     https://api.meditech.com/api/v1/practitioners
```

#### 3. Query Parameter (Solo para testing)
```bash
curl "https://api.meditech.com/api/v1/practitioners?api_token=mdt_vB76IWeUsXv6tVuo6uwSRblL1eYXmmAW894CY56mo0vYBYFfFDUmdtrrt4LO"
```

## 🛡️ Configuración de Seguridad

### Restricciones por IP

- **IP específica**: `192.168.1.100`
- **Rango CIDR**: `192.168.1.0/24`
- **Múltiples IPs**: `["192.168.1.100", "10.0.0.1", "203.0.113.0/24"]`
- **Todas las IPs**: `["*"]` (usar con precaución)

### Sistema de Scopes

- **Acceso total**: `["*"]`
- **Solo lectura**: `["read:*"]`
- **Recurso específico**: `["read:appointments", "write:appointments"]`
- **Patrones soportados**:
  - `read:*` - Lectura de todos los recursos
  - `write:*` - Escritura en todos los recursos
  - `read:appointments` - Solo lectura de citas
  - `write:practitioners` - Solo escritura de practitioners

## 📊 Monitoreo y Auditoría

### Información de Uso
- **last_used_at**: Última vez que se usó el token
- **last_used_ip**: Última IP desde donde se usó
- **Logs de seguridad**: Intentos no autorizados registrados

### Estados de Token
- **active**: Token activo y funcional
- **inactive**: Token desactivado manualmente
- **expired**: Token expirado por fecha

## 🔧 Gestión Programática

### Validación en Controladores

```php
// El middleware automáticamente agrega información del token al request
public function index(Request $request)
{
    $tokenName = $request->get('token_name');
    $tokenScopes = $request->get('token_scopes');
    $apiToken = $request->get('api_token'); // Instancia del modelo
    
    // Tu lógica aquí...
}
```

### Verificación Manual

```php
use App\Models\ApiToken;

$token = ApiToken::where('token', $tokenString)->active()->first();
if ($token && $token->isValidForIp($clientIp)) {
    // Token válido
}
```

## ⚡ Optimizaciones

### Sistema de Caché
- Las validaciones de tokens se cachean por 5 minutos
- Mejora el rendimiento en aplicaciones con alto tráfico
- Cache keys incluyen token, IP y scope para máxima precisión

### Actualizaciones Asíncronas
- La información de "último uso" se actualiza de forma asíncrona
- No impacta el tiempo de respuesta de las peticiones

## 🚨 Seguridad

### Mejores Prácticas

1. **Rotar tokens regularmente**: Especialmente los de larga duración
2. **Usar restricciones IP**: Siempre que sea posible
3. **Scopes mínimos**: Dar solo los permisos necesarios
4. **Monitorear logs**: Revisar intentos de acceso no autorizado
5. **Fechas de expiración**: Para tokens temporales

### Alertas Automáticas

El sistema registra automáticamente:
- Intentos de acceso con tokens inválidos
- Intentos desde IPs no autorizadas
- Uso de tokens expirados

## 📝 Ejemplos de Casos de Uso

### 1. Integración con Sistema Externo
```bash
# Token para sistema de terceros con acceso limitado
php artisan api:generate-token "Sistema Hospital XYZ" \
  --ips="203.0.113.50" \
  --scopes="read:practitioners,read:appointments" \
  --expires="2025-12-31" \
  --description="Integración con hospital XYZ para consulta de doctores"
```

### 2. Servidor de Monitoreo Interno
```bash
# Token para monitoreo desde red interna
php artisan api:generate-token "Monitoreo Nagios" \
  --ips="192.168.1.0/24" \
  --scopes="read:*" \
  --description="Token para sistema de monitoreo interno"
```

### 3. API de Desarrollo
```bash
# Token temporal para desarrollo y testing
php artisan api:generate-token "Dev Testing" \
  --ips="*" \
  --expires="2025-09-30" \
  --description="Token temporal para desarrollo y testing"
```

## 🔍 Resolución de Problemas

### Errores Comunes

1. **"Token no proporcionado"**: Verificar headers de autenticación
2. **"Token inválido o IP no autorizada"**: Revisar token y IP del cliente
3. **"Insufficient scope"**: Token no tiene permisos para el recurso

### Comandos de Diagnóstico

```bash
# Ver todos los tokens
php artisan tinker
>>> App\Models\ApiToken::all(['name', 'token', 'active', 'expires_at']);

# Verificar token específico
>>> $token = App\Models\ApiToken::where('name', 'Test Token')->first();
>>> $token->isValidForIp('192.168.1.100');
>>> $token->hasScope('read:appointments');
```

---

## 🔄 Migración

Este sistema se desplegó con la migración `2025_09_03_082217_create_api_tokens_table.php`

Para verificar que todo esté funcionando:

```bash
# Verificar tabla
php artisan tinker
>>> Schema::hasTable('api_tokens');

# Crear token de prueba
php artisan api:generate-token "Test API"

# Probar endpoint
curl -H "Authorization: Bearer [TOKEN]" http://localhost:8000/api/v1/practitioners
```