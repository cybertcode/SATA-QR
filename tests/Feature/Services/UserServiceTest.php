<?php

namespace Tests\Feature\Services;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SeedRolesAndPermissions;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase, SeedRolesAndPermissions;

    private UserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
        $this->service = new UserService();
    }

    public function test_create_user_with_valid_data(): void
    {
        $user = $this->service->create([
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'dni' => '12345678',
            'role' => 'Docente',
            'tenant_id' => null,
            'cargo' => 'Docente PIP',
            'password' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'juan@example.com',
            'role' => 'Docente',
            'dni' => '12345678',
            'is_active' => true,
        ]);
        $this->assertTrue($user->hasRole('Docente'));
    }

    public function test_create_user_nullifies_tenant_for_non_tenant_roles(): void
    {
        $user = $this->service->create([
            'name' => 'Admin Test',
            'email' => 'admin@example.com',
            'dni' => '87654321',
            'role' => 'SuperAdmin',
            'tenant_id' => 'some-tenant',
            'cargo' => null,
            'password' => 'password123',
        ]);

        $this->assertNull($user->tenant_id);
    }

    public function test_update_user(): void
    {
        $user = User::factory()->docente()->create();
        $user->syncRoles(['Docente']);

        $updated = $this->service->update($user, [
            'name' => 'Nombre Actualizado',
            'email' => $user->email,
            'dni' => $user->dni,
            'role' => 'Auxiliar',
            'tenant_id' => null,
            'cargo' => 'Auxiliar de educación',
            'password' => '',
        ]);

        $this->assertEquals('Nombre Actualizado', $updated->name);
        $this->assertEquals('Auxiliar', $updated->role);
        $this->assertTrue($updated->hasRole('Auxiliar'));
    }

    public function test_update_user_changes_password_when_provided(): void
    {
        $user = User::factory()->create();
        $user->syncRoles(['Auxiliar']);
        $oldPassword = $user->password;

        $this->service->update($user, [
            'name' => $user->name,
            'email' => $user->email,
            'dni' => $user->dni,
            'role' => 'Auxiliar',
            'tenant_id' => null,
            'cargo' => null,
            'password' => 'newpassword123',
        ]);

        $user->refresh();
        $this->assertNotEquals($oldPassword, $user->password);
    }

    public function test_toggle_status(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $this->service->toggleStatus($user);
        $this->assertFalse($user->is_active);

        $this->service->toggleStatus($user);
        $this->assertTrue($user->is_active);
    }

    public function test_soft_delete(): void
    {
        $user = User::factory()->create();

        $this->service->delete($user);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_restore(): void
    {
        $user = User::factory()->create();
        $user->delete();
        $this->assertSoftDeleted('users', ['id' => $user->id]);

        $this->service->restore($user);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'deleted_at' => null]);
    }

    public function test_force_delete(): void
    {
        $user = User::factory()->create();
        $user->delete();

        $this->service->forceDelete($user);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_bulk_toggle_activates_users(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $this->actingAs($admin);

        $users = User::factory()->count(3)->inactive()->create();
        $ids = $users->pluck('id')->toArray();

        $affected = $this->service->bulkToggle($ids, true);

        $this->assertEquals(3, $affected);
        foreach ($users as $u) {
            $u->refresh();
            $this->assertTrue($u->is_active);
        }
    }

    public function test_bulk_toggle_excludes_self(): void
    {
        $admin = User::factory()->superAdmin()->create(['is_active' => true]);
        $this->actingAs($admin);

        $affected = $this->service->bulkToggle([$admin->id], false);

        $this->assertEquals(0, $affected);
        $admin->refresh();
        $this->assertTrue($admin->is_active);
    }

    public function test_get_stats_returns_correct_counts(): void
    {
        User::factory()->count(3)->create(['is_active' => true]);
        User::factory()->count(2)->inactive()->create();
        $trashed = User::factory()->create();
        $trashed->delete();

        $stats = $this->service->getStats();

        $this->assertEquals(5, $stats['total']);
        $this->assertEquals(3, $stats['active']);
        $this->assertEquals(2, $stats['inactive']);
        $this->assertEquals(1, $stats['trashed']);
        $this->assertArrayHasKey('last_login_today', $stats);
    }
}
