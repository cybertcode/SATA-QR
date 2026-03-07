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

        // Auxiliar: Operación diaria (Escáner y visualización básica)
        $auxiliar = Role::firstOrCreate(['name' => 'Auxiliar']);
        $auxiliar->syncPermissions([
            'students.view',
            'attendance.scan',
            'attendance.report',
        ]);

        // 3. ASIGNACIÓN A USUARIOS EXISTENTES
        
        // SuperAdmin Maestro
        $admin = User::where('email', 'admin@admin.com')->first();
        if ($admin) {
            $admin->assignRole($superAdmin);
        }

        // Director de Ejemplo (I.E. Huacaybamba)
        $directorUser = User::where('email', 'director@ie-huacaybamba.edu.pe')->first();
        if ($directorUser) {
            $directorUser->assignRole($director);
        }
    }
}
