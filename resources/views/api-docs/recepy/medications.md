# API de Medicamentos de Recetas - Sistema Recepy

Esta documentación describe los endpoints para gestionar los medicamentos individuales dentro de las recetas médicas.

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

Todos los endpoints de medicamentos están anidados bajo las recetas:
```
/prescriptions/{prescriptionId}/medications
```

### 1. Listar Medicamentos de una Receta

**Endpoint:** `GET /prescriptions/{prescriptionId}/medications`

**Descripción:** Obtiene todos los medicamentos de una receta específica ordenados por línea.

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
      "prescription_id": 1,
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
      "created_at": "2025-01-01T10:00:00.000000Z",
      "updated_at": "2025-01-01T10:00:00.000000Z",
      "full_medication_description": "Enalapril - Tabletas - 10mg",
      "full_instructions": "1 tableta, cada 12 horas, por 30 días. Tomar con alimentos"
    },
    {
      "id": 2,
      "prescription_id": 1,
      "medication_name": "Hidroclorotiazida",
      "presentation": "Tabletas",
      "concentration": "25mg",
      "dosage": "1/2 tableta",
      "frequency": "una vez al día",
      "duration": "30 días",
      "instructions": "Tomar en ayunas por la mañana",
      "quantity": 15,
      "line_order": 2,
      "is_active": true,
      "created_at": "2025-01-01T10:00:00.000000Z",
      "updated_at": "2025-01-01T10:00:00.000000Z",
      "full_medication_description": "Hidroclorotiazida - Tabletas - 25mg",
      "full_instructions": "1/2 tableta, una vez al día, por 30 días. Tomar en ayunas por la mañana"
    }
  ]
}
```

**Response Error (404):**
```json
{
  "success": false,
  "message": "Receta no encontrada"
}
```

### 2. Obtener Medicamento Específico

**Endpoint:** `GET /prescriptions/{prescriptionId}/medications/{medicationId}`

**Descripción:** Obtiene un medicamento específico de una receta.

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "prescription_id": 1,
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
    "created_at": "2025-01-01T10:00:00.000000Z",
    "updated_at": "2025-01-01T10:00:00.000000Z",
    "prescription": {
      "id": 1,
      "prescription_number": "RX-2025-123456",
      "patient_name": "María González",
      "prescription_date": "2025-01-01"
    }
  }
}
```

**Response Error (404):**
```json
{
  "success": false,
  "message": "Medicamento no encontrado"
}
```

### 3. Agregar Medicamento a Receta

**Endpoint:** `POST /prescriptions/{prescriptionId}/medications`

**Descripción:** Agrega un nuevo medicamento a una receta existente.

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
  "medication_name": "Losartán",
  "presentation": "Tabletas",
  "concentration": "50mg",
  "dosage": "1 tableta",
  "frequency": "una vez al día",
  "duration": "30 días",
  "instructions": "Tomar preferiblemente por la noche",
  "quantity": 30,
  "line_order": 3
}
```

**Validaciones:**
- `medication_name`: Requerido, máximo 255 caracteres
- `presentation`: Opcional, máximo 255 caracteres
- `concentration`: Opcional, máximo 255 caracteres
- `dosage`: Requerido, máximo 255 caracteres
- `frequency`: Requerido, máximo 255 caracteres
- `duration`: Opcional, máximo 255 caracteres
- `instructions`: Requerido, texto
- `quantity`: Opcional, entero positivo
- `line_order`: Opcional, entero positivo (se calcula automáticamente si no se proporciona)

**Response Success (201):**
```json
{
  "success": true,
  "message": "Medicamento agregado exitosamente",
  "data": {
    "id": 3,
    "prescription_id": 1,
    "medication_name": "Losartán",
    "presentation": "Tabletas",
    "concentration": "50mg",
    "dosage": "1 tableta",
    "frequency": "una vez al día",
    "duration": "30 días",
    "instructions": "Tomar preferiblemente por la noche",
    "quantity": 30,
    "line_order": 3,
    "is_active": true,
    "created_at": "2025-01-01T12:00:00.000000Z",
    "updated_at": "2025-01-01T12:00:00.000000Z"
  }
}
```

**Response Error (422):**
```json
{
  "success": false,
  "message": "Error de validación",
  "errors": {
    "medication_name": ["El nombre del medicamento es requerido."],
    "dosage": ["La dosis es requerida."],
    "frequency": ["La frecuencia es requerida."],
    "instructions": ["Las instrucciones son requeridas."]
  }
}
```

### 4. Actualizar Medicamento

**Endpoint:** `PUT /prescriptions/{prescriptionId}/medications/{medicationId}`

**Descripción:** Actualiza un medicamento existente en una receta.

**Request Body:**
```json
{
  "medication_name": "Losartán Potásico",
  "presentation": "Tabletas recubiertas",
  "concentration": "100mg",
  "dosage": "1/2 tableta",
  "frequency": "una vez al día",
  "duration": "60 días",
  "instructions": "Tomar preferiblemente por la noche con abundante agua",
  "quantity": 30,
  "line_order": 2,
  "is_active": true
}
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Medicamento actualizado exitosamente",
  "data": {
    "id": 3,
    "prescription_id": 1,
    "medication_name": "Losartán Potásico",
    "presentation": "Tabletas recubiertas",
    "concentration": "100mg",
    "dosage": "1/2 tableta",
    "frequency": "una vez al día",
    "duration": "60 días",
    "instructions": "Tomar preferiblemente por la noche con abundante agua",
    "quantity": 30,
    "line_order": 2,
    "is_active": true,
    "created_at": "2025-01-01T12:00:00.000000Z",
    "updated_at": "2025-01-01T14:00:00.000000Z"
  }
}
```

### 5. Eliminar Medicamento

**Endpoint:** `DELETE /prescriptions/{prescriptionId}/medications/{medicationId}`

**Descripción:** Elimina un medicamento de una receta.

**Response Success (200):**
```json
{
  "success": true,
  "message": "Medicamento eliminado exitosamente"
}
```

**Response Error (404):**
```json
{
  "success": false,
  "message": "Medicamento no encontrado"
}
```

### 6. Activar/Desactivar Medicamento

**Endpoint:** `PATCH /prescriptions/{prescriptionId}/medications/{medicationId}/toggle-active`

**Descripción:** Cambia el estado activo/inactivo de un medicamento sin eliminarlo.

**Response Success (200):**
```json
{
  "success": true,
  "message": "Estado del medicamento actualizado exitosamente",
  "data": {
    "id": 1,
    "medication_name": "Enalapril",
    "is_active": false,
    "updated_at": "2025-01-01T15:00:00.000000Z"
  }
}
```

### 7. Reordenar Medicamentos

**Endpoint:** `PUT /prescriptions/{prescriptionId}/medications/order`

**Descripción:** Actualiza el orden de múltiples medicamentos en una receta.

**Request Body:**
```json
{
  "medications": [
    {
      "id": 2,
      "line_order": 1
    },
    {
      "id": 1,
      "line_order": 2
    },
    {
      "id": 3,
      "line_order": 3
    }
  ]
}
```

**Validaciones:**
- `medications`: Requerido, array de objetos
- `medications.*.id`: Requerido, ID válido de medicamento existente
- `medications.*.line_order`: Requerido, entero positivo

**Response Success (200):**
```json
{
  "success": true,
  "message": "Orden de medicamentos actualizado exitosamente",
  "data": [
    {
      "id": 2,
      "medication_name": "Hidroclorotiazida",
      "line_order": 1,
      "updated_at": "2025-01-01T16:00:00.000000Z"
    },
    {
      "id": 1,
      "medication_name": "Enalapril",
      "line_order": 2,
      "updated_at": "2025-01-01T16:00:00.000000Z"
    },
    {
      "id": 3,
      "medication_name": "Losartán",
      "line_order": 3,
      "updated_at": "2025-01-01T16:00:00.000000Z"
    }
  ]
}
```

### 8. Actualización Masiva de Medicamentos

**Endpoint:** `PUT /prescriptions/{prescriptionId}/medications/bulk-update`

**Descripción:** Permite actualizar, crear y eliminar múltiples medicamentos en una sola operación.

**Request Body:**
```json
{
  "medications": [
    {
      "id": 1,
      "medication_name": "Enalapril Modificado",
      "presentation": "Tabletas",
      "concentration": "5mg",
      "dosage": "2 tabletas",
      "frequency": "cada 12 horas",
      "duration": "30 días",
      "instructions": "Tomar con alimentos",
      "quantity": 60,
      "line_order": 1,
      "is_active": true
    },
    {
      "medication_name": "Nuevo Medicamento",
      "presentation": "Cápsulas",
      "concentration": "20mg",
      "dosage": "1 cápsula",
      "frequency": "cada 24 horas",
      "duration": "15 días",
      "instructions": "Tomar en ayunas",
      "quantity": 15,
      "line_order": 2,
      "is_active": true
    }
  ]
}
```

**Comportamiento:**
- Medicamentos con `id` existente: se actualizan
- Medicamentos sin `id`: se crean nuevos
- Medicamentos existentes no incluidos en el array: se eliminan

**Response Success (200):**
```json
{
  "success": true,
  "message": "Medicamentos actualizados exitosamente",
  "data": [
    {
      "id": 1,
      "medication_name": "Enalapril Modificado",
      "concentration": "5mg",
      "line_order": 1,
      "updated_at": "2025-01-01T17:00:00.000000Z"
    },
    {
      "id": 4,
      "medication_name": "Nuevo Medicamento",
      "concentration": "20mg",
      "line_order": 2,
      "created_at": "2025-01-01T17:00:00.000000Z",
      "updated_at": "2025-01-01T17:00:00.000000Z"
    }
  ]
}
```

## Campos de Medicamento

### Campos Obligatorios
- `medication_name`: Nombre del medicamento
- `dosage`: Dosis a administrar
- `frequency`: Frecuencia de administración
- `instructions`: Instrucciones específicas

### Campos Opcionales
- `presentation`: Forma farmacéutica (tabletas, cápsulas, jarabe, etc.)
- `concentration`: Concentración del principio activo
- `duration`: Duración del tratamiento
- `quantity`: Cantidad a dispensar
- `line_order`: Orden en la receta (se calcula automáticamente)
- `is_active`: Estado del medicamento (default: true)

### Campos Calculados (Accessors)
- `full_medication_description`: Concatena nombre, presentación y concentración
- `full_instructions`: Formatea todas las instrucciones en una cadena legible

## Ejemplos de Uso en Flutter

### Agregar Medicamento
```dart
final medicationData = {
  'medication_name': 'Paracetamol',
  'presentation': 'Tabletas',
  'concentration': '500mg',
  'dosage': '1 tableta',
  'frequency': 'cada 8 horas',
  'duration': '5 días',
  'instructions': 'Tomar con abundante agua',
  'quantity': 15,
};

final response = await http.post(
  Uri.parse('https://tu-dominio.com/api/recepy/prescriptions/1/medications'),
  headers: {
    'Authorization': 'Bearer $token',
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
  body: jsonEncode(medicationData),
);
```

### Reordenar Medicamentos
```dart
final reorderData = {
  'medications': [
    {'id': 2, 'line_order': 1},
    {'id': 1, 'line_order': 2},
    {'id': 3, 'line_order': 3},
  ],
};

final response = await http.put(
  Uri.parse('https://tu-dominio.com/api/recepy/prescriptions/1/medications/order'),
  headers: {
    'Authorization': 'Bearer $token',
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
  body: jsonEncode(reorderData),
);
```

### Activar/Desactivar Medicamento
```dart
final response = await http.patch(
  Uri.parse('https://tu-dominio.com/api/recepy/prescriptions/1/medications/1/toggle-active'),
  headers: {
    'Authorization': 'Bearer $token',
    'Accept': 'application/json',
  },
);
```

### Actualización Masiva
```dart
final bulkUpdateData = {
  'medications': [
    {
      'id': 1, // Actualizar existente
      'medication_name': 'Medicamento Actualizado',
      'dosage': '2 tabletas',
      'frequency': 'cada 12 horas',
      'instructions': 'Nuevas instrucciones',
      'line_order': 1,
      'is_active': true,
    },
    {
      // Crear nuevo (sin ID)
      'medication_name': 'Nuevo Medicamento',
      'dosage': '1 cápsula',
      'frequency': 'una vez al día',
      'instructions': 'Tomar por la mañana',
      'line_order': 2,
      'is_active': true,
    }
  ],
};

final response = await http.put(
  Uri.parse('https://tu-dominio.com/api/recepy/prescriptions/1/medications/bulk-update'),
  headers: {
    'Authorization': 'Bearer $token',
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
  body: jsonEncode(bulkUpdateData),
);
```