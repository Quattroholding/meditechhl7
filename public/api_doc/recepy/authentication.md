# API de Autenticación - Sistema Recepy

Esta documentación describe los endpoints de autenticación para acceder al sistema de generación de recetas médicas.

## Base URL
```
https://tu-dominio.com/api
```

## Endpoints de Autenticación

### 1. Iniciar Sesión (Login)

**Endpoint:** `POST /auth/login`

**Descripción:** Autentica al usuario en el sistema y devuelve un token de acceso.

**Headers:**
```json
{
  "Content-Type": "application/json",
  "Accept": "application/json"
}
```

**Request Body:**
```json
{
  "email": "doctor@example.com",
  "password": "password123"
}
```

**Validaciones:**
- `email`: Requerido, debe ser un email válido
- `password`: Requerido, mínimo 6 caracteres

**Response Success (200):**
```json
{
  "success": true,
  "message": "Login exitoso",
  "data": {
    "user": {
      "id": 1,
      "first_name": "Dr. Juan",
      "last_name": "Pérez",
      "email": "doctor@example.com",
      "email_verified_at": "2025-01-01T12:00:00.000000Z",
      "profile_picture": null,
      "created_at": "2025-01-01T10:00:00.000000Z",
      "updated_at": "2025-01-01T10:00:00.000000Z"
    },
    "token": "1|abc123def456ghi789jkl012mno345pqr678stu901vwx234yz",
    "token_type": "Bearer"
  }
}
```

**Response Error (401):**
```json
{
  "success": false,
  "message": "Credenciales inválidas"
}
```

**Response Error (422):**
```json
{
  "success": false,
  "message": "Error de validación",
  "errors": {
    "email": ["El campo email es obligatorio."],
    "password": ["El campo contraseña debe tener al menos 6 caracteres."]
  }
}
```

### 2. Registrar Usuario

**Endpoint:** `POST /auth/register`

**Descripción:** Registra un nuevo usuario en el sistema.

**Request Body:**
```json
{
  "first_name": "Dr. Juan",
  "last_name": "Pérez",
  "email": "doctor@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Validaciones:**
- `first_name`: Requerido, máximo 255 caracteres
- `last_name`: Requerido, máximo 255 caracteres
- `email`: Requerido, email válido, único en el sistema
- `password`: Requerido, mínimo 8 caracteres
- `password_confirmation`: Requerido, debe coincidir con password

**Response Success (201):**
```json
{
  "success": true,
  "message": "Usuario registrado exitosamente",
  "data": {
    "user": {
      "id": 2,
      "first_name": "Dr. Juan",
      "last_name": "Pérez",
      "email": "doctor@example.com",
      "email_verified_at": null,
      "profile_picture": null,
      "created_at": "2025-01-01T10:00:00.000000Z",
      "updated_at": "2025-01-01T10:00:00.000000Z"
    },
    "token": "2|abc123def456ghi789jkl012mno345pqr678stu901vwx234yz",
    "token_type": "Bearer"
  }
}
```

### 3. Obtener Usuario Autenticado

**Endpoint:** `GET /auth/user`

**Descripción:** Obtiene la información del usuario autenticado.

**Headers:**
```json
{
  "Authorization": "Bearer 1|abc123def456ghi789jkl012mno345pqr678stu901vwx234yz",
  "Accept": "application/json"
}
```

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "first_name": "Dr. Juan",
    "last_name": "Pérez",
    "email": "doctor@example.com",
    "email_verified_at": "2025-01-01T12:00:00.000000Z",
    "profile_picture": null,
    "created_at": "2025-01-01T10:00:00.000000Z",
    "updated_at": "2025-01-01T10:00:00.000000Z"
  }
}
```

### 4. Cerrar Sesión (Logout)

**Endpoint:** `POST /auth/logout`

**Descripción:** Cierra la sesión del usuario y revoca el token actual.

**Headers:**
```json
{
  "Authorization": "Bearer 1|abc123def456ghi789jkl012mno345pqr678stu901vwx234yz",
  "Accept": "application/json"
}
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Sesión cerrada exitosamente"
}
```

### 5. Recuperar Contraseña

**Endpoint:** `POST /auth/forgot-password`

**Descripción:** Envía un enlace de recuperación de contraseña al email del usuario.

**Request Body:**
```json
{
  "email": "doctor@example.com"
}
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Enlace de recuperación enviado a tu email"
}
```

### 6. Restablecer Contraseña

**Endpoint:** `POST /auth/reset-password`

**Descripción:** Restablece la contraseña del usuario usando el token de recuperación.

**Request Body:**
```json
{
  "token": "abc123def456ghi789",
  "email": "doctor@example.com",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Contraseña restablecida exitosamente"
}
```

## Manejo de Errores

### Códigos de Estado HTTP
- **200**: Éxito
- **201**: Creado exitosamente
- **401**: No autenticado
- **403**: No autorizado
- **404**: Recurso no encontrado
- **422**: Error de validación
- **500**: Error interno del servidor

### Estructura de Respuesta de Error
```json
{
  "success": false,
  "message": "Descripción del error",
  "errors": {
    "campo": ["Mensaje de error específico"]
  }
}
```

## Autenticación para Endpoints Protegidos

Todos los endpoints del sistema Recepy requieren autenticación mediante el token Bearer obtenido en el login.

**Formato del Header de Autorización:**
```
Authorization: Bearer {token}
```

**Ejemplo:**
```json
{
  "Authorization": "Bearer 1|abc123def456ghi789jkl012mno345pqr678stu901vwx234yz",
  "Accept": "application/json",
  "Content-Type": "application/json"
}
```

## Notas de Seguridad

1. **Tokens de Acceso**: Los tokens no tienen fecha de expiración automática, pero pueden ser revocados.
2. **HTTPS**: Siempre usar HTTPS en producción.
3. **Almacenamiento Seguro**: Almacenar tokens de forma segura en el dispositivo móvil.
4. **Logout**: Siempre llamar al endpoint de logout al cerrar la aplicación.
5. **Renovación**: No hay endpoint de refresh automático, usar login nuevamente si es necesario.

## Ejemplos de Uso en Flutter

### Login
```dart
final response = await http.post(
  Uri.parse('https://tu-dominio.com/api/auth/login'),
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  body: jsonEncode({
    'email': 'doctor@example.com',
    'password': 'password123',
  }),
);

if (response.statusCode == 200) {
  final data = jsonDecode(response.body);
  final token = data['data']['token'];
  // Guardar token para futuras peticiones
}
```

### Petición Autenticada
```dart
final response = await http.get(
  Uri.parse('https://tu-dominio.com/api/recepy/doctor-profiles'),
  headers: {
    'Authorization': 'Bearer $token',
    'Accept': 'application/json',
  },
);
```