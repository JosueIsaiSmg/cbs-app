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

### Tests Unitarios
- `EntrevistaTest.php` - Tests unitarios para el modelo Entrevista

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