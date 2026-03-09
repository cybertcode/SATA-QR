<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class ConfiguracionGeneralPolicy
{
    /**
     * Solo SuperAdmin puede ver la configuración general.
     */
    public function viewAny(User $auth): bool
    {
        return $auth->roleEnum() === UserRole::SuperAdmin;
    }

    /**
     * Solo SuperAdmin puede modificar la configuración general.
     */
    public function update(User $auth): bool
    {
        return $auth->roleEnum() === UserRole::SuperAdmin;
    }
}
