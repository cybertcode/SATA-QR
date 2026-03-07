<?php

namespace App\Models;

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

    // --- HELPERS DE ROL (delegados a spatie/permission) ---

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('SuperAdmin');
    }

    public function isDirector(): bool
    {
        return $this->hasRole('Director');
    }

    public function isDocente(): bool
    {
        return $this->hasRole('Docente');
    }

    public function isAdministrador(): bool
    {
        return $this->hasRole('Administrador');
    }

    public function isAuxiliar(): bool
    {
        return $this->hasRole('Auxiliar');
    }
}
