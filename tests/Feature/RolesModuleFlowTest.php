<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Livewire\Sata\RoleManager;
use App\Livewire\Sata\RolesTable;
use App\Livewire\Sata\PermissionsTable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\SeedRolesAndPermissions;
use Tests\TestCase;

class RolesModuleFlowTest extends TestCase
{
    use RefreshDatabase, SeedRolesAndPermissions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
    }

    public function test_superadmin_can_see_roles_page(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->get(route('roles.index'))
            ->assertOk();
    }

    public function test_non_superadmin_cannot_access_roles_page(): void
    {
        $user = User::factory()->administrador()->create();

        $response = $this->actingAs($user)->get(route('roles.index'));
        // Spatie middleware may redirect (302) or abort (403)
        $this->assertTrue(in_array($response->getStatusCode(), [302, 403]));
    }

    public function test_create_role_dispatches_events(): void
    {
        $admin = User::factory()->superAdmin()->create();

        Livewire::actingAs($admin)
            ->test(RoleManager::class)
            ->set('roleName', 'Coordinador')
            ->set('selectedPermissions', ['students.view', 'attendance.report'])
            ->call('storeRole')
            ->assertDispatched('refreshRolesTable')
            ->assertDispatched('swal');

        $this->assertDatabaseHas('roles', ['name' => 'Coordinador']);
    }

    public function test_edit_role_updates_permissions(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $role = Role::create(['name' => 'TestEdit', 'guard_name' => 'web']);
        $role->syncPermissions(['students.view']);

        Livewire::actingAs($admin)
            ->test(RoleManager::class)
            ->call('openEditRole', $role->id)
            ->set('selectedPermissions', ['students.view', 'alerts.manage', 'attendance.scan'])
            ->call('updateRole')
            ->assertDispatched('refreshRolesTable')
            ->assertDispatched('swal');

        $role->refresh();
        $this->assertCount(3, $role->permissions);
    }

    public function test_delete_custom_role_works(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $role = Role::create(['name' => 'DeleteMe', 'guard_name' => 'web']);

        Livewire::actingAs($admin)
            ->test(RoleManager::class)
            ->call('destroyRole', $role->id)
            ->assertDispatched('swal');

        $this->assertDatabaseMissing('roles', ['name' => 'DeleteMe']);
    }

    public function test_cannot_delete_protected_role(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $role = Role::findByName('Director');

        Livewire::actingAs($admin)
            ->test(RoleManager::class)
            ->call('destroyRole', $role->id)
            ->assertDispatched('swal');

        $this->assertDatabaseHas('roles', ['name' => 'Director']);
    }

    public function test_delete_role_with_users_detaches_them(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $role = Role::create(['name' => 'Temporal', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole($role);

        $this->assertTrue($user->hasRole('Temporal'));

        Livewire::actingAs($admin)
            ->test(RoleManager::class)
            ->call('destroyRole', $role->id)
            ->assertDispatched('swal');

        $this->assertDatabaseMissing('roles', ['name' => 'Temporal']);
        $user->refresh();
        $this->assertFalse($user->hasRole('Temporal'));
    }

    public function test_create_permission(): void
    {
        $admin = User::factory()->superAdmin()->create();

        Livewire::actingAs($admin)
            ->test(RoleManager::class)
            ->set('permissionName', 'reports.export')
            ->call('storePermission')
            ->assertDispatched('refreshRolesTable')
            ->assertDispatched('swal');

        $this->assertDatabaseHas('permissions', ['name' => 'reports.export']);
    }

    public function test_delete_unused_permission(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $permission = Permission::create(['name' => 'temp.delete', 'guard_name' => 'web']);

        Livewire::actingAs($admin)
            ->test(RoleManager::class)
            ->call('destroyPermission', $permission->id)
            ->assertDispatched('swal');

        $this->assertDatabaseMissing('permissions', ['name' => 'temp.delete']);
    }

    public function test_cannot_delete_permission_in_use(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $permission = Permission::findByName('students.view');

        Livewire::actingAs($admin)
            ->test(RoleManager::class)
            ->call('destroyPermission', $permission->id)
            ->assertDispatched('swal');

        $this->assertDatabaseHas('permissions', ['name' => 'students.view']);
    }

    public function test_roles_table_renders(): void
    {
        $admin = User::factory()->superAdmin()->create();

        Livewire::actingAs($admin)
            ->test(RolesTable::class)
            ->assertOk();
    }

    public function test_permissions_table_renders(): void
    {
        $admin = User::factory()->superAdmin()->create();

        Livewire::actingAs($admin)
            ->test(PermissionsTable::class)
            ->assertOk();
    }

    public function test_stats_show_correct_counts(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $component = Livewire::actingAs($admin)
            ->test(RoleManager::class)
            ->assertSet('stats.total_roles', 5)
            ->assertSet('stats.total_permissions', 10); // 9 original + 1 roles.manage
    }
}
