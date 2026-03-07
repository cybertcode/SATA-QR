<?php

namespace Database\Seeders;

use App\Models\Asistencia;
use App\Models\Estudiante;
use App\Models\Matricula;
use App\Models\Seccion;
use App\Models\AnioLectivo;
use App\Models\AlertaTemprana;
use App\Models\IntervencionMultisectorial;
use App\Models\User;
use App\Models\AliadoEstrategico;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenantId = 'ie-huacaybamba';
        $anioId = AnioLectivo::where('anio', 2026)->first()->id;
        $registradorId = User::where('email', 'director@ie-huacaybamba.edu.pe')->first()->id;
        $aliadoId = AliadoEstrategico::first()->id;

        $nivelSecundariaId = \App\Models\TenantNivel::where('tenant_id', $tenantId)->where('nivel', 'Secundaria')->first()->id;

        // 1. Crear Secciones
        $seccion = Seccion::firstOrCreate([
            'tenant_id' => $tenantId,
            'tenant_nivel_id' => $nivelSecundariaId,
            'grado' => '1',
            'letra' => 'A',
        ]);

        // 2. Crear 20 Estudiantes
        for ($i = 1; $i <= 20; $i++) {
            $estudiante = Estudiante::create([
                'tenant_id' => $tenantId,
                'dni' => str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT),
                'nombres' => "Estudiante $i",
                'apellido_paterno' => "Paterno $i",
                'apellido_materno' => "Materno $i",
                'genero' => rand(0, 1) ? 'M' : 'F',
                'qr_uuid' => Str::uuid(),
            ]);

            $matricula = Matricula::create([
                'tenant_id' => $tenantId,
                'estudiante_id' => $estudiante->id,
                'seccion_id' => $seccion->id,
                'anio_lectivo_id' => $anioId,
                'estado' => 'Activo',
            ]);

            // 3. Generar Historial de Asistencias (últimos 30 días)
            $fecha = Carbon::now()->subDays(30);
            $inasistenciasSeguidas = 0;
            
            while ($fecha <= Carbon::now()) {
                if (!$fecha->isWeekend()) {
                    // Simular inasistencias críticas para los primeros 5 alumnos (para tener datos de alerta)
                    $esAlumnoEnRiesgo = ($i <= 5);
                    $rand = rand(1, 100);
                    
                    if ($esAlumnoEnRiesgo && $rand > 60) {
                        $estado = Asistencia::INJUSTIFICADA;
                        $inasistenciasSeguidas++;
                    } else {
                        $estado = ($rand > 90) ? Asistencia::TARDE : Asistencia::PRESENTE;
                        $inasistenciasSeguidas = 0;
                    }

                    Asistencia::create([
                        'tenant_id' => $tenantId,
                        'matricula_id' => $matricula->id,
                        'registrado_por' => $registradorId,
                        'fecha' => $fecha->toDateString(),
                        'hora_ingreso' => $estado == Asistencia::INJUSTIFICADA ? null : '07:50:00',
                        'estado' => $estado,
                    ]);

                    // 4. Disparar Alerta si tiene 3 inasistencias en el mes (Simulado)
                    if ($inasistenciasSeguidas >= 3) {
                        $alerta = AlertaTemprana::create([
                            'matricula_id' => $matricula->id,
                            'nivel_riesgo' => 'Moderado',
                            'motivo_acumulado' => '3 inasistencias consecutivas detectadas.',
                            'estado_atencion' => 'Derivado',
                            'fecha_emision' => $fecha->toDateString(),
                        ]);

                        // 5. Crear una Intervención para la primera alerta encontrada
                        if ($i == 1) {
                            IntervencionMultisectorial::create([
                                'alerta_id' => $alerta->id,
                                'especialista_id' => $registradorId,
                                'aliado_estrategico_id' => $aliadoId,
                                'descripcion_accion' => 'Visita domiciliaria y coordinación con PNP por presunto abandono.',
                                'fecha_intervencion' => $fecha->toDateString(),
                                'estado' => 'Seguimiento',
                            ]);
                        }
                    }
                }
                $fecha->addDay();
            }
        }
    }
}
