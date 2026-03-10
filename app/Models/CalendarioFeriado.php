<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarioFeriado extends Model
{
    protected $table = 'calendario_feriados';

    protected $fillable = [
        'tenant_id',
        'fecha',
        'descripcion',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    /**
     * Institución asociada (null si es feriado nacional).
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
