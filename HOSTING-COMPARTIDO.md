# SATA-QR — Configuración para Hosting Compartido

## Requisitos Mínimos
- PHP 8.2 o superior
- MySQL 5.7+ o MariaDB 10.3+
- Composer instalado
- Node.js 18+ (para build)
- Extensiones PHP: OpenSSL, PDO, Mbstring, JSON, Ctype, BCMath

## 1. Descargar el Código

```bash
cd /ruta/publica/hosting
git clone https://github.com/cybertcode/SATA-QR.git .
git checkout develop
```

## 2. Instalar Dependencias

```bash
# PHP
composer install --no-dev --optimize-autoloader

# Node.js
npm install
npm run build  # o 'npm run build:prod' en hosting
```

## 3. Configurar .env para Producción

```bash
# Copiar plantilla
cp .env.example .env

# Generar APP_KEY
php artisan key:generate

# Editar .env con credenciales de hosting
```

### Ejemplo .env para Hosting Compartido:

```env
APP_NAME=SATA-QR
APP_ENV=production
APP_KEY=base64:xxxxx  # Generado por artisan key:generate
APP_DEBUG=false
APP_URL=https://tu-dominio.com

# Base de datos (MySQL/MariaDB)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nombre_base_datos
DB_USERNAME=usuario_mysql
DB_PASSWORD=contraseña_mysql

# Cache y Session
CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

# Mail (si usas)
MAIL_MAILER=smtp
MAIL_HOST=mail.tu-hosting.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@dominio.com
MAIL_PASSWORD=contraseña-email
MAIL_FROM_ADDRESS=sata@tu-dominio.com

# Locale
APP_LOCALE=es
APP_FALLBACK_LOCALE=es
```

## 4. Crear Base de Datos

```bash
# En cPanel o DirectAdmin
# 1. Crear base de datos MySQL
# 2. Crear usuario MySQL
# 3. Asignar usuario a BD con permisos TOTALES

# O vía SSH:
mysql -u root -p
CREATE DATABASE sata_qr;
CREATE USER 'sata_user'@'localhost' IDENTIFIED BY 'contraseña_segura';
GRANT ALL PRIVILEGES ON sata_qr.* TO 'sata_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 5. Ejecutar Migraciones

```bash
php artisan migrate --force
php artisan db:seed --class=DatabaseSeeder
```

## 6. Configurar Web Root (IMPORTANTE)

El documento root debe apuntar a la carpeta **`public/`** del proyecto.

### En cPanel:
1. Document Root: `/public_html/sata/public`
2. Asegurar que StaticHTML está deshabilitado

### En DirectAdmin:
1. Modificar domain.conf
2. Apuntar a `/home/usuario/domains/dominio.com/public`

### Estructura esperada:
```
/home/usuario/domains/dominio.com/
├── .env (fuera de public)
├── public/              ← Document Root apunta aquí
│   ├── index.php
│   ├── .htaccess        ← Necesario para rewrites
│   ├── build/
│   └── images/
├── app/
├── routes/
└── resources/
```

## 7. Permisos de Carpetas (CRÍTICO)

```bash
# Las siguientes carpetas necesitan permisos de escritura:
chmod 755 storage
chmod 755 bootstrap/cache
chmod -R 775 storage/logs
chmod -R 775 storage/framework
chmod -R 775 storage/app

# Subdirectorios con permisos de lectura:
find storage bootstrap -type d -exec chmod 755 {} \;
find storage bootstrap -type f -exec chmod 644 {} \;
```

## 8. Verificar .htaccess

Asegúrate que `/public/.htaccess` existe y contiene:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
    RewriteCond %{http_host} ^www\.(.*)$ [NC]
    RewriteRule ^(.*)$ https://%1/$1 [R=301,L]
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

## 9. Optimizaciones para Producción

```bash
# Cache de configuración
php artisan config:cache

# Cache de rutas
php artisan route:cache

# Cache de vistas (opcional)
php artisan view:cache

# Limpiar cachés
php artisan cache:clear
```

## 10. SSL/HTTPS (OBLIGATORIO)

- Usar AutoSSL (Let's Encrypt) si está disponible
- Actualizar `APP_URL=https://tu-dominio.com` en .env
- Forzar HTTPS viía `.htaccess` o panel de hosting

## 11. Solucionar Problemas Comunes

### ❌ Error 500 - Check Storage/logs
```bash
tail -f storage/logs/laravel.log
```

### ❌ Blank Page / No Connection
- Verificar APP_DEBUG=false
- Verificar carpetas de storage con permisos 775
- Verificar .htaccess está presente

### ❌ Errores de Base de Datos
- Verificar credenciales en .env
- Verificar servidor MySQL está corriendo
- Ejecutar: `php artisan migrate:fresh --seed`

### ❌ Problemas de Almacenamiento
```bash
php artisan storage:link  # Si no existe symbolic link
chmod 755 public/storage
```

## 12. Monitoreo y Mantenimiento

```bash
# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Limpiar archivos temporales
php artisan tinker
// RemoveTempFiles::dispatch();

# Hacer backup de BD
mysqldump -u usuario -p base_datos > backup.sql
```

## 13. Soporte Técnico

**Documentación oficial**: https://laravel.com/docs/12/installation

---

**Última actualización**: 10 de marzo de 2026
**Estado**: Production-Ready ✅
**Tests**: 222 passing, 464 assertions
