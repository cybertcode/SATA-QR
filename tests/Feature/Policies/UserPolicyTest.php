<?php

namespace Tests\Feature\Policies;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SeedRolesAndPermissions;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase, SeedRolesAndPermissions;

    private UserPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
        $this->policy = new UserPolicy();
    }

    // ─── viewAny ───

    public function test_superadmin_can_view_any(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $admin->syncRoles(['SuperAdmin']);

        $this->assertTrue($this->policy->viewAny($admin));
    }

    public function test_administrador_can_view_any(): void
    {
        $admin = User::factory()->administrador()->create();
        $admin->syncRoles(['Administrador']);

        $this->assertTrue($this->policy->viewAny($admin));
    }

    public function test_director_cannot_view_any(): void
    {
        $director = User::factory()->director()->create();
        $director->syncRoles(['Director']);

        $this->assertFalse($this->policy->viewAny($director));
    }

    // ─── create ───

    public function test_user_with_manage_permission_can_create(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $admin->syncRoles(['SuperAdmin']);

        $this->assertTrue($this->policy->create($admin));
    }

    public function test_user_without_manage_permission_cannot_create(): void
    {
        $auxiliar = User::factory()->create(['role' => 'Auxiliar']);
        $auxiliar->syncRoles(['Auxiliar']);

        $this->assertFalse($this->policy->create($auxiliar));
    }

    // ─── update ───

    public function test_superadmin_can_update_lower_role(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $target = User::factory()->director()->create();

        $this->assertTrue($this->policy->update($admin, $target));
    }

    public function test_cannot_update_self(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $this->assertFalse($this->policy->update($admin, $admin));
    }

    public function test_lower_role_cannot_update_higher(): void
    {
        $docente = User::factory()->docente()->create();
        $director = User::factory()->director()->create();

        $this->assertFalse($this->policy->update($docente, $director));
    }

    public function test_same_role_cannot_update_each_other(): void
    {
        $director1 = User::factory()->director()->create();
        $director2 = User::factory()->director()->create();

        $this->assertFalse($this->policy->update($director1, $director2));
    }

    // ─── delete ───

    public function test_superadmin_can_delete_lower_role(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $target = User::factory()->director()->create();

        $this->assertTrue($this->policy->delete($admin, $target));
    }

    public function test_cannot_delete_self(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $this->assertFalse($this->policy->delete($admin, $admin));
    }

    // ─── toggleStatus ───

    public function test_superadmin_can_toggle_status_of_lower(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $target = User::factory()->docente()->create();

        $this->assertTrue($this->policy->toggleStatus($admin, $target));
    }

    public function test_cannot_toggle_own_status(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $this->assertFalse($this->policy->toggleStatus($admin, $admin));
    }

    // ─── restore ───

    public function test_superadmin_can_restore(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $target = User::factory()->director()->create();
        $target->delete();

        $this->assertTrue($this->policy->restore($admin, $target));
    }

    public function test_lower_role_cannot_restore_higher(): void
    {
        $docente = User::factory()->docente()->create();
        $admin = User::factory()->superAdmin()->create();
        $admin->delete();

        $this->assertFalse($this->policy->restore($docente, $admin));
    }

    // ─── forceDelete ───

    public function test_superadmin_can_force_delete(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $target = User::factory()->director()->create();
        $target->delete();

        $this->assertTrue($this->policy->forceDelete($admin, $target));
    }

    public function test_non_superadmin_cannot_force_delete(): void
    {
        $admin = User::factory()->administrador()->create();
        $target = User::factory()->docente()->create();
        $target->delete();

        $this->assertFalse($this->policy->forceDelete($admin, $target));
    }

    public function test_superadmin_cannot_force_delete_self(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $this->assertFalse($this->policy->forceDelete($admin, $admin));
    }
}
