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

| #   | Problema                                                                                                         | Principio       | Severidad |
| --- | ---------------------------------------------------------------------------------------------------------------- | --------------- | --------- |
| 1   | `UserManager` es God Component (7 responsabilidades: crear, editar, eliminar, toggle, stats, form reset, render) | **S** (SRP)     | Alta      |
| 2   | `UsersTable` maneja exportación Y lógica bulk además de configurar tabla                                         | **S** (SRP)     | Media     |
| 3   | Lógica de negocio directa en componentes Livewire (sin capa de servicio)                                         | **D** (DIP)     | Alta      |
| 4   | Roles hardcodeados como strings mágicos en 4+ archivos                                                           | **O** (OCP)     | Alta      |
| 5   | Sin autorización granular — no hay Policy ni verificación de jerarquía                                           | **Seguridad**   | Crítica   |
| 6   | `computeStats()` ejecuta 3 queries separadas por cada operación                                                  | **Performance** | Media     |
| 7   | Sin FormRequest ni DTO — validación acoplada al componente                                                       | **I** (ISP)     | Baja      |
| 8   | Helpers de rol redundantes en modelo User (wrappers 1:1 de Spatie)                                               | **O** (OCP)     | Baja      |
| 9   | Password por defecto hardcodeado en property pública                                                             | **Seguridad**   | Media     |
| 10  | Sin scope de tenant — Administrador puede gestionar usuarios de cualquier I.E.                                   | **Seguridad**   | Alta      |

### Plan de refactorización (sin alterar funcionalidad)

1. **Enum `UserRole`** — Fuente única de verdad para roles, eliminar strings mágicos
2. **`UserService`** — Extraer lógica CRUD de UserManager a servicio inyectable (DIP)
3. **`UserPolicy`** — Autorización granular con jerarquía de roles
4. **`UserStatsService`** — Optimizar stats con una sola query agregada
5. **Scope de tenant** — Filtrar datos según el tenant del usuario autenticado
6. **Limpiar modelo User** — Eliminar helpers redundantes, usar enum

### Refactorización aplicada

#### 1. `App\Enums\UserRole` — Fuente única de verdad

- Enum PHP 8.1 `backed: string` con los 5 roles del sistema
- Métodos: `label()`, `level()` (jerarquía numérica), `requiresTenant()`, `values()`, `options()`, `canManage()`
- Elimina strings mágicos dispersos en 4+ archivos

#### 2. `App\Services\UserService` — Capa de servicio (DIP)

- Extrae TODA la lógica de negocio fuera de los componentes Livewire
- Métodos: `create()`, `update()`, `toggleStatus()`, `delete()`, `bulkToggle()`, `getStats()`
- `create()` y `update()` envueltos en `DB::transaction()`
- `getStats()` optimizado: una sola query con `selectRaw()` (antes eran 3 queries separadas)
- `bulkToggle()` centraliza la lógica de bulk activate/deactivate

#### 3. `App\Policies\UserPolicy` — Autorización granular

- Controla: `viewAny`, `create`, `update`, `delete`, `toggleStatus`
- Implementa jerarquía de roles: solo puedes gestionar usuarios con nivel inferior al tuyo
- Auto-protección: no puedes editarte/eliminarte/desactivarte desde la gestión
- Auto-descubierta por Laravel 12 (convención `UserPolicy` → `User`)

#### 4. `UserManager` refactorizado

- Delega operaciones CRUD a `UserService` (inyección en métodos Livewire)
- Usa `Gate::authorize()` y `Gate::denies()` para autorización
- Usa `UserRole::values()` para validación dinámica de roles
- Usa `UserRole::Auxiliar->value` como default en `resetForm()`
- Usa `UserRole::tryFrom()` en `updatedRole()` para verificar tenant
- Password por defecto movido a `private const DEFAULT_PASSWORD`
- Nuevo método `getFormData()` para encapsular datos del formulario

#### 5. `UsersTable` refactorizado

- Filtro de roles usa `UserRole::options()` en lugar de array hardcodeado
- `executeBulkAction()` delega a `UserService::bulkToggle()`
- Retorna count real de afectados en mensajes de bulk

#### 6. Modelo `User` limpiado

- Eliminados 5 helpers redundantes (`isDirector()`, `isDocente()`, etc.)
- Añadido `roleEnum(): ?UserRole` para acceso tipado al enum
- Conservado solo `isSuperAdmin()` (usado en rutas/vistas) pero delegando al enum

---

## 2026-03-09 — Fase 4: SoftDeletes, Mejoras Visuales y Suite de Tests

### Commit — SoftDelete completo para usuarios

Se implementó el flujo completo de papelera (soft delete) con restauración y eliminación permanente.

#### Migración y Modelo

- Nueva migración `2026_03_09_111658_add_soft_deletes_to_users_table.php` — añade columna `deleted_at`.
- `User` model: trait `SoftDeletes` agregado.

#### Service + Policy

- `UserService`: nuevos métodos `restore()` y `forceDelete()`. Stats ahora incluye conteo `trashed` (query separada con `onlyTrashed()`).
- `UserPolicy`: nuevos métodos `restore()` (jerarquía de roles) y `forceDelete()` (solo SuperAdmin, no auto-eliminación).

#### Componentes Livewire

- `UserManager`: nuevos listeners `#[On('restoreUser')]` → `restore()` y `#[On('forceDeleteUser')]` → `forceDestroy()`, ambos con Gate checks y SweetAlert feedback.
- `UsersTable`: propiedad `trashedFilter`, listener `#[On('setTrashedFilter')]`, listener `#[On('setStatusFilter')]`. Builder condicional: `onlyTrashed()` / `withTrashed()` según filtro.

#### Detección robusta de usuarios eliminados

- **Problema encontrado:** `$row->trashed()` en closures `format()` de Rappasoft NO funcionaba — el trait SoftDeletes pierde contexto al rehidratar modelos internamente.
- **Solución:** Doble verificación `$this->trashedFilter === 'trashed' || !empty($row->deleted_at)` en lugar de `$row->trashed()`.
- Aplicado en 3 puntos: `setTrAttributes()`, columna Estado, columna Acciones.

#### UI de papelera

- `actions-cell.blade.php`: condicional `@if ($isTrashed)` — muestra botón **Restaurar** (verde, SweetAlert con confirmación) y botón **Eliminar permanentemente** (rojo, SweetAlert con advertencia irreversible). Cuando NO está en papelera: botones Editar + Eliminar (soft) como antes.
- `status-toggle.blade.php`: cuando `isTrashed`, muestra badge **"Eliminado"** con ícono papelera rojo en lugar del toggle activar/desactivar.
- `user-manager.blade.php`: grupo de botones **Activos / Papelera / Todos** en card-header con highlighting dinámico (Alpine `x-data`). Dispatch `setTrashedFilter` para sincronizar con UsersTable.

### Mejoras Visuales

#### 5ª tarjeta de estadísticas: Papelera

- Grid ampliado a `xl:grid-cols-5 md:grid-cols-3 grid-cols-2`.
- Nueva tarjeta con ícono papelera en color `warning` mostrando `$stats['trashed']`.

#### Tarjetas clickeables

- Las 5 tarjetas ahora son interactivas (`cursor-pointer`, `hover:shadow-md`).
- Click en **Total** → limpia filtros. **Activos** → filtra activos. **Inactivos** → filtra inactivos. **Papelera** → muestra eliminados.
- Dispatches: `setStatusFilter` (para las 3 primeras) y `setTrashedFilter` (para papelera).

#### Avatar coloreado por rol

- `user-cell.blade.php`: el avatar circular ahora usa colores según el rol del usuario:
    - SuperAdmin → `bg-danger/10 text-danger`
    - Administrador → `bg-purple-500/10 text-purple-600`
    - Director → `bg-primary/10 text-primary`
    - Docente → `bg-warning/10 text-warning`
    - Auxiliar → `bg-success/10 text-success`

### Validación y UX

#### DNI obligatorio y único

- Regla `'dni' => ['required', 'regex:/^[0-9]{8}$/', 'unique:users,dni']` (antes era `nullable`).
- Asterisco visual `*` + atributo `required` en ambos modales.

#### Contraseña por defecto = DNI

- Hook `updatedDni()`: cuando se crea un usuario y el DNI tiene 8 dígitos, auto-rellena la contraseña.
- Hint en modal: "Predeterminada: su número de DNI".

### Suite de Tests (51 tests, 97 assertions)

#### Infraestructura de testing

- Habilitado `pdo_sqlite` en php.ini para tests con `:memory:`.
- `UserFactory`: añadidos estados `superAdmin()`, `administrador()`, `director()`, `docente()`, `inactive()`, y campos `dni`, `role`, `is_active`.
- `SeedRolesAndPermissions` trait: setup de roles y permisos Spatie para tests.
- Migraciones compatibles SQLite: `enum()` → `string()`, `DB::statement` con guard de driver.
- `UserService::getStats()`: `CURDATE()` → binding con `DATE(?) = ?` (compatible MySQL + SQLite).

#### Tests unitarios (13 tests)

- `UserRoleTest` (12 tests): labels, levels, jerarquía, `requiresTenant()`, `canManage()`, `values()`, `options()`.

#### Tests de feature (38 tests)

- `UserPolicyTest` (18 tests): `viewAny`, `create`, `update`, `delete`, `toggleStatus`, `restore`, `forceDelete` — flujos positivos y negativos, jerarquía, auto-protección.
- `UserServiceTest` (11 tests): CRUD completo, toggle, softDelete, restore, forceDelete, bulkToggle (incluye exclusión de self), stats con trashed.
- `SoftDeleteFlowTest` (8 tests): flujo e2e con Livewire — soft delete, restore, force delete, permisos denegados, stats con trashed, crear usuario (dispatches swal + refreshDatatable), toggle status.
- `ExampleTest`: corregido para esperar redirect (302) en lugar de 200.

### Arquitectura de eventos actualizada

```
┌─────────────────────────────────────────────────────┐
│ UserManager (padre)                                  │
│   Listeners: #[On('toggleStatus')]                   │
│              #[On('editUser')]                        │
│              #[On('deleteUser')]                      │
│              #[On('restoreUser')]        ← NUEVO      │
│              #[On('forceDeleteUser')]    ← NUEVO      │
│   Dispatches: refreshDatatable, swal                 │
├─────────────────────────────────────────────────────┤
│ UsersTable (hijo - Rappasoft)                         │
│   Listeners: #[On('refreshDatatable')]               │
│              #[On('executeBulkAction')]               │
│              #[On('setTrashedFilter')]   ← NUEVO      │
│              #[On('setStatusFilter')]    ← NUEVO      │
│   Dispatches: confirmBulkAction                      │
├─────────────────────────────────────────────────────┤
│ Layout (vertical.blade.php)                           │
│   JS Listeners: Livewire.on('swal')                  │
│                 Livewire.on('confirmBulkAction')     │
│   JS Dispatches: Livewire.dispatch('executeBulkAction')│
└─────────────────────────────────────────────────────┘
```

---

## 2026-03-09 — Fase 5: Módulo Roles y Permisos

### Commit — Módulo completo de gestión de roles y permisos

Módulo exclusivo para SuperAdmin que permite gestionar roles de Spatie Permission y sus permisos asociados, siguiendo la misma arquitectura de 2 componentes (Manager + Table) del módulo de usuarios.

#### Arquitectura del módulo

| Capa | Archivo | Responsabilidad |
|------|---------|-----------------|
| Controller | `RoleController` | Renderiza vista `sata.roles.index` |
| Livewire Parent | `RoleManager` | CRUD modales, stats, dispatches |
| Livewire Child | `RolesTable` | DataTable de roles (Rappasoft) |
| Livewire Child | `PermissionsTable` | DataTable de permisos (Rappasoft) |
| Service | `RoleService` | Lógica de negocio (create, update, delete role/permission, stats) |
| Policy | `RolePolicy` | Autorización (viewAny, create, update, delete, managePermissions) |

#### Archivos nuevos

- `app/Http/Controllers/Sata/User/RoleController.php`
- `app/Livewire/Sata/RoleManager.php`
- `app/Livewire/Sata/RolesTable.php`
- `app/Livewire/Sata/PermissionsTable.php`
- `app/Services/RoleService.php`
- `app/Policies/RolePolicy.php`
- `resources/views/sata/roles/index.blade.php`
- `resources/views/livewire/sata/role-manager.blade.php`
- `resources/views/livewire/sata/partials/role-actions-cell.blade.php`
- `resources/views/livewire/sata/partials/permission-actions-cell.blade.php`
- `tests/Feature/Policies/RolePolicyTest.php` (11 tests)
- `tests/Feature/Services/RoleServiceTest.php` (7 tests)
- `tests/Feature/RolesModuleFlowTest.php` (13 tests)

#### Archivos modificados

- `routes/web.php` — nueva ruta `GET /roles` con middleware `role:SuperAdmin`
- `resources/views/layouts/partials/sidenav.blade.php` — acordeón "Personal" con sub-items "Gestión de Usuarios" y "Roles y Permisos" (condicional SuperAdmin)
- `app/Providers/AppServiceProvider.php` — registro manual de `RolePolicy` para modelo Spatie `Role` (no auto-descubierto)
- `database/seeders/RolesAndPermissionsSeeder.php` — nuevo permiso `roles.manage`
- `database/factories/UserFactory.php` — hook `afterCreating` que sincroniza Spatie roles automáticamente
- `tests/SeedRolesAndPermissions.php` — nuevo permiso `roles.manage`

#### Funcionalidad de Roles

- **Crear rol**: modal con nombre + checkboxes de permisos, validación unique
- **Editar rol**: modifica nombre y sincroniza permisos (checkbox list dinámica)
- **Eliminar rol**: solo roles custom (no protegidos del enum `UserRole`), solo si no tienen usuarios asignados
- **Roles protegidos**: SuperAdmin, Administrador, Director, Docente, Auxiliar — marcados con ícono candado, no eliminables
- **Tabla**: nombre (con badge "Protegido"), conteo permisos, conteo usuarios, fecha creación, acciones

#### Funcionalidad de Permisos

- **Crear permiso**: formato `modulo.accion` (regex validado), guard `web`
- **Eliminar permiso**: solo si no está asignado a ningún rol
- **Tabla**: nombre formateado (módulo.acción con colores), conteo roles asignados, estados "En uso" vs botón eliminar

#### Autorización

- `RolePolicy::viewAny()` — solo SuperAdmin
- `RolePolicy::create()` — solo SuperAdmin
- `RolePolicy::update()` — solo SuperAdmin, NO puede editar rol SuperAdmin
- `RolePolicy::delete()` — solo SuperAdmin, NO puede eliminar roles del enum `UserRole`
- `RolePolicy::managePermissions()` — solo SuperAdmin

#### Estadísticas (3 tarjetas)

- **Roles**: total de roles del sistema
- **Permisos**: total de permisos registrados
- **Roles con Usuarios**: roles que tienen al menos un usuario asignado

#### Navegación actualizada

- Menú lateral: el item "Gestión de Usuarios" ahora es un acordeón "Personal" con:
  - Gestión de Usuarios (todos con acceso al módulo)
  - Roles y Permisos (visible solo para SuperAdmin)

#### Rappasoft workaround

- `withCount(['permissions', 'users'])` genera aliases `permissions_count` y `users_count` que Rappasoft intenta seleccionar como columnas reales del schema
- Solución: usar `Column::make('Permisos', 'id')->label(fn($row) => ...)` en lugar de `Column::make('Permisos', 'permissions_count')` — evita que Rappasoft incluya el aggregate alias en el SELECT

#### UserFactory mejorado

- Nuevo hook `configure() → afterCreating`: sincroniza automáticamente el Spatie role basándose en la columna `role`
- Esto asegura que `middleware('role:X')` funcione en tests sin configuración manual

#### Suite de tests (82 tests, 151 assertions)

- **RolePolicyTest** (11 tests): viewAny, create, update, delete (protegido/custom), managePermissions
- **RoleServiceTest** (7 tests): create con/sin permisos, update, delete, createPermission, deletePermission, getStats
- **RolesModuleFlowTest** (13 tests): acceso página, CRUD roles (Livewire e2e), protección de roles del sistema, roles con usuarios, CRUD permisos, permisos en uso, render de tablas, stats

### Arquitectura de eventos del módulo

```
┌─────────────────────────────────────────────────────┐
│ RoleManager (padre)                                  │
│   Listeners: #[On('editRole')]                       │
│              #[On('deleteRole')]                      │
│              #[On('deletePermission')]                │
│   Dispatches: refreshRolesTable, swal                │
├─────────────────────────────────────────────────────┤
│ RolesTable (hijo - Rappasoft)                        │
│   Listeners: #[On('refreshRolesTable')]              │
│   Dispatches: (búsqueda interna Rappasoft)           │
├─────────────────────────────────────────────────────┤
│ PermissionsTable (hijo - Rappasoft)                   │
│   Listeners: #[On('refreshRolesTable')]              │
│   Dispatches: (búsqueda interna Rappasoft)           │
└─────────────────────────────────────────────────────┘
```
