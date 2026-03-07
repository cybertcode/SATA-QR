<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConfiguracionAsistencia extends Model
{
    protected $table = 'configuracion_asistencia';

    protected $fillable = [
        'tenant_id',
        'hora_entrada_regular',
        'minutos_tolerancia',
    ];

    /**
     * Institución asociada.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
