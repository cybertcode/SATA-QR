# SATA-QR - Sistema de Alerta Temprana y Control de Asistencia QR

## Descripción

Sistema web para la Unidad de Gestión Educativa Local (UGEL) Huacaybamba que permite el seguimiento en tiempo real de la asistencia estudiantil mediante códigos QR, actuando como motor de alerta temprana para prevenir la deserción escolar.

## Stack Tecnológico

- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** Tailwind CSS 4, Alpine.js, Livewire v3
- **Base de Datos:** MySQL/MariaDB
- **Autenticación:** Laravel Fortify (headless)
- **Autorización:** Spatie Laravel Permission (RBAC)
- **UI Template:** Tailwick
- **Escaneo QR:** html5-qrcode

## Requisitos del Sistema

- PHP 8.2 o superior
- Composer
- Node.js y npm
- MySQL 8.0+ o MariaDB
- Git

## Instalación

### Instalación Rápida (Hosting Compartido)

Para instalación en hosting compartido, sigue la [Guía de Instalación Rápida](GUIA-INSTALACION-RAPIDA.md).

### Instalación Local (Desarrollo)

1. Clona el repositorio:

    ```bash
    git clone <url-del-repositorio>
    cd qr
    ```

2. Instala dependencias de PHP:

    ```bash
    composer install
    ```

3. Instala dependencias de Node.js:

    ```bash
    npm install
    ```

4. Copia el archivo de configuración:

    ```bash
    cp .env.example .env
    ```

5. Configura la base de datos en `.env`:

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=sata_qr
    DB_USERNAME=tu_usuario
    DB_PASSWORD=tu_password
    ```

6. Genera la clave de aplicación:

    ```bash
    php artisan key:generate
    ```

7. Ejecuta las migraciones:

    ```bash
    php artisan migrate
    ```

8. Ejecuta los seeders:

    ```bash
    php artisan db:seed
    ```

9. Compila los assets:

    ```bash
    npm run build
    ```

10. Inicia el servidor:
    ```bash
    php artisan serve
    ```

## Configuración para Producción

Para configuración en hosting compartido, consulta [HOSTING-COMPARTIDO.md](HOSTING-COMPARTIDO.md).

## Uso

### Roles de Usuario

- **SuperAdmin:** Control total del sistema
- **Director:** Gestión de su institución
- **Docente:** Registro de asistencia
- **Auxiliar:** Soporte administrativo
- **Especialista UGEL:** Supervisión regional
- **Aliado Estratégico:** Colaboración externa

### Funcionalidades Principales

- Gestión de estudiantes e instituciones
- Generación de carnets con QR
- Escaneo de asistencia en tiempo real
- Dashboards analíticos
- Alertas tempranas de deserción
- Reportes y exportación de datos

## Desarrollo

### Principios SOLID

Todo el código debe adherirse a los principios SOLID. Consulta [SATA_HISTORY.md](SATA_HISTORY.md) para más detalles.

### Comandos Útiles

- Ejecutar tests: `php artisan test`
- Limpiar cache: `php artisan cache:clear`
- Compilar assets en desarrollo: `npm run dev`

## Contribución

1. Crea una rama desde `develop`
2. Realiza tus cambios siguiendo los principios SOLID
3. Asegúrate de que todos los tests pasen
4. Envía un Pull Request

## Licencia

Este proyecto está bajo la Licencia MIT.

## Contacto

Unidad de Gestión Educativa Local Huacaybamba - Área de Informática y Sistemas
