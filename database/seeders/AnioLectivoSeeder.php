<?php

namespace Database\Seeders;

use App\Models\AnioLectivo;
use Illuminate\Database\Seeder;

class AnioLectivoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AnioLectivo::firstOrCreate(['anio' => 2025], [
            'nombre_anio' => 'Año Escolar 2025',
            'fecha_inicio' => '2025-03-10',
            'fecha_fin' => '2025-12-19',
            'estado' => false,
        ]);

        AnioLectivo::firstOrCreate(['anio' => 2026], [
            'nombre_anio' => 'Año Escolar 2026',
            'fecha_inicio' => '2026-03-09',
            'fecha_fin' => '2026-12-18',
            'estado' => true,
        ]);
    }
}
