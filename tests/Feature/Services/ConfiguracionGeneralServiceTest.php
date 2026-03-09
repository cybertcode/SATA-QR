<?php

namespace Tests\Feature\Services;

use App\Models\ConfiguracionGeneral;
use App\Services\ConfiguracionGeneralService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConfiguracionGeneralServiceTest extends TestCase
{
    use RefreshDatabase;

    private ConfiguracionGeneralService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ConfiguracionGeneralService();
        $this->seedConfigs();
    }

    private function seedConfigs(): void
    {
        ConfiguracionGeneral::create([
            'grupo' => 'general',
            'clave' => 'sistema.nombre',
            'valor' => 'SATA-QR',
            'tipo' => 'string',
            'etiqueta' => 'Nombre del Sistema',
            'orden' => 1,
        ]);
        ConfiguracionGeneral::create([
            'grupo' => 'general',
            'clave' => 'sistema.ugel',
            'valor' => 'UGEL Huacaybamba',
            'tipo' => 'string',
            'etiqueta' => 'UGEL',
            'orden' => 2,
        ]);
        ConfiguracionGeneral::create([
            'grupo' => 'asistencia',
            'clave' => 'asistencia.tolerancia',
            'valor' => '15',
            'tipo' => 'integer',
            'etiqueta' => 'Tolerancia',
            'orden' => 1,
        ]);
        ConfiguracionGeneral::create([
            'grupo' => 'alertas',
            'clave' => 'alertas.activas',
            'valor' => '1',
            'tipo' => 'boolean',
            'etiqueta' => 'Alertas Activas',
            'orden' => 1,
        ]);
    }

    public function test_get_all_grouped(): void
    {
        $grouped = $this->service->getAllGrouped();

        $this->assertCount(3, $grouped);
        $this->assertArrayHasKey('general', $grouped->toArray());
        $this->assertArrayHasKey('asistencia', $grouped->toArray());
        $this->assertArrayHasKey('alertas', $grouped->toArray());
    }

    public function test_get_by_group(): void
    {
        $configs = $this->service->getByGroup('general');

        $this->assertCount(2, $configs);
        $this->assertEquals('sistema.nombre', $configs->first()->clave);
    }

    public function test_update_batch_changes_values(): void
    {
        $updated = $this->service->updateBatch([
            'sistema.nombre' => 'SATA v2',
            'asistencia.tolerancia' => '20',
        ]);

        $this->assertEquals(2, $updated);
        $this->assertEquals('SATA v2', ConfiguracionGeneral::where('clave', 'sistema.nombre')->first()->valor);
        $this->assertEquals('20', ConfiguracionGeneral::where('clave', 'asistencia.tolerancia')->first()->valor);
    }

    public function test_update_batch_no_changes_returns_zero(): void
    {
        $updated = $this->service->updateBatch([
            'sistema.nombre' => 'SATA-QR',
        ]);

        $this->assertEquals(0, $updated);
    }

    public function test_update_batch_ignores_unknown_keys(): void
    {
        $updated = $this->service->updateBatch([
            'clave.inexistente' => 'valor',
        ]);

        $this->assertEquals(0, $updated);
    }

    public function test_get_stats(): void
    {
        $stats = $this->service->getStats();

        $this->assertEquals(4, $stats['total']);
        $this->assertEquals(3, $stats['grupos']);
        $this->assertEquals(0, $stats['modificadas']);
    }

    public function test_model_obtener_static(): void
    {
        $this->assertEquals('SATA-QR', ConfiguracionGeneral::obtener('sistema.nombre'));
        $this->assertEquals(15, ConfiguracionGeneral::obtener('asistencia.tolerancia'));
        $this->assertTrue(ConfiguracionGeneral::obtener('alertas.activas'));
        $this->assertNull(ConfiguracionGeneral::obtener('no.existe'));
        $this->assertEquals('default', ConfiguracionGeneral::obtener('no.existe', 'default'));
    }

    public function test_model_establecer_static(): void
    {
        ConfiguracionGeneral::establecer('sistema.nombre', 'Nuevo Nombre');

        $this->assertEquals('Nuevo Nombre', ConfiguracionGeneral::obtener('sistema.nombre'));
    }
}
