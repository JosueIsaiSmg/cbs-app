# 📋 Sistema de Gestión de Entrevistas

Este proyecto es una aplicación web y API REST para la gestión de entrevistas, vacantes y prospectos, desarrollada con **Laravel**, **Inertia.js** y **Vue.js**.

---

flowchart TD
    A["Usuario (Navegador)"] -- Llenar formulario y enviar --> B["Vue.js (Inertia Form)"]
    B -- POST /entrevistas --> C["Laravel (Controlador Entrevista)"]
    C -- Validación y lógica --> D["Servicio Entrevista"]
    D -- Guardar datos --> E["Base de datos MySQL"]
    E -- Confirmación --> D
    D -- Respuesta (éxito/error) --> C
    C -- Redirección/Respuesta Inertia --> B
    B -- Mostrar mensaje/actualizar UI --> A

    subgraph Docker/Sail
        C
        D
        E
    end

## 🚀 Tecnologías principales
- **Laravel** (backend, API REST, autenticación Sanctum)
- **Inertia.js** (puente entre Laravel y Vue)
- **Vue.js** (frontend SPA)
- **Tailwind CSS** (estilos)
- **MySQL** (base de datos, configurable)
- **PHPUnit** (tests)

---

## 📦 Instalación y configuración

1. **Clona el repositorio:**
   ```bash
   git clone https://github.com/tu-usuario/tu-repo.git
   cd tu-repo
   ```

2. **Instala dependencias:**
   ```bash
   ./vendor/bin/sail composer install
   ./vendor/bin/sail npm install
   ```

3. **Copia y configura el archivo de entorno:**
   ```bash
   cp .env.example .env
   # Edita .env con tus credenciales de base de datos y correo
   ```

4. **Genera la clave de la app:**
   ```bash
   ./vendor/bin/sail artisan key:generate
   ```

5. **Ejecuta migraciones y seeders:**
   ```bash
   ./vendor/bin/sail artisan migrate --seed
   ```

6. **Inicia el servidor de desarrollo:**
   ```bash
   ./vendor/bin/sail up
   ```

7. **Compila los assets:**
   ```bash
   ./vendor/bin/sail npm run dev
   ```

---

## 🔐 Autenticación API

La API REST requiere autenticación con **Bearer Token** (Sanctum). Consulta la [documentación de la API](API_DOCUMENTATION.md#autenticación) para ver cómo obtener y usar el token.

---

## 📖 Documentación

- [Documentación de la API REST](API_DOCUMENTATION.md)
- [Guía de tests y cobertura](./tests/README.md)

---

## 🧪 Tests

Para ejecutar los tests se necesita tener Docker instalado:
```bash
# Con Sail
./vendor/bin/sail test

# O directamente
php artisan test
```

Más detalles en [tests/README.md](./tests/README.md)

---

## 📂 Estructura principal

```
app/
  ├── Http/
  │     ├── Controllers/         # Controladores web
  │     └── Controllers/Api/     # Controladores API
  ├── Services/                  # Lógica de negocio
  └── Models/                    # Modelos Eloquent
resources/
  ├── js/Pages/                  # Vistas Vue.js
  └── views/                     # Vistas Blade (si aplica)
routes/
  ├── web.php                    # Rutas web
  └── api.php                    # Rutas API
```

---

## 📝 Créditos y licencia

Desarrollado por Isai San Miguel powered by cursor.

Licencia MIT.
