<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tenant extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'nombre',
        'ugel',
        'config',
    ];

    protected $casts = [
        'config' => 'json',
    ];

    /**
     * Niveles educativos (con sus respectivos Códigos Modulares).
     */
    public function niveles(): HasMany
    {
        return $this->hasMany(TenantNivel::class);
    }

    /**
     * Estudiantes que pertenecen a esta institución.
     */
    public function estudiantes(): HasMany
    {
        return $this->hasMany(Estudiante::class);
    }

    /**
     * Secciones configuradas en esta institución.
     */
    public function secciones(): HasMany
    {
        return $this->hasMany(Seccion::class);
    }

    /**
     * Configuración de asistencia (horarios, tolerancia).
     */
    public function configuracionAsistencia(): HasOne
    {
        return $this->hasOne(ConfiguracionAsistencia::class);
    }

    /**
     * Usuarios vinculados a esta institución.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Feriados locales o nacionales registrados para esta institución.
     */
    public function feriados(): HasMany
    {
        return $this->hasMany(CalendarioFeriado::class);
    }
}
