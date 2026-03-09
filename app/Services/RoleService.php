<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleService
{
    /**
     * Crear un nuevo rol con permisos.
     */
    public function create(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => 'web',
            ]);

            if (!empty($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            return $role;
        });
    }

    /**
     * Actualizar un rol existente y sus permisos.
     */
    public function update(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data) {
            $role->update(['name' => $data['name']]);

            if (array_key_exists('permissions', $data)) {
                $role->syncPermissions($data['permissions']);
            }

            return $role;
        });
    }

    /**
     * Eliminar un rol (solo si no tiene usuarios asignados).
     */
    public function delete(Role $role): void
    {
        $role->delete();
    }

    /**
     * Crear un nuevo permiso.
     */
    public function createPermission(array $data): Permission
    {
        return Permission::create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);
    }

    /**
     * Eliminar un permiso.
     */
    public function deletePermission(Permission $permission): void
    {
        $permission->delete();
    }

    /**
     * Estadísticas del módulo.
     */
    public function getStats(): array
    {
        return [
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
            'roles_with_users' => Role::whereHas('users')->count(),
        ];
    }
}
