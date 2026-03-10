<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Matricula extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id',
        'estudiante_id',
        'seccion_id',
        'anio_lectivo_id',
        'estado',
    ];

    /**
     * El estudiante matriculado.
     */
    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class);
    }

    /**
     * La sección asignada.
     */
    public function seccion(): BelongsTo
    {
        return $this->belongsTo(Seccion::class);
    }

    /**
     * El año lectivo de la matrícula.
     */
    public function anioLectivo(): BelongsTo
    {
        return $this->belongsTo(AnioLectivo::class);
    }

    /**
     * Asistencias vinculadas a esta matrícula.
     */
    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class);
    }
}
