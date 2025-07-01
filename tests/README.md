# Testing en Laravel con Inertia y Vue.js

Este directorio contiene los tests unitarios y de integración para la aplicación de entrevistas.

## 🐳 Usando Laravel Sail (Docker)

### Configuración de Sail
```bash
# Configurar alias para Sail
alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'

# O usar directamente
./vendor/bin/sail up -d
```

### Ventajas de usar Sail
- ✅ **Entorno consistente** - Mismo entorno en todos los equipos
- ✅ **Aislamiento** - No interfiere con tu PHP local
- ✅ **Dependencias incluidas** - MySQL, Redis, etc. ya configurados
- ✅ **Fácil setup** - Un comando para todo el entorno

## 📁 Estructura de Tests

### Tests de Feature (Integración)
- `EntrevistaControllerTest.php` - Tests para el controlador de entrevistas
- `EntrevistaValidationTest.php` - Tests de validación de formularios
- `EntrevistaAuthenticationTest.php` - Tests de autenticación

### Tests de API
- `Api/EntrevistaApiTest.php` - Tests para el controlador API de entrevistas
- `Api/VacanteApiTest.php` - Tests para el controlador API de vacantes
- `Api/ProspectoApiTest.php` - Tests para el controlador API de prospectos
- `Api/AuthApiTest.php` - Tests para el controlador de autenticación API

### Tests Unitarios
- `EntrevistaTest.php` - Tests unitarios para el modelo Entrevista
- `EntrevistaServiceTest.php` - Tests unitarios para el servicio de entrevistas
- `VacanteServiceTest.php` - Tests unitarios para el servicio de vacantes
- `ProspectoServiceTest.php` - Tests unitarios para el servicio de prospectos

### Tests de Integración
- `Integration/EntrevistaIntegrationTest.php` - Tests de integración entre servicios y controladores

### Tests de Rendimiento
- `Performance/ServicePerformanceTest.php` - Tests de rendimiento para grandes volúmenes de datos

## 🚀 Cómo ejecutar los tests

### Ejecutar todos los tests
```bash
# Con Sail
sail artisan test

# Sin Sail (PHP local)
php artisan test
```

### Ejecutar tests específicos
```bash
# Solo tests de feature
sail artisan test --testsuite=Feature

# Solo tests unitarios
sail artisan test --testsuite=Unit

# Test específico
sail artisan test tests/Feature/EntrevistaControllerTest.php

# Con filtro por método
sail artisan test --filter=test_method_name
```

### Ejecutar tests con cobertura
```bash
# Con Sail
sail artisan test --coverage

# Con Sail y mínimo de cobertura
sail artisan test --coverage --min=80
```

### Ejecutar tests en paralelo
```bash
# Con Sail
sail artisan test --parallel

# Especificar número de procesos
sail artisan test --parallel --processes=4
```

### Ejecutar tests con información detallada
```bash
# Con Sail y verbose
sail artisan test --verbose

# Con Sail y debug
sail artisan test --env=testing --debug
```

## 🧪 Tipos de Tests

### 1. Tests de Controlador (Feature)
```php
/** @test */
public function it_can_display_entrevistas_index()
{
    // Crear datos de prueba
    $vacante = Vacante::factory()->create();
    $prospecto = Prospecto::factory()->create();
    
    // Hacer la petición
    $response = $this->get(route('entrevistas.index'));
    
    // Verificar la respuesta
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Entrevistas/Index')
        ->has('entrevistas', 1)
    );
}
```

### 2. Tests de Validación
```php
/** @test */
public function vacante_is_required()
{
    $response = $this->post(route('entrevistas.store'), [
        // Datos sin vacante
    ]);
    
    $response->assertSessionHasErrors(['vacante']);
}
```

### 3. Tests de Autenticación
```php
/** @test */
public function unauthenticated_users_cannot_access_entrevistas_index()
{
    $response = $this->get(route('entrevistas.index'));
    
    $response->assertRedirect(route('login'));
}
```

### 4. Tests Unitarios
```php
/** @test */
public function it_can_create_entrevista()
{
    $entrevista = Entrevista::create([
        'vacante' => $vacante->id,
        'prospecto' => $prospecto->id,
        // ... otros campos
    ]);
    
    $this->assertInstanceOf(Entrevista::class, $entrevista);
}
```

## 🔧 Configuración

### Base de datos de testing
Los tests usan SQLite en memoria para mayor velocidad:
```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

### Factories
Los tests utilizan factories para crear datos de prueba:
- `Vacante::factory()->create()`
- `Prospecto::factory()->create()`
- `User::factory()->create()`

### Configuración de Sail para testing
```bash
# Asegurar que Sail esté corriendo
sail up -d

# Verificar que la BD de testing esté configurada
sail artisan config:cache

# Ejecutar migraciones de testing si es necesario
sail artisan migrate --env=testing
```

## 📊 Assertions específicas de Inertia

### Verificar componente renderizado
```php
$response->assertInertia(fn (Assert $page) => $page
    ->component('Entrevistas/Index')
);
```

### Verificar datos pasados
```php
$response->assertInertia(fn (Assert $page) => $page
    ->has('entrevistas', 1)
    ->where('entrevistas.0.vacante', $vacante->id)
);
```

### Verificar estructura de datos
```php
$response->assertInertia(fn (Assert $page) => $page
    ->has('entrevistas')
    ->has('vacantes')
    ->has('prospectos')
);
```

## 🎯 Mejores Prácticas

1. **Usar RefreshDatabase** para limpiar la BD entre tests
2. **Crear datos de prueba** con factories
3. **Autenticar usuarios** cuando sea necesario
4. **Usar nombres descriptivos** para los métodos de test
5. **Agrupar tests relacionados** en la misma clase
6. **Verificar tanto casos exitosos como de error**
7. **Usar Sail consistentemente** para evitar problemas de entorno

## 🔌 Tests de API

### Ejecutar tests de API
```bash
# Todos los tests de API
sail artisan test tests/Feature/Api/

# Test específico de API
sail artisan test tests/Feature/Api/EntrevistaApiTest.php

# Con filtro
sail artisan test --filter=EntrevistaApiTest
```

### Estructura de tests de API
```php
/** @test */
public function it_can_create_an_interview_via_api()
{
    $data = [
        'vacante_id' => $this->vacante->id,
        'prospecto_id' => $this->prospecto->id,
        'fecha' => '2024-01-15',
        'hora' => '14:30',
        'tipo' => 'presencial',
        'estado' => 'programada'
    ];

    $response = $this->postJson('/api/entrevistas', $data);

    $response->assertStatus(201)
            ->assertJson([
                'message' => 'Entrevista creada exitosamente'
            ]);
}
```

### Autenticación en tests de API
```php
// Usar Sanctum para autenticación
Sanctum::actingAs($user);

// O crear token manualmente
$token = $user->createToken('test-token')->plainTextToken;
$response = $this->withHeaders([
    'Authorization' => 'Bearer ' . $token,
])->getJson('/api/entrevistas');
```

## 🏗️ Tests de Servicios

### Ejecutar tests de servicios
```bash
# Todos los tests de servicios
sail artisan test tests/Unit/*ServiceTest.php

# Test específico
sail artisan test tests/Unit/EntrevistaServiceTest.php
```

### Estructura de tests de servicios
```php
/** @test */
public function it_can_create_an_interview_via_service()
{
    $data = [
        'vacante_id' => $this->vacante->id,
        'prospecto_id' => $this->prospecto->id,
        'fecha' => '2024-01-15',
        'hora' => '14:30',
        'tipo' => 'presencial',
        'estado' => 'programada'
    ];

    $entrevista = $this->entrevistaService->create($data);

    $this->assertInstanceOf(Entrevista::class, $entrevista);
    $this->assertEquals($data['fecha'], $entrevista->fecha);
}
```

### Tests de validación en servicios
```php
/** @test */
public function it_validates_required_fields()
{
    $this->expectException(ValidationException::class);
    $this->entrevistaService->create([]);
}
```

## 🔗 Tests de Integración

### Ejecutar tests de integración
```bash
# Tests de integración
sail artisan test tests/Feature/Integration/

# Test específico
sail artisan test tests/Feature/Integration/EntrevistaIntegrationTest.php
```

### Propósito de tests de integración
- Verificar interacción entre servicios y controladores
- Probar flujos completos de la aplicación
- Validar consistencia de datos entre capas
- Detectar problemas de integración

## ⚡ Tests de Rendimiento

### Ejecutar tests de rendimiento
```bash
# Tests de rendimiento
sail artisan test tests/Performance/

# Con información de tiempo
sail artisan test tests/Performance/ServicePerformanceTest.php --verbose
```

### Propósito de tests de rendimiento
- Verificar comportamiento con grandes volúmenes de datos
- Medir tiempos de respuesta
- Detectar problemas de memoria
- Validar escalabilidad de la aplicación

### Ejemplo de test de rendimiento
```php
/** @test */
public function it_handles_large_number_of_interviews_efficiently()
{
    $startTime = microtime(true);
    
    // Crear 1000 entrevistas
    for ($i = 0; $i < 1000; $i++) {
        $this->entrevistaService->create($data);
    }
    
    $creationTime = microtime(true) - $startTime;
    
    // Verificar que el tiempo es razonable
    $this->assertLessThan(5.0, $creationTime, 'Creación demasiado lenta');
}
```

## 🔍 Debugging Tests

### Ver errores detallados
```bash
# Con Sail
sail artisan test --verbose

# Sin Sail
php artisan test --verbose
```

### Ejecutar test específico con debug
```bash
# Con Sail
sail artisan test --filter=test_method_name --verbose

# Sin Sail
php artisan test --filter=test_method_name --verbose
```

### Ver queries SQL ejecutadas
```bash
# Con Sail
sail artisan test --env=testing --debug

# Sin Sail
php artisan test --env=testing --debug
```

### Debugging con Sail
```bash
# Acceder al contenedor de Sail
sail shell

# Dentro del contenedor, ejecutar tests
php artisan test --verbose

# Ver logs de Sail
sail logs
```

## 📈 Cobertura de Código

Para generar reportes de cobertura con Sail:
```bash
# Instalar Xdebug en el contenedor (si no está)
sail shell
apt-get update && apt-get install -y php-xdebug

# Ejecutar tests con cobertura
sail artisan test --coverage --min=80

# Generar reporte HTML
sail artisan test --coverage --coverage-html=coverage/
```

## 🚨 Troubleshooting

### Error: "Class 'Tests\TestCase' not found"
```bash
# Con Sail
sail composer dump-autoload

# Sin Sail
composer dump-autoload
```

### Error: "Database connection failed"
```bash
# Verificar que Sail esté corriendo
sail up -d

# Verificar configuración de BD
sail artisan config:clear
sail artisan config:cache
```

### Error: "Inertia assertion failed"
Verificar que el componente Vue existe y se renderiza correctamente.

### Error: "Permission denied" con Sail
```bash
# Dar permisos al directorio
chmod -R 777 storage bootstrap/cache

# O usar Sail con permisos
sail up -d --build
```

### Error: "Container not found"
```bash
# Reconstruir contenedores
sail down
sail build --no-cache
sail up -d
```

## 🐳 Comandos útiles de Sail

```bash
# Iniciar servicios
sail up -d

# Detener servicios
sail down

# Ver logs
sail logs

# Acceder al shell
sail shell

# Ejecutar composer
sail composer install

# Ejecutar artisan
sail artisan migrate

# Ejecutar npm
sail npm install
sail npm run dev

# Ver estado de contenedores
sail ps
```

## 📋 Workflow recomendado con Sail

```bash
# 1. Iniciar entorno
sail up -d

# 2. Instalar dependencias
sail composer install
sail npm install

# 3. Configurar BD
sail artisan migrate
sail artisan db:seed

# 4. Ejecutar tests
sail artisan test

# 5. Desarrollo
sail npm run dev

# 6. Al terminar
sail down
``` 