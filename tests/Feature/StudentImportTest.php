<?php

namespace Tests\Feature;

use App\Models\AnioLectivo;
use App\Models\ConfiguracionAsistencia;
use App\Models\Estudiante;
use App\Models\Matricula;
use App\Models\Seccion;
use App\Models\Tenant;
use App\Models\TenantNivel;
use App\Models\User;
use App\Services\StudentImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Tests\SeedRolesAndPermissions;
use Tests\TestCase;

class StudentImportTest extends TestCase
{
    use RefreshDatabase, SeedRolesAndPermissions;

    private Tenant $tenantA;
    private Tenant $tenantB;
    private AnioLectivo $anio;
    private User $superAdmin;
    private User $directorA;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();

        $this->tenantA = Tenant::create(['id' => 'ie-alpha', 'nombre' => 'I.E. Alpha', 'ugel' => 'TEST']);
        $this->tenantB = Tenant::create(['id' => 'ie-beta', 'nombre' => 'I.E. Beta', 'ugel' => 'TEST']);

        TenantNivel::create(['tenant_id' => 'ie-alpha', 'nivel' => 'Secundaria', 'codigo_modular' => '9999901']);
        TenantNivel::create(['tenant_id' => 'ie-beta', 'nivel' => 'Secundaria', 'codigo_modular' => '9999902']);

        ConfiguracionAsistencia::create(['tenant_id' => 'ie-alpha', 'hora_entrada_regular' => '07:45:00', 'minutos_tolerancia' => 15]);
        ConfiguracionAsistencia::create(['tenant_id' => 'ie-beta', 'hora_entrada_regular' => '08:00:00', 'minutos_tolerancia' => 10]);

        $this->anio = AnioLectivo::create([
            'nombre_anio' => 'Año 2025',
            'anio' => now()->year,
            'fecha_inicio' => now()->startOfYear(),
            'fecha_fin' => now()->endOfYear(),
            'estado' => true,
        ]);

        $this->superAdmin = User::factory()->superAdmin()->create();
        $this->superAdmin->syncRoles(['SuperAdmin']);

        $this->directorA = User::factory()->director()->create(['tenant_id' => 'ie-alpha']);
        $this->directorA->syncRoles(['Director']);
    }

    // ────── StudentImportService (Unit-level in Feature context) ──────

    public function test_import_service_creates_students_and_matriculas(): void
    {
        $service = app(StudentImportService::class);
        $rows = [
            ['dni' => '12345678', 'nombres' => 'JUAN', 'paterno' => 'PEREZ', 'materno' => 'LOPEZ', 'genero' => 'M', 'grado' => '3', 'seccion' => 'A'],
            ['dni' => '87654321', 'nombres' => 'MARIA', 'paterno' => 'GARCIA', 'materno' => 'DIAZ', 'genero' => 'F', 'grado' => '3', 'seccion' => 'B'],
        ];

        $count = $service->import($rows, 'ie-alpha', $this->anio->id, 'Secundaria');

        $this->assertEquals(2, $count);
        $this->assertDatabaseCount('estudiantes', 2);
        $this->assertDatabaseCount('matriculas', 2);
        $this->assertDatabaseHas('estudiantes', ['dni' => '12345678', 'tenant_id' => 'ie-alpha', 'nombres' => 'JUAN']);
        $this->assertDatabaseHas('estudiantes', ['dni' => '87654321', 'tenant_id' => 'ie-alpha', 'genero' => 'F']);
    }

    public function test_import_service_creates_sections_automatically(): void
    {
        $service = app(StudentImportService::class);
        $rows = [
            ['dni' => '11111111', 'nombres' => 'A', 'paterno' => 'B', 'materno' => 'C', 'genero' => 'M', 'grado' => '1', 'seccion' => 'A'],
            ['dni' => '22222222', 'nombres' => 'D', 'paterno' => 'E', 'materno' => 'F', 'genero' => 'F', 'grado' => '2', 'seccion' => 'A'],
            ['dni' => '33333333', 'nombres' => 'G', 'paterno' => 'H', 'materno' => 'I', 'genero' => 'M', 'grado' => '1', 'seccion' => 'A'],
        ];

        $service->import($rows, 'ie-alpha', $this->anio->id, 'Secundaria');

        // 2 secciones: 1A y 2A (el tercer alumno va a 1A existente)
        $this->assertDatabaseCount('secciones', 2);
    }

    public function test_import_service_deduplicates_by_dni(): void
    {
        $service = app(StudentImportService::class);

        // Primera importación
        $service->import(
            [['dni' => '12345678', 'nombres' => 'JUAN', 'paterno' => 'PEREZ', 'materno' => 'LOPEZ', 'genero' => 'M', 'grado' => '3', 'seccion' => 'A']],
            'ie-alpha',
            $this->anio->id,
            'Secundaria'
        );

        // Segunda importación con mismo DNI pero nombre actualizado
        $service->import(
            [['dni' => '12345678', 'nombres' => 'JUAN CARLOS', 'paterno' => 'PEREZ', 'materno' => 'LOPEZ', 'genero' => 'M', 'grado' => '4', 'seccion' => 'A']],
            'ie-alpha',
            $this->anio->id,
            'Secundaria'
        );

        $this->assertDatabaseCount('estudiantes', 1);
        $this->assertDatabaseHas('estudiantes', ['dni' => '12345678', 'nombres' => 'JUAN CARLOS']);
    }

    public function test_import_service_assigns_qr_uuid(): void
    {
        $service = app(StudentImportService::class);
        $service->import(
            [['dni' => '12345678', 'nombres' => 'JUAN', 'paterno' => 'PEREZ', 'materno' => 'LOPEZ', 'genero' => 'M', 'grado' => '3', 'seccion' => 'A']],
            'ie-alpha',
            $this->anio->id,
            'Secundaria'
        );

        $student = Estudiante::withoutGlobalScopes()->where('dni', '12345678')->first();
        $this->assertNotNull($student->qr_uuid);
        $this->assertTrue(strlen($student->qr_uuid) === 36); // UUID format
    }

    public function test_import_service_creates_tenant_nivel_if_missing(): void
    {
        // Primaria no existe aún para ie-alpha
        $service = app(StudentImportService::class);
        $service->import(
            [['dni' => '12345678', 'nombres' => 'JUAN', 'paterno' => 'PEREZ', 'materno' => 'LOPEZ', 'genero' => 'M', 'grado' => '3', 'seccion' => 'A']],
            'ie-alpha',
            $this->anio->id,
            'Primaria'
        );

        $this->assertDatabaseHas('tenant_niveles', ['tenant_id' => 'ie-alpha', 'nivel' => 'Primaria']);
    }

    public function test_import_isolates_students_per_tenant(): void
    {
        $service = app(StudentImportService::class);

        $service->import(
            [['dni' => '11111111', 'nombres' => 'EST1', 'paterno' => 'A', 'materno' => 'B', 'genero' => 'M', 'grado' => '1', 'seccion' => 'A']],
            'ie-alpha',
            $this->anio->id,
            'Secundaria'
        );

        $service->import(
            [['dni' => '22222222', 'nombres' => 'EST2', 'paterno' => 'C', 'materno' => 'D', 'genero' => 'F', 'grado' => '1', 'seccion' => 'A']],
            'ie-beta',
            $this->anio->id,
            'Secundaria'
        );

        $this->assertDatabaseCount('estudiantes', 2);

        // Director A solo ve su estudiante
        $this->actingAs($this->directorA);
        $this->assertEquals(1, Estudiante::count());
        $this->assertEquals('EST1', Estudiante::first()->nombres);
    }

    // ────── HTTP Import Routes ──────

    public function test_director_can_access_import_page(): void
    {
        $response = $this->actingAs($this->directorA)->get(route('students.import'));

        $response->assertStatus(200);
        $response->assertSee('Carga Masiva SIAGIE');
        $response->assertSee('I.E. Alpha'); // Muestra su IE
    }

    public function test_superadmin_sees_ie_selector_on_import_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('students.import'));

        $response->assertStatus(200);
        $response->assertSee('Institución Educativa Destino');
        $response->assertSee('I.E. Alpha');
        $response->assertSee('I.E. Beta');
    }

    public function test_import_requires_file(): void
    {
        $response = $this->actingAs($this->directorA)->post(route('students.import.process'), [
            'anio_lectivo_id' => $this->anio->id,
            'nivel' => 'Secundaria',
        ]);

        $response->assertSessionHasErrors('archivo_siagie');
    }

    public function test_import_requires_nivel(): void
    {
        $fakeFile = UploadedFile::fake()->create('test.xlsx', 100, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $response = $this->actingAs($this->directorA)->post(route('students.import.process'), [
            'archivo_siagie' => $fakeFile,
            'anio_lectivo_id' => $this->anio->id,
        ]);

        $response->assertSessionHasErrors('nivel');
    }

    public function test_superadmin_import_requires_tenant_id(): void
    {
        $fakeFile = UploadedFile::fake()->create('test.xlsx', 100, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $response = $this->actingAs($this->superAdmin)->post(route('students.import.process'), [
            'archivo_siagie' => $fakeFile,
            'anio_lectivo_id' => $this->anio->id,
            'nivel' => 'Secundaria',
        ]);

        $response->assertSessionHasErrors('tenant_id');
    }

    public function test_director_import_uses_own_tenant_id(): void
    {
        // Mock Excel to return controlled data
        Excel::fake();

        $fakeFile = UploadedFile::fake()->create('test.xlsx', 100, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // The import will proceed through validation but we can't fully test Excel parsing
        // without a real file. We verified the service in unit tests above.
        // This test ensures that a Director doesn't need to provide tenant_id
        $response = $this->actingAs($this->directorA)->post(route('students.import.process'), [
            'archivo_siagie' => $fakeFile,
            'anio_lectivo_id' => $this->anio->id,
            'nivel' => 'Secundaria',
        ]);

        // Should not have tenant_id validation error (Director doesn't need it)
        $response->assertSessionDoesntHaveErrors('tenant_id');
    }

    public function test_nivel_only_accepts_primaria_or_secundaria(): void
    {
        $fakeFile = UploadedFile::fake()->create('test.xlsx', 100, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $response = $this->actingAs($this->directorA)->post(route('students.import.process'), [
            'archivo_siagie' => $fakeFile,
            'anio_lectivo_id' => $this->anio->id,
            'nivel' => 'Universitaria',
        ]);

        $response->assertSessionHasErrors('nivel');
    }
}
