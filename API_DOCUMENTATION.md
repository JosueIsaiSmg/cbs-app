# 📚 Documentación de la API - Sistema de Entrevistas

## 📋 Información General

Esta API permite gestionar un sistema completo de entrevistas, incluyendo vacantes, prospectos y las entrevistas entre ellos.

- **Base URL**: `http://localhost/api`
- **Versión**: 1.0
- **Formato de respuesta**: JSON
- **Autenticación**: Bearer Token (Sanctum) ⚠️ **REQUERIDA**

---

## 🔐 Autenticación

**IMPORTANTE**: Todas las rutas de la API requieren autenticación con Bearer Token.

### 1. Crear un usuario (si no tienes uno)

```bash
# Acceder al tinker de Laravel
./vendor/bin/sail artisan tinker

# Crear un usuario de prueba
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => Hash::make('password123')
]);
```

### 2. Obtener token de autenticación

```bash
curl -X POST "http://localhost/api/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

**Respuesta exitosa:**
```json
{
    "user": {
        "id": 1,
        "name": "Test User",
        "email": "test@example.com"
    },
    "token": "1|abc123def456ghi789..."
}
```

### 3. Usar el token en las peticiones

Incluye el header de autorización en todas tus requests:

```http
Authorization: Bearer 1|abc123def456ghi789...
```

### 4. Cerrar sesión (logout)

```bash
curl -X POST "http://localhost/api/logout" \
  -H "Authorization: Bearer {tu_token}" \
  -H "Accept: application/json"
```

### 5. Error de autenticación

Si no incluyes el token o es inválido, recibirás:

```json
{
    "message": "Unauthenticated."
}
```

---

## 📊 Endpoints Disponibles

### 🎯 Entrevistas

#### 1. Obtener todas las entrevistas
```http
GET /api/entrevistas
```

**Headers requeridos:**
```http
Authorization: Bearer {tu_token}
Accept: application/json
```

**Respuesta exitosa (200):**
```json
{
    "success": true,
    "data": [
        {
            "vacante": 1,
            "prospecto": 1,
            "fecha_entrevista": "2024-01-15",
            "notas": "Entrevista técnica exitosa",
            "reclutado": true,
            "vacante": {
                "id": 1,
                "titulo": "Desarrollador Full Stack",
                "descripcion": "Desarrollo web con Laravel y Vue.js",
                "salario": 50000,
                "ubicacion": "Madrid",
                "tipo_contrato": "Indefinido",
                "estado": "activa"
            },
            "prospecto": {
                "id": 1,
                "nombre": "Juan Pérez",
                "email": "juan@example.com",
                "telefono": "+34 600 123 456",
                "cv": "Experiencia en Laravel y Vue.js",
                "experiencia": 3,
                "estado": "activo"
            }
        }
    ],
    "message": "Entrevistas obtenidas exitosamente"
}
```

#### 2. Crear una nueva entrevista
```http
POST /api/entrevistas
```

**Headers requeridos:**
```http
Authorization: Bearer {tu_token}
Accept: application/json
Content-Type: application/json
```

**Body:**
```json
{
    "vacante": 1,
    "prospecto": 1,
    "fecha_entrevista": "2024-01-15",
    "notas": "Entrevista técnica exitosa",
    "reclutado": true
}
```

**Respuesta exitosa (201):**
```json
{
    "success": true,
    "data": {
        "vacante": 1,
        "prospecto": 1,
        "fecha_entrevista": "2024-01-15",
        "notas": "Entrevista técnica exitosa",
        "reclutado": true
    },
    "message": "Entrevista creada exitosamente"
}
```

**Error de validación (422):**
```json
{
    "success": false,
    "message": "Datos de validación incorrectos",
    "errors": {
        "vacante": ["El campo vacante es obligatorio."],
        "prospecto": ["El campo prospecto es obligatorio."],
        "fecha_entrevista": ["El campo fecha entrevista es obligatorio."],
        "notas": ["El campo notas es obligatorio."],
        "reclutado": ["El campo reclutado es obligatorio."]
    }
}
```

**Error de duplicado (409):**
```json
{
    "success": false,
    "message": "Ya existe una entrevista para esta vacante y prospecto"
}
```

#### 3. Obtener una entrevista específica
```http
GET /api/entrevistas/{vacante}/{prospecto}
```

**Ejemplo:**
```http
GET /api/entrevistas/1/1
```

**Respuesta exitosa (200):**
```json
{
    "success": true,
    "data": {
        "vacante": 1,
        "prospecto": 1,
        "fecha_entrevista": "2024-01-15",
        "notas": "Entrevista técnica exitosa",
        "reclutado": true
    },
    "message": "Entrevista obtenida exitosamente"
}
```

**Error no encontrado (404):**
```json
{
    "success": false,
    "message": "Entrevista no encontrada"
}
```

#### 4. Actualizar una entrevista
```http
PUT /api/entrevistas/{vacante}/{prospecto}
```

**Body:**
```json
{
    "vacante": 1,
    "prospecto": 1,
    "fecha_entrevista": "2024-01-20",
    "notas": "Entrevista actualizada con éxito",
    "reclutado": false
}
```

**Respuesta exitosa (200):**
```json
{
    "success": true,
    "data": {
        "vacante": 1,
        "prospecto": 1,
        "fecha_entrevista": "2024-01-20",
        "notas": "Entrevista actualizada con éxito",
        "reclutado": false
    },
    "message": "Entrevista actualizada exitosamente"
}
```

#### 5. Eliminar una entrevista
```http
DELETE /api/entrevistas/{vacante}/{prospecto}
```

**Respuesta exitosa (200):**
```json
{
    "success": true,
    "message": "Entrevista eliminada exitosamente"
}
```

#### 6. Obtener entrevistas por vacante
```http
GET /api/entrevistas/vacante/{vacanteId}
```

**Ejemplo:**
```http
GET /api/entrevistas/vacante/1
```

#### 7. Obtener entrevistas por prospecto
```http
GET /api/entrevistas/prospecto/{prospectoId}
```

**Ejemplo:**
```http
GET /api/entrevistas/prospecto/1
```

---

### 💼 Vacantes

#### 1. Obtener todas las vacantes
```http
GET /api/vacantes
```

**Respuesta exitosa (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "titulo": "Desarrollador Full Stack",
            "descripcion": "Desarrollo web con Laravel y Vue.js",
            "salario": 50000,
            "ubicacion": "Madrid",
            "tipo_contrato": "Indefinido",
            "estado": "activa"
        }
    ],
    "message": "Vacantes obtenidas exitosamente"
}
```

#### 2. Crear una nueva vacante
```http
POST /api/vacantes
```

**Body:**
```json
{
    "titulo": "Desarrollador Backend",
    "descripcion": "Desarrollo de APIs con Laravel",
    "salario": 45000,
    "ubicacion": "Barcelona",
    "tipo_contrato": "Indefinido",
    "estado": "activa"
}
```

**Error de validación (422):**
```json
{
    "success": false,
    "message": "Datos de validación incorrectos",
    "errors": {
        "titulo": ["El campo título es obligatorio."],
        "descripcion": ["El campo descripción es obligatorio."],
        "estado": ["El estado seleccionado no es válido."]
    }
}
```

#### 3. Obtener una vacante específica
```http
GET /api/vacantes/{id}
```

#### 4. Actualizar una vacante
```http
PUT /api/vacantes/{id}
```

#### 5. Eliminar una vacante
```http
DELETE /api/vacantes/{id}
```

**Error si tiene entrevistas asociadas:**
```json
{
    "success": false,
    "message": "No se puede eliminar la vacante porque tiene entrevistas asociadas"
}
```

#### 6. Obtener vacantes activas
```http
GET /api/vacantes/activas
```

---

### 👥 Prospectos

#### 1. Obtener todos los prospectos
```http
GET /api/prospectos
```

**Respuesta exitosa (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nombre": "Juan Pérez",
            "email": "juan@example.com",
            "telefono": "+34 600 123 456",
            "cv": "Experiencia en Laravel y Vue.js",
            "experiencia": 3,
            "estado": "activo"
        }
    ],
    "message": "Prospectos obtenidos exitosamente"
}
```

#### 2. Crear un nuevo prospecto
```http
POST /api/prospectos
```

**Body:**
```json
{
    "nombre": "María García",
    "email": "maria@example.com",
    "telefono": "+34 600 654 321",
    "cv": "Desarrolladora con 5 años de experiencia",
    "experiencia": 5,
    "estado": "activo"
}
```

**Error de validación (422):**
```json
{
    "success": false,
    "message": "Datos de validación incorrectos",
    "errors": {
        "nombre": ["El campo nombre es obligatorio."],
        "email": ["El campo email debe ser una dirección de correo válida."],
        "email": ["El email ya está en uso."],
        "estado": ["El estado seleccionado no es válido."]
    }
}
```

#### 3. Obtener un prospecto específico
```http
GET /api/prospectos/{id}
```

#### 4. Actualizar un prospecto
```http
PUT /api/prospectos/{id}
```

#### 5. Eliminar un prospecto
```http
DELETE /api/prospectos/{id}
```

**Error si tiene entrevistas asociadas:**
```json
{
    "success": false,
    "message": "No se puede eliminar el prospecto porque tiene entrevistas asociadas"
}
```

#### 6. Obtener prospectos activos
```http
GET /api/prospectos/activos
```

#### 7. Buscar prospectos
```http
GET /api/prospectos/search?q=Juan
```

**Respuesta exitosa (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nombre": "Juan Pérez",
            "email": "juan@example.com"
        }
    ],
    "message": "Búsqueda de prospectos completada"
}
```

**Error si no se proporciona query (400):**
```json
{
    "success": false,
    "message": "Query parameter is required"
}
```

---

## 📊 Códigos de Estado HTTP

| Código | Descripción | Uso |
|--------|-------------|-----|
| **200** | OK | Operación exitosa (GET, PUT, DELETE) |
| **201** | Created | Recurso creado exitosamente (POST) |
| **400** | Bad Request | Solicitud incorrecta (parámetros faltantes) |
| **401** | Unauthorized | No autenticado (token faltante o inválido) |
| **404** | Not Found | Recurso no encontrado |
| **409** | Conflict | Conflicto (ej: entrevista duplicada) |
| **422** | Unprocessable Entity | Error de validación |
| **500** | Internal Server Error | Error del servidor |

---

## 🔧 Ejemplos de uso con cURL

### 1. Login para obtener token
```bash
curl -X POST "http://localhost/api/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

### 2. Obtener todas las entrevistas (con token)
```bash
curl -X GET "http://localhost/api/entrevistas" \
  -H "Authorization: Bearer {tu_token}" \
  -H "Accept: application/json"
```

### 3. Crear una entrevista (con token)
```bash
curl -X POST "http://localhost/api/entrevistas" \
  -H "Authorization: Bearer {tu_token}" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "vacante": 1,
    "prospecto": 1,
    "fecha_entrevista": "2024-01-15",
    "notas": "Entrevista técnica exitosa",
    "reclutado": true
  }'
```

### 4. Actualizar una entrevista (con token)
```bash
curl -X PUT "http://localhost/api/entrevistas/1/1" \
  -H "Authorization: Bearer {tu_token}" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "vacante": 1,
    "prospecto": 1,
    "fecha_entrevista": "2024-01-20",
    "notas": "Entrevista actualizada",
    "reclutado": false
  }'
```

### 5. Eliminar una entrevista (con token)
```bash
curl -X DELETE "http://localhost/api/entrevistas/1/1" \
  -H "Authorization: Bearer {tu_token}" \
  -H "Accept: application/json"
```

### 6. Buscar prospectos (con token)
```bash
curl -X GET "http://localhost/api/prospectos/search?q=Juan" \
  -H "Authorization: Bearer {tu_token}" \
  -H "Accept: application/json"
```

### 7. Logout
```bash
curl -X POST "http://localhost/api/logout" \
  -H "Authorization: Bearer {tu_token}" \
  -H "Accept: application/json"
```

---

## ⚠️ Notas importantes

### 1. **Autenticación obligatoria**
- **TODAS** las rutas de la API requieren autenticación
- Sin token válido recibirás error 401 "Unauthenticated"
- El token debe incluirse en el header `Authorization: Bearer {token}`

### 2. **Claves compuestas**
Las entrevistas usan claves compuestas (vacante + prospecto) en lugar de un ID único.

### 3. **Validaciones**
- Todos los endpoints validan los datos de entrada
- Los errores de validación devuelven código 422
- Los mensajes de error están en español

### 4. **Relaciones**
- Los datos incluyen las relaciones cargadas automáticamente
- Las entrevistas incluyen datos de vacante y prospecto

### 5. **Duplicados**
- Se previenen entrevistas duplicadas para la misma vacante y prospecto
- Los errores de duplicado devuelven código 409

### 6. **Integridad referencial**
- No se pueden eliminar vacantes o prospectos que tengan entrevistas asociadas
- Los errores de integridad devuelven mensajes descriptivos

### 7. **Logging**
- Todas las operaciones se registran en los logs
- Incluye información del usuario y detalles de la operación

---

## 🚀 Próximas mejoras

- [ ] Paginación para listados grandes
- [ ] Filtros avanzados por fecha, estado, etc.
- [ ] Exportación de datos (PDF, Excel)
- [ ] Notificaciones por email
- [ ] Dashboard con estadísticas

---

## 📞 Soporte

Para dudas o problemas con la API, contacta al equipo de desarrollo.

**Versión de la documentación**: 1.1  
**Última actualización**: Junio 2024 