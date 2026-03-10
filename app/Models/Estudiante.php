<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Estudiante extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id',
        'dni',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'genero',
        'fecha_nacimiento',
        'qr_uuid',
    ];

    /**
     * Nombre completo del estudiante (formato: Apellidos, Nombres).
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->apellido_paterno} {$this->apellido_materno}, {$this->nombres}";
    }

    /**
     * Historial de matrículas en distintos años.
     */
    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class);
    }

    /**
     * Obtener la matrícula activa para el año actual.
     */
    public function matriculaActual(): HasOne
    {
        return $this->hasOne(Matricula::class)
            ->whereHas('anioLectivo', fn($q) => $q->where('anio', date('Y')))
            ->where('estado', 'Activo');
    }
}
