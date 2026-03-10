<?php

namespace Tests\Feature\Policies;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Tests\SeedRolesAndPermissions;
use Tests\TestCase;

class RolePolicyTest extends TestCase
{
    use RefreshDatabase, SeedRolesAndPermissions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
    }

    // ─── viewAny ───

    public function test_superadmin_can_view_any_roles(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $this->assertTrue(Gate::forUser($admin)->allows('viewAny', Role::class));
    }

    public function test_non_superadmin_cannot_view_any_roles(): void
    {
        $director = User::factory()->director()->create(['tenant_id' => null]);
        $this->assertTrue(Gate::forUser($director)->denies('viewAny', Role::class));
    }

    // ─── create ───

    public function test_superadmin_can_create_role(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $this->assertTrue(Gate::forUser($admin)->allows('create', Role::class));
    }

    public function test_administrador_cannot_create_role(): void
    {
        $user = User::factory()->administrador()->create();
        $this->assertTrue(Gate::forUser($user)->denies('create', Role::class));
    }

    // ─── update ───

    public function test_superadmin_can_update_non_superadmin_role(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $role = Role::findByName('Director');
        $this->assertTrue(Gate::forUser($admin)->allows('update', $role));
    }

    public function test_superadmin_cannot_update_superadmin_role(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $role = Role::findByName('SuperAdmin');
        $this->assertTrue(Gate::forUser($admin)->denies('update', $role));
    }

    // ─── delete ───

    public function test_superadmin_can_delete_custom_role(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $customRole = Role::create(['name' => 'CustomTest', 'guard_name' => 'web']);
        $this->assertTrue(Gate::forUser($admin)->allows('delete', $customRole));
    }

    public function test_superadmin_cannot_delete_protected_role(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $role = Role::findByName('Docente');
        $this->assertTrue(Gate::forUser($admin)->denies('delete', $role));
    }

    public function test_non_superadmin_cannot_delete_any_role(): void
    {
        $user = User::factory()->administrador()->create();
        $customRole = Role::create(['name' => 'CustomTest2', 'guard_name' => 'web']);
        $this->assertTrue(Gate::forUser($user)->denies('delete', $customRole));
    }

    // ─── managePermissions ───

    public function test_superadmin_can_manage_permissions(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $this->assertTrue(Gate::forUser($admin)->allows('managePermissions', Role::class));
    }

    public function test_non_superadmin_cannot_manage_permissions(): void
    {
        $user = User::factory()->director()->create(['tenant_id' => null]);
        $this->assertTrue(Gate::forUser($user)->denies('managePermissions', Role::class));
    }
}
