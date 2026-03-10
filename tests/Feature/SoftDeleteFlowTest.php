<?php

namespace Tests\Feature;

use App\Models\User;
use App\Livewire\Sata\UserManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\SeedRolesAndPermissions;
use Tests\TestCase;

class SoftDeleteFlowTest extends TestCase
{
    use RefreshDatabase, SeedRolesAndPermissions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPermissions();
    }

    private function createSuperAdmin(): User
    {
        $admin = User::factory()->superAdmin()->create();
        $admin->syncRoles(['SuperAdmin']);
        return $admin;
    }

    public function test_soft_delete_moves_user_to_trash(): void
    {
        $admin = $this->createSuperAdmin();
        $target = User::factory()->docente()->create();
        $target->syncRoles(['Docente']);

        $this->actingAs($admin);

        Livewire::test(UserManager::class)
            ->call('destroy', $target->id);

        $this->assertSoftDeleted('users', ['id' => $target->id]);
    }

    public function test_restore_brings_user_back(): void
    {
        $admin = $this->createSuperAdmin();
        $target = User::factory()->docente()->create();
        $target->syncRoles(['Docente']);
        $target->delete();

        $this->actingAs($admin);

        Livewire::test(UserManager::class)
            ->call('restore', $target->id);

        $target->refresh();
        $this->assertNull($target->deleted_at);
    }

    public function test_force_delete_removes_user_permanently(): void
    {
        $admin = $this->createSuperAdmin();
        $target = User::factory()->docente()->create();
        $target->syncRoles(['Docente']);
        $target->delete();

        $this->actingAs($admin);

        Livewire::test(UserManager::class)
            ->call('forceDestroy', $target->id);

        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_non_superadmin_cannot_force_delete(): void
    {
        $admin = User::factory()->administrador()->create();
        $admin->syncRoles(['Administrador']);

        $target = User::factory()->docente()->create();
        $target->syncRoles(['Docente']);
        $target->delete();

        $this->actingAs($admin);

        Livewire::test(UserManager::class)
            ->call('forceDestroy', $target->id)
            ->assertDispatched('swal', icon: 'error');
    }

    public function test_stats_include_trashed_count(): void
    {
        $admin = $this->createSuperAdmin();

        User::factory()->count(2)->create(['is_active' => true]);
        $trashed = User::factory()->create();
        $trashed->delete();

        $this->actingAs($admin);

        $component = Livewire::test(UserManager::class);

        // Stats should include our admin + 2 active users = 3 total, 1 trashed
        $stats = $component->get('stats');

        $this->assertEquals(3, $stats['total']); // admin + 2 created (not trashed)
        $this->assertEquals(1, $stats['trashed']);
    }

    public function test_cannot_delete_self(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin);

        Livewire::test(UserManager::class)
            ->call('destroy', $admin->id)
            ->assertDispatched('swal', icon: 'error');
    }

    public function test_create_user_dispatches_events(): void
    {
        $admin = $this->createSuperAdmin();
        $this->actingAs($admin);

        Livewire::test(UserManager::class)
            ->set('name', 'Nuevo Usuario Test')
            ->set('email', 'nuevousuario@gmail.com')
            ->set('dni', '99887766')
            ->set('role', 'Auxiliar')
            ->set('password', 'password123')
            ->call('store')
            ->assertHasNoErrors()
            ->assertDispatched('swal', icon: 'success')
            ->assertDispatched('refreshDatatable');

        $this->assertDatabaseHas('users', ['email' => 'nuevousuario@gmail.com']);
    }

    public function test_toggle_status_works(): void
    {
        $admin = $this->createSuperAdmin();
        $target = User::factory()->create(['is_active' => true]);

        $this->actingAs($admin);

        Livewire::test(UserManager::class)
            ->call('toggleStatus', $target->id);

        $target->refresh();
        $this->assertFalse($target->is_active);
    }
}
