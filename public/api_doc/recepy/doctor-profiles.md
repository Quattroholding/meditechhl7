# API de Perfiles de Doctor - Sistema Recepy

Esta documentación describe los endpoints para gestionar los perfiles de doctores que generarán recetas médicas.

## Base URL
```
https://tu-dominio.com/api/recepy
```

## Autenticación Requerida

Todos los endpoints requieren autenticación mediante Bearer Token:
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json"
}
```

## Endpoints

### 1. Listar Perfiles de Doctores

**Endpoint:** `GET /doctor-profiles`

**Descripción:** Obtiene todos los perfiles de doctores activos.

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json"
}
```

**Response Success (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "logo": "recepy/logos/logo123.jpg",
      "address": "Av. Principal 123, Caracas, Venezuela",
      "phone": "+58412-1234567",
      "email": "consultorio@doctor.com",
      "signature": "recepy/signatures/firma123.png",
      "seal": "recepy/seals/sello123.png",
      "medical_license_number": "MED-12345",
      "is_active": true,
      "created_at": "2025-01-01T10:00:00.000000Z",
      "updated_at": "2025-01-01T10:00:00.000000Z",
      "user": {
        "id": 1,
        "first_name": "Dr. Juan",
        "last_name": "Pérez",
        "email": "doctor@example.com"
      }
    }
  ]
}
```

### 2. Obtener Perfil por ID

**Endpoint:** `GET /doctor-profiles/{id}`

**Descripción:** Obtiene un perfil de doctor específico con sus recetas relacionadas.

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "user_id": 1,
    "logo": "recepy/logos/logo123.jpg",
    "address": "Av. Principal 123, Caracas, Venezuela",
    "phone": "+58412-1234567",
    "email": "consultorio@doctor.com",
    "signature": "recepy/signatures/firma123.png",
    "seal": "recepy/seals/sello123.png",
    "medical_license_number": "MED-12345",
    "is_active": true,
    "created_at": "2025-01-01T10:00:00.000000Z",
    "updated_at": "2025-01-01T10:00:00.000000Z",
    "user": {
      "id": 1,
      "first_name": "Dr. Juan",
      "last_name": "Pérez",
      "email": "doctor@example.com"
    },
    "prescriptions": [
      {
        "id": 1,
        "prescription_number": "RX-2025-123456",
        "patient_name": "María González",
        "prescription_date": "2025-01-01",
        "status": "active"
      }
    ]
  }
}
```

**Response Error (404):**
```json
{
  "success": false,
  "message": "Perfil de doctor no encontrado"
}
```

### 3. Crear Perfil de Doctor

**Endpoint:** `POST /doctor-profiles`

**Descripción:** Crea un nuevo perfil de doctor.

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "Content-Type": "multipart/form-data"
}
```

**Request Body (Form Data):**
```
user_id: 1
address: "Av. Principal 123, Caracas, Venezuela"
phone: "+58412-1234567"
email: "consultorio@doctor.com"
medical_license_number: "MED-12345"
logo: (archivo de imagen)
signature: (archivo de imagen)
seal: (archivo de imagen)
```

**Validaciones:**
- `user_id`: Requerido, debe existir en la tabla users, único
- `address`: Opcional, texto
- `phone`: Opcional, máximo 20 caracteres
- `email`: Opcional, formato email válido
- `medical_license_number`: Opcional, texto
- `logo`: Opcional, imagen (jpeg, png, jpg), máximo 2MB
- `signature`: Opcional, imagen (jpeg, png, jpg), máximo 2MB
- `seal`: Opcional, imagen (jpeg, png, jpg), máximo 2MB

**Response Success (201):**
```json
{
  "success": true,
  "message": "Perfil de doctor creado exitosamente",
  "data": {
    "id": 2,
    "user_id": 1,
    "logo": "recepy/logos/logo456.jpg",
    "address": "Av. Principal 123, Caracas, Venezuela",
    "phone": "+58412-1234567",
    "email": "consultorio@doctor.com",
    "signature": "recepy/signatures/firma456.png",
    "seal": "recepy/seals/sello456.png",
    "medical_license_number": "MED-12345",
    "is_active": true,
    "created_at": "2025-01-01T12:00:00.000000Z",
    "updated_at": "2025-01-01T12:00:00.000000Z",
    "user": {
      "id": 1,
      "first_name": "Dr. Juan",
      "last_name": "Pérez",
      "email": "doctor@example.com"
    }
  }
}
```

**Response Error (422):**
```json
{
  "success": false,
  "message": "Error de validación",
  "errors": {
    "user_id": ["El usuario ya tiene un perfil de doctor."],
    "email": ["El email debe ser válido."],
    "logo": ["El logo debe ser una imagen."]
  }
}
```

### 4. Actualizar Perfil de Doctor

**Endpoint:** `PUT /doctor-profiles/{id}`

**Descripción:** Actualiza un perfil de doctor existente.

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "Content-Type": "multipart/form-data"
}
```

**Request Body (Form Data):**
```
address: "Nueva dirección actualizada"
phone: "+58412-9876543"
email: "nuevoemail@doctor.com"
medical_license_number: "MED-54321"
is_active: true
logo: (nuevo archivo de imagen opcional)
signature: (nuevo archivo de imagen opcional)
seal: (nuevo archivo de imagen opcional)
```

**Validaciones:**
- `address`: Opcional, texto
- `phone`: Opcional, máximo 20 caracteres
- `email`: Opcional, formato email válido
- `medical_license_number`: Opcional, texto
- `is_active`: Opcional, booleano
- `logo`: Opcional, imagen (jpeg, png, jpg), máximo 2MB
- `signature`: Opcional, imagen (jpeg, png, jpg), máximo 2MB
- `seal`: Opcional, imagen (jpeg, png, jpg), máximo 2MB

**Response Success (200):**
```json
{
  "success": true,
  "message": "Perfil de doctor actualizado exitosamente",
  "data": {
    "id": 1,
    "user_id": 1,
    "logo": "recepy/logos/nuevo_logo.jpg",
    "address": "Nueva dirección actualizada",
    "phone": "+58412-9876543",
    "email": "nuevoemail@doctor.com",
    "signature": "recepy/signatures/nueva_firma.png",
    "seal": "recepy/seals/nuevo_sello.png",
    "medical_license_number": "MED-54321",
    "is_active": true,
    "created_at": "2025-01-01T10:00:00.000000Z",
    "updated_at": "2025-01-01T14:00:00.000000Z",
    "user": {
      "id": 1,
      "first_name": "Dr. Juan",
      "last_name": "Pérez",
      "email": "doctor@example.com"
    }
  }
}
```

### 5. Eliminar Perfil de Doctor

**Endpoint:** `DELETE /doctor-profiles/{id}`

**Descripción:** Elimina un perfil de doctor y todos sus archivos asociados.

**Response Success (200):**
```json
{
  "success": true,
  "message": "Perfil de doctor eliminado exitosamente"
}
```

**Response Error (404):**
```json
{
  "success": false,
  "message": "Perfil de doctor no encontrado"
}
```

### 6. Obtener Perfil por Usuario

**Endpoint:** `GET /users/{userId}/doctor-profile`

**Descripción:** Obtiene el perfil de doctor de un usuario específico.

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "user_id": 1,
    "logo": "recepy/logos/logo123.jpg",
    "address": "Av. Principal 123, Caracas, Venezuela",
    "phone": "+58412-1234567",
    "email": "consultorio@doctor.com",
    "signature": "recepy/signatures/firma123.png",
    "seal": "recepy/seals/sello123.png",
    "medical_license_number": "MED-12345",
    "is_active": true,
    "created_at": "2025-01-01T10:00:00.000000Z",
    "updated_at": "2025-01-01T10:00:00.000000Z",
    "user": {
      "id": 1,
      "first_name": "Dr. Juan",
      "last_name": "Pérez",
      "email": "doctor@example.com"
    }
  }
}
```

**Response Error (404):**
```json
{
  "success": false,
  "message": "Perfil de doctor no encontrado para este usuario"
}
```

## Gestión de Archivos

### Subida de Archivos
- Los archivos se almacenan en `storage/app/public/recepy/`
- Tipos permitidos: JPEG, PNG, JPG
- Tamaño máximo: 2MB por archivo
- Los archivos antiguos se eliminan automáticamente al actualizar

### Acceso a Archivos
Los archivos subidos pueden accederse via:
```
https://tu-dominio.com/storage/{ruta_del_archivo}
```

Ejemplo:
```
https://tu-dominio.com/storage/recepy/logos/logo123.jpg
```

## Ejemplos de Uso en Flutter

### Crear Perfil con Imágenes
```dart
var request = http.MultipartRequest(
  'POST',
  Uri.parse('https://tu-dominio.com/api/recepy/doctor-profiles'),
);

request.headers.addAll({
  'Authorization': 'Bearer $token',
  'Accept': 'application/json',
});

request.fields.addAll({
  'user_id': '1',
  'address': 'Av. Principal 123, Caracas, Venezuela',
  'phone': '+58412-1234567',
  'email': 'consultorio@doctor.com',
  'medical_license_number': 'MED-12345',
});

// Agregar archivos
if (logoFile != null) {
  request.files.add(await http.MultipartFile.fromPath('logo', logoFile.path));
}
if (signatureFile != null) {
  request.files.add(await http.MultipartFile.fromPath('signature', signatureFile.path));
}
if (sealFile != null) {
  request.files.add(await http.MultipartFile.fromPath('seal', sealFile.path));
}

var response = await request.send();
```

### Obtener Lista de Perfiles
```dart
final response = await http.get(
  Uri.parse('https://tu-dominio.com/api/recepy/doctor-profiles'),
  headers: {
    'Authorization': 'Bearer $token',
    'Accept': 'application/json',
  },
);

if (response.statusCode == 200) {
  final data = jsonDecode(response.body);
  final profiles = data['data'] as List;
}
```