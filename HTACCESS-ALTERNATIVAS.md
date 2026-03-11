# ⚙️ CONFIGURACIONES ALTERNATIVAS DE .HTACCESS

Según el tipo de problema, usa una de estas configuraciones.

---

## OPCIÓN 1: ESTÁNDAR (RECOMENDADA) - Ya está en uso

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On
    RewriteBase /

    # Deny access to .env files
    RewriteRule "^\.env" - [F]

    # Deny access to vendor and storage directories
    RewriteRule "^vendor/" - [F]
    RewriteRule "^storage/" - [F]
    RewriteRule "^bootstrap/" - [F]

    # Handle public folder - rewrite requests to public/
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(?!public/)(.*)$ public/$1 [L]

    # From public folder - rewrite to public/index.php
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)?$ public/index.php [L]

    # Security Headers
    <FilesMatch "\.(env|json|lock|md)$">
        Order allow,deny
        Deny from all
    </FilesMatch>

    <Files .env>
        order allow,deny
        deny from all
    </Files>
</IfModule>
```

---

## OPCIÓN 2: MINIMALISTA (Para hosting que no reconoce reglas complejas)

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ public/index.php?/$1 [QSA,L]
</IfModule>

<Files .env>
    order allow,deny
    deny from all
</Files>
```

---

## OPCIÓN 3: CON MANEJO DE QUERY STRING

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /

    # Deny .env
    RewriteRule "^\.env" - [F]

    # If it's NOT a directory or file, route to /public/index.php
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ public/index.php [L]
</IfModule>
```

---

## OPCIÓN 4: CON EXPLICITACIÓN DE EXCLUIR PUBLIC

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /

    # Don't rewrite if it's the public directory
    RewriteCond %{REQUEST_URI} ^/public/ [OR]
    RewriteCond %{REQUEST_FILENAME} -f [OR]
    RewriteCond %{REQUEST_FILENAME} -d
    RewriteRule ^ - [L]

    # Rewrite to public
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

---

## OPCIÓN 5: SI DOCUMENT ROOT YA APUNTA A PUBLIC/

(Si en cPanel el Document Root es `/home/ugelhuacaybamba/public_html/informatica/public`)

Entonces ELIMINA el .htaccess de raíz y usa SOLO el de public/.htaccess

**public/.htaccess (contenido):**

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

## OPCIÓN 6: SUPER AGRESIVA (Para hosting muy restrictivo)

```apache
Options -Indexes
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/index.php [L]
</IfModule>
```

---

## CÓMO PROBAR CADA OPCIÓN

1. **Edita `.htaccess` en raíz** con una de las opciones arriba
2. **Borra logs viejos** para evitar confusión:
    ```bash
    > storage/logs/laravel.log
    ```
3. **Accede a la URL:**
    ```
    https://informatica.ugelhuacaybamba.edu.pe/
    ```
4. **Revisa los logs:**
    ```bash
    tail -20 storage/logs/laravel.log
    ```

---

## COMPARACIÓN DE OPCIONES

| Opción | Complejidad | Seguridad | Mejor Para                  |
| ------ | ----------- | --------- | --------------------------- |
| 1      | Alta        | Máxima    | Hosting estándar            |
| 2      | Baja        | Media     | Hosting muy restrictivo     |
| 3      | Media       | Alta      | Problemas con query strings |
| 4      | Media       | Alta      | Testing                     |
| 5      | Baja        | Alta      | Si Document Root = public/  |
| 6      | Muy Baja    | Baja      | Emergencia                  |

---

## VERIFICACIÓN DESPUÉS DE CAMBIAR .HTACCESS

```bash
# Limpiar cache de Laravel
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Acceder a URL
curl https://informatica.ugelhuacaybamba.edu.pe/

# Ver status HTTP
curl -w "%{http_code}" https://informatica.ugelhuacaybamba.edu.pe/

# Si da 200, ¡éxito!
# Si da 301/302, hay redirección (revisar para dónde)
# Si da 404, no se encuentra
# Si da 500, error interno (ver logs)
```

---

## CHEQUEO: COMPARAR CON WORDPRESS

Como dice que WordPress funciona, vamos a copiar su configuración:

```bash
# Si WordPress está en otro directorio, ver su .htaccess
cat /home/ugelhuacaybamba/public_html/wordpress/.htaccess

# O donde esté WordPress
# Copiar la lógica de rewrite de WordPress y adaptarla para Laravel
```

La diferencia principal:

- **WordPress:** Reescribe todo a `index.php`
- **Laravel:** Reescribe a `public/index.php` (si raíz es Document Root)

---

## SOLUCIÓN FINAL SI NADA FUNCIONA

Si después de probar todas las opciones sigue sin funcionar:

1. **Contactar soporte del hosting y pedir:**
    - Confirmar que mod_rewrite está habilitado
    - Confirmar que AllowOverride All está configurado
    - Confirmar versión de PHP (debe ser 8.2 o superior)

2. **Mientras tanto, usar OPCIÓN 5:**
    - Pedir a soporte que cambie Document Root a `/public`
    - Así Laravel funciona sin necesidad de .htaccess en raíz

3. **O usar WORKAROUND:**
    - Crear symlink desde `index.php` a `public/index.php`
    - (Aunque esto no es recomendado)
