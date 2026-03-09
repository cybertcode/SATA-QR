<?php

namespace Tests\Feature\Services;

use App\Enums\UserRole;
use App\Services\RoleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\SeedRolesAndPermissions;
use Tests\TestCase;

class RoleServiceTest extends TestCase
{
    use RefreshDatabase, SeedRolesAndPermissions;

    protected RoleService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
        $this->service = new RoleService();
    }

    public function test_create_role_with_permissions(): void
    {
        $role = $this->service->create([
            'name' => 'Coordinador',
            'permissions' => ['students.view', 'attendance.report'],
        ]);

        $this->assertDatabaseHas('roles', ['name' => 'Coordinador']);
        $this->assertTrue($role->hasPermissionTo('students.view'));
        $this->assertTrue($role->hasPermissionTo('attendance.report'));
        $this->assertFalse($role->hasPermissionTo('users.manage'));
    }

    public function test_create_role_without_permissions(): void
    {
        $role = $this->service->create([
            'name' => 'Invitado',
            'permissions' => [],
        ]);

        $this->assertDatabaseHas('roles', ['name' => 'Invitado']);
        $this->assertCount(0, $role->permissions);
    }

    public function test_update_role_name_and_permissions(): void
    {
        $role = $this->service->create([
            'name' => 'TestRole',
            'permissions' => ['students.view'],
        ]);

        $this->service->update($role, [
            'name' => 'TestRoleUpdated',
            'permissions' => ['students.view', 'alerts.manage'],
        ]);

        $role->refresh();
        $this->assertEquals('TestRoleUpdated', $role->name);
        $this->assertTrue($role->hasPermissionTo('alerts.manage'));
    }

    public function test_delete_role(): void
    {
        $role = $this->service->create(['name' => 'Temporal', 'permissions' => []]);

        $this->service->delete($role);

        $this->assertDatabaseMissing('roles', ['name' => 'Temporal']);
    }

    public function test_create_permission(): void
    {
        $permission = $this->service->createPermission(['name' => 'reports.export']);

        $this->assertDatabaseHas('permissions', ['name' => 'reports.export']);
        $this->assertEquals('web', $permission->guard_name);
    }

    public function test_delete_permission(): void
    {
        $permission = $this->service->createPermission(['name' => 'temp.permission']);

        $this->service->deletePermission($permission);

        $this->assertDatabaseMissing('permissions', ['name' => 'temp.permission']);
    }

    public function test_get_stats_returns_correct_counts(): void
    {
        $stats = $this->service->getStats();

        $this->assertArrayHasKey('total_roles', $stats);
        $this->assertArrayHasKey('total_permissions', $stats);
        $this->assertArrayHasKey('roles_with_users', $stats);
        $this->assertEquals(5, $stats['total_roles']); // 5 system roles
        $this->assertGreaterThanOrEqual(9, $stats['total_permissions']); // 9+ permissions
    }
}
