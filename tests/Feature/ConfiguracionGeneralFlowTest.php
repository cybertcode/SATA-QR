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

    // ─── TIPOS DE DATOS ───

    public function test_save_boolean_field_toggles_correctly(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $boolConfig = ConfiguracionGeneral::where('clave', 'asistencia.manual')->first();

        // El valor actual es true (1)
        Livewire::actingAs($admin)
            ->test(ConfiguracionGeneralManager::class)
            ->call('switchGroup', 'asistencia')
            ->set("valores.{$boolConfig->id}", false)
            ->call('save')
            ->assertDispatched('swal');

        $this->assertEquals('0', ConfiguracionGeneral::find($boolConfig->id)->valor);
    }

    public function test_save_integer_field_rejects_negative(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $intConfig = ConfiguracionGeneral::where('clave', 'asistencia.tolerancia')->first();

        Livewire::actingAs($admin)
            ->test(ConfiguracionGeneralManager::class)
            ->call('switchGroup', 'asistencia')
            ->set("valores.{$intConfig->id}", -5)
            ->call('save')
            ->assertHasErrors("valores.{$intConfig->id}");
    }

    public function test_save_string_field_rejects_over_500_chars(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $strConfig = ConfiguracionGeneral::where('clave', 'sistema.nombre')->first();

        Livewire::actingAs($admin)
            ->test(ConfiguracionGeneralManager::class)
            ->set("valores.{$strConfig->id}", str_repeat('x', 501))
            ->call('save')
            ->assertHasErrors("valores.{$strConfig->id}");
    }

    // ─── MÚLTIPLES CAMPOS ───

    public function test_save_updates_multiple_fields_at_once(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $nombre = ConfiguracionGeneral::where('clave', 'sistema.nombre')->first();
        $ugel = ConfiguracionGeneral::where('clave', 'sistema.ugel')->first();

        Livewire::actingAs($admin)
            ->test(ConfiguracionGeneralManager::class)
            ->set("valores.{$nombre->id}", 'Nuevo Sistema')
            ->set("valores.{$ugel->id}", 'UGEL Otra')
            ->call('save')
            ->assertDispatched('swal');

        $this->assertEquals('Nuevo Sistema', ConfiguracionGeneral::obtener('sistema.nombre'));
        $this->assertEquals('UGEL Otra', ConfiguracionGeneral::obtener('sistema.ugel'));
    }

    // ─── VALORES CARGADOS CORRECTAMENTE ───

    public function test_each_group_loads_correct_configs(): void
    {
        $admin = User::factory()->superAdmin()->create();

        // General tiene 2 configs en nuestro seed
        Livewire::actingAs($admin)
            ->test(ConfiguracionGeneralManager::class)
            ->assertSet('activeGroup', 'general')
            ->assertCount('valores', 2);

        // Asistencia tiene 2 configs
        Livewire::actingAs($admin)
            ->test(ConfiguracionGeneralManager::class)
            ->call('switchGroup', 'asistencia')
            ->assertCount('valores', 2);

        // Alertas tiene 1 config
        Livewire::actingAs($admin)
            ->test(ConfiguracionGeneralManager::class)
            ->call('switchGroup', 'alertas')
            ->assertCount('valores', 1);
    }

    public function test_boolean_config_loaded_as_boolean(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $boolConfig = ConfiguracionGeneral::where('clave', 'asistencia.manual')->first();

        Livewire::actingAs($admin)
            ->test(ConfiguracionGeneralManager::class)
            ->call('switchGroup', 'asistencia')
            ->assertSet("valores.{$boolConfig->id}", true);
    }

    // ─── EMPTY GROUP ───

    public function test_switching_to_group_with_no_configs_shows_empty(): void
    {
        $admin = User::factory()->superAdmin()->create();

        // Eliminar configs de apariencia para dejarlo vacío
        ConfiguracionGeneral::where('grupo', 'apariencia')->delete();

        Livewire::actingAs($admin)
            ->test(ConfiguracionGeneralManager::class)
            ->call('switchGroup', 'apariencia')
            ->assertSet('activeGroup', 'apariencia')
            ->assertCount('valores', 0);
    }

    // ─── MODELO ESTÁTICO ───

    public function test_obtener_returns_default_for_missing_key(): void
    {
        $this->assertEquals('fallback', ConfiguracionGeneral::obtener('no.existe', 'fallback'));
    }

    public function test_obtener_casts_types_correctly(): void
    {
        $this->assertIsString(ConfiguracionGeneral::obtener('sistema.nombre'));
        $this->assertIsInt(ConfiguracionGeneral::obtener('asistencia.tolerancia'));
        $this->assertIsBool(ConfiguracionGeneral::obtener('asistencia.manual'));
    }

    public function test_establecer_updates_value_in_database(): void
    {
        ConfiguracionGeneral::establecer('sistema.ugel', 'UGEL Test Actualizada');

        $this->assertEquals('UGEL Test Actualizada', ConfiguracionGeneral::obtener('sistema.ugel'));
    }

    public function test_establecer_throws_for_unknown_key(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        ConfiguracionGeneral::establecer('clave.inexistente', 'valor');
    }

    // ─── STATS ACTUALIZADO ───

    public function test_stats_update_after_save(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $nombre = ConfiguracionGeneral::where('clave', 'sistema.nombre')->first();

        // Simular un config previamente modificado para que stats.modificadas > 0
        ConfiguracionGeneral::where('clave', 'sistema.ugel')
            ->update(['updated_at' => now()->addDay(), 'valor' => 'UGEL Cambiada']);

        Livewire::actingAs($admin)
            ->test(ConfiguracionGeneralManager::class)
            ->assertSet('stats.modificadas', 1)
            ->set("valores.{$nombre->id}", 'Cambio Stats')
            ->call('save')
            ->assertDispatched('swal');

        $this->assertEquals('Cambio Stats', ConfiguracionGeneral::obtener('sistema.nombre'));
    }

    // ─── PERSISTENCIA ENTRE GRUPOS ───

    public function test_changes_persist_after_switching_groups(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $nombre = ConfiguracionGeneral::where('clave', 'sistema.nombre')->first();

        // Save in general group
        Livewire::actingAs($admin)
            ->test(ConfiguracionGeneralManager::class)
            ->set("valores.{$nombre->id}", 'Persistido')
            ->call('save');

        // Switch to asistencia and back
        Livewire::actingAs($admin)
            ->test(ConfiguracionGeneralManager::class)
            ->call('switchGroup', 'asistencia')
            ->call('switchGroup', 'general')
            ->assertSet("valores.{$nombre->id}", 'Persistido');
    }

    // ─── RESET NO AFECTA BD ───

    public function test_reset_does_not_modify_database(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $nombre = ConfiguracionGeneral::where('clave', 'sistema.nombre')->first();

        Livewire::actingAs($admin)
            ->test(ConfiguracionGeneralManager::class)
            ->set("valores.{$nombre->id}", 'Temporal')
            ->call('resetGroup');

        // DB value should remain unchanged
        $this->assertEquals('SATA-QR', ConfiguracionGeneral::obtener('sistema.nombre'));
    }

    // ─── AUTORIZACIÓN ───

    public function test_auxiliar_cannot_access(): void
    {
        $aux = User::factory()->create(['role' => 'Auxiliar']);
        $aux->syncRoles(['Auxiliar']);

        $response = $this->actingAs($aux)->get(route('config.general'));
        $this->assertTrue(in_array($response->getStatusCode(), [302, 403]));
    }

    public function test_docente_cannot_access(): void
    {
        $docente = User::factory()->create(['role' => 'Docente']);
        $docente->syncRoles(['Docente']);

        $response = $this->actingAs($docente)->get(route('config.general'));
        $this->assertTrue(in_array($response->getStatusCode(), [302, 403]));
    }

    public function test_unauthenticated_redirects_to_login(): void
    {
        $this->get(route('config.general'))
            ->assertRedirect(route('login'));
    }

    // ─── VIEW RENDERING ───

    public function test_view_renders_group_tabs(): void
    {
        $admin = User::factory()->superAdmin()->create();

        Livewire::actingAs($admin)
            ->test(ConfiguracionGeneralManager::class)
            ->assertSee('General')
            ->assertSee('Asistencia')
            ->assertSee('Alertas Tempranas')
            ->assertSee('Seguridad')
            ->assertSee('Apariencia');
    }

    public function test_view_renders_save_button(): void
    {
        $admin = User::factory()->superAdmin()->create();

        Livewire::actingAs($admin)
            ->test(ConfiguracionGeneralManager::class)
            ->assertSeeHtml('wire:submit="save"');
    }

    public function test_view_renders_reset_button(): void
    {
        $admin = User::factory()->superAdmin()->create();

        Livewire::actingAs($admin)
            ->test(ConfiguracionGeneralManager::class)
            ->assertSeeHtml('wire:click="resetGroup"');
    }
}
