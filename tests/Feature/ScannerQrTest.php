<?php

namespace Tests\Feature;

use App\Models\AnioLectivo;
use App\Models\Asistencia;
use App\Models\ConfiguracionAsistencia;
use App\Models\Estudiante;
use App\Models\Matricula;
use App\Models\Seccion;
use App\Models\Tenant;
use App\Models\TenantNivel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\SeedRolesAndPermissions;
use Tests\TestCase;

class ScannerQrTest extends TestCase
{
    use RefreshDatabase, SeedRolesAndPermissions;

    private Tenant $tenant;
    private TenantNivel $nivel;
    private AnioLectivo $anio;
    private User $auxiliar;
    private ConfiguracionAsistencia $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();

        $this->tenant = Tenant::create(['id' => 'ie-scan', 'nombre' => 'I.E. Scanner Test', 'ugel' => 'TEST']);
        $this->nivel = TenantNivel::create(['tenant_id' => 'ie-scan', 'nivel' => 'Secundaria', 'codigo_modular' => '8888801']);

        $this->config = ConfiguracionAsistencia::create([
            'tenant_id' => 'ie-scan',
            'hora_entrada_regular' => '07:45:00',
            'minutos_tolerancia' => 15,
        ]);

        $this->anio = AnioLectivo::create([
            'nombre_anio' => 'Año Scanner',
            'anio' => now()->year,
            'fecha_inicio' => now()->startOfYear(),
            'fecha_fin' => now()->endOfYear(),
            'estado' => true,
        ]);

        $this->auxiliar = User::factory()->create(['tenant_id' => 'ie-scan', 'role' => 'Auxiliar']);
        $this->auxiliar->syncRoles(['Auxiliar']);
    }

    private function createStudentWithEnrollment(string $estado = 'Activo'): Estudiante
    {
        $student = Estudiante::withoutGlobalScopes()->create([
            'tenant_id' => 'ie-scan',
            'dni' => fake()->unique()->numerify('########'),
            'nombres' => fake()->firstName(),
            'apellido_paterno' => fake()->lastName(),
            'apellido_materno' => fake()->lastName(),
            'genero' => 'M',
            'qr_uuid' => (string) Str::uuid(),
        ]);

        $seccion = Seccion::withoutGlobalScopes()->firstOrCreate([
            'tenant_id' => 'ie-scan',
            'tenant_nivel_id' => $this->nivel->id,
            'grado' => '1',
            'letra' => 'A',
        ]);

        Matricula::withoutGlobalScopes()->create([
            'tenant_id' => 'ie-scan',
            'estudiante_id' => $student->id,
            'seccion_id' => $seccion->id,
            'anio_lectivo_id' => $this->anio->id,
            'estado' => $estado,
        ]);

        return $student;
    }

    // ─── VALIDACIÓN ────────────────────────────────────

    public function test_scan_requires_authentication(): void
    {
        $response = $this->postJson(route('scan.process'), ['qr_uuid' => Str::uuid()]);

        $response->assertStatus(401);
    }

    public function test_scan_requires_qr_uuid(): void
    {
        $response = $this->actingAs($this->auxiliar)
            ->postJson(route('scan.process'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('qr_uuid');
    }

    public function test_scan_requires_valid_uuid_format(): void
    {
        $response = $this->actingAs($this->auxiliar)
            ->postJson(route('scan.process'), ['qr_uuid' => 'not-a-uuid']);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('qr_uuid');
    }

    // ─── QR NO ENCONTRADO ──────────────────────────────

    public function test_scan_unknown_qr_returns_404(): void
    {
        $response = $this->actingAs($this->auxiliar)
            ->postJson(route('scan.process'), ['qr_uuid' => (string) Str::uuid()]);

        $response->assertStatus(404)
            ->assertJson(['success' => false, 'message' => 'Código QR no reconocido.']);
    }

    // ─── SIN MATRÍCULA ACTIVA ──────────────────────────

    public function test_scan_student_without_active_enrollment_returns_403(): void
    {
        $student = $this->createStudentWithEnrollment('Retirado');

        $response = $this->actingAs($this->auxiliar)
            ->postJson(route('scan.process'), ['qr_uuid' => $student->qr_uuid]);

        $response->assertStatus(403)
            ->assertJson(['success' => false, 'message' => 'Estudiante sin matrícula activa.']);
    }

    // ─── ASISTENCIA PUNTUAL (PRESENTE) ─────────────────

    public function test_scan_on_time_marks_present(): void
    {
        $student = $this->createStudentWithEnrollment();

        // 07:50 → dentro de tolerancia (07:45 + 15min = 08:00)
        Carbon::setTestNow(Carbon::today()->setTime(7, 50, 0));

        $response = $this->actingAs($this->auxiliar)
            ->postJson(route('scan.process'), ['qr_uuid' => $student->qr_uuid]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'estado' => 'PRESENTE',
                'dni' => $student->dni,
            ]);

        $asistencia = Asistencia::withoutGlobalScopes()->where('tenant_id', 'ie-scan')->first();
        $this->assertNotNull($asistencia);
        $this->assertEquals('P', $asistencia->estado);
        $this->assertEquals(Carbon::today()->toDateString(), $asistencia->fecha->toDateString());

        Carbon::setTestNow();
    }

    // ─── ASISTENCIA TARDE ──────────────────────────────

    public function test_scan_late_marks_tarde(): void
    {
        $student = $this->createStudentWithEnrollment();

        // 08:30 → fuera de tolerancia (07:45 + 15min = 08:00)
        Carbon::setTestNow(Carbon::today()->setTime(8, 30, 0));

        $response = $this->actingAs($this->auxiliar)
            ->postJson(route('scan.process'), ['qr_uuid' => $student->qr_uuid]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'estado' => 'TARDE',
            ]);

        $this->assertDatabaseHas('asistencias', [
            'tenant_id' => 'ie-scan',
            'estado' => 'T',
        ]);

        Carbon::setTestNow();
    }

    // ─── EXACTAMENTE AL LÍMITE DE TOLERANCIA ───────────

    public function test_scan_at_tolerance_limit_marks_present(): void
    {
        $student = $this->createStudentWithEnrollment();

        // 08:00:00 exacto → límite de tolerancia (07:45 + 15min)
        Carbon::setTestNow(Carbon::today()->setTime(8, 0, 0));

        $response = $this->actingAs($this->auxiliar)
            ->postJson(route('scan.process'), ['qr_uuid' => $student->qr_uuid]);

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'estado' => 'PRESENTE']);

        Carbon::setTestNow();
    }

    // ─── UN MINUTO DESPUÉS DE TOLERANCIA ───────────────

    public function test_scan_one_minute_after_tolerance_marks_tarde(): void
    {
        $student = $this->createStudentWithEnrollment();

        // 08:01 → 1 minuto después del límite
        Carbon::setTestNow(Carbon::today()->setTime(8, 1, 0));

        $response = $this->actingAs($this->auxiliar)
            ->postJson(route('scan.process'), ['qr_uuid' => $student->qr_uuid]);

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'estado' => 'TARDE']);

        Carbon::setTestNow();
    }

    // ─── DOBLE ESCANEO MISMO DÍA (UPDATE) ──────────────

    public function test_double_scan_same_day_updates_existing_record(): void
    {
        $student = $this->createStudentWithEnrollment();

        // Primer escaneo a las 07:30 (PRESENTE)
        Carbon::setTestNow(Carbon::today()->setTime(7, 30, 0));
        $this->actingAs($this->auxiliar)
            ->postJson(route('scan.process'), ['qr_uuid' => $student->qr_uuid]);

        // Segundo escaneo a las 09:00 (TARDE) — mismo día
        Carbon::setTestNow(Carbon::today()->setTime(9, 0, 0));
        $this->actingAs($this->auxiliar)
            ->postJson(route('scan.process'), ['qr_uuid' => $student->qr_uuid]);

        // Solo debe existir 1 registro (updateOrCreate)
        $count = Asistencia::withoutGlobalScopes()
            ->where('tenant_id', 'ie-scan')
            ->count();

        $this->assertEquals(1, $count);

        Carbon::setTestNow();
    }

    // ─── ESCANEO EN DÍAS DIFERENTES ────────────────────

    public function test_scan_different_days_creates_separate_records(): void
    {
        $student = $this->createStudentWithEnrollment();

        // Día 1
        Carbon::setTestNow(Carbon::today()->setTime(7, 30, 0));
        $this->actingAs($this->auxiliar)
            ->postJson(route('scan.process'), ['qr_uuid' => $student->qr_uuid]);

        // Día 2
        Carbon::setTestNow(Carbon::today()->addDay()->setTime(7, 30, 0));
        $this->actingAs($this->auxiliar)
            ->postJson(route('scan.process'), ['qr_uuid' => $student->qr_uuid]);

        $count = Asistencia::withoutGlobalScopes()
            ->where('tenant_id', 'ie-scan')
            ->count();

        $this->assertEquals(2, $count);

        Carbon::setTestNow();
    }

    // ─── RESPUESTA JSON COMPLETA ───────────────────────

    public function test_scan_returns_complete_json_structure(): void
    {
        $student = $this->createStudentWithEnrollment();

        Carbon::setTestNow(Carbon::today()->setTime(7, 50, 0));

        $response = $this->actingAs($this->auxiliar)
            ->postJson(route('scan.process'), ['qr_uuid' => $student->qr_uuid]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'student',
                'dni',
                'hora',
                'estado',
                'vulnerabilidad',
            ]);

        $data = $response->json();
        $this->assertTrue($data['success']);
        $this->assertEquals($student->nombre_completo, $data['student']);
        $this->assertEquals($student->dni, $data['dni']);

        Carbon::setTestNow();
    }

    // ─── REGISTRADO POR ────────────────────────────────

    public function test_scan_records_authenticated_user_as_registrador(): void
    {
        $student = $this->createStudentWithEnrollment();

        Carbon::setTestNow(Carbon::today()->setTime(7, 50, 0));

        $this->actingAs($this->auxiliar)
            ->postJson(route('scan.process'), ['qr_uuid' => $student->qr_uuid]);

        $this->assertDatabaseHas('asistencias', [
            'registrado_por' => $this->auxiliar->id,
            'tenant_id' => 'ie-scan',
        ]);

        Carbon::setTestNow();
    }

    // ─── CUALQUIER ROL PUEDE ESCANEAR ──────────────────

    public function test_any_authenticated_role_can_scan(): void
    {
        $student = $this->createStudentWithEnrollment();
        $director = User::factory()->director()->create(['tenant_id' => 'ie-scan']);
        $director->syncRoles(['Director']);

        Carbon::setTestNow(Carbon::today()->setTime(7, 50, 0));

        $response = $this->actingAs($director)
            ->postJson(route('scan.process'), ['qr_uuid' => $student->qr_uuid]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        Carbon::setTestNow();
    }

    // ─── PÁGINA DEL ESCÁNER ────────────────────────────

    public function test_scanner_page_loads_for_authenticated_user(): void
    {
        $response = $this->actingAs($this->auxiliar)
            ->get(route('root'));

        $response->assertStatus(200);
    }

    public function test_scanner_page_redirects_unauthenticated(): void
    {
        $response = $this->get(route('root'));

        $response->assertRedirect(route('login'));
    }
}
