<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntervencionMultisectorial extends Model
{
    protected $table = 'intervenciones_multisectoriales';

    protected $fillable = [
        'alerta_id',
        'especialista_id',
        'aliado_estrategico_id',
        'descripcion_accion',
        'fecha_intervencion',
        'resultado_seguimiento',
        'estado',
    ];

    protected $casts = [
        'fecha_intervencion' => 'date',
    ];

    /**
     * Alerta vinculada.
     */
    public function alerta(): BelongsTo
    {
        return $this->belongsTo(AlertaTemprana::class, 'alerta_id');
    }

    /**
     * Especialista de la UGEL a cargo.
     */
    public function especialista(): BelongsTo
    {
        return $this->belongsTo(User::class, 'especialista_id');
    }

    /**
     * Aliado externo que colabora (null si es intervención interna).
     */
    public function aliado(): BelongsTo
    {
        return $this->belongsTo(AliadoEstrategico::class, 'aliado_estrategico_id');
    }
}
