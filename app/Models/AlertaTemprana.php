<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlertaTemprana extends Model
{
    protected $table = 'alertas_tempranas';

    protected $fillable = [
        'matricula_id',
        'nivel_riesgo',
        'motivo_acumulado',
        'estado_atencion',
        'fecha_emision',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
    ];

    /**
     * Matrícula que originó la alerta.
     */
    public function matricula(): BelongsTo
    {
        return $this->belongsTo(Matricula::class);
    }

    /**
     * Intervenciones asociadas a esta alerta.
     */
    public function intervenciones(): HasMany
    {
        return $this->hasMany(IntervencionMultisectorial::class, 'alerta_id');
    }
}
