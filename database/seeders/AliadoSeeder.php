<?php

namespace Database\Seeders;

use App\Models\AliadoEstrategico;
use Illuminate\Database\Seeder;

class AliadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AliadoEstrategico::firstOrCreate(['nombre' => 'Comisaría PNP Huacaybamba'], ['tipo' => 'Seguridad', 'contacto' => '987654321']);
        AliadoEstrategico::firstOrCreate(['nombre' => 'Centro de Salud Huacaybamba'], ['tipo' => 'Salud', 'contacto' => '988776655']);
        AliadoEstrategico::firstOrCreate(['nombre' => 'CEM (Centro Emergencia Mujer)'], ['tipo' => 'Social', 'contacto' => '911223344']);
    }
}
