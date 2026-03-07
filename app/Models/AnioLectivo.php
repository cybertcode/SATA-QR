<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnioLectivo extends Model
{
    protected $table = 'anios_lectivos';

    protected $fillable = [
        'nombre_anio',
        'anio',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'estado' => 'boolean',
    ];

    /**
     * Matrículas realizadas en este año lectivo.
     */
    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class);
    }
}
