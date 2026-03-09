<?php

namespace App\Services;

use App\Models\ConfiguracionGeneral;
use Illuminate\Support\Collection;

class ConfiguracionGeneralService
{
    /**
     * Obtener todas las configuraciones agrupadas por grupo.
     */
    public function getAllGrouped(): Collection
    {
        return ConfiguracionGeneral::orderBy('grupo')
            ->orderBy('orden')
            ->get()
            ->groupBy('grupo');
    }

    /**
     * Obtener configuraciones de un grupo específico.
     */
    public function getByGroup(string $grupo): Collection
    {
        return ConfiguracionGeneral::grupo($grupo)
            ->orderBy('orden')
            ->get();
    }

    /**
     * Actualizar múltiples configuraciones en lote.
     */
    public function updateBatch(array $valores): int
    {
        $updated = 0;

        foreach ($valores as $clave => $valor) {
            $config = ConfiguracionGeneral::where('clave', $clave)->first();
            if (!$config) {
                continue;
            }

            $valorGuardado = match ($config->tipo) {
                'boolean' => $valor ? '1' : '0',
                'json' => json_encode($valor),
                default => (string) $valor,
            };

            if ($config->valor !== $valorGuardado) {
                $config->update(['valor' => $valorGuardado]);
                $updated++;
            }
        }

        return $updated;
    }

    /**
     * Estadísticas del módulo.
     */
    public function getStats(): array
    {
        $configs = ConfiguracionGeneral::all();

        return [
            'total' => $configs->count(),
            'grupos' => $configs->pluck('grupo')->unique()->count(),
            'modificadas' => $configs->filter(fn($c) => $c->updated_at->gt($c->created_at))->count(),
        ];
    }

    /**
     * Mapa de metadatos de grupos para la UI.
     */
    public static function gruposMeta(): array
    {
        return [
            'general' => [
                'label' => 'General',
                'icon' => 'settings',
                'color' => 'primary',
                'descripcion' => 'Nombre del sistema, UGEL y datos institucionales.',
            ],
            'asistencia' => [
                'label' => 'Asistencia',
                'icon' => 'clock',
                'color' => 'success',
                'descripcion' => 'Umbrales, tolerancias y reglas de asistencia QR.',
            ],
            'alertas' => [
                'label' => 'Alertas Tempranas',
                'icon' => 'alert-triangle',
                'color' => 'warning',
                'descripcion' => 'Umbrales para generación automática de alertas.',
            ],
            'seguridad' => [
                'label' => 'Seguridad',
                'icon' => 'shield',
                'color' => 'danger',
                'descripcion' => 'Políticas de acceso, sesiones y autenticación.',
            ],
            'apariencia' => [
                'label' => 'Apariencia',
                'icon' => 'palette',
                'color' => 'info',
                'descripcion' => 'Tema visual y personalización de la interfaz.',
            ],
        ];
    }
}
