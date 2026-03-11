# 🔍 DIAGNÓSTICO COMPLETO PARA LARAVEL EN HOSTING

Ejecuta estos comandos en orden vía SSH para identificar el problema.

## PASO 1: Verificar Versión de PHP y Extensiones

```bash
# Versión PHP (debe ser >= 8.2)
php -v

# Extensiones crítticas para Laravel (debe haber un ✓ en todas)
php -m | grep -E "^(pdo|PDO|mysql|MySQL|mbstring|Mbstring|openssl|OpenSSL|tokenizer|Tokenizer|ctype|Ctype|json|JSON|fileinfo|Fileinfo|bcmath|BCMath)$"

# Verificar extensión PDO MySQL específicamente
php -m | grep -i "pdo"
php -m | grep -i "mysql"

# Si lo anterior no funciona, listar TODAS las extensiones
php -m
```

**Esperado:**

- PHP 8.2 o superior
- Extensiones: pdo, pdo_mysql, mbstring, openssl, tokenizer, ctype, json, fileinfo, bcmath

---

## PASO 2: Verificar que mod_rewrite está habilitado

```bash
# Verificar si mod_rewrite está habilitado en Apache
apachectl -M | grep rewrite

# O verificar directamente
apache2ctl -M | grep rewrite
```

**Esperado:**

```
rewrite_module (shared)
```

Si no aparece nada, contactar a soporte para habilitar mod_rewrite.

---

## PASO 3: Verificar Estructura de Directorios

```bash
cd /home/ugelhuacaybamba/public_html/informatica

# Verificar que todos los directorios existen
ls -la .env .htaccess public/index.php storage/logs bootstrap/cache

# Verificar que public/.htaccess existe
ls -la public/.htaccess

# Ver toda la estructura
tree -L 2 (o si no tienes tree: find . -maxdepth 2 -type f | head -20)
```

**Esperado:**

- `.env` debe existir (si no, renombra `.env.production` a `.env`)
- `.htaccess` en raíz
- `public/.htaccess` también debe existir
- `storage/logs/` debe ser escribible

---

## PASO 4: Verificar Contenido de .env

```bash
# Ver .env (primeras líneas)
head -20 .env

# Verificar APP_KEY
grep "APP_KEY=" .env | cut -c1-50  # Debe NO estar vacío

# Verificar APP_DEBUG
grep "APP_DEBUG=" .env

# Verificar base de datos
grep "DB_" .env
```

**Esperado:**

- `APP_KEY=base64:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX` (NO vacío)
- `APP_DEBUG=true` (temporalmente para diagnosticar)
- DB\_\* con credenciales correctas

Si APP_KEY está vacío:

```bash
php artisan key:generate
```

---

## PASO 5: Verificar Permisos

```bash
# Permisos de storage y bootstrap
ls -la storage/ bootstrap/

# Deben tener permiso 775 (rwxrwxr-x) para el propietario
# Si no, ejecutar:
chmod -R 775 storage bootstrap
chmod -R 775 storage/logs storage/framework storage/app
```

---

## PASO 6: Limpiar Cache Laravel

```bash
cd /home/ugelhuacaybamba/public_html/informatica

# Limpiar todos los caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Opcional: regenerar caches (más rápido en producción)
php artisan optimize
```

---

## PASO 7: Verificar Database Connection

```bash
# Probar conexión a BD
php artisan tinker

# Dentro de tinker, ejecutar:
DB::connection()->getPdo();

# Debe retornar un objeto PDO. Si hay error, verificar credenciales en .env
exit
```

---

## PASO 8: Revisar Logs

```bash
# Log de Laravel
tail -50 storage/logs/laravel.log

# Si no hay archivo, crear uno
touch storage/logs/laravel.log
chmod 666 storage/logs/laravel.log

# Ver últimos errores del servidor Apache
tail -50 /usr/local/apache/logs/error_log

# O si no existe esa ruta, buscar error_log:
find / -name "error_log" -type f 2>/dev/null | head -5
```

---

## PASO 9: Probar URL Directa

```bash
# Acceder directamente a public/index.php
curl -I https://informatica.ugelhuacaybamba.edu.pe/public/index.php

# Debe retornar: HTTP/2 200 OK
# Si retorna 500, el problema es dentro de Laravel
# Si retorna 404, el archivo no se encuentra
# Si retorna 403, permiso denegado

# Ver respuesta completa (primeras líneas)
curl -v https://informatica.ugelhuacaybamba.edu.pe/public/index.php 2>&1 | head -30
```

---

## PASO 10: Verificar que .env no es accesible públicamente

```bash
# Lo PEOR sería que .env sea visible públicamente
curl https://informatica.ugelhuacaybamba.edu.pe/.env

# Debe retornar 403 Forbidden o similar
# Si retorna el contenido de .env, tenemos un problema de seguridad CRÍTICO
```

---

## RESUMEN DE PROBLEMAS Y SOLUCIONES

### Problema: Error 500 al acceder

**Diagnosticar:**

```bash
# 1. Ver el error específico
grep "ERROR" storage/logs/laravel.log | tail -5

# 2. Caso común: APP_KEY vacío
php artisan key:generate

# 3. Caso común: Conexión a BD
php artisan tinker
DB::connection()->getPdo();
exit

# 4. Caso común: Permisos
chmod -R 775 storage bootstrap
```

### Problema: Error 404 al acceder

**Diagnosticar:**

```bash
# 1. Verificar que public/.htaccess existe
ls -la public/.htaccess

# 2. Verificar que .htaccess en raíz existe
ls -la .htaccess

# 3. Verificar que mod_rewrite está habilitado
apachectl -M | grep rewrite

# 4. Caso específico: Document Root mal configurado
# Si cPanel tiene Document Root en /public, NO necesitas .htaccess en raíz
```

### Problema: Directory Listing (muestra carpetas)

**Solución:**

```bash
# Significa que .htaccess no se está leyendo
# Contactar soporte del hosting para:
# 1. Habilitar mod_rewrite
# 2. Cambiar AllowOverride None → AllowOverride All
```

### Problema: .env es visible en el navegador

**Solución inmediata:**

```apache
# En .htaccess de raíz (ya incluido), pero verificar:
<Files .env>
    order allow,deny
    deny from all
</Files>
```

---

## COMPARACIÓN CON WORDPRESS (por qué WordPress funciona)

WordPress funciona porque:

- ✅ Tiene un `.htaccess` más simple
- ✅ No requiere generar clave criptográfica
- ✅ Escribe en base de datos sin necesitar migraciones previas
- ✅ Sus archivos ejecutables se encuentran directamente en el Document Root

Laravel requiere:

- 🔴 APP_KEY generado (cripto)
- 🔴 Migraciones de BD ejecutadas
- 🔴 public/ como Document Root o reescritura inteligente
- 🔴 Permisos en storage/ y bootstrap/

---

## CHECKLIST RÁPIDO

```
[ ] PHP >= 8.2 (php -v)
[ ] PDO + PDO_MySQL (php -m)
[ ] mod_rewrite habilitado (apachectl -M)
[ ] .env renombrado de .env.production
[ ] APP_KEY no está vacío (grep APP_KEY .env)
[ ] DB_* credenciales correctas (grep DB_ .env)
[ ] Permisos storage/bootstrap en 775
[ ] Laravel logs sin errores (tail storage/logs/laravel.log)
[ ] BD conexión OK (php artisan tinker → DB::connection()->getPdo())
[ ] .env no visible públicamente (curl https://tudominio/.env)
```

Si TODOS pasan ✓ pero sigue sin funcionar, ejecutar:

```bash
php artisan migrate --force
php artisan db:seed --class=DatabaseSeeder
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Luego: **Accede a https://informatica.ugelhuacaybamba.edu.pe/ y comparte qué ves exactamente.**
