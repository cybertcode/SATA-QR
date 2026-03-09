<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Crear un nuevo usuario.
     */
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $role = UserRole::from($data['role']);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'dni' => $data['dni'] ?: null,
                'role' => $role->value,
                'tenant_id' => $role->requiresTenant() ? $data['tenant_id'] : null,
                'cargo' => $data['cargo'] ?: null,
                'password' => Hash::make($data['password']),
                'is_active' => true,
            ]);

            $user->syncRoles([$role->value]);

            return $user;
        });
    }

    /**
     * Actualizar un usuario existente.
     */
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $role = UserRole::from($data['role']);

            $user->name = $data['name'];
            $user->dni = $data['dni'] ?: null;
            $user->role = $role->value;
            $user->tenant_id = $role->requiresTenant() ? $data['tenant_id'] : null;
            $user->cargo = $data['cargo'] ?: null;

            if (!empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            $user->save();
            $user->syncRoles([$role->value]);

            return $user;
        });
    }

    /**
     * Alternar estado activo/inactivo.
     */
    public function toggleStatus(User $user): User
    {
        $user->is_active = !$user->is_active;
        $user->save();

        return $user;
    }

    /**
     * Eliminar un usuario.
     */
    public function delete(User $user): void
    {
        $user->delete();
    }

    /**
     * Activar/desactivar usuarios en bulk (excluye al usuario autenticado).
     */
    public function bulkToggle(array $ids, bool $activate): int
    {
        return User::whereIn('id', $ids)
            ->where('id', '!=', Auth::id())
            ->update(['is_active' => $activate]);
    }

    /**
     * Estadísticas agregadas en una sola query.
     */
    public function getStats(): array
    {
        $stats = User::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active')
            ->selectRaw('SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive')
            ->selectRaw('COUNT(DISTINCT role) as roles_count')
            ->first();

        return [
            'total' => (int) $stats->total,
            'active' => (int) $stats->active,
            'inactive' => (int) $stats->inactive,
            'roles_count' => (int) $stats->roles_count,
        ];
    }
}
