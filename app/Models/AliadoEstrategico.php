<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AliadoEstrategico extends Model
{
    protected $table = 'aliados_estrategicos';

    protected $fillable = [
        'nombre',
        'tipo',
        'contacto',
    ];

    /**
     * Intervenciones en las que ha participado este aliado.
     */
    public function intervenciones(): HasMany
    {
        return $this->hasMany(IntervencionMultisectorial::class);
    }
}
