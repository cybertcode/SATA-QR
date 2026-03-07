<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seccion extends Model
{
    protected $table = 'secciones';

    protected $fillable = [
        'tenant_id',
        'tenant_nivel_id',
        'grado',
        'letra',
        'tutor_id',
    ];

    /**
     * Institución asociada.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Nivel Educativo asociado (con su código modular).
     */
    public function nivelEducativo(): BelongsTo
    {
        return $this->belongsTo(TenantNivel::class, 'tenant_nivel_id');
    }

    /**
     * Matrículas registradas en esta sección.
     */
    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class);
    }

    /**
     * Tutor de la sección.
     */
    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }
}
