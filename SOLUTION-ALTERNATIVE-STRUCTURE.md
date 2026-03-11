# Solución Alternativa: Laravel con DocumentRoot en Raíz

Este documento detalla cómo desplegar Laravel cuando el DocumentRoot apunta a la raíz del proyecto en lugar de a `/public/`.

**Situación:**

- Document Root: `/home/usuario/public_html/informatica/` ❌
- No puedes cambiar a: `/home/usuario/public_html/informatica/public/`
- Necesitas una solución de trabajo

---

## Opción 1: Reorganizar Directorios (Recomendada)

### ¿Cuándo usar?

- Tienes acceso SSH completo
- Puedes reorganizar directorios
- No hay restricciones del hosting

### Paso 1: Respaldar el proyecto (CRÍTICO)

```bash
# Desde SSH en el servidor
cd /home/usuario
mkdir -p backups
cp -R public_html/informatica backups/informatica_backup_$(date +%Y%m%d_%H%M%S)
```

### Paso 2: Crear estructura alternativa

```bash
# Estructura final:
# /home/usuario/
# ├── public_html/informatica/        ← Document Root aquí (accessible)
# │   ├── index.php                   ← Modificado (newBootstrap)
# │   ├── .htaccess                   ← Reescrituras
# │   ├── public/                     ← Assets (symlink o movido)
# │   ├── build/ (o public/build)     ← Build de assets
# │   └── ...
# │
# └── laravel_core/                   ← Privado (NO web accessible)
#     ├── app/
#     ├── config/
#     ├── routes/
#     ├── bootstrap/
#     ├── vendor/
#     ├── storage/
#     ├── database/
#     ├── .env
#     ├── composer.json
#     └── artisan

# Ejecutar:

# 1. Mover código confidencial
mkdir -p /home/usuario/laravel_core
mv /home/usuario/public_html/informatica/app /home/usuario/laravel_core/
mv /home/usuario/public_html/informatica/config /home/usuario/laravel_core/
mv /home/usuario/public_html/informatica/routes /home/usuario/laravel_core/
mv /home/usuario/public_html/informatica/database /home/usuario/laravel_core/
mv /home/usuario/public_html/informatica/bootstrap /home/usuario/laravel_core/
mv /home/usuario/public_html/informatica/vendor /home/usuario/laravel_core/
mv /home/usuario/public_html/informatica/storage /home/usuario/laravel_core/
mv /home/usuario/public_html/informatica/.env /home/usuario/laravel_core/
mv /home/usuario/public_html/informatica/composer.json /home/usuario/laravel_core/
mv /home/usuario/public_html/informatica/composer.lock /home/usuario/laravel_core/
mv /home/usuario/public_html/informatica/artisan /home/usuario/laravel_core/

# 2. Mover assets públicos a un subdirectorio accesible
if [ -d /home/usuario/public_html/informatica/public ]; then
    mkdir -p /home/usuario/public_html/informatica/public_files
    mv /home/usuario/public_html/informatica/public/* /home/usuario/public_html/informatica/public_files/
    rm -rf /home/usuario/public_html/informatica/public
fi

# 3. Mover archivos no críticos
mv /home/usuario/public_html/informatica/resources /home/usuario/laravel_core/
mv /home/usuario/public_html/informatica/tests /home/usuario/laravel_core/
mv /home/usuario/public_html/informatica/.gitignore /home/usuario/laravel_core/
mv /home/usuario/public_html/informatica/README.md /home/usuario/laravel_core/

# 4. Crear symlinks para código accesible
ln -s /home/usuario/laravel_core/resources /home/usuario/public_html/informatica/resources
ln -s /home/usuario/laravel_core/tests /home/usuario/public_html/informatica/tests
```

### Paso 3: Crear nuevo `index.php` en public_html

```php
<?php
/**
 * Laravel Entry Point - Modified for Alternative Structure
 *
 * Locations:
 * - This file: /home/usuario/public_html/informatica/index.php
 * - Laravel Core: /home/usuario/laravel_core/
 *
 * Created: 2026-03-10
 */

// Define start time for debug info
define('LARAVEL_START', microtime(true));

// Define paths
$laravel_root = dirname(__DIR__) . '/../../laravel_core';  // Up 2 levels then /laravel_core
$public_path = __DIR__;

// Verify paths exist
if (!is_dir($laravel_root)) {
    die('ERROR: Laravel core directory not found at: ' . $laravel_root);
}

if (!is_file($laravel_root . '/bootstrap/autoload.php')) {
    die('ERROR: Could not find bootstrap/autoload.php. Check paths.');
}

// Load Composer autoloader
require_once $laravel_root . '/bootstrap/autoload.php';

// Create Laravel application instance
$app = require_once $laravel_root . '/bootstrap/app.php';

// Set custom base path (required for alternative structure)
$app->bind('path.base', $laravel_root);
$app->bind('path.app', $laravel_root . '/app');
$app->bind('path.config', $laravel_root . '/config');
$app->bind('path.database', $laravel_root . '/database');
$app->bind('path.resources', $laravel_root . '/resources');
$app->bind('path.bootstrap', $laravel_root . '/bootstrap');
$app->bind('path.storage', $laravel_root . '/storage');
$app->bind('path.routes', $laravel_root . '/routes');

// Get HTTP kernel
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Handle request
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

// Send response
$response->send();

// Terminate application
$kernel->terminate($request, $response);
```

### Paso 4: Crear `.htaccess` en public_html/informatica

```apache
<IfModule mod_rewrite.c>
    Options -MultiViews -Indexes
    RewriteEngine On

    # ===== SECURITY =====
    # Deny sensitive folders
    RewriteRule ^(\.|vendor|bootstrap|config|database|routes|storage|app|tests|resources)(/|$) - [F,L]

    # Deny sensitive files
    RewriteRule ^(\.|artisan|composer\.|phpunit\.|Dockerfile|\.env)$ - [F,L]

    # ===== HTTPS & REDIRECTS =====
    RewriteCond %{HTTPS} off
    RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L,QSA]

    RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
    RewriteRule ^(.*)$ https://%1/$1 [R=301,L,QSA]

    # ===== HEADERS =====
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # ===== ROUTING =====
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# ===== SECURITY HEADERS =====
<IfModule mod_headers.c>
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
</IfModule>

# ===== PROTECT FILES =====
<FilesMatch "\.(env|lock|log|sql|git|md)$">
    Deny from all
</FilesMatch>
```

### Paso 5: Actualizar `config/app.php`

```php
// config/app.php

return [
    // ... otras configuraciones ...

    'base_path' => getenv('LARAVEL_BASE_PATH') ?: dirname(__DIR__),

    // Asegura que los paths están correctos
    'paths' => [
        'app' => base_path('app'),
        'config' => base_path('config'),
        'database' => base_path('database'),
        'resources' => base_path('resources'),
        'storage' => base_path('storage'),
        'bootstrap' => base_path('bootstrap'),
        'routes' => base_path('routes'),
    ],

    // ... resto del config ...
];
```

### Paso 6: Compilar Assets

```bash
# En servidor o localmente
cd /home/usuario/laravel_core

npm install
npm run build

# Copiar build a directorio accesible
cp -R public/build /home/usuario/public_html/informatica/public_files/build

# O crear symlink
ln -s /home/usuario/laravel_core/public/build /home/usuario/public_html/informatica/public_files/build
```

### Paso 7: Permisos

```bash
# Asegurar que web server puede leer/escribir
chown -R www-data:www-data /home/usuario/laravel_core
chown -R www-data:www-data /home/usuario/public_html/informatica

chmod 755 /home/usuario/laravel_core
chmod -R 775 /home/usuario/laravel_core/storage
chmod -R 775 /home/usuario/laravel_core/bootstrap/cache
chmod 755 /home/usuario/public_html/informatica
```

### Paso 8: Actualizar referencias en `.env`

```env
# .env en /home/usuario/laravel_core/

APP_NAME=SATA-QR
APP_ENV=production
APP_DEBUG=false
APP_URL=https://informatica.ugelhuacaybamba.edu.pe

# Rutas
LARAVEL_BASE_PATH=/home/usuario/laravel_core

# Resto de configuración normal
```

### Paso 9: Ejecutar Migraciones

```bash
cd /home/usuario/laravel_core

# Generar APP_KEY si falta
php artisan key:generate

# Ejecutar migraciones
php artisan migrate --force

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimize para production
php artisan optimize
```

### Paso 10: Verificar Funcionamiento

```bash
# Ver archivos creados
ls -la /home/usuario/public_html/informatica/
# Debe mostrar: index.php, .htaccess, public_files/

# Probar en navegador
https://informatica.ugelhuacaybamba.edu.pe/

# Verificar seguridad
curl https://informatica.ugelhuacaybamba.edu.pe/.env
# Debe retorner 403 Forbidden

curl https://informatica.ugelhuacaybamba.edu.pe/vendor/
# Debe retorner 403 Forbidden
```

---

## Opción 2: Usar Symlinks (Menos confiable)

### ¿Cuándo usar?

- Hosting no deshabilita symlinks
- No quieres reorganizar archivos
- Necesita solución rápida

### Implementación

```bash
cd /home/usuario/public_html/informatica

# Crear symlink a public como point of reference
ln -s public public_root

# En .htaccess:
RewriteRule ^ public_root/index.php [L]
```

**Advertencia:** Muchos hostings deshabilitan symlinks por seguridad.

---

## Opción 3: Modificar `public/index.php` Mínimamente

### Para mantener estructura original

Si NO puedes reorganizar, modifica solo `public/index.php`:

```php
<?php
/**
 * Modified public/index.php for root document root
 */

define('LARAVEL_START', microtime(true));

// Original Laravel bootstrap code:
require __DIR__ . '/../bootstrap/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// ... resto normal ...
```

**Problema:** `.env` y `composer.json` seguirán siendo accesibles. Usa con protección extra en `.htaccess`:

```apache
<FilesMatch "^(\.|\.env|\.git|\.gitignore|composer\.|phpunit\.|Dockerfile|artisan)">
    Deny from all
</FilesMatch>
```

---

## Opción 4: Usar PHP Wrappers y .htaccess Avanzado

### ¿Cuándo usar?

- No puedes mover archivos
- Necesitas solución solo con .htaccess

### Implementación

```apache
# /public_html/informatica/.htaccess

<IfModule mod_rewrite.c>
    RewriteEngine On

    # ===== BLOQUEAR ACCESO A CARPETAS PELIGROSAS =====

    # Bloquear directorios
    RewriteRule ^(app|bootstrap|config|database|routes|storage|vendor|node_modules)(/|$) - [F,NC,L]

    # Bloquear archivos específicos
    RewriteRule ^\.env$ - [F,NC,L]
    RewriteRule ^artisan$ - [F,NC,L]
    RewriteRule ^composer\.(json|lock)$ - [F,NC,L]
    RewriteRule ^package\.(json|lock)$ - [F,NC,L]
    RewriteRule ^phpunit\.xml$ - [F,NC,L]
    RewriteRule ^Dockerfile$ - [F,NC,L]
    RewriteRule ^README\.md$ - [F,NC,L]

    # ===== ROUTING =====

    # Permitir archivos reales
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f

    # Reescribir a public/index.php
    RewriteRule ^(.*)$ public/index.php?request=$1 [QSA,L]
</IfModule>

# ===== PREVENT DIRECTORY LISTING =====
<IfModule mod_autoindex.c>
    Options -Indexes
</IfModule>

# ===== FILE PROTECTIONS =====
<FilesMatch "\.(env|lock|log|git)$">
    Deny from all
</FilesMatch>
```

---

## Checklist Verificación

Después de implementar, verificar:

```bash
# 1. ¿Carga la página principal?
curl -I https://informatica.ugelhuacaybamba.edu.pe/
# Debe retorner 200

# 2. ¿.env NO accesible?
curl https://informatica.ugelhuacaybamba.edu.pe/.env
# Debe retorner 403 Forbidden

# 3. ¿vendor NO listable?
curl https://informatica.ugelhuacaybamba.edu.pe/vendor/
# Debe retorner 403 Forbidden

# 4. ¿Las rutas funcionan?
curl -I https://informatica.ugelhuacaybamba.edu.pe/api/users
# Debe retorner 200 o 401 (autenticación), NO 404

# 5. ¿Assets cargan?
curl -I https://informatica.ugelhuacaybamba.edu.pe/public_files/build/app.js
# Debe retorner 200

# 6. ¿Logs se escriben?
ls -la laravel_core/storage/logs/
# Debe haber archivo laravel.log reciente

# 7. ¿Migraciones ejecutadas?
php artisan migrate:status
# Todos los migraciones deben estar [Yes]
```

---

## Solución Rápida (Emergency)

Si necesitas funcional en 5 minutos:

```bash
# 1. Cambiar Document Root en cPanel a /public
# O contactar soporte

# 2. Si cPanel no permite:
# Poner solo esto en public_html/informatica/.htaccess:

<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ public/index.php [L]
</IfModule>

# Protección mínima:
<FilesMatch "^(\.|\.env|artisan|composer\.)">
    Deny from all
</FilesMatch>

# 3. Ejecutar:
php artisan config:clear
php artisan migrate --force

# 4. Probar:
https://informatica.ugelhuacaybamba.edu.pe
```

---

## Comparativa de Opciones

| Opción               | Seguridad  | Complejidad | Tiempo | Recomendación |
| -------------------- | ---------- | ----------- | ------ | ------------- |
| Reorganizar dirs     | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐    | 30 min | **MEJOR**     |
| Symlinks             | ⭐⭐⭐⭐   | ⭐⭐        | 5 min  | Alternativa   |
| Solo .htaccess       | ⭐⭐⭐     | ⭐          | 2 min  | Temporal      |
| Change Document Root | ⭐⭐⭐⭐⭐ | ⭐          | 5 min  | **IDEAL**     |

---

## Troubleshooting

### "ERROR: Laravel core directory not found"

```bash
# index.php a la ruta de laravel_core es incorrecta
# Verificar:

# 1. ¿Existe laravel_core?
ls /home/usuario/laravel_core

# 2. ¿Path en index.php es correcto?
grep laravel_root /home/usuario/public_html/informatica/index.php

# 3. Actualizar el path si es necesario
# Editar index.php con rutas correctas
```

### "Class 'Illuminate...' not found"

```bash
# vendor/ no está accesible desde index.php
# Soluciones:

# 1. Ejecutar composer en laravel_core
cd /home/usuario/laravel_core
composer install --optimize-autoloader --no-dev

# 2. Verificar path a autoload.php en index.php
grep -n "autoload.php" /home/usuario/public_html/informatica/index.php
```

### Assets no cargan desde public_files/

```bash
# En .env, verificar:
ASSET_URL=/public_files

# En Blade, usar:
{{ asset('build/app.js') }}
# Debe generar: /public_files/build/app.js
```
