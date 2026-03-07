<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TenantNivel extends Model
{
    protected $table = 'tenant_niveles';

    protected $fillable = [
        'tenant_id',
        'nivel',
        'codigo_modular',
    ];

    /**
     * Institución Educativa a la que pertenece.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Secciones de este nivel.
     */
    public function secciones(): HasMany
    {
        return $this->hasMany(Seccion::class);
    }
}
