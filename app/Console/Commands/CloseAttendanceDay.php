<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Matricula;
use App\Models\Asistencia;
use Carbon\Carbon;

class CloseAttendanceDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sata:close-day {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marca como Falta Injustificada (FI) a todos los alumnos que no registraron asistencia.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->argument('date') ?? Carbon::now()->toDateString();
        $this->info("Iniciando cierre de asistencia para la fecha: $date");

        // 1. Obtener todas las matrículas activas
        $matriculas = Matricula::where('estado', 'Activo')->get();
        $count = 0;

        foreach ($matriculas as $matricula) {
            // 2. Verificar si ya tiene asistencia registrada para hoy
            $existe = Asistencia::where('matricula_id', $matricula->id)
                ->where('fecha', $date)
                ->exists();

            if (!$existe) {
                // 3. Crear registro de Falta Injustificada (FI)
                Asistencia::create([
                    'tenant_id' => $matricula->tenant_id,
                    'matricula_id' => $matricula->id,
                    'fecha' => $date,
                    'estado' => Asistencia::INJUSTIFICADA,
                    'registrado_por' => null // Sistema
                ]);
                $count++;
            }
        }

        $this->info("Proceso completado. Se registraron $count inasistencias automáticas.");
    }
}
