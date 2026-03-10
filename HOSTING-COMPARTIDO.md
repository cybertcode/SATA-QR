# SATA-QR — Configuración para Hosting Compartido

## Requisitos Mínimos

- PHP 8.2 o superior
- MySQL 5.7+ o MariaDB 10.3+
- Composer instalado
- Node.js 18+ (para build)
- Extensiones PHP: OpenSSL, PDO, Mbstring, JSON, Ctype, BCMath

## 1. Descargar el Código

Para subdominio: `https://informatica.ugelhuacaybamba.edu.pe/`

```bash
# Conectar vía SSH al hosting
ssh usuario@ugelhuacaybamba.edu.pe

# Navegar a la carpeta del subdominio
cd /home/ugelhuacaybamba/public_html/informatica
# O verificar ruta exacta con:
pwd

# Clonar el repositorio
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

### Ejemplo .env para UGEL Huacaybamba (Subdominio informatica):

```env
APP_NAME=SATA-QR
APP_ENV=production
APP_KEY=base64:xxxxx  # Generado por artisan key:generate
APP_DEBUG=false
APP_URL=https://informatica.ugelhuacaybamba.edu.pe

# Base de datos (MySQL/MariaDB) - Contactar a soporte del hosting
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=ugelhuacaybamba_sata_qr
DB_USERNAME=ugelhuacaybamba_user
DB_PASSWORD=contraseña_segura_mysql

# Cache y Session
CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

# Mail (Contactar a IT para credenciales SMTP)
MAIL_MAILER=smtp
MAIL_HOST=mail.ugelhuacaybamba.edu.pe
MAIL_PORT=587
MAIL_USERNAME=sata@ugelhuacaybamba.edu.pe
MAIL_PASSWORD=contraseña_email
MAIL_FROM_ADDRESS=sata@ugelhuacaybamba.edu.pe
MAIL_FROM_NAME="SATA-QR UGEL Huacaybamba"

# Locale Perú
APP_LOCALE=es
APP_FALLBACK_LOCALE=es
APP_TIMEZONE=America/Lima
```

## 4. Crear Base de Datos en cPanel

**El hosting de UGEL Huacaybamba usa cPanel. Seguir estos pasos:**

1. Acceder a cPanel: `https://ugelhuacaybamba.edu.pe:2083/`
2. Buscar "MySQL Databases" o "Bases de Datos MySQL"
3. **Crear nueva BD:**
    - Nombre: `ugelhuacaybamba_sata_qr`
    - Guardar las credenciales
4. **Crear usuario MySQL:**
    - Usuario: `ugelhuacaybamba_user`
    - Contraseña: Generar contraseña segura (20+ caracteres)
5. **Asignar usuario a BD:**
    - Marcar TODAS las casillas de permisos
    - Click en "Add User to Database"

**Guardar credenciales en .env (paso anterior)**

### Alternativa vía SSH:

```bash
mysql -u admin -p
CREATE DATABASE ugelhuacaybamba_sata_qr;
CREATE USER 'ugelhuacaybamba_user'@'localhost' IDENTIFIED BY 'contraseña_segura_20caracteres';
GRANT ALL PRIVILEGES ON ugelhuacaybamba_sata_qr.* TO 'ugelhuacaybamba_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 5. Ejecutar Migraciones

```bash
php artisan migrate --force
php artisan db:seed --class=DatabaseSeeder
```

## 6. Configurar Web Root en cPanel (CRÍTICO)

**Subdominio:** `https://informatica.ugelhuacaybamba.edu.pe/`

### Pasos en cPanel:

1. Acceder a cPanel: `https://ugelhuacaybamba.edu.pe:2083/`
2. Ir a **"Addons Domains"** o **"Dominios Adicionales"**
3. Buscar `informatica.ugelhuacaybamba.edu.pe` (debe estar pre-creado ya)
4. Editar y configurar:
    - **Document Root:** `/home/ugelhuacaybamba/public_html/informatica/public`
    - (IMPORTANTE: termina en `/public`, NO en la raíz del proyecto)
5. Guardar cambios
6. **Esperar 5-10 minutos** para que replique

### Estructura correcta en servidor:

```
/home/ugelhuacaybamba/public_html/informatica/
├── .env                    ← NO accesible públicamente ✅
├── .gitignore
├── composer.json
├── artisan
├── app/
├── routes/
├── resources/
├── database/
├── public/                 ← ← ← DOCUMENT ROOT APUNTA AQUÍ
│   ├── index.php
│   ├── .htaccess           ← NECESARIO para rewrites
│   ├── build/              ← Assets compilados
│   ├── images/
│   └── js/
├── storage/                ← Necesita permisos 775
├── bootstrap/              ← Necesita permisos 775
└── vendor/
```

### Verificar que funciona:

```bash
# Abrir en navegador:
https://informatica.ugelhuacaybamba.edu.pe/

# Debe mostrar:
# ✅ Página de Login de SATA-QR (si está migrada)
# ✅ Ó laravel welcome page (si es primera vez)
# ❌ NUNCA: "Directory Listing" / "Index Of" / Error 404
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

### Si mod_rewrite no funciona:

1. Verificar que Apache tenga `mod_rewrite` habilitado:

    ```bash
    apachectl -M | grep rewrite
    # Debe mostrar: rewrite_module (shared)
    ```

2. Si no está habilitado, contactar al soporte del hosting para activarlo.

3. Alternativa: Si el hosting no permite mod_rewrite, usar configuración sin rewrites (menos recomendado):
    ```apache
    DirectoryIndex index.php
    Options -Indexes
    ```

## 9. Troubleshooting Común

### Error 500 - Internal Server Error

- Verificar `.env` existe y APP_KEY está generado
- Permisos de carpetas: `storage` y `bootstrap/cache` deben ser 775
- Logs en `storage/logs/laravel.log`

### Error 404 - Not Found

- Document Root apunta a `/public`? Verificar en cPanel
- `.htaccess` existe en `/public`?
- `mod_rewrite` habilitado?

### Error de Base de Datos

- Credenciales correctas en `.env`?
- Usuario MySQL tiene permisos en la BD?
- Host es `localhost` (no IP externa)?

### Blanco Page (White Screen)

- `APP_DEBUG=true` temporalmente para ver errores
- Verificar PHP version >= 8.2
- Extensiones PHP instaladas (ver requisitos arriba)

### Assets no cargan (CSS/JS)

- Ejecutar `npm run build` en servidor?
- Paths correctos en `public/build/`?

### Verificar instalación:

```bash
# Probar rutas básicas
curl -I https://informatica.ugelhuacaybamba.edu.pe/
# Debe retornar 200 OK

# Probar PHP
php -v
# Debe ser 8.2+

# Probar Laravel
php artisan --version
# Debe mostrar Laravel Framework 12.x.x
```

## 10. Configuración de Subdominio en cPanel (Detallada)

Si el subdominio no existe aún:

1. En cPanel → **Subdomains**
2. **Subdomain:** `informatica`
3. **Domain:** `ugelhuacaybamba.edu.pe`
4. **Document Root:** `/home/ugelhuacaybamba/public_html/informatica`
5. Crear

Luego, subir el proyecto a `/home/ugelhuacaybamba/public_html/informatica/`

**IMPORTANTE:** El Document Root debe apuntar a la carpeta `public` dentro del proyecto Laravel, NO a la raíz del proyecto.

### Estructura final correcta:

```
/home/ugelhuacaybamba/public_html/informatica/
├── app/
├── config/
├── database/
├── public/          ← Document Root apunta aquí
│   ├── index.php
│   ├── .htaccess
│   └── ...
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
├── composer.json
└── artisan
```

## 11. Configuración de SSL (Opcional pero Recomendado)

1. En cPanel → **SSL/TLS Status**
2. Buscar `informatica.ugelhuacaybamba.edu.pe`
3. **Run AutoSSL** para certificado gratuito Let's Encrypt

## 12. Monitoreo y Logs

- Logs de Laravel: `storage/logs/laravel.log`
- Logs de Apache: `/usr/local/apache/logs/error_log`
- Verificar con: `tail -f storage/logs/laravel.log`

## Contacto de Soporte

Para problemas específicos del hosting UGEL:

- Contactar al área de Informática y Sistemas
- Proporcionar URL exacta del error
- Incluir logs relevantes
  RewriteCond %{HTTP:Authorization} .
  RewriteRule ._ - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
  RewriteCond %{http_host} ^www\.(._)$ [NC]
  RewriteRule ^(.\*)$ https://%1/$1 [R=301,L]
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
  RewriteRule ^ %1 [L,R=301]
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^ index.php [L]
  </IfModule>

````

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
````

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
