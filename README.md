
# homefriens

Proyecto Laravel (Filament) para la gestión de reservas, gastos y métricas de departamentos. Este README explica cómo poner en marcha el proyecto en desarrollo, las herramientas que usamos, cómo configurar conexiones, ejecutar migraciones, compilar assets y soluciones de troubleshooting comunes.

## Contenido
- Resumen
- Requisitos
- Instalación (dependencias)
- Configuración (.env)
- Base de datos, migraciones y seeders
- Assets (Vite / npm)
- Ejecutar la aplicación
- Comandos útiles
- Filament (panel administrativo)
- Tests
- Despliegue básico y recomendaciones
- Troubleshooting

---

## Resumen

Este proyecto está construido con Laravel y Filament como panel administrativo. Contiene modelos y servicios para manejar reservas, limpiezas, gastos, metas mensuales y reportes/exports (Excel/PDF). Utiliza Vite para el frontend (assets JS/CSS) y varias librerías comunes de Laravel.

## Herramientas y dependencias principales

- PHP 8.x (compatible con Laravel 10+)
- Composer
- Node.js + npm (para Vite, Bulma/Tailwind u otros assets según el proyecto)
- MySQL / MariaDB (u otro driver soportado por Laravel)
- Artisan (CLI de Laravel)
- Filament (paquete de admin)
- Paquetes composer incluidos (ejemplos): maatwebsite/excel, livewire, filament, phpoffice, carbon, guzzle, etc. (revisa `composer.json`)

## Requisitos mínimos locales

- PHP 8.1+
- Composer 2+
- Node.js 16+ (compatible con Vite)
- MySQL / MariaDB (o configuración equivalente con PostgreSQL si adaptas DB_CONNECTION)

## Instalación (developer)

1. Clonar el repositorio y ubicarse en la carpeta del proyecto:

```powershell
git clone <tu-repositorio> c:\Users\user\homefriens
cd c:\Users\user\homefriens
```

2. Instalar dependencias PHP con Composer:

```powershell
composer install --no-interaction --prefer-dist
```

3. Instalar dependencias JS (Vite):

```powershell
npm install
```

4. Copiar el archivo de entorno y generar APP_KEY:

```powershell
cp .env.example .env
php artisan key:generate
```

En Windows PowerShell puedes usar `copy .env.example .env` si `cp` no está disponible.

## Configuración (.env)

Edita el archivo `.env` con tus credenciales y settings locales. Valores importantes:

- APP_NAME=HomeFriens
- APP_ENV=local
- APP_DEBUG=true (activar sólo en desarrollo)
- APP_URL=http://localhost:8000
- DB_CONNECTION=mysql
- DB_HOST=127.0.0.1
- DB_PORT=3306
- DB_DATABASE=homedb
- DB_USERNAME=root
- DB_PASSWORD=secret
- MAIL_MAILER=smtp (o log para evitar envíos en local)
- MAIL_HOST=...
- MAIL_PORT=...
- QUEUE_CONNECTION=sync (puedes usar redis/ database en producción)

Si usas Docker, mapea correctamente puertos y variables.

## Base de datos: migraciones y seeders

1. Crea la base de datos indicada en `.env` (ej: `homedb`).
2. Ejecuta migraciones y seeders (si aplica):

```powershell
php artisan migrate --seed
```

Nota: si prefieres evitar seeders, ejecuta solo `php artisan migrate`.

## Storage y permisos

Crear enlace simbólico para storage (si necesitas servir archivos públicos):

```powershell
php artisan storage:link
```

En Windows, PowerShell crea el enlace correctamente si tienes permisos; si hay problemas, ejecuta PowerShell como Administrador.

## Compilar assets (Vite)

Modo desarrollo (watch / hot-reload):

```powershell
npm run dev
```

Build para producción:

```powershell
npm run build
```

## Levantar la aplicación (servidor local)

Puedes usar el servidor embebido de Laravel para pruebas locales:

```powershell
php artisan serve --host=127.0.0.1 --port=8000
```

Abrir en el navegador: http://127.0.0.1:8000

## Filament (panel administrativo)

Filament normalmente se expone en `/admin` o `/filament` dependiendo de la configuración. Para saber la ruta exacta revisa `config/filament.php` o busca el `panel` registrado.

Comandos útiles para Filament:

- Registrar un usuario administrador (si existe un seeder que cree un admin, úsalo).
- Si necesitas reconstruir la cache de Filament (panel):

```powershell
php artisan optimize:clear
php artisan config:cache
```

## Comandos útiles (resumen rápido)

- Instalar dependencias PHP: `composer install`
- Instalar dependencias JS: `npm install`
- Generar key: `php artisan key:generate`
- Migrar BD: `php artisan migrate`
- Ejecutar seeders: `php artisan db:seed` o `php artisan migrate --seed`
- Crear enlace storage: `php artisan storage:link`
- Limpiar caches: `php artisan cache:clear; php artisan config:clear; php artisan view:clear; php artisan route:clear`
- Optimizar (cachés): `php artisan optimize`
- Ejecutar tests: `./vendor/bin/phpunit` (o `vendor\\bin\\phpunit` en Windows)

## Tests

El proyecto contiene pruebas en `tests/`. Ejecuta:

```powershell
./vendor/bin/phpunit
```

Si usas Windows y el comando anterior falla, prueba:

```powershell
vendor\\bin\\phpunit
```

## Exportes y reportes

El código incluye exports para reportes (ej. `ReservasPropietarioExport.php`). Estos usan `maatwebsite/excel` para generar archivos Excel. Revisa `app/Exports` y el código de controladores para endpoints que generen downloads.

## Migraciones / Estructura relevante

- `database/migrations/` contiene migraciones para usuarios, departamentos, empresas, reservas, limpiezas, etc.
- `database/factories/` y `database/seeders/DatabaseSeeder.php` ayudan a poblar datos de ejemplo.

## Despliegue básico / recomendaciones

1. En producción, establece en `.env`:

- APP_ENV=production
- APP_DEBUG=false
- QUEUE_CONNECTION=redis (opcional)

2. Ejecutar migraciones en el servidor y cachear configuraciones:

```powershell
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. Compilar assets con `npm run build` y servirlos con un servidor web (NGINX/Apache).

4. Asegurar permisos adecuados en `storage/` y `bootstrap/cache/`.

## Troubleshooting (problemas comunes)

- Error: "Attempt to read property 'departamento' on null"
	- Significa que en algún lugar se asume que la relación existe. Revisa logs en `storage/logs/laravel.log` para la traza completa. El proyecto ya implementa protecciones (uso de `optional()`), pero busca vistas/widgets/exports que accedan directamente a `->departamento`.

- Problema de permisos en `storage` o `bootstrap/cache`
	- En Linux normalmente `chown -R www-data:www-data storage bootstrap/cache`.
	- En Windows, ejecuta PowerShell como administrador o ajusta permisos de carpeta.

- Problemas con Vite (HMR) en Windows
	- Asegúrate de que Node y npm estén correctamente instalados. Ejecuta `npm run dev` en PowerShell y revisa la consola para errores.

- Errores de migración
	- Verifica credenciales en `.env`, que la base de datos exista y que el usuario tenga permisos. Para forzar migraciones en producción usa `--force`.

## Cómo contribuir / añadir features

- Fork y PR: sigue el flujo estándar de GitHub. De ser necesario, añade tests para la nueva funcionalidad.
- Mantén coherencia con la estructura de servicios y traits (por ejemplo `App\\Services\\ReservaService`, traits de Filament para campos y cálculos).

## Contacto

Si necesitas ayuda adicional sobre cómo ejecutar o extender el proyecto, comparte:

- La salida completa del error (stack trace) cuando algo falla.
- Contenido de `.env` (sin contraseñas) para comprobar valores de conexión.
- Capturas/consola si fallan comandos como `npm run dev` o `php artisan migrate`.

---

Si quieres, adapto este README con pasos específicos para Docker (compose), CI/CD o añado una sección con ejemplos de cómo crear una reserva desde tinker o un script de seed para generar datos de prueba. ¿Quieres que agregue la sección Docker/Compose o scripts de seed? 
 
## Rutas principales (endpoints) y para qué sirven

A continuación tienes las rutas más importantes del proyecto. La interfaz administrativa está montada con Filament en `/admin` (ver `routes/web.php` que redirige `/` a `/admin`). Dentro de `/admin` hay recursos (listados, creación, edición) para las entidades del sistema:

- `/` -> Redirige a `/admin`.
- `/admin` -> Dashboard de Filament (vista principal del panel).
- `/admin/login` -> Formulario de login del administrador.
- `/admin/logout` (POST) -> Cerrar sesión del usuario administrador.

- Recursos CRUD (rutas principales bajo `/admin/<resource>`; cada recurso admite al menos: listar, crear y editar):
	- `/admin/departamentos` -> Gestión de departamentos (listado, crear, editar). Aquí se administran datos como nombre, piso, número, precios y configuración del departamento.
	- `/admin/propietarios` -> Gestión de propietarios.
	- `/admin/empresa-administradoras` -> Empresas administradoras (porcentaje/fee que cobra la empresa).
	- `/admin/empresa-limpiezas` -> Empresas que realizan limpieza.
	- `/admin/limpiezas` -> Gestión de limpiezas programadas (relacionadas a reservas).
	- `/admin/gastos` -> Registro y administración de gastos por departamento.
	- `/admin/pagos` -> Pagos realizados (registro de cobros y conciliación).
	- `/admin/reservas` -> Gestión de reservas (crear, editar, ver). Aquí se administran fechas, huésped, monto, distribución, etc.
	- `/admin/huespeds` -> Gestión de huéspedes.
	- `/admin/resenas` -> Reseñas de huéspedes/estancias.
	- `/admin/calificacions` -> Calificaciones (si aplica).
	- `/admin/canal-reservas` -> Canales desde donde llega la reserva (Airbnb, Booking, directo, etc.).
	- `/admin/contratos` -> Contratos vinculados a departamentos/propietarios.
	- `/admin/meta-mensuals` -> Metas mensuales por departamento (se usa en el widget 'Metas Mensuales').

- Rutas auxiliares usadas internamente:
	- `/livewire/*` -> Endpoints de Livewire para montar componentes interactivos (ej: `/livewire/update`).
	- `/filament/exports/{export}/download` -> Descarga de archivos generados por Filament (exports).
	- `/filament/imports/{import}/failed-rows/download` -> Descarga de filas fallidas en imports.
	- `/storage/{path}` -> Ruta para servir archivos desde `storage/app/public` si está habilitado `storage:link`.

Notas y cómo leer las rutas
- Muchas rutas del admin siguen el patrón RESTful de Filament: `GET /admin/<resource>` (list), `GET /admin/<resource>/create` (formulario), `POST /admin/<resource>` (guardar), `GET /admin/<resource>/{record}/edit` (editar), etc.
- Para encontrar la lista completa de rutas del panel (incluidas las páginas/acciones) puedes ejecutar en el proyecto:

```powershell
php artisan route:list --path=admin
```

o ver el archivo cacheado con todas las rutas en `bootstrap/cache/routes-v7.php` (útil para depurar en local).

Si quieres, puedo añadir a esta sección una tabla automática con todas las rutas (ejecutando `php artisan route:list` y volcando la salida formateada), o agregar ejemplos de cómo usar cada endpoint con curl/Postman. ¿Quieres que genere la tabla completa de rutas y métodos ahora? 

