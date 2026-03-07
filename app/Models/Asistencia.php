<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asistencia extends Model
{
    // Estados de Asistencia
    public const PRESENTE = 'P';
    public const TARDE = 'T';
    public const JUSTIFICADA = 'FJ';
    public const INJUSTIFICADA = 'FI';

    protected $fillable = [
        'tenant_id',
        'matricula_id',
        'registrado_por',
        'fecha',
        'hora_ingreso',
        'estado',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    /**
     * Institución asociada (optimiza consultas de dashboard).
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Matrícula a la que pertenece el registro.
     */
    public function matricula(): BelongsTo
    {
        return $this->belongsTo(Matricula::class);
    }

    /**
     * Usuario que registró la asistencia (auxiliar/docente).
     */
    public function registrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
