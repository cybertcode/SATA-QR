# Diagnostic Script: Verificar Instalación Laravel en Hosting Compartido

Este documento contiene comandos y pruebas para diagnosticar problemas comunes.

## Parte 1: Verificación Rápida (5 minutos)

### 1.1 Conectar vía SSH

```bash
# Conectar al hosting
ssh usuario@ugelhuacaybamba.edu.pe

# Verificar ubicación
pwd
# Debe mostrar: /home/usuario/public_html/informatica (u otra ruta)

# Listar contenido
ls -la
# Debe mostrar: app, artisan, bootstrap, composer.json, public, routes, storage, vendor, .env, etc.
```

### 1.2 Verificar Versión PHP

```bash
php -v
# Debe ser >= PHP 8.2.0

# Verificar extensiones requeridas
php -m | grep -E "(pdo|mysql|mbstring|openssl|tokenizer|ctype|json|fileinfo|bcmath)"
# Todas deben aparecer
```

### 1.3 Verificar Archivo .env

```bash
# Ver contenido (sin revelar credenciales)
grep -E "^APP_|^DB_HOST|^APP_URL" .env

# Verificar APP_KEY existe
grep "APP_KEY" .env | grep -v "^#"
# Debe mostrar: APP_KEY=base64:xxxxx...

# Verificar APP_URL es correcto
grep "APP_URL" .env
# Debe ser: APP_URL=https://informatica.ugelhuacaybamba.edu.pe
```

### 1.4 Verificar Permisos Críticos

```bash
# Storage: debe estar con permisos 775
ls -la storage/ | head -1
# Debe mostrar: drwxrwxr-x

# Bootstrap cache
ls -la bootstrap/ | head -1
# Debe mostrar: drwxrwxr-x

# Si no, arreglar:
chmod 755 storage bootstrap
chmod -R 775 storage/* bootstrap/cache
```

### 1.5 Verificar .htaccess en public/

```bash
# Debe existir
ls -la public/.htaccess

# Ver primeras líneas
head -5 public/.htaccess
# Debe mostrar: <IfModule mod_rewrite.c>

# Debe ser legible y ejecutable
# Si no:
chmod 644 public/.htaccess
```

### 1.6 Verificar Accesibilidad de Archivos Sensibles

```bash
# Estos comandos se ejecutan DESDE EL NAVEGADOR (o con curl):

# ❌ .env NO debe ser accesible
curl https://informatica.ugelhuacaybamba.edu.pe/.env
# Debe retornar: 403 Forbidden o 404 Not Found
# NUNCA: 200 OK con contenido de .env

# ❌ vendor no debe listar directorios
curl https://informatica.ugelhuacaybamba.edu.pe/vendor/
# Debe retornar: 403 Forbidden o 404
# NUNCA: "Index of /vendor"

# ✅ index.php sí debe ser accesible
curl -I https://informatica.ugelhuacaybamba.edu.pe/
# Debe retornar: 200 OK
```

---

## Parte 2: Verificación de Configuración Laravel

### 2.1 Comprobar Base de Datos

```bash
# Conectar a MySQL/MariaDB
mysql -u usuario_db -p
# Ingresar contraseña

# Una vez dentro:
SHOW DATABASES;
# Debe aparecer: ugelhuacaybamba_sata_qr

# Verificar tablas
SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'ugelhuacaybamba_sata_qr';
# Si devuelve 0: base de datos vacía, necesita migrar

# Verificar usuario tiene permisos
SHOW GRANTS FOR 'usuario_db'@'localhost';
# Debe mostrar: GRANT ALL PRIVILEGES

EXIT;
```

### 2.2 Verificar Logs de Laravel

```bash
# Ver últimos errores
tail -20 storage/logs/laravel.log

# Ver errores en tiempo real
tail -f storage/logs/laravel.log
# Presionar Ctrl+C para salir

# Buscar errores específicos
grep -i "error\|exception" storage/logs/laravel.log | tail -10
```

### 2.3 Ejecutar Migraciones

```bash
# Ver estado de migraciones
php artisan migrate:status

# Si hay pendientes:
php artisan migrate --force
# --force es necesario en production

# Verifique que no hay errores
# Si hay error 500, revisar storage/logs/laravel.log
```

### 2.4 Limpiar Cache

```bash
# Limpiar todos los caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Reconstruir cache optimizado
php artisan config:cache
php artisan route:cache
```

---

## Parte 3: Pruebas de Routing

### 3.1 Verificar que mod_rewrite funciona

```bash
# Opción A: Desde servidor
apachectl -M | grep rewrite
# Debe mostrar: rewrite_module (shared)

# Opción B: Desde navegador/curl
curl -I https://informatica.ugelhuacaybamba.edu.pe/api/users
# Debe retorner 200 o 403 (NOT 404 indicaría fallo en rewrite)

curl -I https://informatica.ugelhuacaybamba.edu.pe/admin/dashboard
# Debe retorner 200, 302 (redirect) o 403 (no 404)
```

### 3.2 Verificar rutas específicas

```bash
# Supongamos que tienes una ruta GET /api/status
curl https://informatica.ugelhuacaybamba.edu.pe/api/status
# Debe retorner JSON, NO error 404

# Probar POST
curl -X POST https://informatica.ugelhuacaybamba.edu.pe/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"test"}'
# Debe procesar el request
```

### 3.3 Verificar Assets (CSS/JS)

```bash
# Verificar que build/ existe
ls -la public/build/

# Verificar manifest.json
cat public/build/manifest.json

# Si build/ está vacío:
npm install
npm run build  # Compilar assets

# Verificar desde navegador:
curl -I https://informatica.ugelhuacaybamba.edu.pe/build/assets/app.js
# Debe retorner 200
```

---

## Parte 4: Diagnóstico de Errores Específicos

### Error 500: Internal Server Error

```bash
# Paso 1: Verificar logs
tail -50 storage/logs/laravel.log

# Paso 2: Ver todas las extensiones PHP disponibles
php -m
# Buscar: openssl, pdo, pdo_mysql, mbstring, json, ctype, fileinfo, bcmath

# Paso 3: Verificar APP_KEY
grep APP_KEY .env | grep base64
# Si está vacío o sin base64:
php artisan key:generate

# Paso 4: Verificar permisos
ls -la storage bootstrap
chmod -R 775 storage/* bootstrap/cache

# Paso 5: Verificar DB connection
php artisan tinker
>>> DB::connection()->getPdo();
>>> exit()
# Debe retorner: Success o conexión exitosa
```

### Error 404 en todas las rutas

```bash
# Paso 1: Verificar Document Root es PUBLIC
# En cPanel o contactar soporte:
# Document Root DEBE ser: /home/usuario/public_html/informatica/public

# Paso 2: Verificar .htaccess existe
ls -la public/.htaccess

# Paso 3: Verificar mod_rewrite
apachectl -M | grep rewrite

# Paso 4: Reinstalar .htaccess (si es necesario)
# Descargar desde repositorio o copiar de abajo

# Paso 5: Verificar rewrites en cPanel
# En cPanel → PHP SELECTOR → Extensions
# Verificar mod_rewrite esté habilitado

# Paso 6: Test de rewrite
curl https://informatica.ugelhuacaybamba.edu.pe/nonexistent-file.txt
# Debe mostrar error de LARAVEL (403, error page)
# NO debe mostrar "File not found" de Apache
```

### Archivos .env visibles

```bash
# CRÍTICO: Esto es un problema de seguridad grave

# Verificar que NO es accesible
curl https://informatica.ugelhuacaybamba.edu.pe/.env
# DEBE retorner: 403 Forbidden
# SI retorna 200 OK: PROBLEMA GRAVE

# Solución: Document Root está en lugar equivocado
# Cambiar en cPanel a: /home/usuario/public_html/informatica/public

# Mientras tanto, proteger en .htaccess:
# Agregar al .htaccess de public/
<FilesMatch "^\.env">
    Deny from all
</FilesMatch>
```

### Blanco Page (White Screen)

```bash
# Activar debug temporalmente
nano .env
# Cambiar: APP_DEBUG=true

# Actualizar y recarguar
php artisan config:clear

# Ver en navegador
# Ahora debe mostrar mensaje de error específico

# Volver a desactivar
nano .env
# Cambiar: APP_DEBUG=false
php artisan config:clear
```

### Email no envía

```bash
# Verificar configuración
grep -i "MAIL_" .env

# Test con Tinker
php artisan tinker

>>> Mail::raw('Email de prueba', function($msg) {
    $msg->to('tuemail@test.com')
        ->subject('Test')
        ->from('sata@ugelhuacaybamba.edu.pe');
});

>>> exit()

# Revisar logs
tail -20 storage/logs/laravel.log | grep -i mail
```

---

## Parte 5: Script de Diagnóstico Automatizado

Guarda este script como `diag.sh`:

```bash
#!/bin/bash

echo "===== DIAGN脳STICO LARAVEL ====="
echo ""

# 1. PHP Version
echo "1. Versión PHP:"
php -v | head -1
echo ""

# 2. Extensiones
echo "2. Extensiones PHP (requeridas):"
php -r "
\$ext = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'json', 'ctype', 'fileinfo', 'bcmath', 'tokenizer'];
foreach (\$ext as \$e) {
    \$check = extension_loaded(\$e) ? '✓' : '✗';
    echo \$check . ' ' . \$e . PHP_EOL;
}
"
echo ""

# 3. Permisos
echo "3. Permisos de carpetas:"
ls -ld storage bootstrap | awk '{print $1, $NF}'
echo ""

# 4. .env
echo "4. Estado de .env:"
[ -f .env ] && echo "✓ .env existe" || echo "✗ .env NO existe"
grep -q "APP_KEY=base64:" .env && echo "✓ APP_KEY configurado" || echo "✗ APP_KEY NO configurado"
echo ""

# 5. Database
echo "5. Conexión a BD:"
php artisan tinker << 'EOF'
try {
    DB::connection()->getPdo();
    echo "✓ BD conectada correctamente\n";
} catch (\Exception $e) {
    echo "✗ Error BD: " . $e->getMessage() . "\n";
}
exit();
EOF
echo ""

# 6. Logs
echo "6. Últimos errores en logs:"
tail -5 storage/logs/laravel.log | grep -i error || echo "✓ Sin errores recientes"
echo ""

echo "===== FIN DIAGNÓSTICO ====="
```

Ejecutar:

```bash
chmod +x diag.sh
./diag.sh
```

---

## Parte 6: Verificación de Seguridad

### Checklist de Seguridad

```bash
#!/bin/bash

echo "===== VERIFICACIÓN DE SEGURIDAD ====="

# 1. .env visible
echo "1. .env accesible:"
curl -s -o /dev/null -w "%{http_code}" https://informatica.ugelhuacaybamba.edu.pe/.env
echo ""

# 2. vendor visible
echo "2. vendor listable:"
curl -s https://informatica.ugelhuacaybamba.edu.pe/vendor/ | grep -q "Index of" && echo "❌ PELIGRO" || echo "✓ OK"
echo ""

# 3. .git visible
echo "3. .git accesible:"
curl -s -o /dev/null -w "%{http_code}" https://informatica.ugelhuacaybamba.edu.pe/.git
echo ""

# 4. Composer.json visible
echo "4. composer.json visible:"
curl -s -o /dev/null -w "%{http_code}" https://informatica.ugelhuacaybamba.edu.pe/composer.json
echo ""

# 5. APP_DEBUG
echo "5. APP_DEBUG:"
grep "APP_DEBUG=" .env
echo ""

echo "===== FIN VERIFICACIÓN ====="
```

---

## Parte 7: Comandos Útiles Rápidos

```bash
# Ver status de la app
php artisan status

# Ver todas las rutas
php artisan route:list

# Ver migraciones pendientes
php artisan migrate:status

# Resetear BD completamente (PELIGRO - borrador datos)
php artisan migrate:fresh --seed

# Ver config cargada
php artisan config:show

# Probar que email funciona
php artisan mail:send

# Ver ambiente actual
php artisan env

# Ver version de Laravel
php artisan --version

# Optimization para production
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear everything
php artisan optimize:clear
```

---

## Resumen: Árbol de Decisión

```
¿La app funciona?
├─ NO → Error 500
│  ├─ Ver: storage/logs/laravel.log
│  ├─ Ejecutar: php artisan config:clear
│  ├─ Verificar: AppKey, permisos, BD
│  └─ Si persiste: APP_DEBUG=true
│
├─ Routing 404
│  ├─ Verificar: Document Root = /public
│  ├─ Verificar: .htaccess existe
│  ├─ Verificar: mod_rewrite habilitado
│  └─ Si nada funciona: Cambiar Document Root en cPanel
│
├─ Assets (CSS/JS) no cargan
│  ├─ Verificar: npm run build ejecutado
│  ├─ Verificar: APP_URL correcto
│  └─ Verificar: public/build/ tiene contenido
│
├─ .env accesible (HTTP 200)
│  ├─ CRÍTICO: Cambiar Document Root AHORA a /public
│  ├─ Temporalmente proteger con .htaccess:
│  │  <FilesMatch "^\.env">
│  │      Deny from all
│  │  </FilesMatch>
│  └─ Mover .env fuera de raíz web root
│
└─ Email no funciona
   ├─ Verificar: credenciales SMTP en .env
   ├─ Verificar: puerto (587 para TLS, 465 para SSL)
   ├─ Test manual: php artisan tinker → Mail::raw(...)
   └─ Ver logs: grep -i mail storage/logs/laravel.log
```
