# SATA-QR — Guía de Instalación Rápida

## Subdominio: https://informatica.ugelhuacaybamba.edu.pe/

> **Última actualización**: 10 de marzo de 2026
> **Estado**: Production-Ready ✅

---

## ⚡ Checklist Rápido (5 pasos)

- [ ] **Paso 1:** Conectar vía SSH + Clonar repositorio
- [ ] **Paso 2:** Crear base de datos + usuario MySQL en cPanel
- [ ] **Paso 3:** Configurar .env con credenciales
- [ ] **Paso 4:** Ejecutar migraciones y seeds
- [ ] **Paso 5:** Verificar en navegador

---

## Paso 1️⃣: Conectar + Clonar

```bash
# Conectar vía SSH (datos en cPanel SSH Access)
ssh usuario@informatica.ugelhuacaybamba.edu.pe
# O si tienes IP:
ssh usuario@ip-servidor

# Moverse a la carpeta del subdominio
cd /home/ugelhuacaybamba/public_html/informatica
# (Si no existe, crearla: mkdir -p informatica && cd informatica)

# Limpiar carpeta si tiene algo
rm -rf *.*

# Clonar el código
git clone https://github.com/cybertcode/SATA-QR.git .
git checkout develop

# Instalar dependencias
composer install --no-dev --optimize-autoloader
npm install && npm run build
```

---

## Paso 2️⃣: Crear Base de Datos en cPanel

**URL**: `https://ugelhuacaybamba.edu.pe:2083/`

### Opción A: Vía cPanel GUI (fácil)

1. Buscar "**MySQL Databases**" en cPanel
2. Click en "**Create New Database**"
    - Name: `ugelhuacaybamba_sata_qr`
    - Click "Create Database"
3. Click en "**MySQL Users**"
    - Username: `ugelhuacaybamba_user`
    - Password: Generar 20+ caracteres (copiar)
    - Click "Create User"
4. Buscar "**Add User to Database**"
    - Database: `ugelhuacaybamba_sata_qr`
    - User: `ugelhuacaybamba_user`
    - Marcar TODAS las casillas
    - Click "Add"

✅ **Guardar credenciales para el paso 3**

### Opción B: Vía SSH (directo)

```bash
mysql -h localhost -u admin -p
```

Cuando pida password, ingresar la contraseña de root (pedir a IT si no sabes):

```sql
CREATE DATABASE ugelhuacaybamba_sata_qr;
CREATE USER 'ugelhuacaybamba_user'@'localhost' IDENTIFIED BY 'CONTRASEÑA_SEGURA_20_CARACTERES';
GRANT ALL PRIVILEGES ON ugelhuacaybamba_sata_qr.* TO 'ugelhuacaybamba_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## Paso 3️⃣: Configurar .env

```bash
# En el servidor, dentro de la carpeta informatica/

# Descargar el template
cp .env.production .env

# O copiar desde local: usa sftp/WinSCP para subir .env.production y renombrarlo a .env
```

**Editar `.env` vía nano:**

```bash
nano .env
```

Buscar y actualizar estas líneas (solo si no están ya):

```env
APP_URL=https://informatica.ugelhuacaybamba.edu.pe
DB_DATABASE=ugelhuacaybamba_sata_qr
DB_USERNAME=ugelhuacaybamba_user
DB_PASSWORD=la_contraseña_que_creaste_arriba
MAIL_HOST=mail.ugelhuacaybamba.edu.pe        # (Pedir a IT)
MAIL_USERNAME=sata@ugelhuacaybamba.edu.pe    # (Pedir a IT)
MAIL_PASSWORD=contraseña_email               # (Pedir a IT)
```

Guardar: `Ctrl + O` → Enter → `Ctrl + X`

**Generar APP_KEY:**

```bash
php artisan key:generate
```

---

## Paso 4️⃣: Migraciones + Seeds

```bash
# Dentro de /home/ugelhuacaybamba/public_html/informatica/

# Realizar migraciones
php artisan migrate --force

# Cargar datos iniciales (20 IEs, 623 estudiantes, roles, permisos)
php artisan db:seed --class=DatabaseSeeder

# Limpiar cachés
php artisan config:cache
php artisan route:cache
```

---

## Paso 5️⃣: Verificar en Navegador

Abrir en navegador:

```
https://informatica.ugelhuacaybamba.edu.pe/
```

### Posibles pantallas al abrir:

✅ **Pantalla de Login** (página principal de SATA-QR)

- Usuario: `superadmin@sata.test`
- Contraseña: `password`

✅ **Laravel Welcome Page** (página por defecto si es primera vez)

- Indica que Laravel está corriendo

❌ **Nunca debería ver:**

- "Directory Listing" / "Index Of"
- Error 404 / 500
- Blank page (check storage/logs/laravel.log)

---

## 🚨 Si hay problemas:

### Error: "Connection refused" a MySQL

```bash
# Verificar credenciales en .env
grep DB_ .env

# Conectar directamente a MySQL
mysql -h localhost -u ugelhuacaybamba_user -p
# Cuando pida password, ingresar la del usuario
# Si conecta: OK
# Si NO conecta: Usuario/contraseña incorrecta → recrear en cPanel
```

### Error: "Migraciones fallaron"

```bash
# Ver qué pasó:
php artisan migrate --force 2>&1

# Si dice "Base de datos no existe":
# Volver al Paso 2 y crear BD correctamente

# Si dice "Tabla ya existe":
# Es normal si ya se corrió antes. Ignorar y continuar.
```

### Blank Page / Error 500

```bash
# Ver logs
tail -f storage/logs/laravel.log

# Ajustar permisos
chmod -R 775 storage bootstrap/cache

# Limpiar cachés
php artisan cache:clear
php artisan config:clear
```

---

## ✅ Checklist Final (Antes de entregar)

- [ ] ✅ Acceder a `https://informatica.ugelhuacaybamba.edu.pe/`
- [ ] ✅ Ver página de login
- [ ] ✅ Ingresar con superadmin@sata.test / password
- [ ] ✅ Ver dashboard con instituciones + estudiantes
- [ ] ✅ Probar módulo de Configuración General
- [ ] ✅ Probar módulo de Usuarios
- [ ] ✅ Probar módulo de Instituciones
- [ ] ✅ Cambiar usuario a "Director" desde perfil
- [ ] ✅ Verificar que Director VE sus datos (aislados por IE)

---

## 📞 Contacto IT UGEL

**Para credenciales que necesitarás:**

- Credenciales SMTP (mail.ugelhuacaybamba.edu.pe)
- Acceso SSH/cPanel
- Ruta exacta de carpetas en el servidor

---

**¡Listo! 🚀 SATA-QR debería estar corriendo en:**

```
https://informatica.ugelhuacaybamba.edu.pe/
```
