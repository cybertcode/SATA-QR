<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\ConfiguracionAsistencia;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ie = Tenant::firstOrCreate([
            'id' => 'ie-huacaybamba',
        ], [
            'nombre' => 'I.E. Huacaybamba Integrada',
            'ugel' => 'HUACAYBAMBA',
            'config' => ['primary_color' => '#1e3a8a'],
        ]);

        // Crear los niveles de la Institución con sus códigos modulares de SIAGIE
        \App\Models\TenantNivel::firstOrCreate(['tenant_id' => $ie->id, 'nivel' => 'Primaria'], ['codigo_modular' => '0543210']);
        \App\Models\TenantNivel::firstOrCreate(['tenant_id' => $ie->id, 'nivel' => 'Secundaria'], ['codigo_modular' => '0543211']);

        ConfiguracionAsistencia::updateOrCreate(['tenant_id' => $ie->id], [
            'hora_entrada_regular' => '07:45:00',
            'minutos_tolerancia' => 15,
        ]);

        $ie2 = Tenant::firstOrCreate([
            'id' => 'ie-canchabamba',
        ], [
            'nombre' => 'I.E. Canchabamba',
            'ugel' => 'HUACAYBAMBA',
            'config' => ['primary_color' => '#b91c1c'],
        ]);

        \App\Models\TenantNivel::firstOrCreate(['tenant_id' => $ie2->id, 'nivel' => 'Secundaria'], ['codigo_modular' => '0123456']);

        ConfiguracionAsistencia::updateOrCreate(['tenant_id' => $ie2->id], [
            'hora_entrada_regular' => '08:00:00',
            'minutos_tolerancia' => 10,
        ]);
    }
}
