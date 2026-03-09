<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
        'role',
        'dni',
        'cargo',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Relación con la Institución (Tenant).
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Obtener el enum de rol del usuario.
     */
    public function roleEnum(): ?UserRole
    {
        return UserRole::tryFrom($this->role);
    }

    /**
     * Atajos de rol — delegados al enum para evitar strings mágicos.
     */
    public function isSuperAdmin(): bool
    {
        return $this->roleEnum() === UserRole::SuperAdmin;
    }
}
