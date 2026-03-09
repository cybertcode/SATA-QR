<?php

namespace Tests\Feature\Policies;

use App\Models\ConfiguracionGeneral;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\SeedRolesAndPermissions;
use Tests\TestCase;

class ConfiguracionGeneralPolicyTest extends TestCase
{
    use RefreshDatabase, SeedRolesAndPermissions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
    }

    public function test_superadmin_can_view_any(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin);
        $this->assertTrue(Gate::allows('viewAny', ConfiguracionGeneral::class));
    }

    public function test_administrador_cannot_view_any(): void
    {
        $user = User::factory()->administrador()->create();

        $this->actingAs($user);
        $this->assertFalse(Gate::allows('viewAny', ConfiguracionGeneral::class));
    }

    public function test_director_cannot_view_any(): void
    {
        $user = User::factory()->director()->create();

        $this->actingAs($user);
        $this->assertFalse(Gate::allows('viewAny', ConfiguracionGeneral::class));
    }

    public function test_superadmin_can_update(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin);
        $this->assertTrue(Gate::allows('update', ConfiguracionGeneral::class));
    }

    public function test_non_superadmin_cannot_update(): void
    {
        $user = User::factory()->administrador()->create();

        $this->actingAs($user);
        $this->assertFalse(Gate::allows('update', ConfiguracionGeneral::class));
    }
}
