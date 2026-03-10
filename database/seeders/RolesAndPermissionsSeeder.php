<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar cache de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. DEFINICIÓN DE PERMISOS (Granularidad Profesional)
        $permissions = [
            'config.global',      // Gestión de la UGEL
            'config.ie',          // Configuración de la propia escuela
            'students.view',      // Ver alumnos
            'students.manage',    // Crear/Editar alumnos e importación SIAGIE
            'attendance.scan',    // Usar el escáner QR
            'attendance.report',  // Ver reportes de asistencia
            'alerts.manage',      // Gestionar alertas tempranas
            'interventions.log',  // Registrar intervenciones multisectoriales
            'users.manage',       // Gestionar directores/auxiliares
            'roles.manage',       // Gestionar roles y permisos
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. CREACIÓN DE ROLES Y ASIGNACIÓN DE PERMISOS

        // SuperAdmin: Control total regional (Especialista UGEL)
        $superAdmin = Role::firstOrCreate(['name' => 'SuperAdmin']);
        $superAdmin->syncPermissions(Permission::all());

        // Director: Gestión total de su institución
        $director = Role::firstOrCreate(['name' => 'Director']);
        $director->syncPermissions([
            'config.ie',
            'students.view',
            'students.manage',
            'attendance.scan',
            'attendance.report',
            'alerts.manage',
            'interventions.log',
        ]);

        // Administrador: Gestión administrativa (UGEL/IE)
        $administrador = Role::firstOrCreate(['name' => 'Administrador']);
        $administrador->syncPermissions([
            'config.ie',
            'students.view',
            'students.manage',
            'attendance.report',
            'alerts.manage',
        ]);

        // Docente PIP: Seguimiento pedagógico y atención al estudiante
        $docente = Role::firstOrCreate(['name' => 'Docente']);
        $docente->syncPermissions([
            'students.view',
            'students.manage',
            'attendance.scan',
            'attendance.report',
            'alerts.manage',
        ]);

        // Auxiliar: Operación diaria (Escáner y visualización básica)
        $auxiliar = Role::firstOrCreate(['name' => 'Auxiliar']);
        $auxiliar->syncPermissions([
            'students.view',
            'attendance.scan',
            'attendance.report',
        ]);

        // 3. SINCRONIZAR ROLES CON USUARIOS EXISTENTES
        // Asigna el rol spatie según la columna 'role' de cada usuario
        $roleMap = [
            'SuperAdmin' => $superAdmin,
            'Administrador' => $administrador,
            'Director' => $director,
            'Docente' => $docente,
            'Auxiliar' => $auxiliar,
        ];

        foreach (User::whereNotNull('role')->cursor() as $user) {
            if (isset($roleMap[$user->role])) {
                $user->syncRoles([$roleMap[$user->role]]);
            }
        }
    }
}
