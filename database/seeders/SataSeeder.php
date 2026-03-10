<?php

namespace Database\Seeders;

use App\Models\Asistencia;
use App\Models\Estudiante;
use App\Models\Matricula;
use App\Models\Seccion;
use App\Models\AnioLectivo;
use App\Models\TenantNivel;
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
     * Nombres y apellidos peruanos para datos más realistas.
     */
    private array $nombres_m = ['José', 'Luis', 'Carlos', 'Miguel', 'Juan', 'Pedro', 'Diego', 'Andrés', 'Fernando', 'Ricardo', 'Eduardo', 'Manuel', 'Ángel', 'Héctor', 'Roberto'];
    private array $nombres_f = ['María', 'Ana', 'Rosa', 'Carmen', 'Luisa', 'Elena', 'Sofía', 'Valentina', 'Isabel', 'Patricia', 'Gabriela', 'Teresa', 'Julia', 'Lucía', 'Daniela'];
    private array $apellidos = ['Quispe', 'Huamán', 'Mamani', 'Condori', 'Flores', 'Rojas', 'Sánchez', 'López', 'García', 'Pérez', 'Torres', 'Rivera', 'Díaz', 'Ramos', 'Espinoza', 'Vargas', 'Mendoza', 'Castillo', 'Arce', 'Paredes'];

    public function run(): void
    {
        $anioLectivo = AnioLectivo::where('anio', now()->year)->first()
            ?? AnioLectivo::where('estado', true)->first();
        $aliadoId = AliadoEstrategico::first()?->id;

        // IEs con datos de prueba (las 5 principales con directores)
        $iesConDatos = [
            'ie-huacaybamba' => ['alumnos_por_seccion' => 15],
            'ie-canchabamba' => ['alumnos_por_seccion' => 12],
            'ie-cochabamba' => ['alumnos_por_seccion' => 10],
            'ie-pinra' => ['alumnos_por_seccion' => 10],
            'ie-arancay' => ['alumnos_por_seccion' => 8],
        ];

        $dniCounter = 70000001;

        foreach ($iesConDatos as $tenantId => $config) {
            $niveles = TenantNivel::where('tenant_id', $tenantId)->get();
            $registrador = User::where('tenant_id', $tenantId)->first();

            if ($niveles->isEmpty() || !$registrador || !$anioLectivo) {
                continue;
            }

            $registradorId = $registrador->id;
            $seccionesCreadas = [];

            // Crear secciones para CADA nivel de la IE
            foreach ($niveles as $nivel) {
                $gradosNivel = $nivel->nivel === 'Primaria'
                    ? ['1', '2', '3', '4', '5', '6']
                    : ['1', '2', '3', '4', '5'];

                foreach ($gradosNivel as $grado) {
                    $seccion = Seccion::firstOrCreate([
                        'tenant_id' => $tenantId,
                        'tenant_nivel_id' => $nivel->id,
                        'grado' => $grado,
                        'letra' => 'A',
                    ]);
                    $seccionesCreadas[] = $seccion;

                    // Sección "B" solo para grados 1-3 de Huacaybamba (IE grande)
                    if ($tenantId === 'ie-huacaybamba' && in_array($grado, ['1', '2', '3'])) {
                        $seccionB = Seccion::firstOrCreate([
                            'tenant_id' => $tenantId,
                            'tenant_nivel_id' => $nivel->id,
                            'grado' => $grado,
                            'letra' => 'B',
                        ]);
                        $seccionesCreadas[] = $seccionB;
                    }
                }
            }

            if (empty($seccionesCreadas)) {
                continue;
            }

            // Crear alumnos distribuidos entre TODAS las secciones
            $estudianteCount = 0;
            $totalAlumnos = count($seccionesCreadas) * $config['alumnos_por_seccion'];

            for ($i = 0; $i < $totalAlumnos; $i++) {
                $genero = rand(0, 1) ? 'M' : 'F';
                $nombres = $genero === 'M' ? $this->nombres_m : $this->nombres_f;

                $estudiante = Estudiante::withoutGlobalScopes()->create([
                    'tenant_id' => $tenantId,
                    'dni' => str_pad($dniCounter++, 8, '0', STR_PAD_LEFT),
                    'nombres' => $nombres[array_rand($nombres)],
                    'apellido_paterno' => $this->apellidos[array_rand($this->apellidos)],
                    'apellido_materno' => $this->apellidos[array_rand($this->apellidos)],
                    'genero' => $genero,
                    'fecha_nacimiento' => Carbon::now()->subYears(rand(6, 17))->subDays(rand(0, 365))->toDateString(),
                    'qr_uuid' => Str::uuid(),
                ]);

                $seccion = $seccionesCreadas[$i % count($seccionesCreadas)];

                $matricula = Matricula::withoutGlobalScopes()->create([
                    'tenant_id' => $tenantId,
                    'estudiante_id' => $estudiante->id,
                    'seccion_id' => $seccion->id,
                    'anio_lectivo_id' => $anioLectivo->id,
                    'estado' => 'Activo',
                ]);

                // Generar asistencias últimos 30 días
                $fecha = Carbon::now()->subDays(30)->copy();
                $inasistenciasSeguidas = 0;
                $esAlumnoEnRiesgo = ($estudianteCount < 3); // Primeros 3 de cada IE en riesgo

                while ($fecha->lte(Carbon::now())) {
                    if (!$fecha->isWeekend()) {
                        $rand = rand(1, 100);

                        if ($esAlumnoEnRiesgo && $rand > 55) {
                            $estado = Asistencia::INJUSTIFICADA;
                            $inasistenciasSeguidas++;
                        } elseif ($rand > 92) {
                            $estado = Asistencia::TARDE;
                            $inasistenciasSeguidas = 0;
                        } else {
                            $estado = Asistencia::PRESENTE;
                            $inasistenciasSeguidas = 0;
                        }

                        Asistencia::withoutGlobalScopes()->create([
                            'tenant_id' => $tenantId,
                            'matricula_id' => $matricula->id,
                            'registrado_por' => $registradorId,
                            'fecha' => $fecha->toDateString(),
                            'hora_ingreso' => $estado === Asistencia::INJUSTIFICADA ? null : '07:' . str_pad(rand(30, 59), 2, '0', STR_PAD_LEFT) . ':00',
                            'estado' => $estado,
                        ]);

                        // Generar alerta si hay 3+ inasistencias consecutivas
                        if ($inasistenciasSeguidas >= 3) {
                            $alerta = AlertaTemprana::create([
                                'matricula_id' => $matricula->id,
                                'nivel_riesgo' => $inasistenciasSeguidas >= 5 ? 'Alto' : 'Moderado',
                                'motivo_acumulado' => "{$inasistenciasSeguidas} inasistencias consecutivas detectadas.",
                                'estado_atencion' => 'Derivado',
                                'fecha_emision' => $fecha->toDateString(),
                            ]);

                            // Intervención para el primer alumno en riesgo de la primera IE
                            if ($estudianteCount === 0 && $tenantId === 'ie-huacaybamba' && $aliadoId) {
                                IntervencionMultisectorial::create([
                                    'alerta_id' => $alerta->id,
                                    'especialista_id' => $registradorId,
                                    'aliado_estrategico_id' => $aliadoId,
                                    'descripcion_accion' => 'Visita domiciliaria y coordinación con PNP.',
                                    'fecha_intervencion' => $fecha->toDateString(),
                                    'estado' => 'Seguimiento',
                                ]);
                            }

                            $inasistenciasSeguidas = 0; // Reset para no generar alertas en cada día
                        }
                    }
                    $fecha->addDay();
                }

                $estudianteCount++;
            }
        }
    }
}
