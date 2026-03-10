<?php

namespace App\Services;

use App\Models\Estudiante;
use App\Models\Seccion;
use App\Models\TenantNivel;
use App\Models\Matricula;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class StudentImportService
{
    /**
     * Procesa la data cruda del Excel (Simulada para esta arquitectura)
     * En producción usaría Maatwebsite\Excel
     */
    public function import(array $rows, $tenantId, $anioLectivoId, $nivel)
    {
        return DB::transaction(function () use ($rows, $tenantId, $anioLectivoId, $nivel) {
            $importedCount = 0;

            // 1. Asegurar que el nivel existe para este colegio
            $tenantNivel = TenantNivel::firstOrCreate(
                ['tenant_id' => $tenantId, 'nivel' => $nivel],
                ['codigo_modular' => '0000000'] // Default si no existe
            );

            foreach ($rows as $row) {
                // 2. Crear o actualizar Estudiante (DNI es la clave única)
                $student = Estudiante::updateOrCreate(
                    ['dni' => $row['dni']],
                    [
                        'tenant_id' => $tenantId,
                        'nombres' => $row['nombres'],
                        'apellido_paterno' => $row['paterno'],
                        'apellido_materno' => $row['materno'],
                        'genero' => $row['genero'],
                        'qr_uuid' => (string) Str::uuid(), // Genera QR si es nuevo
                    ]
                );

                // 3. Crear o encontrar Sección
                $seccion = Seccion::firstOrCreate([
                    'tenant_id' => $tenantId,
                    'tenant_nivel_id' => $tenantNivel->id,
                    'grado' => $row['grado'],
                    'letra' => $row['seccion']
                ]);

                // 4. Matricular
                Matricula::updateOrCreate(
                    ['estudiante_id' => $student->id, 'anio_lectivo_id' => $anioLectivoId],
                    ['seccion_id' => $seccion->id, 'estado' => 'Activo', 'tenant_id' => $tenantId]
                );

                $importedCount++;
            }

            return $importedCount;
        });
    }
}
