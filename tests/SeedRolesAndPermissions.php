<?php

namespace Tests;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

trait SeedRolesAndPermissions
{
    protected function seedRolesAndPermissions(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'config.global',
            'config.ie',
            'students.view',
            'students.manage',
            'attendance.scan',
            'attendance.report',
            'alerts.manage',
            'interventions.log',
            'users.manage',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'SuperAdmin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $admin->syncPermissions(['config.ie', 'students.view', 'students.manage', 'attendance.report', 'alerts.manage']);

        $director = Role::firstOrCreate(['name' => 'Director', 'guard_name' => 'web']);
        $director->syncPermissions(['config.ie', 'students.view', 'students.manage', 'attendance.scan', 'attendance.report', 'alerts.manage', 'interventions.log']);

        $docente = Role::firstOrCreate(['name' => 'Docente', 'guard_name' => 'web']);
        $docente->syncPermissions(['students.view', 'attendance.scan', 'attendance.report']);

        $auxiliar = Role::firstOrCreate(['name' => 'Auxiliar', 'guard_name' => 'web']);
        $auxiliar->syncPermissions(['attendance.scan']);
    }
}
