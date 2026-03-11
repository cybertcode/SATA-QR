# Laravel en Hosting Compartido: Guía Completa

## Índice

1. [Arquitectura Estándar](#arquitectura-estándar)
2. [Problemas Comunes](#problemas-comunes)
3. [Configuraciones de .htaccess](#configuraciones-de-htaccess)
4. [Soluciones Alternativas](#soluciones-alternativas)
5. [Troubleshooting](#troubleshooting)

---

## Arquitectura Estándar

### Estructura Laravel Normal

```
/home/usuario/public_html/miapp/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/                      ← DOCUMENT ROOT AQUÍ
│   ├── index.php                ← Punto de entrada
│   ├── .htaccess               ← Rewrites
│   └── assets/
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env                        ← NO debe ser accesible
└── composer.json
```

### Configuración en cPanel

**Mejor práctica:**

```
Subdomain: informatica
Domain: ugelhuacaybamba.edu.pe
Document Root: /home/ugelhuacaybamba/public_html/informatica/public
```

**Estructura física en servidor:**

```
/home/ugelhuacaybamba/public_html/
├── informatica/                           ← Código Laravel
│   ├── app/, config/, routes/, etc.
│   └── public/                            ← Accesible WWW
│       ├── index.php
│       └── .htaccess
└── www/ (u otros dominios)
```

---

## Problemas Comunes

### Problema 1: Document Root en Raíz del Proyecto

**Escenario:** Document Root apunta a `/informatica/` en lugar de `/informatica/public/`

```
❌ INCORRECTO:
Document Root: /home/ugelhuacaybamba/public_html/informatica

✅ CORRECTO:
Document Root: /home/ugelhuacaybamba/public_html/informatica/public
```

**Síntomas:**

- Directory Listing: muestra carpetas `app/`, `config/`, `vendor/`
- Archivos `.env` visibles (`HTTP 200` en `https://tudominio/.env`)
- Security breach: credenciales de BD expuestas
- Error 404 con rewrites incorrectos

### Problema 2: mod_rewrite No Habilitado

**Síntomas:**

- `.htaccess` ignorado
- Routes devuelven 404 excepto `/index.php`
- Todos, requests a `index.php?url=/ruta`

**Verificar:**

```bash
apache2ctl -M | grep rewrite
# Debe mostrar: rewrite_module (shared)
```

### Problema 3: Permisos Incorrectos

**Síntomas:**

- Error 500 al crear/guardar archivos
- Logs no se escriben
- Cache no funciona

**Correcto:**

```bash
# Storage y bootstrap necesitan escritura
chmod 755 storage bootstrap
chmod -R 775 storage/* bootstrap/cache
```

### Problema 4: PATH Absoluto en Redirects

**Síntomas:**

- Redirects a rutas inexistentes
- CSS/JS no carga desde subdirectorio

**Solución:**

```bash
# .env
APP_URL=https://informatica.ugelhuacaybamba.edu.pe

# NO: https://ugelhuacaybamba.edu.pe/informatica
```

---

## Configuraciones de .htaccess

### Opción 1: Estándar (Recomendada)

**Ubicación:** `/public/.htaccess`

```apache
<IfModule mod_rewrite.c>
    # Desactivar listado de directorios
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Preservar Authorization header (para APIs)
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Preservar X-XSRF-Token header
    RewriteCond %{HTTP:x-xsrf-token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

    # Redirigir www a sin www (opcional)
    RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
    RewriteRule ^(.*)$ https://%1/$1 [R=301,L]

    # Eliminar slash final en URLs (si no es directorio)
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Archivos y directorios existentes: no reescribir
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f

    # Todas las demás requests → index.php
    RewriteRule ^ index.php [L]
</IfModule>

# Proteger .env si llega a haber aquí (NUNCA debe estar)
<FilesMatch "\.env$">
    Deny from all
</FilesMatch>
```

### Opción 2: Sin www → con www

```apache
<IfModule mod_rewrite.c>
    Options -MultiViews -Indexes
    RewriteEngine On

    # Sin www → con www
    RewriteCond %{HTTP_HOST} !^www\. [NC]
    RewriteCond %{HTTP_HOST} !^$
    RewriteRule ^(.*)$ https://www.%{HTTP_HOST}$1 [R=301,L]

    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### Opción 3: Forzar HTTPS

```apache
<IfModule mod_rewrite.c>
    Options -MultiViews -Indexes
    RewriteEngine On

    # Forzar HTTPS
    RewriteCond %{HTTPS} !on
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]

    # www redirects
    RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
    RewriteRule ^(.*)$ https://%1/$1 [R=301,L]

    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### Opción 4: Subdirectorio del Dominio Principal

**Escenario:** `https://principal.com/informatica/`

```apache
<IfModule mod_rewrite.c>
    Options -MultiViews -Indexes
    RewriteEngine On

    # Base del subdirectorio
    RewriteBase /informatica/

    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

**Configuración complementaria en `config/app.php`:**

```php
'url' => env('APP_URL', 'https://principal.com/informatica'),
```

---

## Soluciones Alternativas

### SOLUCIÓN 1: Cambiar Document Root (RECOMENDADA)

Si tienes acceso a cPanel o WHM:

#### Método cPanel:

1. **Addons Domains** → Editar subdominio
2. cambiar Document Root: `/home/usuario/public_html/informatica/public`
3. Guardar y esperar 5-10 minutos

#### Método SSH/WHM:

```bash
# Editar lista de alias
vi /etc/userdomains

# Línea para informatica.ugelhuacaybamba.edu.pe:
informatica.ugelhuacaybamba.edu.pe: ugelhuacaybamba - /home/ugelhuacaybamba/public_html/informatica/public
```

**Ventajas:**

- ✅ Seguridad máxima (.env, composer.json no accesibles)
- ✅ URLs limpias
- ✅ Performance óptimo

---

### SOLUCIÓN 2: Symlink (alternativa si no puedes cambiar Document Root)

Si cPanel no permite cambiar el document root:

```bash
# Desde el directorio del proyecto
cd /home/usuario/public_html/informatica

# 1. Mover el contenido de public fuera
mkdir -p /home/usuario/public_files/informatica
mv public/* /home/usuario/public_files/informatica/

# 2. Crear symlink
ln -s /home/usuario/public_files/informatica public

# 3. Verificar
ls -la public/
# Debe mostrar: public -> /home/usuario/public_files/informatica
```

**Problema:** Algunos hostings deshabilitan symlinks por seguridad.

---

### SOLUCIÓN 3: Cambiar Estructura (Modificar index.php)

**Alternativa más segura sin cambiar Document Root:**

#### Paso 1: Mover archivos confidenciales fuera del web root

```bash
# Estructura final:
/home/usuario/
├── public_html/informatica/
│   ├── index.php           ← MODIFICADO
│   ├── .htaccess           ← Reescrituras
│   ├── build/              ← Assets
│   ├── images/
│   └── js/
│
└── laravel_root/           ← SECRETO (no accesible web)
    ├── app/
    ├── config/
    ├── routes/
    ├── bootstrap/
    ├── vendor/
    ├── storage/
    ├── .env
    ├── composer.json
    └── artisan
```

#### Paso 2: Modificar `public/index.php`

```php
<?php
/**
 * Laravel index.php - Ubicación alternativa del root
 *
 * Modificado para apuntar fuera del web root
 */

// Cambia esta ruta según tu servidor
define('LARAVEL_START', microtime(true));

require __DIR__ . '/../../../laravel_root/bootstrap/autoload.php';

// O si está en el mismo nivel:
// require __DIR__ . '/../bootstrap/autoload.php';

$app = require_once __DIR__ . '/../../../laravel_root/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
```

#### Paso 3: Configurar .htaccess

```apache
<IfModule mod_rewrite.c>
    Options -MultiViews -Indexes
    RewriteEngine On

    # Reescribir todas las requests a index.php del directorio actual
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Proteger archivos sensibles si se copian aquí
<FilesMatch "\.(env|php|htaccess|htpasswd|log|lock)$">
    Deny from all
</FilesMatch>
```

#### Paso 4: Actualizar paths en `config/filesystems.php`

```php
'disks' => [
    'local' => [
        'driver' => 'local',
        // Ajustar rutas absolutas si es necesario
        'root' => base_path('storage/app'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'private',
    ],

    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
],
```

**Ventajas:**

- ✅ .env protegido fuera del web root
- ✅ No requiere cambiar Document Root
- ✅ Funciona con mod_rewrite

**Desventajas:**

- ⚠️ Mantenimiento de rutas manual
- ⚠️ Deploys más complejos (git clone en laravel_root/)

---

### SOLUCIÓN 4: Hosting con Controlador Específico

Si el hosting tiene un director como `application_start.php`:

```php
<?php
// application_start.php en public_html/informatica/

// Define constantes de paths
define('APP_PATH', __DIR__ . '/../laravel_app');

// Incluye el bootstrap de Laravel
require APP_PATH . '/bootstrap/autoload.php';
require APP_PATH . '/bootstrap/app.php';

// Resto del código...
```

---

### SOLUCIÓN 5: .htaccess Avanzado (Fallback)

Si solo tienes acceso a .htaccess y DocumentRoot está en raíz:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Denegar acceso a carpetas sensibles
    RewriteRule ^(app|bootstrap|config|database|routes|storage|vendor|\.env)(.*)$ - [F,L]

    # Archivos específicos denegados
    RewriteRule ^\.env$ - [F,L]
    RewriteRule ^\.git(.*)$ - [F,L]
    RewriteRule ^composer\.(json|lock)$ - [F,L]
    RewriteRule ^artisan$ - [F,L]
    RewriteRule ^Dockerfile$ - [F,L]

    # Si existe public/*, servir desde ahí
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_URI} !^/public/

    # Reescribir requests a public/index.php
    RewriteRule ^(.*)$ /public/index.php?request=$1 [L]
</IfModule>

# Prevenir acceso directo a archivos sensibles
<FilesMatch "\.(env|lock|log|sql|git|htaccess)$">
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
    <IfModule !mod_authz_core.c>
        Order allow,deny
        Deny from all
    </IfModule>
</FilesMatch>
```

**IMPORTANTE:** Esta es la opción MENOS segura. Solo usarla como último recurso.

---

## Checklist de Seguridad

- [ ] Document Root apunta a `/public/`
- [ ] `.env` NO es accesible (test: `curl https://tudominio/.env`)
- [ ] `/vendor` NO listable (test: `curl https://tudominio/vendor/`)
- [ ] Permisos correctos:
    - [ ] `chmod 755` en app, config, routes, public
    - [ ] `chmod 775` en storage, bootstrap/cache
- [ ] `.htaccess` existe en `/public/`
- [ ] mod_rewrite habilitado
- [ ] HTTPS forzado (APP_URL comienza con `https://`)
- [ ] Logs fuera del web root

---

## Troubleshooting Completo

### Error 500: Internal Server Error

```bash
# 1. Verificar APP_KEY
php artisan key:generate

# 2. Permisos de carpetas
chmod -R 775 storage bootstrap/cache

# 3. Clear cache
php artisan config:clear
php artisan cache:clear

# 4. Ver logs
tail -f storage/logs/laravel.log

# 5. Verificar extensiones PHP
php -m | grep -E "(pdo|mysql|mbstring|openssl|tokenizer|ctype|json|fileinfo|bcmath)"
```

### Error 404 en todas las rutas

```bash
# 1. ¿Document Root apunta a public/?
ls -la | grep index.php
# Si index.php está en directorio actual, está correcto

# 2. ¿Existe .htaccess?
cat .htaccess | head -5

# 3. ¿mod_rewrite habilitado?
apache2ctl -M | grep rewrite

# 4. ¿AllowOverride configurado?
curl -i https://tudominio/test-ruta-inexistente
# Debe retornar 404 de Laravel, NO "Directory Listing"
```

### Archivos .env visibles

```bash
# GRAVE: esto significa Document Root está mal
# Solución inmediata: Cambiar Document Root en cPanel a /public

# Verification:
curl https://tudominio/.env

# Debería retornar 403 Forbidden (no 200 OK)
```

### CSS/JS no carga

```bash
# 1. ¿Existe directorio public/build/?
ls -la public/build/

# 2. ¿Archivo manifest.json presente?
ls public/build/manifest.json

# 3. ¿Build compilado?
npm run build  # En servidor o local

# 4. ¿APP_URL correcto en .env?
grep APP_URL .env

# 5. Verificar rutas en sources
# En navegador: Inspect → Network → revisa URLs de JS/CSS
```

### Permisos 403 Forbidden

```bash
# Verificar permisos reales
ls -la storage/

# Debería mostrar:
# drwxrwxr-x storage/

# Si no, ejecutar:
sudo chown www-data:www-data storage/ -R
sudo chmod 775 storage/ -R
```

### Email no envía

```bash
# Verificar credenciales SMTP
grep -i mail .env

# Test rápido
php artisan tinker
>>> Mail::raw('test', function($msg) { $msg->to('admin@example.com'); });

# Ver logs
tail storage/logs/laravel.log | grep -i mail
```

---

## Comparativa de Soluciones

| Solución                      | Seguridad  | Complejidad | Performance | Recomendación  |
| ----------------------------- | ---------- | ----------- | ----------- | -------------- |
| Cambiar Document Root         | ⭐⭐⭐⭐⭐ | ⭐          | ⭐⭐⭐⭐⭐  | **MEJOR**      |
| Symlink                       | ⭐⭐⭐⭐   | ⭐⭐        | ⭐⭐⭐⭐⭐  | Alternativa    |
| Mover archivos fuera web root | ⭐⭐⭐⭐⭐ | ⭐⭐⭐      | ⭐⭐⭐⭐    | Flexible       |
| .htaccess avanzado            | ⭐⭐⭐     | ⭐⭐⭐      | ⭐⭐⭐      | Último recurso |

---

## Configuración ENV Recomendada para Hosting

```env
APP_NAME="SATA-QR"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://informatica.ugelhuacaybamba.edu.pe

APP_KEY=base64:xxxxx
# Generar con: php artisan key:generate

# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=ugelhuacaybamba_sata_qr
DB_USERNAME=ugelhuacaybamba_user
DB_PASSWORD=SuperSegura@123456789

# Cache & Session
CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

# Locale
APP_LOCALE=es
APP_FALLBACK_LOCALE=es
APP_TIMEZONE=America/Lima

# Mail
MAIL_MAILER=smtp
MAIL_HOST=mail.ugelhuacaybamba.edu.pe
MAIL_PORT=587
MAIL_USERNAME=sata@ugelhuacaybamba.edu.pe
MAIL_PASSWORD=SeguraParaEmail@123
MAIL_FROM_ADDRESS=sata@ugelhuacaybamba.edu.pe
MAIL_FROM_NAME="SATA-QR"

# Logs
LOG_CHANNEL=single
LOG_LEVEL=debug

# NO habilitar en producción:
# APP_DEBUG=true
# LOG_LEVEL=debug
```

---

## Referencias y Recursos

- **Documentación Laravel:** https://laravel.com/docs/deployment
- **Apache Rewrite Guide:** https://httpd.apache.org/docs/current/mod/mod_rewrite.html
- **Permisos Unix:** https://www.gnu.org/software/coreutils/manual/coreutils.html#chmod
- **cPanel Docs:** https://docs.cpanel.net/
