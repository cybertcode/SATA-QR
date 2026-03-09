# HISTORIAL DE DESARROLLO - SATA-QR (UGEL Huacaybamba)

> **Stack:** Laravel 12 · PHP 8.2 · Livewire v3 · Rappasoft Datatables v3 · Tailwind CSS 4 · Alpine.js · MySQL
> **Rama:** `develop`
> **Principios de diseño:** SOLID obligatorio en todo el desarrollo

### Principios SOLID — Requisito de Desarrollo

Todo código producido en este proyecto DEBE adherirse a los principios SOLID:

| Principio                     | Aplicación en SATA-QR                                                                                                                                      |
| ----------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **S** — Single Responsibility | Cada clase/componente tiene una única razón de cambio. Ej: `UserManager` (CRUD + modales), `UsersTable` (datatable), `StudentImportService` (importación). |
| **O** — Open/Closed           | Abierto a extensión, cerrado a modificación. Usar traits, interfaces y eventos para extender funcionalidad sin alterar clases existentes.                  |
| **L** — Liskov Substitution   | Las subclases y componentes deben ser intercambiables sin romper el contrato. Respetar los contratos de Livewire, Rappasoft y Eloquent.                    |
| **I** — Interface Segregation | Interfaces pequeñas y específicas. No forzar dependencias innecesarias. Separar contratos (ej: exportación, importación, notificación).                    |
| **D** — Dependency Inversion  | Depender de abstracciones, no de implementaciones concretas. Usar inyección de dependencias de Laravel, Service Container y contratos (`Contracts`).       |

---

## 2026-03-06 — Fase 1: Análisis, Infraestructura Base y Módulos Core

### Commit `4cf03ab` — Template base Tailwick

- Plantilla Tailwick instalada con módulos UI genéricos (Dashboards, HR, Apps, E-commerce).
- Estructura MVC estándar de Laravel 12.

### Commit `b421996` — Estructura de BD y Modelos (3NF)

- Arquitectura Single-Database con `tenant_id` para aislamiento multi-tenant.
- Migraciones: `tenants`, `tenant_niveles`, `anios_lectivos`, `estudiantes`, `aliados_estrategicos`, `secciones`, `matriculas`, `asistencias`, `alertas_tempranas`, `intervenciones`, `calendario_feriados`, `configuracion_asistencia`.
- Modelos Eloquent con relaciones, scopes y casts para todas las entidades.

### Commit `1cade95` — Seguridad: RBAC con Spatie

- `spatie/laravel-permission` configurado con roles: SuperAdmin, Director, Docente, Auxiliar.
- Middleware `CheckRole` para protección de rutas.
- Seeder `RolesAndPermissionsSeeder` con permisos granulares.
- Login con `laravel/fortify` headless usando formularios nativos de Tailwick.

### Commit `090613a` — Escáner QR v7.1 (Modo Kiosko)

- Interfaz de escaneo inmersiva con `html5-qrcode`.
- Selector dinámico de cámaras, validación visual < 1.5s.
- Algoritmo cooldown antifraude.
- Registro manual para estudiantes sin carnet.

### Commit `ff2b2dc` — Dashboards Administrativos y Gestión Estudiantil

- Dashboards analíticos con ApexCharts.
- Importación masiva de estudiantes desde formatos SIAGIE (CSV/Excel).
- Servicio `StudentImportService` para procesamiento por lotes.

### Commit `9e287e9` — Reestructuración y Localización

- Eliminación de vistas genéricas de la plantilla (apps, HR, e-commerce, landing, layouts-eg).
- Regionalización Perú: zona horaria `America/Lima`, locale `es`, formato de fechas peruano.
- Soporte multi-modular (Códigos Modulares por I.E.).
- Identidad institucional: logo UGEL Huacaybamba en Sidenav, Topbar y Login.

---

## 2026-03-07 — Fase 2: Módulo de Usuarios (Livewire + Rappasoft)

### Migración a Livewire v3 + Rappasoft DataTables

- Migración completa del módulo Usuarios de Blade tradicional a arquitectura reactiva.
- **Componente padre:** `UserManager` — CRUD completo, modales y estadísticas.
- **Componente hijo:** `UsersTable` — DataTableComponent de Rappasoft con:
    - Columnas colapsables (`setCollapsingColumnsEnabled`)
    - Filtros por rol, estado e institución
    - Selección de columnas con query string limpio (`setQueryStringForColumnSelectDisabled`)
    - Búsqueda global por nombre, email y DNI
    - Exportación masiva: Excel, CSV, PDF
    - Acciones bulk: Activar/Desactivar seleccionados (con confirmación SweetAlert2)

### Nuevos roles y campos de usuario

- Migración `update_role_enum`: añadido rol `Administrador` al enum de la tabla `users`.
- Migración `add_status_fields`: campos `is_active`, `last_login_at`, `last_login_ip` en tabla `users`.
- Seeder `MassUserSeeder`: generación de 250 usuarios de prueba distribuidos por rol e institución.

### Componentes de vista (Blade)

- `user-manager.blade.php` — Layout principal con:
    - Barra de carga global animada (`wire:loading`)
    - Indicador offline (`wire:offline`)
    - 4 stats cards reactivas (Total, Activos, Inactivos, En línea hoy) con `animate-pulse`
    - Modal Crear Usuario con validación en tiempo real
    - Modal Editar Usuario con carga dinámica de datos
    - Modales con `@entangle` + `x-show` + `x-transition` para apertura/cierre instantáneo
- `partials/status-toggle.blade.php` — Toggle switch animado para activar/desactivar usuarios
- `partials/actions-cell.blade.php` — Botones Editar y Eliminar por fila
- `exports/users-pdf.blade.php` — Plantilla PDF para exportación

### Integración con layout

- `vertical.blade.php` actualizado con:
    - Listener global `Livewire.on('swal')` para toasts SweetAlert2
    - Listener `Livewire.on('confirmBulkAction')` para confirmaciones de acciones masivas
    - Hook `Livewire.hook('commit')` para re-renderizar iconos Lucide después de cada commit

### Correcciones críticas aplicadas

#### Fix: Error Alpine.js por atributo `lazy` (scope chain roto)

- **Problema:** `<livewire:sata.users-table lazy />` causaba que Alpine procesara el DOM antes de que Rappasoft inyectara su `x-data="laravellivewiretable($wire)"`, rompiendo toda la cadena de scope.
- **Errores:** `currentlyReorderingStatus is not defined`, `selectedItems is not defined`, etc.
- **Solución:** Eliminar atributo `lazy` del componente. Eliminar `setLoadingPlaceholderEnabled()` de `configure()`.

#### Fix: URL contaminado con parámetros de columnas

- **Problema:** Query string se llenaba de `?table-columns[]=nombre&table-columns[]=email...`
- **Solución:** Añadir `$this->setQueryStringForColumnSelectDisabled()` en `configure()`.

#### Fix: Overlays de carga en modales (mala UX)

- **Problema:** Al guardar/editar, un overlay con blur y spinner "Registrando usuario..." cubría todo el modal.
- **Solución:** Eliminar overlays. Reemplazar con botones inline que muestran spinner + "Guardando..." vía `wire:loading.attr="disabled"` + `wire:loading`/`wire:loading.remove` con `wire:target`.

#### Fix: Toggle activar/desactivar no funcionaba

- **Problema 1 (dispatch):** `$wire.dispatch('toggleStatus')` en `status-toggle.blade.php` apuntaba al scope de `UsersTable` (componente hijo), pero el listener `#[On('toggleStatus')]` está en `UserManager` (componente padre). Cambiado a `Livewire.dispatch()` (global).
- **Problema 2 (race condition Alpine):** `x-on:click` en el `<label>` ponía `toggling = true` antes de que el browser propagara el click al checkbox → checkbox quedaba disabled → `change` nunca se disparaba → el dispatch nunca se ejecutaba → spinner giraba y nada pasaba.
- **Solución:** Mover toda la lógica (`toggling = true` + `Livewire.dispatch()`) al `x-on:change` del checkbox, donde se ejecuta **después** de que el checkbox cambia de estado.

#### Fix: Botón eliminar no funcionaba

- **Problema:** Mismo issue de scope — `$wire.dispatch('deleteUser')` apuntaba a `UsersTable`.
- **Solución:** Cambiado a `Livewire.dispatch('deleteUser')`.

### Arquitectura de eventos inter-componente (referencia)

```
┌─────────────────────────────────────────────────────┐
│ UserManager (padre)                                  │
│   Listeners: #[On('toggleStatus')]                   │
│              #[On('editUser')]                        │
│              #[On('deleteUser')]                      │
│   Dispatches: refreshDatatable, swal                 │
├─────────────────────────────────────────────────────┤
│ UsersTable (hijo - Rappasoft)                         │
│   Listeners: #[On('refreshDatatable')]               │
│              #[On('executeBulkAction')]               │
│   Dispatches: confirmBulkAction                      │
├─────────────────────────────────────────────────────┤
│ Layout (vertical.blade.php)                           │
│   JS Listeners: Livewire.on('swal')                  │
│                 Livewire.on('confirmBulkAction')     │
│   JS Dispatches: Livewire.dispatch('executeBulkAction')│
└─────────────────────────────────────────────────────┘

⚠️ IMPORTANTE: $wire en partials de Rappasoft (status-toggle, actions-cell)
   apunta a UsersTable, NO a UserManager.
   → Usar Livewire.dispatch() para eventos que deben llegar al padre.
   → $dispatch() en wire:click ya es global en Livewire v3.
```

---

## 2026-03-09 — Fase 3: Auditoría SOLID y Refactorización del Módulo Usuarios

### Auditoría SOLID realizada (pre-refactoring)

Se analizó el módulo completo de usuarios y se identificaron las siguientes violaciones:

| # | Problema | Principio | Severidad |
|---|---|---|---|
| 1 | `UserManager` es God Component (7 responsabilidades: crear, editar, eliminar, toggle, stats, form reset, render) | **S** (SRP) | Alta |
| 2 | `UsersTable` maneja exportación Y lógica bulk además de configurar tabla | **S** (SRP) | Media |
| 3 | Lógica de negocio directa en componentes Livewire (sin capa de servicio) | **D** (DIP) | Alta |
| 4 | Roles hardcodeados como strings mágicos en 4+ archivos | **O** (OCP) | Alta |
| 5 | Sin autorización granular — no hay Policy ni verificación de jerarquía | **Seguridad** | Crítica |
| 6 | `computeStats()` ejecuta 3 queries separadas por cada operación | **Performance** | Media |
| 7 | Sin FormRequest ni DTO — validación acoplada al componente | **I** (ISP) | Baja |
| 8 | Helpers de rol redundantes en modelo User (wrappers 1:1 de Spatie) | **O** (OCP) | Baja |
| 9 | Password por defecto hardcodeado en property pública | **Seguridad** | Media |
| 10 | Sin scope de tenant — Administrador puede gestionar usuarios de cualquier I.E. | **Seguridad** | Alta |

### Plan de refactorización (sin alterar funcionalidad)

1. **Enum `UserRole`** — Fuente única de verdad para roles, eliminar strings mágicos
2. **`UserService`** — Extraer lógica CRUD de UserManager a servicio inyectable (DIP)
3. **`UserPolicy`** — Autorización granular con jerarquía de roles
4. **`UserStatsService`** — Optimizar stats con una sola query agregada
5. **Scope de tenant** — Filtrar datos según el tenant del usuario autenticado
6. **Limpiar modelo User** — Eliminar helpers redundantes, usar enum
