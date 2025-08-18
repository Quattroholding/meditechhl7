# API Endpoints para Flutter - Historial Médico

## Autenticación
Todos los endpoints requieren autenticación usando `Bearer Token` (Sanctum).

```http
Authorization: Bearer {token}
```

## Endpoints de Historial Médico del Paciente

### 1. Historial Médico Completo

**Endpoint:** `GET /api/patient/medical-history`

**Descripción:** Devuelve el historial médico completo del paciente autenticado. Soporta paginación condicional.

**Query Parameters (opcionales):**
- `per_page`: Si se proporciona, activa la paginación para todas las colecciones (1-100, default: sin paginación)

**Headers:**
```http
Content-Type: application/json
Authorization: Bearer {token}
```

**Ejemplo de Uso:**
```http
GET /api/patient/medical-history                    # Sin paginación (formato original)
GET /api/patient/medical-history?per_page=10        # Con paginación (todas las colecciones)
```

**Respuesta de Ejemplo (sin paginación):**
```json
{
  "data": {
    "patient_info": {
      "id": 111,
      "fhir_id": "patient-111",
      "identifier": "8-1234-567",
      "identifier_type": "CC",
      "name": "Juan Carlos Pérez",
      "given_name": "Juan Carlos",
      "family_name": "Pérez",
      "gender": "male",
      "birth_date": "15-05-1985",
      "age": 39,
      "blood_type": "O+",
      "marital_status": "casado",
      "phone": "+507 6123-4567",
      "email": "juan.perez@email.com"
    },
    "medical_history": [
      {
        "id": 1,
        "fhir_id": "medical-history-1",
        "category": "medical",
        "title": "Hipertensión",
        "description": "Hipertensión arterial diagnosticada en 2020",
        "recorded_date": "15-01-2020",
        "occurrence_date": "10-01-2020",
        "clinical_status": "active",
        "verification_status": "confirmed"
      }
    ],
    "encounters": [
      {
        "id": 1,
        "fhir_id": "encounter-1",
        "status": "finished",
        "start": "15-08-2024 09:00",
        "end": "15-08-2024 09:30",
        "service_type": "Consulta General",
        "speciality": "Medicina General",
        "practitioner_name": "Dr. María González",
        "reason": "Control rutinario"
      }
    ],
    "conditions": [
      {
        "id": 1,
        "code": "I10",
        "description": "Hipertensión esencial (primaria)",
        "clinical_status": "active",
        "severity": "moderate",
        "onset_date": "10-01-2020",
        "recorded_date": "15-01-2020"
      }
    ],
    "vital_signs": [
      {
        "id": 1,
        "code": "blood-pressure",
        "description": "Presión Arterial",
        "value": "140/90",
        "unit": "mmHg",
        "effective_date": "15-08-2024",
        "observation_type": "Presión Arterial Sistólica/Diastólica"
      }
    ],
    "physical_exams": [
      {
        "id": 1,
        "code": "general-exam",
        "description": "Examen Físico General",
        "conclusion": "Paciente en buen estado general",
        "effective_date": "15-08-2024",
        "observation_type": "Examen Físico Completo"
      }
    ],
    "medications": [
      {
        "id": 1,
        "status": "active",
        "medication_name": "Losartan 50mg",
        "dosage": "1 tableta cada 24 horas",
        "route": "oral",
        "frequency": "diaria",
        "valid_from": "15-08-2024",
        "valid_to": "15-11-2024",
        "requester_name": "Dr. María González",
        "authored_on": "15-08-2024"
      }
    ],
    "services": [
      {
        "id": 1,
        "status": "completed",
        "display": "Laboratorio",
        "description": "Examen de sangre completo",
        "requesterName": "Dr. María González",
        "authoredOn": "15-08-2024",
        "occurrence_date": "16-08-2024"
      }
    ],
    "procedures": [
      {
        "id": 1,
        "code": "procedure-1",
        "status": "completed",
        "description": "Electrocardiograma",
        "performed_date": "15-08-2024",
        "outcome": "Normal"
      }
    ],
    "allergies": [
      {
        "id": 1,
        "title": "Penicilina",
        "description": "Alergia a penicilina - erupción cutánea",
        "recorded_date": "15-01-2020",
        "clinical_status": "active"
      }
    ],
    "last_updated": "2024-08-15T10:30:00.000000Z"
  }
}
```

**Respuesta de Ejemplo (con paginación):**
```json
{
  "data": {
    "patient_info": {
      "id": 111,
      "fhir_id": "patient-111",
      "name": "Juan Carlos Pérez",
      "given_name": "Juan Carlos",
      "family_name": "Pérez",
      "gender": "male",
      "birth_date": "15-05-1985",
      "age": 39,
      "blood_type": "O+",
      "marital_status": "casado",
      "phone": "+507 6123-4567",
      "email": "juan.perez@email.com"
    },
    "collections": {
      "medical_history": {
        "data": [
          {
            "id": 1,
            "fhir_id": "medical-history-1",
            "category": "medical",
            "title": "Hipertensión",
            "description": "Hipertensión arterial diagnosticada en 2020",
            "recorded_date": "2020-01-15",
            "occurrence_date": "2020-01-10",
            "clinical_status": "active",
            "verification_status": "confirmed"
          }
        ],
        "pagination": {
          "current_page": 1,
          "per_page": 10,
          "total": 5,
          "last_page": 1,
          "from": 1,
          "to": 5,
          "has_more_pages": false
        }
      },
      "conditions": {
        "data": [...],
        "pagination": {...}
      },
      "vital_signs": {
        "data": [...],
        "pagination": {...}
      },
      "medications": {
        "data": [...],
        "pagination": {...}
      }
      // ... otras colecciones con el mismo formato
    }
  }
}
```

### 2. Sección Específica del Historial

**Endpoint:** `GET /api/patient/medical-history/{section}`

**Descripción:** Devuelve una sección específica del historial médico.

**Parámetros de Ruta:**
- `section`: Una de las siguientes opciones:
  - `info` - Información básica del paciente
  - `medical-history` - Historial médico general
  - `encounters` - Consultas/encuentros
  - `conditions` - Condiciones/diagnósticos
  - `vital-signs` - Signos vitales
  - `physical-exams` - Exámenes físicos
  - `medications` - Medicamentos
  - `services` - Servicios solicitados
  - `procedures` - Procedimientos
  - `allergies` - Alergias

**Headers:**
```http
Content-Type: application/json
Authorization: Bearer {token}
```

**Ejemplo de Uso:**
```http
GET /api/patient/medical-history/conditions
```

**Respuesta de Ejemplo:**
```json
{
  "section": "conditions",
  "data": [
    {
      "id": 1,
      "code": "I10",
      "clinical_status": "active",
      "severity": "moderate",
      "description": "Hipertensión esencial (primaria)",
      "onset_date": "10-01-2020",
      "recorded_date": "15-01-2020"
    }
  ]
}
```

## Endpoint de Practitioners (Corregido)

### Lista de Médicos (Paginada)

**Endpoint:** `GET /api/practitioners`

**Descripción:** Devuelve la lista de médicos disponibles con paginación.

**Query Parameters (opcionales):**
- `page`: Número de página (default: 1)
- `per_page`: Elementos por página (default: 10, máximo: 50)
- `speciality_id`: Filtrar por especialidad médica
- `search`: Búsqueda multi-campo (insensible a mayúsculas/minúsculas)

**Headers:**
```http
Content-Type: application/json
Authorization: Bearer {token}
```

**Búsqueda Multi-Campo:**
El parámetro `search` busca en los siguientes campos:
- Nombre completo (`name`)
- Nombre (`given_name`)  
- Apellido (`family_name`)
- Email (`email`)
- Teléfono (`phone`)
- Identificador (`identifier`)
- Especialidades médicas (relación)
- Clientes/Clínicas asociadas (relación)

**Ejemplo de Uso:**
```http
GET /api/practitioners?page=1&per_page=10
GET /api/practitioners?page=2&per_page=5&search=María
GET /api/practitioners?page=1&per_page=10&speciality_id=1
GET /api/practitioners?search=Cardiología
GET /api/practitioners?search=@gmail.com
GET /api/practitioners?search=6123
```

**Respuesta de Ejemplo:**
```json
{
  "data": [
    {
      "id": 1,
      "fhir_id": "practitioner-1",
      "name": "Dr. María González",
      "given_name": "María",
      "family_name": "González",
      "email": "maria.gonzalez@clinic.com",
      "phone": "+507 6987-6543",
      "gender": "female",
      "birth_date": "15/03/1980",
      "qualification": "Médico General",
      "license_number": "MED-12345",
      "specialties": [
        {
          "id": 1,
          "name": "Medicina General",
          "description": "Atención médica integral"
        }
      ],
      "clients": [
        {
          "id": 1,
          "name": "Clínica San José",
          "code": "CSJ001"
        }
      ],
      "profile_photo": "https://app.com/storage/avatars/doctor1.jpg",
      "active": true,
      "created_at": "15-01-2024",
      "updated_at": "15-08-2024"
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "total": 71,
    "last_page": 8,
    "from": 1,
    "to": 10,
    "has_more_pages": true
  }
}
```

## Códigos de Estado HTTP

- **200**: Éxito
- **401**: No autorizado (token inválido o expirado)
- **404**: Paciente no encontrado
- **422**: Datos de validación incorrectos
- **500**: Error interno del servidor

## Notas para Implementación en Flutter

1. **Autenticación**: Guarda el token después del login y úsalo en todas las peticiones.

2. **Manejo de Errores**: Implementa manejo de errores para códigos 401 (redirigir al login), 404, etc.

3. **Paginación**: Para listas grandes, considera implementar paginación en el futuro.

4. **Cache**: Considera cachear los datos del historial médico para mejorar la experiencia de usuario.

5. **Formato de Fechas**: Las fechas se devuelven en formato "d-m-Y" o "d-m-Y H:i" para fácil display.

6. **Campos Opcionales**: Algunos campos pueden ser `null`, manéjalos apropiadamente en Flutter.

## Ejemplo de Uso en Flutter

```dart
// Obtener historial completo (sin paginación)
Future<Map<String, dynamic>> getMedicalHistory() async {
  final response = await http.get(
    Uri.parse('$baseUrl/api/patient/medical-history'),
    headers: {
      'Content-Type': 'application/json',
      'Authorization': 'Bearer $token',
    },
  );
  
  if (response.statusCode == 200) {
    return json.decode(response.body);
  } else {
    throw Exception('Failed to load medical history');
  }
}

// Obtener historial completo (con paginación)
Future<Map<String, dynamic>> getMedicalHistoryPaginated({int perPage = 10}) async {
  final uri = Uri.parse('$baseUrl/api/patient/medical-history').replace(
    queryParameters: {'per_page': perPage.toString()},
  );
  
  final response = await http.get(
    uri,
    headers: {
      'Content-Type': 'application/json',
      'Authorization': 'Bearer $token',
    },
  );
  
  if (response.statusCode == 200) {
    return json.decode(response.body);
  } else {
    throw Exception('Failed to load medical history');
  }
}

// Obtener sección específica
Future<Map<String, dynamic>> getMedicalHistorySection(String section) async {
  final response = await http.get(
    Uri.parse('$baseUrl/api/patient/medical-history/$section'),
    headers: {
      'Content-Type': 'application/json',
      'Authorization': 'Bearer $token',
    },
  );
  
  if (response.statusCode == 200) {
    return json.decode(response.body);
  } else {
    throw Exception('Failed to load $section');
  }
}

// Obtener practitioners paginados
Future<Map<String, dynamic>> getPractitioners({
  int page = 1,
  int perPage = 10,
  String? search,
  int? specialityId,
}) async {
  var queryParams = {
    'page': page.toString(),
    'per_page': perPage.toString(),
  };
  
  if (search != null && search.isNotEmpty) {
    queryParams['search'] = search;
  }
  
  if (specialityId != null) {
    queryParams['speciality_id'] = specialityId.toString();
  }
  
  final uri = Uri.parse('$baseUrl/api/practitioners').replace(
    queryParameters: queryParams,
  );
  
  final response = await http.get(
    uri,
    headers: {
      'Content-Type': 'application/json',
      'Authorization': 'Bearer $token',
    },
  );
  
  if (response.statusCode == 200) {
    return json.decode(response.body);
  } else {
    throw Exception('Failed to load practitioners');
  }
}

// Ejemplo de uso de paginación en Flutter
void loadPractitioners() async {
  try {
    final result = await getPractitioners(page: 1, perPage: 10);
    final practitioners = result['data'] as List;
    final pagination = result['pagination'] as Map<String, dynamic>;
    
    print('Loaded ${practitioners.length} practitioners');
    print('Page ${pagination['current_page']} of ${pagination['last_page']}');
    print('Total: ${pagination['total']} practitioners');
    
    // Usar los datos en tu UI
    setState(() {
      _practitioners = practitioners;
      _currentPage = pagination['current_page'];
      _hasMorePages = pagination['has_more_pages'];
    });
  } catch (e) {
    print('Error loading practitioners: $e');
  }
}
```