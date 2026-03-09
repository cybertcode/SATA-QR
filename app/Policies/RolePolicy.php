<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    /**
     * Solo SuperAdmin puede ver el módulo de roles y permisos.
     */
    public function viewAny(User $auth): bool
    {
        return $auth->roleEnum() === UserRole::SuperAdmin;
    }

    /**
     * Solo SuperAdmin puede crear roles.
     */
    public function create(User $auth): bool
    {
        return $auth->roleEnum() === UserRole::SuperAdmin;
    }

    /**
     * Solo SuperAdmin puede editar roles.
     * No se puede editar el rol SuperAdmin.
     */
    public function update(User $auth, Role $role): bool
    {
        if ($role->name === UserRole::SuperAdmin->value) {
            return false;
        }

        return $auth->roleEnum() === UserRole::SuperAdmin;
    }

    /**
     * Solo SuperAdmin puede eliminar roles.
     * No se pueden eliminar los roles del enum (protegidos).
     */
    public function delete(User $auth, Role $role): bool
    {
        // No eliminar roles protegidos del sistema
        if (in_array($role->name, UserRole::values())) {
            return false;
        }

        return $auth->roleEnum() === UserRole::SuperAdmin;
    }

    /**
     * Solo SuperAdmin puede gestionar permisos.
     */
    public function managePermissions(User $auth): bool
    {
        return $auth->roleEnum() === UserRole::SuperAdmin;
    }
}
