<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionGeneral extends Model
{
    protected $table = 'configuraciones_generales';

    protected $fillable = [
        'grupo',
        'clave',
        'valor',
        'tipo',
        'etiqueta',
        'descripcion',
        'orden',
    ];

    /**
     * Obtener el valor casteado según su tipo.
     */
    public function getValorCasteadoAttribute(): mixed
    {
        return match ($this->tipo) {
            'integer' => (int) $this->valor,
            'boolean' => filter_var($this->valor, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($this->valor, true),
            default => $this->valor,
        };
    }

    /**
     * Obtener una configuración por clave.
     */
    public static function obtener(string $clave, mixed $default = null): mixed
    {
        $config = static::where('clave', $clave)->first();

        return $config ? $config->valor_casteado : $default;
    }

    /**
     * Establecer el valor de una configuración.
     */
    public static function establecer(string $clave, mixed $valor): void
    {
        $config = static::where('clave', $clave)->firstOrFail();

        $config->update([
            'valor' => $config->tipo === 'json' ? json_encode($valor) : (string) $valor,
        ]);
    }

    /**
     * Scope para filtrar por grupo.
     */
    public function scopeGrupo($query, string $grupo)
    {
        return $query->where('grupo', $grupo);
    }
}
