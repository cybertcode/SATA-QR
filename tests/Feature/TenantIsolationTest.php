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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\SeedRolesAndPermissions;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase, SeedRolesAndPermissions;

    private Tenant $tenantA;
    private Tenant $tenantB;
    private TenantNivel $nivelA;
    private TenantNivel $nivelB;
    private AnioLectivo $anio;
    private User $superAdmin;
    private User $directorA;
    private User $directorB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();

        $this->tenantA = Tenant::create(['id' => 'ie-alpha', 'nombre' => 'I.E. Alpha', 'ugel' => 'TEST']);
        $this->tenantB = Tenant::create(['id' => 'ie-beta', 'nombre' => 'I.E. Beta', 'ugel' => 'TEST']);

        $this->nivelA = TenantNivel::create(['tenant_id' => 'ie-alpha', 'nivel' => 'Secundaria', 'codigo_modular' => '9999901']);
        $this->nivelB = TenantNivel::create(['tenant_id' => 'ie-beta', 'nivel' => 'Secundaria', 'codigo_modular' => '9999902']);

        ConfiguracionAsistencia::create(['tenant_id' => 'ie-alpha', 'hora_entrada_regular' => '07:45:00', 'minutos_tolerancia' => 15]);
        ConfiguracionAsistencia::create(['tenant_id' => 'ie-beta', 'hora_entrada_regular' => '08:00:00', 'minutos_tolerancia' => 10]);

        $this->anio = AnioLectivo::create([
            'nombre_anio' => 'Año Test',
            'anio' => now()->year,
            'fecha_inicio' => now()->startOfYear(),
            'fecha_fin' => now()->endOfYear(),
            'estado' => true,
        ]);

        $this->superAdmin = User::factory()->superAdmin()->create();
        $this->superAdmin->syncRoles(['SuperAdmin']);

        $this->directorA = User::factory()->director()->create(['tenant_id' => 'ie-alpha']);
        $this->directorA->syncRoles(['Director']);

        $this->directorB = User::factory()->director()->create(['tenant_id' => 'ie-beta']);
        $this->directorB->syncRoles(['Director']);
    }

    private function createStudentWithEnrollment(string $tenantId, TenantNivel $nivel): Estudiante
    {
        $student = Estudiante::withoutGlobalScopes()->create([
            'tenant_id' => $tenantId,
            'dni' => fake()->unique()->numerify('########'),
            'nombres' => fake()->firstName(),
            'apellido_paterno' => fake()->lastName(),
            'apellido_materno' => fake()->lastName(),
            'genero' => 'M',
            'qr_uuid' => Str::uuid(),
        ]);

        $seccion = Seccion::withoutGlobalScopes()->firstOrCreate([
            'tenant_id' => $tenantId,
            'tenant_nivel_id' => $nivel->id,
            'grado' => '1',
            'letra' => 'A',
        ]);

        Matricula::withoutGlobalScopes()->create([
            'tenant_id' => $tenantId,
            'estudiante_id' => $student->id,
            'seccion_id' => $seccion->id,
            'anio_lectivo_id' => $this->anio->id,
            'estado' => 'Activo',
        ]);

        return $student;
    }

    // ─── SCOPE ISOLATION ──────────────────────────────────

    public function test_director_only_sees_own_ie_students(): void
    {
        $studentA = $this->createStudentWithEnrollment('ie-alpha', $this->nivelA);
        $studentB = $this->createStudentWithEnrollment('ie-beta', $this->nivelB);

        $this->actingAs($this->directorA);

        $students = Estudiante::all();

        $this->assertCount(1, $students);
        $this->assertEquals($studentA->id, $students->first()->id);
    }

    public function test_superadmin_sees_all_students(): void
    {
        $this->createStudentWithEnrollment('ie-alpha', $this->nivelA);
        $this->createStudentWithEnrollment('ie-beta', $this->nivelB);

        $this->actingAs($this->superAdmin);

        $students = Estudiante::all();

        $this->assertCount(2, $students);
    }

    public function test_director_only_sees_own_ie_matriculas(): void
    {
        $this->createStudentWithEnrollment('ie-alpha', $this->nivelA);
        $this->createStudentWithEnrollment('ie-beta', $this->nivelB);

        $this->actingAs($this->directorA);

        $matriculas = Matricula::all();

        $this->assertCount(1, $matriculas);
        $this->assertEquals('ie-alpha', $matriculas->first()->tenant_id);
    }

    public function test_director_only_sees_own_ie_secciones(): void
    {
        $this->createStudentWithEnrollment('ie-alpha', $this->nivelA);
        $this->createStudentWithEnrollment('ie-beta', $this->nivelB);

        $this->actingAs($this->directorA);

        $secciones = Seccion::all();

        $this->assertCount(1, $secciones);
        $this->assertEquals('ie-alpha', $secciones->first()->tenant_id);
    }

    public function test_director_only_sees_own_ie_asistencias(): void
    {
        $studentA = $this->createStudentWithEnrollment('ie-alpha', $this->nivelA);
        $studentB = $this->createStudentWithEnrollment('ie-beta', $this->nivelB);

        $matriculaA = Matricula::withoutGlobalScopes()->where('estudiante_id', $studentA->id)->first();
        $matriculaB = Matricula::withoutGlobalScopes()->where('estudiante_id', $studentB->id)->first();

        Asistencia::withoutGlobalScopes()->create([
            'tenant_id' => 'ie-alpha',
            'matricula_id' => $matriculaA->id,
            'registrado_por' => $this->directorA->id,
            'fecha' => now()->toDateString(),
            'estado' => 'P',
        ]);

        Asistencia::withoutGlobalScopes()->create([
            'tenant_id' => 'ie-beta',
            'matricula_id' => $matriculaB->id,
            'registrado_por' => $this->directorB->id,
            'fecha' => now()->toDateString(),
            'estado' => 'T',
        ]);

        $this->actingAs($this->directorA);

        $asistencias = Asistencia::all();

        $this->assertCount(1, $asistencias);
        $this->assertEquals('P', $asistencias->first()->estado);
    }

    // ─── AUTOMATIC TENANT ASSIGNMENT ──────────────────────

    public function test_creating_student_auto_assigns_tenant(): void
    {
        $this->actingAs($this->directorA);

        $student = Estudiante::create([
            'dni' => '99999999',
            'nombres' => 'Auto',
            'apellido_paterno' => 'Asignado',
            'apellido_materno' => 'Test',
            'genero' => 'M',
            'qr_uuid' => Str::uuid(),
        ]);

        $this->assertEquals('ie-alpha', $student->tenant_id);
    }

    public function test_creating_student_respects_explicit_tenant(): void
    {
        $this->actingAs($this->superAdmin);

        $student = Estudiante::create([
            'tenant_id' => 'ie-beta',
            'dni' => '88888888',
            'nombres' => 'Explicit',
            'apellido_paterno' => 'Tenant',
            'apellido_materno' => 'Test',
            'genero' => 'F',
            'qr_uuid' => Str::uuid(),
        ]);

        $this->assertEquals('ie-beta', $student->tenant_id);
    }

    // ─── HTTP ROUTE ISOLATION ─────────────────────────────

    public function test_students_index_shows_only_own_ie(): void
    {
        $this->createStudentWithEnrollment('ie-alpha', $this->nivelA);
        $this->createStudentWithEnrollment('ie-alpha', $this->nivelA);
        $this->createStudentWithEnrollment('ie-beta', $this->nivelB);

        $response = $this->actingAs($this->directorA)->get(route('students.index'));

        $response->assertStatus(200);
        $response->assertViewHas('students', fn($students) => $students->total() === 2);
    }

    public function test_superadmin_students_index_shows_all(): void
    {
        $this->createStudentWithEnrollment('ie-alpha', $this->nivelA);
        $this->createStudentWithEnrollment('ie-beta', $this->nivelB);

        $response = $this->actingAs($this->superAdmin)->get(route('students.index'));

        $response->assertStatus(200);
        $response->assertViewHas('students', fn($students) => $students->total() === 2);
    }

    public function test_superadmin_can_filter_by_ie(): void
    {
        $this->createStudentWithEnrollment('ie-alpha', $this->nivelA);
        $this->createStudentWithEnrollment('ie-beta', $this->nivelB);

        $response = $this->actingAs($this->superAdmin)->get(route('students.index', ['ie' => 'ie-beta']));

        $response->assertStatus(200);
        $response->assertViewHas('students', fn($students) => $students->total() === 1);
    }

    public function test_director_cannot_see_other_ie_student_show(): void
    {
        $studentB = $this->createStudentWithEnrollment('ie-beta', $this->nivelB);

        $response = $this->actingAs($this->directorA)->get(route('students.show', $studentB->id));

        $response->assertStatus(404);
    }

    public function test_director_can_see_own_ie_student_show(): void
    {
        $studentA = $this->createStudentWithEnrollment('ie-alpha', $this->nivelA);

        $response = $this->actingAs($this->directorA)->get(route('students.show', $studentA->id));

        $response->assertStatus(200);
    }

    public function test_director_cannot_access_other_ie_student_qr(): void
    {
        $studentB = $this->createStudentWithEnrollment('ie-beta', $this->nivelB);

        $response = $this->actingAs($this->directorA)->get(route('students.qr', $studentB->id));

        $response->assertStatus(404);
    }

    public function test_search_filter_works(): void
    {
        $this->actingAs($this->superAdmin);

        $studentA = $this->createStudentWithEnrollment('ie-alpha', $this->nivelA);

        $response = $this->get(route('students.index', ['search' => $studentA->dni]));

        $response->assertStatus(200);
        $response->assertViewHas('students', fn($students) => $students->total() === 1);
    }

    // ─── WITHOUTGLOBALSCOPES BYPASS ───────────────────────

    public function test_withoutGlobalScopes_bypasses_tenant_filter(): void
    {
        $this->createStudentWithEnrollment('ie-alpha', $this->nivelA);
        $this->createStudentWithEnrollment('ie-beta', $this->nivelB);

        $this->actingAs($this->directorA);

        // Con scope: solo ve los suyos
        $this->assertCount(1, Estudiante::all());

        // Sin scope: ve todos
        $this->assertCount(2, Estudiante::withoutGlobalScopes()->get());
    }
}
