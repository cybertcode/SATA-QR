# Índice: Desplegar Laravel en Hosting Compartido

Este archivo organiza todos los recursos disponibles para desplegar SATA-QR en hosting compartido.

---

## 🚀 Inicio Rápido (5 minutos)

**Situación:** Quiero desplegar la aplicación ahora, sin complicaciones.

1. Leer: [HOSTING-COMPARTIDO.md](HOSTING-COMPARTIDO.md) (pasos 1-7)
2. Cambiar Document Root en cPanel a `/public/`
3. Ejecutar: `php artisan migrate --force`
4. Listo

**Si Document Root NO puede cambiar:** Ver [⚠️ Document Root en raíz](#document-root-en-raíz)

---

## 📚 Documentos Disponibles

### 1. **HOSTING-COMPARTIDO.md** (90% de casos)

**Cuándo usar:** Cuando tienes control sobre Document Root en cPanel

**Contenido:**

- Descargar código
- Instalar dependencias
- Configurar .env
- Crear base de datos
- Permisos de carpetas
- Verificar .htaccess
- Troubleshooting común

**Duración:** 30-60 min.

**Resultado:** Aplicación funcional con seguridad estándar

---

### 2. **HOSTING-COMPARTIDO-COMPLETO.md** (Guía exhaustiva)

**Cuándo usar:** Necesitas entender TODA la arquitectura

**Contenido:**

- Arquitectura de directorios
- Problemas comunes y síntomas
- 5 configuraciones de .htaccess diferentes
- Soluciones alternativas completas (symlinks, reorganizar, etc.)
- Checklist de seguridad
- Comparativa de soluciones

**Duración:** 2-3 horas de lectura

**Caso de uso ideal:** Arquitectos de sistemas, problemas complejos

---

### 3. **HTACCESS-CONFIGS.md** (Referencia)

**Cuándo usar:** Necesitas configuración de .htaccess específica

**Contenido:**

- 10 configuraciones de .htaccess listas para copiar/pegar
- Estándar (recomendada)
- Con HTTPS forzado
- Con www/sin www
- Subdirectorio
- Document Root incorrecto (fallback)
- Production-ready completa
- Con caché
- Con protección IP
- Múltiples apps

**Duración:** 5-10 min. (referencia)

**Caso de uso:** "Necesito .htaccess para X situación"

---

### 4. **DIAGNOSTIC-HOSTING.md** (Troubleshooting)

**Cuándo usar:** Algo no funciona y no sabes qué es

**Contenido:**

- Verificación rápida (5 min)
- Verificación de configuración Laravel
- Pruebas de routing
- Diagnóstico de errores:
    - Error 500
    - Error 404
    - Archivos .env visibles
    - Blanco page
    - Assets no cargan
    - Permisos 403
    - Email no envía
- Scripts automatizados de diagnóstico
- Árbol de decisión

**Duración:** 5-20 min. según problema

**Resultado:** Identificación rápida del problema

---

### 5. **SOLUTION-ALTERNATIVE-STRUCTURE.md** (Para casos especiales)

**Cuándo usar:** Document Root apunta a raíz y NO PUEDE cambiar

**Contenido:**

- Opción 1: Reorganizar directorios (recomendada)
- Opción 2: Usar symlinks
- Opción 3: Modificar index.php
- Opción 4: Solo .htaccess avanzado
- Paso a paso detallado
- Verificación
- Troubleshooting

**Duración:** 60-90 min. (opción 1)

**Caso de uso:** "El hosting no me permite cambiar Document Root"

---

## 🎯 Matriz de Decisión

### ¿Qué documento necesito?

```
┌─ ¿Primera vez desplegando?
│  ├─ SÍ → HOSTING-COMPARTIDO.md (inicio rápido)
│  └─ NO → [ver abajo]
│
├─ ¿Algo no funciona?
│  └─ DIAGNOSTIC-HOSTING.md (diagnosticar problema)
│
├─ ¿Necesitas .htaccess específico?
│  └─ HTACCESS-CONFIGS.md (copiar configuración)
│
├─ ¿Document Root está en raíz?
│  └─ SOLUTION-ALTERNATIVE-STRUCTURE.md
│
└─ ¿Quieres entender TODO?
   └─ HOSTING-COMPARTIDO-COMPLETO.md
```

---

## ⚠️ Document Root en Raíz

**Problema:** El hosting apunta el Document Root a `/home/usuario/public_html/informatica/` en lugar de `/public/`

### Síntomas:

- Directory Listing: muestra carpetas `app/`, `config/`, etc.
- `.env` accesible (HTTP 200)
- Credenciales de BD expuestas

### Soluciones (en orden):

1. **MEJOR:** Cambiar Document Root en cPanel a `/public/`
    - Contactar soporte del hosting si no sabes cómo
    - Tiempo: 5 min + propagación DNS (5-10 min)

2. **ALTERNATIVA:** Reorganizar directorios
    - Ver: [SOLUTION-ALTERNATIVE-STRUCTURE.md](SOLUTION-ALTERNATIVE-STRUCTURE.md) Opción 1
    - Mueve código confidencial fuera del web root
    - Tiempo: 60-90 min.

3. **TEMPORAL:** Proteger con .htaccess
    - Ver: [HTACCESS-CONFIGS.md](HTACCESS-CONFIGS.md) Configuración 6
    - Menos seguro, pero funciona mientras arreglas #1
    - Tiempo: 5 min.

---

## 🔍 Troubleshooting Rápido

### Error 500 al acceder

```bash
# Ir a: DIAGNOSTIC-HOSTING.md → Parte 4 → Error 500: Internal Server Error
# O ejecutar:
tail -20 storage/logs/laravel.log
php artisan config:clear
```

### Error 404 en todas las rutas

```bash
# Ir a: DIAGNOSTIC-HOSTING.md → Parte 4 → Error 404 en todas las rutas
# Verificar: Document Root apunta a /public ?
```

### .env visible (CRÍTICO)

```bash
# Ir a: DIAGNOSTIC-HOSTING.md → Parte 4 → Archivos .env visibles
# O directamente:
# 1. Cambiar Document Root en cPanel a /public
# 2. Si no puedes: SOLUTION-ALTERNATIVE-STRUCTURE.md
```

### Assets (CSS/JS) no cargan

```bash
# Ir a: DIAGNOSTIC-HOSTING.md → Parte 4 → Assets no cargan
# O ejecutar:
npm run build
```

---

## 📋 Checklist Pre-Deploy

Antes de hacer `git push` a producción:

- [ ] ¿Tienes acceso SSH al servidor?
- [ ] ¿Sabes la ruta del Document Root? (`/public_html/informatica/` o similar)
- [ ] ¿Acceso a cPanel para cambiar Document Root?
- [ ] ¿Acceso a crear Base de Datos MySQL?
- [ ] ¿APP_KEY generado? (`php artisan key:generate`)
- [ ] ¿Archivo .env preparado con credenciales reales?
- [ ] ¿Dominio/Subdominio configurado en DNS?
- [ ] ¿Certificado SSL habilitado?
- [ ] ¿Node.js instalado en servidor? (para build)
- [ ] ¿Composer instalado en servidor?

**Si dijiste NO a algo:** Contactar IT del hosting antes de continuar.

---

## 📞 Ayuda por Tipo de Hosting

### cPanel (Most Common)

- Docs: HOSTING-COMPARTIDO.md
- Document Root: cPanel → Addons Domains
- Base de Datos: cPanel → MySQL Databases
- SSH: cPanel → Terminal

### Plesk

- Document Root: Similar a cPanel
- Database: Plesk → Databases
- SSH: Plesk → Tools & Settings

### DirectAdmin

- Document Root: DirectAdmin → Domain Management
- Base de Datos: Similar a cPanel
- SSH: Similar a cPanel

### Archivo de Configuración

Si tu hosting usa otro Panel, los conceptos siguen siendo los mismos:

1. Document Root → /path/to/project/public
2. Base de Datos MySQL → Crear + asignar usuario
3. SSH → Conectar y ejecutar comandos de Laravel
4. .env → Configurar credenciales

---

## ⏱️ Tiempos Estimados

| Tarea                          | Tiempo    | Ruta de Docs                                         |
| ------------------------------ | --------- | ---------------------------------------------------- |
| Primera instalación            | 30-60 min | HOSTING-COMPARTIDO.md                                |
| Diagnosticar problema          | 5-20 min  | DIAGNOSTIC-HOSTING.md                                |
| Arreglar routing 404           | 15-30 min | HOSTING-COMPARTIDO-COMPLETO.md + HTACCESS-CONFIGS.md |
| Reorganizar estructura         | 60-90 min | SOLUTION-ALTERNATIVE-STRUCTURE.md                    |
| Entender arquitectura completa | 2-3 horas | HOSTING-COMPARTIDO-COMPLETO.md                       |

---

## 🔐 Security First

**NUNCA HACER:**

- ❌ Subir `.env` a git
- ❌ Dejar Document Root en raíz sin protección
- ❌ APP_DEBUG=true en production
- ❌ Usar credenciales débiles en MySQL
- ❌ Olvidar HTTPS

**SIEMPRE HACER:**

- ✅ Cambiar Document Root a `/public/`
- ✅ Proteger `.env` con .htaccess (de respaldo)
- ✅ APP_DEBUG=false en production
- ✅ Contraseñas MySQL 20+ caracteres aleatorias
- ✅ Certificado SSL (Let's Encrypt es gratis)
- ✅ Ejecutar `php artisan optimize` en production

---

## 🎓 Conceptos Clave

### Document Root

- Carpeta que el servidor web sirve públicamente
- **Correcto:** `/path/to/project/public/`
- **Incorrecto:** `/path/to/project/`
- **Cambiar en:** cPanel, Plesk, o archivo de config

### .env

- Archivo con credenciales y configuración
- **NUNCA debe ser accesible públicamente**
- Se lee SOLO en el servidor
- Valores únicos por servidor

### .htaccess

- Archivo de reescrituras HTTP (Apache)
- Transforma URLs para Laravel (`/users` → `/index.php?request=/users`)
- Requiere `mod_rewrite` habilitado
- Alternativa: nginx.conf (si usas Nginx)

### Migraciones

- Scripts SQL que crean/modifican la BD
- `php artisan migrate` las ejecuta
- `--force` flag para production (sin confirmación)

---

## 📖 Referencias Externas

- **Documentación Laravel:** https://laravel.com/docs/11/deployment
- **Apache .htaccess:** https://httpd.apache.org/docs/current/mod/mod_rewrite.html
- **cPanel Docs:** https://docs.cpanel.net/
- **MySQL Basics:** https://dev.mysql.com/doc/

---

## 📝 Historial de Cambios

| Fecha      | Cambio                                           |
| ---------- | ------------------------------------------------ |
| 2026-03-10 | Versión inicial con 5 documentos inclusos        |
|            | Reorganización completa de HOSTING-COMPARTIDO.md |
|            | Creación de HOSTING-COMPARTIDO-COMPLETO.md       |
|            | Creación de HTACCESS-CONFIGS.md con 10 opciones  |
|            | Creación de DIAGNOSTIC-HOSTING.md                |
|            | Creación de SOLUTION-ALTERNATIVE-STRUCTURE.md    |

---

## ❓ FAQ Rápido

**P: ¿Cuál documento leo primero?**
A: HOSTING-COMPARTIDO.md (pasos 1-7). Luego los otros según necesites.

**P: Mi hosting no me deja cambiar Document Root**
A: Lee SOLUTION-ALTERNATIVE-STRUCTURE.md Opción 1

**P: ¿Cómo sé si .env está protegido?**
A: `curl https://tudominio/.env` debe retornar 403, NO 200

**P: ¿Debo tener Node.js en el servidor?**
A: Idealmente sí. Si no, compilar assets localmente con `npm run build` y subir la carpeta `public/build/`

**P: ¿Apache vs Nginx?**
A: Casi todos los hostings usan Apache. Los comandos son iguales. Los .htaccess funcionan en Apache. Nginx usa nginx.conf (ver con soporte)

**P: ¿Cómo automatizo deployes?**
A: Después de la primera instalación funcional, usa git webhooks o cron para `git pull && php artisan migrate`

---

## 🎯 Contacto para Dudas

- **Error técnico específico:** Ver DIAGNOSTIC-HOSTING.md
- **Dudas sobre .htaccess:** Ver HTACCESS-CONFIGS.md
- **Quiero entender TODO:** Lee HOSTING-COMPARTIDO-COMPLETO.md
- **Document Root problema:** Ver SOLUTION-ALTERNATIVE-STRUCTURE.md

---

**Última actualización:** 10 de marzo, 2026
