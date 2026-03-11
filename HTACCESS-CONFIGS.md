# .htaccess Configurations for Laravel

## Este archivo contiene múltiples configuraciones de .htaccess listas para uso

---

## CONFIGURACIÓN 1: Estándar Laravel (RECOMENDADA)

Para: `/public/.htaccess`
Uso: Cuando Document Root apunta correctamente a `/public`

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Handle X-XSRF-Token Header
    RewriteCond %{HTTP:x-xsrf-token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## CONFIGURACIÓN 2: Con SSL/HTTPS Forzado

Para: `/public/.htaccess`
Uso: Forzar todas las conexiones a HTTPS

```apache
<IfModule mod_rewrite.c>
    Options -MultiViews -Indexes
    RewriteEngine On

    # Forzar HTTPS (líneas 1-3)
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L,QSA]

    # Redirigir www a sin www
    RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
    RewriteRule ^(.*)$ https://%1/$1 [R=301,L]

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Proteger archivos sensibles (por seguridad extra)
<FilesMatch "\.(env|lock|log)$">
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
    <IfModule !mod_authz_core.c>
        Order allow,deny
        Deny from all
    </IfModule>
</FilesMatch>
```

---

## CONFIGURACIÓN 3: WWW a Sin WWW

Para: `/public/.htaccess`
Uso: Redirigir todo desde www.dominio.com → dominio.com

```apache
<IfModule mod_rewrite.c>
    Options -MultiViews -Indexes
    RewriteEngine On

    # Remove www
    RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
    RewriteRule ^(.*)$ https://%1/$1 [R=301,L]

    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## CONFIGURACIÓN 4: Sin WWW a WWW

Para: `/public/.htaccess`
Uso: Redirigir todo desde dominio.com → www.dominio.com

```apache
<IfModule mod_rewrite.c>
    Options -MultiViews -Indexes
    RewriteEngine On

    # Add www
    RewriteCond %{HTTP_HOST} !^www\. [NC]
    RewriteCond %{HTTP_HOST} !^$
    RewriteRule ^(.*)$ https://www.%{HTTP_HOST}/$1 [R=301,L]

    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## CONFIGURACIÓN 5: Subdirectorio (ej: /informatica/)

Para: `/informatica/.htaccess` O `/informatica/public/.htaccess`
Uso: App en subdirectorio principal: https://dominio.com/informatica/

```apache
<IfModule mod_rewrite.c>
    Options -MultiViews -Indexes
    RewriteEngine On

    # Base path - MODIFICAR según ubicación real
    RewriteBase /informatica/

    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Si la app está en /informatica/public/, añadir:
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} ^/informatica/public/
    RewriteRule ^ /informatica/public/index.php [L]

    # Si la app está en /informatica/ directamente:
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

**Nota:** Requiere configuración en `config/app.php`:

```php
'url' => env('APP_URL', 'https://dominio.com/informatica'),
'asset_url' => env('ASSET_URL', 'https://dominio.com/informatica'),
```

---

## CONFIGURACIÓN 6: Document Root en Raíz (NO RECOMENDADO - Fallback)

Para: `/public/.htaccess`
Uso: Cuando Document Root apunta a raíz en lugar de `/public/` (como último recurso)

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    # ===== SEGURIDAD: DENEGAR ACCESO A CARPETAS SENSIBLES =====

    # Denegar carpetas peligrosas
    RewriteRule ^(app|bootstrap|config|database|routes|storage|vendor|node_modules|\.git)(/|$) - [F,L]

    # Denegar archivos sensibles
    RewriteRule ^(\.env|\.env\.|composer\.|artisan|Dockerfile|.gitignore|README)$ - [F,L]
    RewriteRule ^package\.(json|lock)$ - [F,L]
    RewriteRule ^phpunit\.xml$ - [F,L]

    # ===== CORE REWRITING =====

    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Solicitudes reales a archivos/directorios: permitir
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f

    # TODO lo demás → public/index.php
    RewriteRule ^.*$ public/index.php [L]
</IfModule>

# Extra: Denegar acceso a ciertos tipos de archivo
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

---

## CONFIGURACIÓN 7: Múltiples Apps en Mismo Hosting

Para: `/public/.htaccess` (en cada app)
Uso: Cuando hay varias apps Laravel en el mismo servidor

```apache
<IfModule mod_rewrite.c>
    Options -MultiViews -Indexes
    RewriteEngine On

    # Identificar la app (por Host)
    RewriteCond %{HTTP_HOST} ^app1\.dominio\.com$ [NC]
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php?app=app1 [L,QSA]

    RewriteCond %{HTTP_HOST} ^app2\.dominio\.com$ [NC]
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php?app=app2 [L,QSA]

    # Default
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## CONFIGURACIÓN 8: Con Caché Agresivo

Para: `/public/.htaccess`
Uso: Cuando necesitas máxima performance (caché en navegador)

```apache
<IfModule mod_rewrite.c>
    Options -MultiViews -Indexes
    RewriteEngine On

    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# ===== CACHE BROWSER =====

<IfModule mod_headers.c>
    # CSS y JS: 1 año
    <FilesMatch "\.(css|js)$">
        Header set Cache-Control "max-age=31536000, public"
    </FilesMatch>

    # Imágenes: 1 año
    <FilesMatch "\.(jpg|jpeg|png|gif|ico|svg|webp)$">
        Header set Cache-Control "max-age=31536000, public"
    </FilesMatch>

    # Fonts: 1 año
    <FilesMatch "\.(woff|woff2|ttf|otf|eot)$">
        Header set Cache-Control "max-age=31536000, public"
    </FilesMatch>

    # HTML: Sin caché
    <FilesMatch "\.html?$">
        Header set Cache-Control "no-cache, no-store, must-revalidate"
    </FilesMatch>

    # Default para otros archivos
    <FilesMatch "\.(php)$">
        Header set Cache-Control "no-cache, no-store, must-revalidate"
    </FilesMatch>
</IfModule>

# Compresión gzip
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript image/svg+xml application/json
</IfModule>
```

---

## CONFIGURACIÓN 9: Con Protección IP

Para: `/public/.htaccess`
Uso: Cuando solo ciertos IPs deben acceder (ej: admin panel)

```apache
<IfModule mod_rewrite.c>
    Options -MultiViews -Indexes
    RewriteEngine On

    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Proteger rutas sensibles por IP
<FilesMatch "^(admin|api-admin|settings)">
    Order deny,allow
    Deny from all

    # IPs permitidas (cambiar por las tuyas)
    Allow from 200.1.2.3
    Allow from 192.168.1.0/24
    Allow from ::1  # IPv6 localhost
</FilesMatch>
```

---

## CONFIGURACIÓN 10: Production-Ready Completa

Para: `/public/.htaccess`
Uso: Máxima seguridad, performance y compatibilidad

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # ===== HTTPS Y REDIRECTS =====

    # Forzar HTTPS
    RewriteCond %{HTTPS} off
    RewriteCond %{HTTP:X-Forwarded-Proto} !https
    RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L,QSA]

    # De www a sin www
    RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
    RewriteRule ^(.*)$ https://%1/$1 [R=301,L,QSA]

    # ===== HEADERS IMPORTANTES =====

    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    RewriteCond %{HTTP:x-xsrf-token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

    # ===== URL CLEANUP =====

    # Eliminar extensión .php
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME}.php -f
    RewriteRule ^([^\.]+)$ $1.php [L]

    # Trailing slashes
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # ===== ROUTER =====

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# ===== HEADERS DE SEGURIDAD =====

<IfModule mod_headers.c>
    # Prevenir clickjacking
    Header always set X-Frame-Options "SAMEORIGIN"

    # Prevenir MIME sniffing
    Header always set X-Content-Type-Options "nosniff"

    # XSS Protection
    Header always set X-XSS-Protection "1; mode=block"

    # Content Security Policy (opcional)
    Header always set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:;"

    # Referrer Policy
    Header always set Referrer-Policy "strict-origin-when-cross-origin"

    # Feature Policy
    Header always set Permissions-Policy "geolocation=(), camera=(), microphone=()"
</IfModule>

# ===== PROTECCIÓN DE ARCHIVOS =====

<FilesMatch "\.(env|lock|log|sql|git|md|txt|json|lock)$">
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
    <IfModule !mod_authz_core.c>
        Order allow,deny
        Deny from all
    </IfModule>
</FilesMatch>

# ===== GZIP COMPRESSION =====

<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json image/svg+xml
    AddEncoding gzip .gz
    AddType application/x-gzip .gz
</IfModule>

# ===== BROWSER CACHING =====

<IfModule mod_expires.c>
    ExpiresActive On

    # HTML: Sin caché
    ExpiresByType text/html "access plus 0 seconds"

    # CSS y JS: 1 año
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType text/javascript "access plus 1 year"

    # Imágenes: 1 año
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType image/x-icon "access plus 1 year"

    # Fonts: 1 año
    ExpiresByType font/ttf "access plus 1 year"
    ExpiresByType font/otf "access plus 1 year"
    ExpiresByType font/woff "access plus 1 year"
    ExpiresByType font/woff2 "access plus 1 year"
    ExpiresByType application/font-woff "access plus 1 year"
</IfModule>

# Default expiration
Header append Cache-Control "public"
```

---

## Tabla de Referencia Rápida

| Escenario                | Archivo                  | Líneas Clave |
| ------------------------ | ------------------------ | ------------ |
| Estándar                 | `/public/.htaccess`      | Conf 1       |
| Con HTTPS                | `/public/.htaccess`      | Conf 2       |
| Sin www                  | `/public/.htaccess`      | Conf 3       |
| Subdirectorio            | `/informatica/.htaccess` | Conf 5       |
| Document Root incorrecto | `/public/.htaccess`      | Conf 6       |
| Production               | `/public/.htaccess`      | Conf 10      |

---

## Verificación de Configuración

```bash
# 1. Probar que .htaccess se lee
curl -i https://tudominio/.htaccess
# Debe retornar 403 Forbidden, NO 200 OK

# 2. Probar rewrites funcionan
curl -i https://tudominio/usuarios
# Debe retornar 200 (no 404)

# 3. Probar archivos estáticos no se reescriben
curl -i https://tudominio/js/app.js
# Debe retornar 200

# 4. Probar .env NO accesible
curl -i https://tudominio/.env
# Debe retornar 404 o 403, NUNCA 200

# 5. Ver headers de seguridad
curl -I https://tudominio/ | grep -i "x-frame-options\|x-content-type\|x-xss"
```
