# API de Recetas Médicas - Sistema Recepy

Esta documentación describe los endpoints para gestionar las recetas médicas generadas por los doctores.

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

### 1. Listar Recetas

**Endpoint:** `GET /prescriptions`

**Descripción:** Obtiene todas las recetas con paginación y filtros opcionales.

**Parámetros de Consulta (Query Parameters):**
- `doctor_profile_id` (opcional): Filtrar por perfil de doctor
- `status` (opcional): Filtrar por estado (active, completed, cancelled)
- `date_from` (opcional): Filtrar desde fecha (YYYY-MM-DD)
- `date_to` (opcional): Filtrar hasta fecha (YYYY-MM-DD)
- `search` (opcional): Buscar por nombre de paciente o número de receta
- `page` (opcional): Número de página (default: 1)

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json"
}
```

**Ejemplo de URL con filtros:**
```
GET /prescriptions?doctor_profile_id=1&status=active&date_from=2025-01-01&search=María
```

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "doctor_profile_id": 1,
        "patient_name": "María González",
        "patient_document": "V-12345678",
        "patient_birth_date": "1985-05-15",
        "patient_gender": "F",
        "patient_address": "Calle 123, Caracas",
        "patient_phone": "+58412-1234567",
        "diagnosis": "Hipertensión arterial",
        "additional_notes": "Paciente con antecedentes familiares de HTA",
        "prescription_date": "2025-01-01",
        "prescription_number": "RX-2025-123456",
        "status": "active",
        "created_at": "2025-01-01T10:00:00.000000Z",
        "updated_at": "2025-01-01T10:00:00.000000Z",
        "doctor_profile": {
          "id": 1,
          "medical_license_number": "MED-12345",
          "user": {
            "id": 1,
            "first_name": "Dr. Juan",
            "last_name": "Pérez"
          }
        },
        "medications": [
          {
            "id": 1,
            "medication_name": "Enalapril",
            "presentation": "Tabletas",
            "concentration": "10mg",
            "dosage": "1 tableta",
            "frequency": "cada 12 horas",
            "duration": "30 días",
            "instructions": "Tomar con alimentos",
            "quantity": 60,
            "line_order": 1,
            "is_active": true
          }
        ]
      }
    ],
    "first_page_url": "https://tu-dominio.com/api/recepy/prescriptions?page=1",
    "from": 1,
    "last_page": 5,
    "last_page_url": "https://tu-dominio.com/api/recepy/prescriptions?page=5",
    "links": [...],
    "next_page_url": "https://tu-dominio.com/api/recepy/prescriptions?page=2",
    "path": "https://tu-dominio.com/api/recepy/prescriptions",
    "per_page": 15,
    "prev_page_url": null,
    "to": 15,
    "total": 75
  }
}
```

### 2. Obtener Receta por ID

**Endpoint:** `GET /prescriptions/{id}`

**Descripción:** Obtiene una receta específica con todos sus medicamentos activos.

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "doctor_profile_id": 1,
    "patient_name": "María González",
    "patient_document": "V-12345678",
    "patient_birth_date": "1985-05-15",
    "patient_gender": "F",
    "patient_address": "Calle 123, Caracas",
    "patient_phone": "+58412-1234567",
    "diagnosis": "Hipertensión arterial",
    "additional_notes": "Paciente con antecedentes familiares de HTA",
    "prescription_date": "2025-01-01",
    "prescription_number": "RX-2025-123456",
    "status": "active",
    "patient_age": 39,
    "created_at": "2025-01-01T10:00:00.000000Z",
    "updated_at": "2025-01-01T10:00:00.000000Z",
    "doctor_profile": {
      "id": 1,
      "logo": "recepy/logos/logo123.jpg",
      "address": "Av. Principal 123, Caracas",
      "phone": "+58412-1234567",
      "email": "consultorio@doctor.com",
      "signature": "recepy/signatures/firma123.png",
      "seal": "recepy/seals/sello123.png",
      "medical_license_number": "MED-12345",
      "user": {
        "id": 1,
        "first_name": "Dr. Juan",
        "last_name": "Pérez",
        "email": "doctor@example.com"
      }
    },
    "active_medications": [
      {
        "id": 1,
        "medication_name": "Enalapril",
        "presentation": "Tabletas",
        "concentration": "10mg",
        "dosage": "1 tableta",
        "frequency": "cada 12 horas",
        "duration": "30 días",
        "instructions": "Tomar con alimentos",
        "quantity": 60,
        "line_order": 1,
        "is_active": true,
        "full_medication_description": "Enalapril - Tabletas - 10mg",
        "full_instructions": "1 tableta, cada 12 horas, por 30 días. Tomar con alimentos"
      }
    ]
  }
}
```

**Response Error (404):**
```json
{
  "success": false,
  "message": "Receta no encontrada"
}
```

### 3. Crear Receta

**Endpoint:** `POST /prescriptions`

**Descripción:** Crea una nueva receta médica con sus medicamentos.

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json",
  "Content-Type": "application/json"
}
```

**Request Body:**
```json
{
  "doctor_profile_id": 1,
  "patient_name": "María González",
  "patient_document": "V-12345678",
  "patient_birth_date": "1985-05-15",
  "patient_gender": "F",
  "patient_address": "Calle 123, Caracas",
  "patient_phone": "+58412-1234567",
  "diagnosis": "Hipertensión arterial",
  "additional_notes": "Paciente con antecedentes familiares de HTA",
  "prescription_date": "2025-01-01",
  "prescription_number": "RX-2025-123456",
  "medications": [
    {
      "medication_name": "Enalapril",
      "presentation": "Tabletas",
      "concentration": "10mg",
      "dosage": "1 tableta",
      "frequency": "cada 12 horas",
      "duration": "30 días",
      "instructions": "Tomar con alimentos",
      "quantity": 60
    },
    {
      "medication_name": "Hidroclorotiazida",
      "presentation": "Tabletas",
      "concentration": "25mg",
      "dosage": "1/2 tableta",
      "frequency": "una vez al día",
      "duration": "30 días",
      "instructions": "Tomar en ayunas",
      "quantity": 15
    }
  ]
}
```

**Validaciones:**
- `doctor_profile_id`: Requerido, debe existir
- `patient_name`: Requerido, máximo 255 caracteres
- `patient_document`: Opcional, máximo 50 caracteres
- `patient_birth_date`: Opcional, fecha válida
- `patient_gender`: Opcional, valores: M, F, O
- `patient_address`: Opcional, texto
- `patient_phone`: Opcional, máximo 20 caracteres
- `diagnosis`: Opcional, texto
- `additional_notes`: Opcional, texto
- `prescription_date`: Requerido, fecha válida
- `prescription_number`: Opcional, único (se genera automáticamente si no se proporciona)
- `medications`: Requerido, array con al menos 1 medicamento
- `medications.*.medication_name`: Requerido para cada medicamento
- `medications.*.dosage`: Requerido para cada medicamento
- `medications.*.frequency`: Requerido para cada medicamento
- `medications.*.instructions`: Requerido para cada medicamento

**Response Success (201):**
```json
{
  "success": true,
  "message": "Receta creada exitosamente",
  "data": {
    "id": 2,
    "doctor_profile_id": 1,
    "patient_name": "María González",
    "patient_document": "V-12345678",
    "patient_birth_date": "1985-05-15",
    "patient_gender": "F",
    "patient_address": "Calle 123, Caracas",
    "patient_phone": "+58412-1234567",
    "diagnosis": "Hipertensión arterial",
    "additional_notes": "Paciente con antecedentes familiares de HTA",
    "prescription_date": "2025-01-01",
    "prescription_number": "RX-2025-654321",
    "status": "active",
    "created_at": "2025-01-01T12:00:00.000000Z",
    "updated_at": "2025-01-01T12:00:00.000000Z",
    "doctor_profile": {...},
    "active_medications": [...]
  }
}
```

**Response Error (422):**
```json
{
  "success": false,
  "message": "Error de validación",
  "errors": {
    "doctor_profile_id": ["El perfil de doctor es requerido."],
    "patient_name": ["El nombre del paciente es requerido."],
    "medications": ["Debe incluir al menos un medicamento."],
    "medications.0.medication_name": ["El nombre del medicamento es requerido."]
  }
}
```

### 4. Actualizar Receta

**Endpoint:** `PUT /prescriptions/{id}`

**Descripción:** Actualiza la información básica de una receta (no incluye medicamentos).

**Request Body:**
```json
{
  "patient_name": "María González Actualizada",
  "patient_document": "V-87654321",
  "patient_birth_date": "1985-05-15",
  "patient_gender": "F",
  "patient_address": "Nueva dirección",
  "patient_phone": "+58412-9876543",
  "diagnosis": "Hipertensión arterial estadio 2",
  "additional_notes": "Notas actualizadas",
  "prescription_date": "2025-01-02",
  "status": "completed"
}
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Receta actualizada exitosamente",
  "data": {
    "id": 1,
    "doctor_profile_id": 1,
    "patient_name": "María González Actualizada",
    "patient_document": "V-87654321",
    "patient_birth_date": "1985-05-15",
    "patient_gender": "F",
    "patient_address": "Nueva dirección",
    "patient_phone": "+58412-9876543",
    "diagnosis": "Hipertensión arterial estadio 2",
    "additional_notes": "Notas actualizadas",
    "prescription_date": "2025-01-02",
    "prescription_number": "RX-2025-123456",
    "status": "completed",
    "created_at": "2025-01-01T10:00:00.000000Z",
    "updated_at": "2025-01-01T14:00:00.000000Z",
    "doctor_profile": {...},
    "active_medications": [...]
  }
}
```

### 5. Eliminar Receta

**Endpoint:** `DELETE /prescriptions/{id}`

**Descripción:** Elimina una receta y todos sus medicamentos asociados.

**Response Success (200):**
```json
{
  "success": true,
  "message": "Receta eliminada exitosamente"
}
```

### 6. Actualizar Estado de Receta

**Endpoint:** `PATCH /prescriptions/{id}/status`

**Descripción:** Cambia únicamente el estado de una receta.

**Request Body:**
```json
{
  "status": "completed"
}
```

**Estados válidos:**
- `active`: Receta activa
- `completed`: Receta completada
- `cancelled`: Receta cancelada

**Response Success (200):**
```json
{
  "success": true,
  "message": "Estado de receta actualizado exitosamente",
  "data": {
    "id": 1,
    "status": "completed",
    "updated_at": "2025-01-01T15:00:00.000000Z"
  }
}
```

### 7. Obtener Recetas por Perfil de Doctor

**Endpoint:** `GET /doctor-profiles/{doctorProfileId}/prescriptions`

**Descripción:** Obtiene todas las recetas de un doctor específico con paginación.

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "patient_name": "María González",
        "prescription_date": "2025-01-01",
        "prescription_number": "RX-2025-123456",
        "status": "active",
        "medications": [...]
      }
    ],
    "first_page_url": "...",
    "from": 1,
    "last_page": 3,
    "per_page": 15,
    "total": 42
  }
}
```

## Estados de Receta

| Estado | Descripción |
|--------|-------------|
| `active` | Receta activa, puede ser modificada |
| `completed` | Receta completada, tratamiento finalizado |
| `cancelled` | Receta cancelada por el médico |

## Ejemplos de Uso en Flutter

### Crear Receta con Medicamentos
```dart
final prescriptionData = {
  'doctor_profile_id': 1,
  'patient_name': 'María González',
  'patient_document': 'V-12345678',
  'patient_birth_date': '1985-05-15',
  'patient_gender': 'F',
  'diagnosis': 'Hipertensión arterial',
  'prescription_date': '2025-01-01',
  'medications': [
    {
      'medication_name': 'Enalapril',
      'presentation': 'Tabletas',
      'concentration': '10mg',
      'dosage': '1 tableta',
      'frequency': 'cada 12 horas',
      'duration': '30 días',
      'instructions': 'Tomar con alimentos',
      'quantity': 60,
    }
  ],
};

final response = await http.post(
  Uri.parse('https://tu-dominio.com/api/recepy/prescriptions'),
  headers: {
    'Authorization': 'Bearer $token',
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
  body: jsonEncode(prescriptionData),
);
```

### Listar Recetas con Filtros
```dart
final queryParams = {
  'doctor_profile_id': '1',
  'status': 'active',
  'date_from': '2025-01-01',
  'search': 'María',
  'page': '1',
};

final uri = Uri.parse('https://tu-dominio.com/api/recepy/prescriptions')
    .replace(queryParameters: queryParams);

final response = await http.get(
  uri,
  headers: {
    'Authorization': 'Bearer $token',
    'Accept': 'application/json',
  },
);
```

### Cambiar Estado de Receta
```dart
final response = await http.patch(
  Uri.parse('https://tu-dominio.com/api/recepy/prescriptions/1/status'),
  headers: {
    'Authorization': 'Bearer $token',
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
  body: jsonEncode({'status': 'completed'}),
);
```