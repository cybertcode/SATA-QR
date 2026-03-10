<?php

namespace Tests\Feature;

use App\Models\CalendarioFeriado;
use App\Models\ConfiguracionAsistencia;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\SeedRolesAndPermissions;
use Tests\TestCase;

class InstitutionSettingsTest extends TestCase
{
    use RefreshDatabase, SeedRolesAndPermissions;

    private Tenant $tenant;
    private User $director;
    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();

        $this->tenant = Tenant::create(['id' => 'ie-settings', 'nombre' => 'I.E. Settings Test', 'ugel' => 'TEST']);

        $this->director = User::factory()->create(['tenant_id' => 'ie-settings', 'role' => 'Director']);
        $this->director->syncRoles(['Director']);

        $this->superAdmin = User::factory()->create(['tenant_id' => 'ie-settings', 'role' => 'SuperAdmin']);
        $this->superAdmin->syncRoles(['SuperAdmin']);
    }

    // ─── ACCESO ───

    public function test_director_can_access_settings_page(): void
    {
        $this->actingAs($this->director)
            ->get(route('institution.settings'))
            ->assertOk();
    }

    public function test_superadmin_can_access_settings_page(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(route('institution.settings'))
            ->assertOk();
    }

    public function test_docente_cannot_access_settings_page(): void
    {
        $docente = User::factory()->create(['tenant_id' => 'ie-settings', 'role' => 'Docente']);
        $docente->syncRoles(['Docente']);

        $response = $this->actingAs($docente)
            ->get(route('institution.settings'));

        // Middleware role redirects (302) or forbids (403)
        $this->assertTrue(in_array($response->status(), [302, 403]));
    }

    public function test_unauthenticated_redirects_to_login(): void
    {
        $this->get(route('institution.settings'))
            ->assertRedirect(route('login'));
    }

    // ─── COMPONENTE LIVEWIRE RENDER ───

    public function test_component_renders_for_director(): void
    {
        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->assertSee('Horarios y Tolerancia')
            ->assertSee('Identidad Visual')
            ->assertSee('Días Feriados');
    }

    public function test_component_shows_no_tenant_message_when_no_tenants_exist(): void
    {
        // Remove all tenants so SuperAdmin has nothing to select
        CalendarioFeriado::query()->delete();
        ConfiguracionAsistencia::query()->delete();
        \App\Models\Matricula::query()->delete();
        \App\Models\Estudiante::withoutGlobalScopes()->delete();
        \App\Models\Seccion::withoutGlobalScopes()->delete();
        \App\Models\TenantNivel::query()->delete();
        User::query()->delete();
        Tenant::query()->delete();

        $noTenantUser = User::factory()->create(['tenant_id' => null, 'role' => 'SuperAdmin']);
        $noTenantUser->syncRoles(['SuperAdmin']);

        Livewire::actingAs($noTenantUser)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->assertSee('Sin Institución Asignada');
    }

    public function test_superadmin_auto_selects_first_tenant(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->assertSet('isSuperAdmin', true)
            ->assertSet('tenantId', 'ie-settings')
            ->assertSee('Horarios y Tolerancia');
    }

    public function test_superadmin_can_switch_tenant(): void
    {
        $other = Tenant::create(['id' => 'ie-second', 'nombre' => 'I.E. Segunda', 'ugel' => 'TEST']);
        ConfiguracionAsistencia::create([
            'tenant_id' => 'ie-second',
            'hora_entrada_regular' => '08:00:00',
            'minutos_tolerancia' => 20,
            'dias_inasistencia_riesgo' => 5,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->call('selectTenant', 'ie-second')
            ->assertSet('tenantId', 'ie-second')
            ->assertSet('tenantNombre', 'I.E. Segunda')
            ->assertSet('hora_entrada_regular', '08:00')
            ->assertSet('minutos_tolerancia', 20)
            ->assertSet('dias_inasistencia_riesgo', 5);
    }

    public function test_superadmin_sees_tenant_selector(): void
    {
        Tenant::create(['id' => 'ie-extra', 'nombre' => 'I.E. Extra', 'ugel' => 'TEST']);

        Livewire::actingAs($this->superAdmin)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->assertSee('tenant-selector');
    }

    public function test_director_does_not_see_tenant_selector(): void
    {
        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->assertDontSee('Seleccione la Institución Educativa');
    }

    // ─── TABS ───

    public function test_switch_tab_to_identidad(): void
    {
        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->assertSet('activeTab', 'horarios')
            ->call('switchTab', 'identidad')
            ->assertSet('activeTab', 'identidad');
    }

    public function test_switch_tab_to_feriados(): void
    {
        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->call('switchTab', 'feriados')
            ->assertSet('activeTab', 'feriados');
    }

    // ─── HORARIOS: GUARDAR ───

    public function test_save_horarios_creates_config(): void
    {
        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->set('hora_entrada_regular', '08:00')
            ->set('minutos_tolerancia', 10)
            ->set('dias_inasistencia_riesgo', 5)
            ->call('saveHorarios')
            ->assertDispatched('swal');

        $this->assertDatabaseHas('configuracion_asistencia', [
            'tenant_id' => 'ie-settings',
            'hora_entrada_regular' => '08:00:00',
            'minutos_tolerancia' => 10,
            'dias_inasistencia_riesgo' => 5,
        ]);
    }

    public function test_save_horarios_updates_existing_config(): void
    {
        ConfiguracionAsistencia::create([
            'tenant_id' => 'ie-settings',
            'hora_entrada_regular' => '07:30:00',
            'minutos_tolerancia' => 10,
            'dias_inasistencia_riesgo' => 3,
        ]);

        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->set('hora_entrada_regular', '08:15')
            ->set('minutos_tolerancia', 20)
            ->set('dias_inasistencia_riesgo', 7)
            ->call('saveHorarios')
            ->assertDispatched('swal');

        $this->assertDatabaseHas('configuracion_asistencia', [
            'tenant_id' => 'ie-settings',
            'hora_entrada_regular' => '08:15:00',
            'minutos_tolerancia' => 20,
            'dias_inasistencia_riesgo' => 7,
        ]);

        $this->assertDatabaseCount('configuracion_asistencia', 1);
    }

    public function test_save_horarios_validates_hora_required(): void
    {
        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->set('hora_entrada_regular', '')
            ->call('saveHorarios')
            ->assertHasErrors('hora_entrada_regular');
    }

    public function test_save_horarios_validates_tolerancia_max(): void
    {
        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->set('minutos_tolerancia', 61)
            ->call('saveHorarios')
            ->assertHasErrors('minutos_tolerancia');
    }

    public function test_save_horarios_validates_dias_riesgo_min(): void
    {
        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->set('dias_inasistencia_riesgo', 0)
            ->call('saveHorarios')
            ->assertHasErrors('dias_inasistencia_riesgo');
    }

    // ─── HORARIOS: CARGA INICIAL ───

    public function test_mount_loads_existing_config(): void
    {
        ConfiguracionAsistencia::create([
            'tenant_id' => 'ie-settings',
            'hora_entrada_regular' => '08:30:00',
            'minutos_tolerancia' => 20,
            'dias_inasistencia_riesgo' => 4,
        ]);

        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->assertSet('hora_entrada_regular', '08:30')
            ->assertSet('minutos_tolerancia', 20)
            ->assertSet('dias_inasistencia_riesgo', 4);
    }

    // ─── IDENTIDAD VISUAL ───

    public function test_save_identidad_updates_tenant_config(): void
    {
        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->call('switchTab', 'identidad')
            ->set('primary_color', '#ff5500')
            ->set('lema', 'Educando con valores')
            ->call('saveIdentidad')
            ->assertDispatched('swal');

        $tenant = Tenant::find('ie-settings');
        $this->assertEquals('#ff5500', $tenant->config['primary_color']);
        $this->assertEquals('Educando con valores', $tenant->config['lema']);
    }

    public function test_save_identidad_validates_color_format(): void
    {
        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->set('primary_color', 'invalid')
            ->call('saveIdentidad')
            ->assertHasErrors('primary_color');
    }

    public function test_save_identidad_validates_lema_max_length(): void
    {
        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->set('lema', str_repeat('x', 201))
            ->call('saveIdentidad')
            ->assertHasErrors('lema');
    }

    public function test_mount_loads_existing_identidad(): void
    {
        $this->tenant->update(['config' => ['primary_color' => '#00ff00', 'lema' => 'Test lema']]);

        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->assertSet('primary_color', '#00ff00')
            ->assertSet('lema', 'Test lema');
    }

    // ─── FERIADOS: CREAR ───

    public function test_create_feriado(): void
    {
        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->call('switchTab', 'feriados')
            ->set('feriado_fecha', '2026-07-06')
            ->set('feriado_descripcion', 'Día del Maestro')
            ->call('saveFeriado')
            ->assertDispatched('swal');

        $feriado = CalendarioFeriado::where('tenant_id', 'ie-settings')->first();
        $this->assertNotNull($feriado);
        $this->assertEquals('2026-07-06', $feriado->fecha->format('Y-m-d'));
        $this->assertEquals('Día del Maestro', $feriado->descripcion);
    }

    public function test_create_feriado_validates_required_fields(): void
    {
        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->call('switchTab', 'feriados')
            ->set('feriado_fecha', '')
            ->set('feriado_descripcion', '')
            ->call('saveFeriado')
            ->assertHasErrors(['feriado_fecha', 'feriado_descripcion']);
    }

    public function test_create_feriado_prevents_duplicate_date(): void
    {
        CalendarioFeriado::create([
            'tenant_id' => 'ie-settings',
            'fecha' => '2026-07-28',
            'descripcion' => 'Fiestas Patrias',
        ]);

        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->call('switchTab', 'feriados')
            ->set('feriado_fecha', '2026-07-28')
            ->set('feriado_descripcion', 'Otro evento')
            ->call('saveFeriado')
            ->assertHasErrors('feriado_fecha');

        // Only one record should exist for that date
        $this->assertEquals(1, CalendarioFeriado::where('tenant_id', 'ie-settings')
            ->whereDate('fecha', '2026-07-28')->count());
    }

    // ─── FERIADOS: EDITAR ───

    public function test_edit_feriado(): void
    {
        $feriado = CalendarioFeriado::create([
            'tenant_id' => 'ie-settings',
            'fecha' => '2026-12-25',
            'descripcion' => 'Navidad',
        ]);

        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->call('switchTab', 'feriados')
            ->call('editFeriado', $feriado->id)
            ->assertSet('editingFeriadoId', $feriado->id)
            ->assertSet('feriado_fecha', '2026-12-25')
            ->assertSet('feriado_descripcion', 'Navidad')
            ->set('feriado_descripcion', 'Navidad — Receso')
            ->call('saveFeriado')
            ->assertDispatched('swal');

        $this->assertDatabaseHas('calendario_feriados', [
            'id' => $feriado->id,
            'descripcion' => 'Navidad — Receso',
        ]);
    }

    public function test_cancel_edit_feriado_resets_form(): void
    {
        $feriado = CalendarioFeriado::create([
            'tenant_id' => 'ie-settings',
            'fecha' => '2026-12-25',
            'descripcion' => 'Navidad',
        ]);

        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->call('switchTab', 'feriados')
            ->call('editFeriado', $feriado->id)
            ->assertSet('editingFeriadoId', $feriado->id)
            ->call('cancelEditFeriado')
            ->assertSet('editingFeriadoId', null)
            ->assertSet('feriado_fecha', '')
            ->assertSet('feriado_descripcion', '');
    }

    // ─── FERIADOS: ELIMINAR ───

    public function test_delete_feriado(): void
    {
        $feriado = CalendarioFeriado::create([
            'tenant_id' => 'ie-settings',
            'fecha' => '2026-05-01',
            'descripcion' => 'Día del Trabajo',
        ]);

        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->call('switchTab', 'feriados')
            ->call('deleteFeriado', $feriado->id)
            ->assertDispatched('swal');

        $this->assertDatabaseMissing('calendario_feriados', ['id' => $feriado->id]);
    }

    // ─── FERIADOS: AISLAMIENTO TENANT ───

    public function test_cannot_edit_feriado_from_other_tenant(): void
    {
        Tenant::create(['id' => 'ie-other', 'nombre' => 'Otra I.E.', 'ugel' => 'TEST']);
        $feriado = CalendarioFeriado::create([
            'tenant_id' => 'ie-other',
            'fecha' => '2026-01-01',
            'descripcion' => 'Año Nuevo',
        ]);

        // firstOrFail throws ModelNotFoundException which Livewire converts to 404
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->call('switchTab', 'feriados')
            ->call('editFeriado', $feriado->id);
    }

    public function test_cannot_delete_feriado_from_other_tenant(): void
    {
        $otherTenant = Tenant::create(['id' => 'ie-other2', 'nombre' => 'Otra I.E. 2', 'ugel' => 'TEST']);
        $feriado = CalendarioFeriado::create([
            'tenant_id' => 'ie-other2',
            'fecha' => '2026-01-01',
            'descripcion' => 'Año Nuevo',
        ]);

        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->call('switchTab', 'feriados')
            ->call('deleteFeriado', $feriado->id);

        // Feriado should still exist because it belongs to another tenant
        $this->assertDatabaseHas('calendario_feriados', ['id' => $feriado->id]);
    }

    // ─── CIERRE ASISTENCIA ───

    public function test_close_day_dispatches_swal(): void
    {
        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->call('closeDay')
            ->assertDispatched('swal');
    }

    // ─── AUTORIZACIÓN ADICIONAL ───

    public function test_auxiliar_cannot_access_settings(): void
    {
        $aux = User::factory()->create(['tenant_id' => 'ie-settings', 'role' => 'Auxiliar']);
        $aux->syncRoles(['Auxiliar']);

        $response = $this->actingAs($aux)->get(route('institution.settings'));
        $this->assertTrue(in_array($response->status(), [302, 403]));
    }

    public function test_administrador_cannot_access_settings(): void
    {
        $admin = User::factory()->create(['tenant_id' => 'ie-settings', 'role' => 'Administrador']);
        $admin->syncRoles(['Administrador']);

        $response = $this->actingAs($admin)->get(route('institution.settings'));
        $this->assertTrue(in_array($response->status(), [302, 403]));
    }

    // ─── HORARIOS: VALIDACIONES ADICIONALES ───

    public function test_save_horarios_validates_tolerancia_min(): void
    {
        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->set('minutos_tolerancia', -1)
            ->call('saveHorarios')
            ->assertHasErrors('minutos_tolerancia');
    }

    public function test_save_horarios_validates_dias_riesgo_max(): void
    {
        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->set('dias_inasistencia_riesgo', 31)
            ->call('saveHorarios')
            ->assertHasErrors('dias_inasistencia_riesgo');
    }

    public function test_save_horarios_validates_hora_format(): void
    {
        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->set('hora_entrada_regular', 'no-es-hora')
            ->call('saveHorarios')
            ->assertHasErrors('hora_entrada_regular');
    }

    // ─── IDENTIDAD: VALIDACIONES ADICIONALES ───

    public function test_save_identidad_allows_empty_lema(): void
    {
        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->call('switchTab', 'identidad')
            ->set('primary_color', '#ff0000')
            ->set('lema', '')
            ->call('saveIdentidad')
            ->assertHasNoErrors()
            ->assertDispatched('swal');
    }

    public function test_save_identidad_preserves_existing_config(): void
    {
        $this->tenant->update(['config' => ['primary_color' => '#123456', 'lema' => 'Original', 'extra_key' => 'extra_val']]);

        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->call('switchTab', 'identidad')
            ->set('primary_color', '#654321')
            ->set('lema', 'Nuevo lema')
            ->call('saveIdentidad');

        $tenant = Tenant::find('ie-settings');
        $this->assertEquals('#654321', $tenant->config['primary_color']);
        $this->assertEquals('Nuevo lema', $tenant->config['lema']);
        $this->assertEquals('extra_val', $tenant->config['extra_key']);
    }

    // ─── FERIADOS: VALIDACIONES ADICIONALES ───

    public function test_create_feriado_validates_description_max(): void
    {
        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->call('switchTab', 'feriados')
            ->set('feriado_fecha', '2026-12-25')
            ->set('feriado_descripcion', str_repeat('x', 151))
            ->call('saveFeriado')
            ->assertHasErrors('feriado_descripcion');
    }

    public function test_create_feriado_validates_date_format(): void
    {
        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->call('switchTab', 'feriados')
            ->set('feriado_fecha', 'no-es-fecha')
            ->set('feriado_descripcion', 'Test')
            ->call('saveFeriado')
            ->assertHasErrors('feriado_fecha');
    }

    public function test_edit_feriado_allows_same_date_when_editing_same(): void
    {
        $feriado = CalendarioFeriado::create([
            'tenant_id' => 'ie-settings',
            'fecha' => '2026-09-15',
            'descripcion' => 'Evento original',
        ]);

        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->call('switchTab', 'feriados')
            ->call('editFeriado', $feriado->id)
            ->set('feriado_descripcion', 'Evento editado')
            ->call('saveFeriado')
            ->assertHasNoErrors()
            ->assertDispatched('swal');

        $this->assertEquals('Evento editado', CalendarioFeriado::find($feriado->id)->descripcion);
    }

    public function test_feriados_list_shows_only_current_tenant(): void
    {
        CalendarioFeriado::create([
            'tenant_id' => 'ie-settings',
            'fecha' => '2026-05-01',
            'descripcion' => 'Día del Trabajo',
        ]);

        Tenant::create(['id' => 'ie-otro', 'nombre' => 'Otra IE', 'ugel' => 'TEST']);
        CalendarioFeriado::create([
            'tenant_id' => 'ie-otro',
            'fecha' => '2026-06-24',
            'descripcion' => 'Inti Raymi',
        ]);

        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->call('switchTab', 'feriados')
            ->assertSee('Día del Trabajo')
            ->assertDontSee('Inti Raymi');
    }

    // ─── SUPERADMIN: TENANT SWITCH ───

    public function test_superadmin_switch_loads_correct_identidad(): void
    {
        $otherTenant = Tenant::create([
            'id' => 'ie-switch',
            'nombre' => 'I.E. Switch Test',
            'ugel' => 'TEST',
            'config' => ['primary_color' => '#aabbcc', 'lema' => 'Lema Switch'],
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->call('selectTenant', 'ie-switch')
            ->assertSet('primary_color', '#aabbcc')
            ->assertSet('lema', 'Lema Switch')
            ->assertSet('tenantNombre', 'I.E. Switch Test');
    }

    public function test_superadmin_switch_resets_feriado_form(): void
    {
        Tenant::create(['id' => 'ie-reset', 'nombre' => 'I.E. Reset', 'ugel' => 'TEST']);

        Livewire::actingAs($this->superAdmin)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->call('switchTab', 'feriados')
            ->set('feriado_fecha', '2026-07-28')
            ->set('feriado_descripcion', 'Fiestas')
            ->call('selectTenant', 'ie-reset')
            ->assertSet('feriado_fecha', '')
            ->assertSet('feriado_descripcion', '')
            ->assertSet('editingFeriadoId', null);
    }

    public function test_superadmin_switch_invalid_tenant_throws(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::actingAs($this->superAdmin)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->call('selectTenant', 'ie-no-existe');
    }

    // ─── VIEW RENDERING ───

    public function test_view_renders_three_tabs(): void
    {
        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->assertSee('Horarios y Tolerancia')
            ->assertSee('Identidad Visual')
            ->assertSee('Días Feriados');
    }

    public function test_view_renders_tenant_name(): void
    {
        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->assertSee('I.E. Settings Test');
    }

    public function test_horarios_tab_shows_save_button(): void
    {
        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->assertSeeHtml('wire:submit="saveHorarios"');
    }

    public function test_identidad_tab_shows_fields(): void
    {
        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->call('switchTab', 'identidad')
            ->assertSeeHtml('wire:model.live="primary_color"')
            ->assertSeeHtml('wire:model="lema"');
    }

    public function test_feriados_tab_shows_form_fields(): void
    {
        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->call('switchTab', 'feriados')
            ->assertSeeHtml('wire:model="feriado_fecha"')
            ->assertSeeHtml('wire:model="feriado_descripcion"');
    }

    public function test_horarios_tab_shows_close_day_section(): void
    {
        Livewire::actingAs($this->director)
            ->test(\App\Livewire\Sata\InstitutionSettingsManager::class)
            ->assertSee('Cierre de Asistencia Diario');
    }
}
