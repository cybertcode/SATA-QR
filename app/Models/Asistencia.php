<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asistencia extends Model
{
    use BelongsToTenant;
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
