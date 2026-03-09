<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    /**
     * Solo SuperAdmin y Administrador pueden ver el listado.
     */
    public function viewAny(User $auth): bool
    {
        return $auth->hasAnyRole([UserRole::SuperAdmin->value, UserRole::Administrador->value]);
    }

    /**
     * Puede crear usuarios si tiene permiso users.manage.
     */
    public function create(User $auth): bool
    {
        return $auth->hasPermissionTo('users.manage');
    }

    /**
     * Puede editar si su rol es jerárquicamente superior al del target.
     * No puede editarse a sí mismo desde la gestión (debe ir a perfil).
     */
    public function update(User $auth, User $target): bool
    {
        if ($auth->id === $target->id) {
            return false;
        }

        return $this->canManageTarget($auth, $target);
    }

    /**
     * Puede eliminar si es jerárquicamente superior. No puede eliminarse a sí mismo.
     */
    public function delete(User $auth, User $target): bool
    {
        if ($auth->id === $target->id) {
            return false;
        }

        return $this->canManageTarget($auth, $target);
    }

    /**
     * Puede cambiar estado si es jerárquicamente superior.
     */
    public function toggleStatus(User $auth, User $target): bool
    {
        if ($auth->id === $target->id) {
            return false;
        }

        return $this->canManageTarget($auth, $target);
    }

    /**
     * Verifica jerarquía de roles: el autenticado debe tener nivel superior al target.
     */
    private function canManageTarget(User $auth, User $target): bool
    {
        $authRole = UserRole::tryFrom($auth->role);
        $targetRole = UserRole::tryFrom($target->role);

        if (!$authRole || !$targetRole) {
            return false;
        }

        return $authRole->canManage($targetRole);
    }
}
