<?php

namespace Tests\Feature;

use App\Livewire\Sata\ConfiguracionGeneralManager;
use App\Models\ConfiguracionGeneral;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\SeedRolesAndPermissions;
use Tests\TestCase;

class ConfiguracionGeneralFlowTest extends TestCase
{
    use RefreshDatabase, SeedRolesAndPermissions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
        $this->seedConfigs();
    }

    private function seedConfigs(): void
    {
        $configs = [
            ['grupo' => 'general', 'clave' => 'sistema.nombre', 'valor' => 'SATA-QR', 'tipo' => 'string', 'etiqueta' => 'Nombre', 'orden' => 1],
            ['grupo' => 'general', 'clave' => 'sistema.ugel', 'valor' => 'UGEL Huacaybamba', 'tipo' => 'string', 'etiqueta' => 'UGEL', 'orden' => 2],
            ['grupo' => 'asistencia', 'clave' => 'asistencia.tolerancia', 'valor' => '15', 'tipo' => 'integer', 'etiqueta' => 'Tolerancia', 'orden' => 1],
            ['grupo' => 'asistencia', 'clave' => 'asistencia.manual', 'valor' => '1', 'tipo' => 'boolean', 'etiqueta' => 'Registro Manual', 'orden' => 2],
            ['grupo' => 'alertas', 'clave' => 'alertas.dias_leve', 'valor' => '3', 'tipo' => 'integer', 'etiqueta' => 'Días Leve', 'orden' => 1],
            ['grupo' => 'seguridad', 'clave' => 'seguridad.intentos', 'valor' => '5', 'tipo' => 'integer', 'etiqueta' => 'Intentos Login', 'orden' => 1],
            ['grupo' => 'apariencia', 'clave' => 'apariencia.color', 'valor' => '#4f46e5', 'tipo' => 'string', 'etiqueta' => 'Color', 'orden' => 1],
        ];

        foreach ($configs as $config) {
            ConfiguracionGeneral::create($config);
        }
    }

    public function test_superadmin_can_access_page(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->get(route('config.general'))
            ->assertOk();
    }

    public function test_non_superadmin_cannot_access_page(): void
    {
        $user = User::factory()->administrador()->create();

        $response = $this->actingAs($user)->get(route('config.general'));
        $this->assertTrue(in_array($response->getStatusCode(), [302, 403]));
    }

    public function test_component_renders_with_default_group(): void
    {
        $admin = User::factory()->superAdmin()->create();

        Livewire::actingAs($admin)
            ->test(ConfiguracionGeneralManager::class)
            ->assertSet('activeGroup', 'general')
            ->assertSee('Nombre')
            ->assertSee('UGEL');
    }

    public function test_switch_group(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $toleranciaConfig = ConfiguracionGeneral::where('clave', 'asistencia.tolerancia')->first();

        Livewire::actingAs($admin)
            ->test(ConfiguracionGeneralManager::class)
            ->call('switchGroup', 'asistencia')
            ->assertSet('activeGroup', 'asistencia')
            ->assertSet("valores.{$toleranciaConfig->id}", '15');
    }

    public function test_save_updates_values(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $nombreConfig = ConfiguracionGeneral::where('clave', 'sistema.nombre')->first();

        Livewire::actingAs($admin)
            ->test(ConfiguracionGeneralManager::class)
            ->set("valores.{$nombreConfig->id}", 'SATA v2')
            ->call('save')
            ->assertDispatched('swal');

        $this->assertEquals('SATA v2', ConfiguracionGeneral::obtener('sistema.nombre'));
    }

    public function test_save_no_changes_dispatches_info(): void
    {
        $admin = User::factory()->superAdmin()->create();

        Livewire::actingAs($admin)
            ->test(ConfiguracionGeneralManager::class)
            ->call('save')
            ->assertDispatched('swal');
    }

    public function test_save_validates_integer_fields(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $toleranciaConfig = ConfiguracionGeneral::where('clave', 'asistencia.tolerancia')->first();

        Livewire::actingAs($admin)
            ->test(ConfiguracionGeneralManager::class)
            ->call('switchGroup', 'asistencia')
            ->set("valores.{$toleranciaConfig->id}", 'abc')
            ->call('save')
            ->assertHasErrors("valores.{$toleranciaConfig->id}");
    }

    public function test_save_validates_required_fields(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $nombreConfig = ConfiguracionGeneral::where('clave', 'sistema.nombre')->first();

        Livewire::actingAs($admin)
            ->test(ConfiguracionGeneralManager::class)
            ->set("valores.{$nombreConfig->id}", '')
            ->call('save')
            ->assertHasErrors("valores.{$nombreConfig->id}");
    }

    public function test_reset_group_restores_saved_values(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $nombreConfig = ConfiguracionGeneral::where('clave', 'sistema.nombre')->first();

        Livewire::actingAs($admin)
            ->test(ConfiguracionGeneralManager::class)
            ->set("valores.{$nombreConfig->id}", 'Changed')
            ->call('resetGroup')
            ->assertSet("valores.{$nombreConfig->id}", 'SATA-QR')
            ->assertDispatched('swal');
    }

    public function test_stats_are_correct(): void
    {
        $admin = User::factory()->superAdmin()->create();

        Livewire::actingAs($admin)
            ->test(ConfiguracionGeneralManager::class)
            ->assertSet('stats.total', 7)
            ->assertSet('stats.grupos', 5);
    }

    public function test_all_groups_navigable(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $groups = ['general', 'asistencia', 'alertas', 'seguridad', 'apariencia'];

        foreach ($groups as $group) {
            Livewire::actingAs($admin)
                ->test(ConfiguracionGeneralManager::class)
                ->call('switchGroup', $group)
                ->assertSet('activeGroup', $group)
                ->assertOk();
        }
    }

    public function test_director_cannot_access(): void
    {
        $director = User::factory()->director()->create();

        $response = $this->actingAs($director)->get(route('config.general'));
        $this->assertTrue(in_array($response->getStatusCode(), [302, 403]));
    }
}
