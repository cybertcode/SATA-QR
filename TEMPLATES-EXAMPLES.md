# Templates y Ejemplos Prácticos

Plantillas listas para usar, específicas para SATA-QR.

---

## Template: .env Completo para Hosting

**Ubicación:** `/home/usuario/laravel_core/.env` (o raíz del proyecto)

```env
###############################################
# SATA-QR Configuration for Shared Hosting
# Generated: 2026-03-10
###############################################

# ===== APPLICATION =====
APP_NAME="SATA-QR - UGEL Huacaybamba"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://informatica.ugelhuacaybamba.edu.pe

# Generate with: php artisan key:generate
APP_KEY=base64:XXXXX-CHANGE-ME-WITH-artisan-key-generate-XXXXX

# Locale Configuration (Perú)
APP_LOCALE=es
APP_FALLBACK_LOCALE=es
APP_TIMEZONE=America/Lima

# ===== DATABASE =====
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=ugelhuacaybamba_sata_qr
DB_USERNAME=ugelhuacaybamba_user
DB_PASSWORD=INSERT-STRONG-PASSWORD-HERE-20-CHARS-MIN

# ===== CACHE & SESSION =====
CACHE_STORE=file
CACHE_PREFIX=

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

# ===== QUEUE =====
QUEUE_CONNECTION=database
# QUEUE_CONNECTION=sync  # For development testing

HORIZON_PREFIX=horizon:

# ===== MAIL =====
MAIL_MAILER=smtp
MAIL_SCHEME=tls
MAIL_HOST=mail.ugelhuacaybamba.edu.pe
MAIL_PORT=587
MAIL_USERNAME=sata@ugelhuacaybamba.edu.pe
MAIL_PASSWORD=INSERT-EMAIL-PASSWORD-HERE
MAIL_FROM_ADDRESS=sata@ugelhuacaybamba.edu.pe
MAIL_FROM_NAME="${APP_NAME}"

# ===== LOGGING =====
LOG_CHANNEL=single
LOG_STACK=single
LOG_LEVEL=debug

# ===== FILESYSTEM =====
FILESYSTEM_DISK=local
FILESYSTEM_VISIBILITY=private

# ===== AWS (Optional) =====
# AWS_ACCESS_KEY_ID=
# AWS_SECRET_ACCESS_KEY=
# AWS_DEFAULT_REGION=us-east-1
# AWS_BUCKET=
# AWS_USE_PATH_STYLE_ENDPOINT=false

# ===== FILE UPLOAD LIMITS =====
# Default 2.5GB
# PHP php.ini limits may override this
UPLOAD_MAX_FILESIZE=100M
POST_MAX_SIZE=100M

# ===== SATA-QR SPECIFIC =====
# Multi-tenancy (if applicable)
TENANCY_HOST=informatica.ugelhuacaybamba.edu.pe

# API Configuration
API_RATE_LIMIT=60  # requests per minute

# Integrations
SIAGI_API_ENABLED=true
SIAGI_API_URL=https://api.siagi.gob.pe
SIAGI_API_KEY=INSERT-API-KEY-IF-NEEDED

# ===== DEVELOPMENT ONLY (Keep false in Production) =====
APP_DEBUG=false
DEBUGBAR_ENABLED=false

###############################################
# Security Reminders:
# 1. Change APP_KEY with: php artisan key:generate
# 2. Generate strong DB passwords (20+ chars, mixed)
# 3. Keep this file ONLY on server, NEVER in git
# 4. Add .env to .gitignore
# 5. Review and change all INSERT-XXX values
###############################################
```

---

## Template: .htaccess para /public/

**Ubicación:** `/home/usuario/project/public/.htaccess`

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # ===== SECURITY HEADERS =====
    # Preserve Authorization Header (for API/JWT)
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Preserve X-XSRF-Token Header (CSRF protection)
    RewriteCond %{HTTP:x-xsrf-token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

    # ===== HTTPS & WWW REDIRECTS =====

    # Force HTTPS (if not already)
    RewriteCond %{HTTPS} off
    RewriteCond %{HTTP:X-Forwarded-Proto} !https
    RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L,QSA]

    # Remove www prefix
    RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
    RewriteRule ^(.*)$ https://%1/$1 [R=301,L,QSA]

    # ===== URL CLEANUP =====

    # Remove trailing slashes (if not a directory)
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # ===== ROUTING =====

    # Don't rewrite real files or directories
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f

    # Send all requests to index.php (Laravel router)
    RewriteRule ^ index.php [L]
</IfModule>

# ===== SECURITY HEADERS =====
<IfModule mod_headers.c>
    # Prevent clickjacking attacks
    Header always set X-Frame-Options "SAMEORIGIN"

    # Prevent MIME-type sniffing
    Header always set X-Content-Type-Options "nosniff"

    # Enable XSS protections in older browsers
    Header always set X-XSS-Protection "1; mode=block"

    # Referrer Policy - Don't send referer to external sites
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>

# ===== GZIP COMPRESSION =====
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE text/javascript
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/json
    AddOutputFilterByType DEFLATE image/svg+xml
</IfModule>

# ===== FILE PROTECTION =====
<FilesMatch "\.(env|lock|log|sql|git|md|json)$">
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
    <IfModule !mod_authz_core.c>
        Order allow,deny
        Deny from all
    </IfModule>
</FilesMatch>

# ===== CACHING POLICY =====
<IfModule mod_expires.c>
    ExpiresActive On

    # Cache static assets for 1 year
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType text/javascript "access plus 1 year"

    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType image/x-icon "access plus 1 year"

    ExpiresByType font/woff "access plus 1 year"
    ExpiresByType font/woff2 "access plus 1 year"
    ExpiresByType application/font-woff "access plus 1 year"

    # Don't cache HTML and PHP
    ExpiresByType text/html "access plus 0 seconds"
    ExpiresByType application/xhtml+xml "access plus 0 seconds"
</IfModule>

# ===== DENY EXECUTION OF SCRIPTS IN UPLOAD DIRS =====
# If uploads are in public/, protect them:
# <Directory "uploads">
#     php_flag engine off
#     RemoveHandler .php .phtml .php3 .php4 .php5 .php6 .phps .pht .phtml .php7 .php8
#     RemoveType .php .phtml .php3 .php4 .php5 .php6 .phps .pht .phtml .php7 .php8
# </Directory>
```

---

## Template: Deployment Script (deploy.sh)

**Ubicación:** `/home/usuario/deploy.sh`

Ejecutar: `bash deploy.sh` después de hacer git push

```bash
#!/bin/bash

# SATA-QR Deployment Script for Shared Hosting
# Usage: bash deploy.sh
# Date: 2026-03-10

set -e  # Exit on first error

PROJECT_PATH="/home/usuario/public_html/informatica"
LOG_FILE="/var/log/sata-qr-deploy.log"

echo "========================================="
echo "SATA-QR Deployment Started"
echo "Time: $(date)"
echo "========================================="

# Step 1: Pull latest code from git
echo "[1/7] Pulling latest code from Git..."
cd $PROJECT_PATH
git fetch origin
git reset --hard origin/develop
git checkout develop
echo "✓ Git pull successful"

# Step 2: Install/Update PHP dependencies
echo "[2/7] Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction
echo "✓ Composer dependencies updated"

# Step 3: Install/Update Node dependencies
echo "[3/7] Installing Node.js dependencies..."
npm install --production
echo "✓ Node dependencies installed"

# Step 4: Build assets (CSS, JS)
echo "[4/7] Building assets..."
npm run build
echo "✓ Assets built successfully"

# Step 5: Run migrations
echo "[5/7] Running database migrations..."
php artisan migrate --force --no-interaction
echo "✓ Migrations completed"

# Step 6: Clear and cache configuration
echo "[6/7] Optimizing Laravel..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✓ Laravel optimization completed"

# Step 7: Set permissions
echo "[7/7] Setting proper permissions..."
chown -R www-data:www-data $PROJECT_PATH
chmod -R 755 $PROJECT_PATH
chmod -R 775 $PROJECT_PATH/storage
chmod -R 775 $PROJECT_PATH/bootstrap/cache
echo "✓ Permissions set correctly"

echo ""
echo "========================================="
echo "✓ SATA-QR Deployment Completed Successfully"
echo "Time: $(date)"
echo "========================================="
echo ""
echo "[INFO] Check application at: https://informatica.ugelhuacaybamba.edu.pe"
echo "[INFO] If any issues, check logs:"
echo "       tail -f $PROJECT_PATH/storage/logs/laravel.log"
echo ""

# Log deployment
echo "[$(date)] Deployment completed successfully" >> $LOG_FILE
```

Hacer ejecutable: `chmod +x deploy.sh`

---

## Template: Nginx Config (Si usas Nginx)

**Ubicación:** `/etc/nginx/sites-available/informatica.ugelhuacaybamba.edu.pe`

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name informatica.ugelhuacaybamba.edu.pe;

    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name informatica.ugelhuacaybamba.edu.pe;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/informatica.ugelhuacaybamba.edu.pe/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/informatica.ugelhuacaybamba.edu.pe/privkey.pem;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_protocols TLSv1.2 TLSv1.3;

    # Project root
    root /home/usuario/public_html/informatica/public;
    index index.php;

    # Access and error logs
    access_log /var/log/nginx/informatica_access.log;
    error_log /var/log/nginx/informatica_error.log;

    # Prevent directory listing
    autoindex off;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # PHP handling
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;  # Adjust PHP version
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Deny access to sensitive files
    location ~ /\.env {
        deny all;
    }

    location ~ /\.git {
        deny all;
    }

    location ~ /composer\.(json|lock) {
        deny all;
    }

    # Laravel routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Static files caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

Después de crear/modificar:

```bash
sudo nginx -t  # Test config
sudo systemctl restart nginx  # Aplicar cambios
```

---

## Template: Cronjob para Auto-Deploye

**Para cPanel:** Agregar en cPanel → Cron Jobs

```bash
*/5 * * * * cd /home/usuario/public_html/informatica && git pull origin develop && php artisan migrate --force >/dev/null 2>&1
```

O via SSH:

```bash
# Editar crontab
crontab -e

# Agregar línea:
*/5 * * * * /home/usuario/deploy.sh >> /var/log/sata-qr-cron.log 2>&1

# Ver crontabs
crontab -l
```

---

## Template: PHP-FPM Config (Si aplica)

**Ubicación:** `/etc/php/8.2/fpm/pool.d/www.conf` (si tienes acceso)

```ini
; SATA-QR Pool Configuration

[sata-qr]
; Pool name
user = www-data
group = www-data

; Listen
listen = /var/run/php/php8.2-fpm.sock

; Process management
pm = dynamic
pm.max_children = 20
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 10

; Memory and timeouts
memory_limit = 256M
max_execution_time = 300

; File uploads
upload_max_filesize = 100M
post_max_size = 100M

; Environment
env[APP_ENV] = production
env[APP_DEBUG] = false
```

---

## Template: Monitoreo de Salud

**Ubicación:** `routes/web.php` - Agregar:

```php
// Health check endpoint (no middleware)
Route::get('health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
        'environment' => app()->environment(),
        'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected',
    ]);
})->name('health');
```

Luego en cron monitoring script:

```bash
#!/bin/bash

URL="https://informatica.ugelhuacaybamba.edu.pe/health"
RESPONSE=$(curl -s $URL)

if echo "$RESPONSE" | grep -q '"status":"ok"'; then
    echo "[$(date)] ✓ Health check passed"
else
    echo "[$(date)] ✗ Health check FAILED"
    # Send alert email, etc.
    mail -s "SATA-QR Health Check Failed" admin@ugelhuacaybamba.edu.pe
fi
```

---

## Template: Backup Script

**Ubicación:** `/home/usuario/backup.sh`

```bash
#!/bin/bash

# SATA-QR Backup Script
# Backup database and important files

BACKUP_DIR="/home/usuario/backups/sata-qr"
PROJECT_PATH="/home/usuario/public_html/informatica"
DB_NAME="ugelhuacaybamba_sata_qr"
DB_USER="ugelhuacaybamba_user"
DB_PASS="your_db_password_here"

mkdir -p $BACKUP_DIR

# 1. Database backup
echo "Backing up database..."
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_$(date +%Y%m%d_%H%M%S).sql

# 2. Files backup (storage and .env only)
echo "Backing up important files..."
tar -czf $BACKUP_DIR/files_$(date +%Y%m%d_%H%M%S).tar.gz \
    $PROJECT_PATH/storage \
    $PROJECT_PATH/.env

# 3. Keep only last 7 backups
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup completed!"
```

Ejecutar: `chmod +x backup.sh && ./backup.sh`

---

## Template: Verificación Post-Deploy

**Ejecutar después de instalar:**

```bash
#!/bin/bash

PROJECT="/home/usuario/public_html/informatica"

echo "=== POST-DEPLOYMENT VERIFICATION ==="
echo ""

# 1. PHP version
echo "1. PHP Version:"
php -v | head -1

# 2. PHP extensions
echo ""
echo "2. Required PHP Extensions:"
php -r "
\$ext = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'json', 'ctype', 'fileinfo', 'bcmath'];
\$ok = 0;
foreach (\$ext as \$e) {
    if (extension_loaded(\$e)) {
        echo '✓ ' . \$e . PHP_EOL;
        \$ok++;
    } else {
        echo '✗ ' . \$e . ' MISSING' . PHP_EOL;
    }
}
echo PHP_EOL . '\$ok/' . count(\$ext) . ' OK' . PHP_EOL;
"

# 3. Database connection
echo ""
echo "3. Database Connection:"
cd $PROJECT && php artisan tinker << 'EOF'
try {
    DB::connection()->getPdo();
    echo "✓ Database connected\n";
} catch (Exception $e) {
    echo "✗ Database ERROR: " . $e->getMessage() . "\n";
}
exit();
EOF

# 4. File permissions
echo ""
echo "4. File Permissions:"
echo "Storage: $(ls -ld $PROJECT/storage | awk '{print $1}')"
echo "Bootstrap: $(ls -ld $PROJECT/bootstrap | awk '{print $1}')"

# 5. Check .env
echo ""
echo "5. Configuration Files:"
[ -f $PROJECT/.env ] && echo "✓ .env exists" || echo "✗ .env MISSING"
[ -f $PROJECT/public/.htaccess ] && echo "✓ .htaccess exists" || echo "✗ .htaccess MISSING"

# 6. Test URL
echo ""
echo "6. URL Response:"
curl -s -o /dev/null -w "HTTP Status: %{http_code}\n" https://informatica.ugelhuacaybamba.edu.pe/

echo ""
echo "=== VERIFICATION COMPLETE ==="
```

---

## Ejemplo Completo: Paso a Paso

Ver: [HOSTING-COMPARTIDO.md](HOSTING-COMPARTIDO.md) - Pasos 1-10

O si DocumentRoot está en raíz:
Ver: [SOLUTION-ALTERNATIVE-STRUCTURE.md](SOLUTION-ALTERNATIVE-STRUCTURE.md)

---

## Customización para SATA-QR Específico

Si necesitas cambios específicos para el proyecto SATA-QR:

1. **Multi-tenancy:** Verificar `config/tenancy.php`
2. **Roles y Permisos:** Ejecutar seeders: `php artisan db:seed --class=RolePermissionSeeder`
3. **Sync de SIAGI:** Ver `app/Services/SiagiService.php`
4. **Reportes:** Estructura almacenada en `storage/app/reports/`

---
