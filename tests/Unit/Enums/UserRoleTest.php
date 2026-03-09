<?php

namespace Tests\Unit\Enums;

use App\Enums\UserRole;
use PHPUnit\Framework\TestCase;

class UserRoleTest extends TestCase
{
    public function test_all_roles_have_labels(): void
    {
        foreach (UserRole::cases() as $role) {
            $this->assertNotEmpty($role->label());
        }
    }

    public function test_all_roles_have_levels(): void
    {
        foreach (UserRole::cases() as $role) {
            $this->assertIsInt($role->level());
            $this->assertGreaterThan(0, $role->level());
        }
    }

    public function test_level_hierarchy_is_correct(): void
    {
        $this->assertGreaterThan(UserRole::Administrador->level(), UserRole::SuperAdmin->level());
        $this->assertGreaterThan(UserRole::Director->level(), UserRole::Administrador->level());
        $this->assertGreaterThan(UserRole::Docente->level(), UserRole::Director->level());
        $this->assertGreaterThan(UserRole::Auxiliar->level(), UserRole::Docente->level());
    }

    public function test_superadmin_does_not_require_tenant(): void
    {
        $this->assertFalse(UserRole::SuperAdmin->requiresTenant());
    }

    public function test_administrador_does_not_require_tenant(): void
    {
        $this->assertFalse(UserRole::Administrador->requiresTenant());
    }

    public function test_director_requires_tenant(): void
    {
        $this->assertTrue(UserRole::Director->requiresTenant());
    }

    public function test_docente_requires_tenant(): void
    {
        $this->assertTrue(UserRole::Docente->requiresTenant());
    }

    public function test_auxiliar_requires_tenant(): void
    {
        $this->assertTrue(UserRole::Auxiliar->requiresTenant());
    }

    public function test_can_manage_returns_true_for_higher_role(): void
    {
        $this->assertTrue(UserRole::SuperAdmin->canManage(UserRole::Administrador));
        $this->assertTrue(UserRole::SuperAdmin->canManage(UserRole::Director));
        $this->assertTrue(UserRole::Administrador->canManage(UserRole::Director));
        $this->assertTrue(UserRole::Director->canManage(UserRole::Docente));
        $this->assertTrue(UserRole::Docente->canManage(UserRole::Auxiliar));
    }

    public function test_can_manage_returns_false_for_same_or_lower_role(): void
    {
        $this->assertFalse(UserRole::SuperAdmin->canManage(UserRole::SuperAdmin));
        $this->assertFalse(UserRole::Auxiliar->canManage(UserRole::Docente));
        $this->assertFalse(UserRole::Director->canManage(UserRole::Administrador));
    }

    public function test_values_returns_all_role_strings(): void
    {
        $values = UserRole::values();

        $this->assertCount(5, $values);
        $this->assertContains('SuperAdmin', $values);
        $this->assertContains('Administrador', $values);
        $this->assertContains('Director', $values);
        $this->assertContains('Docente', $values);
        $this->assertContains('Auxiliar', $values);
    }

    public function test_options_returns_associative_array(): void
    {
        $options = UserRole::options();

        $this->assertCount(5, $options);
        $this->assertArrayHasKey('SuperAdmin', $options);
        $this->assertEquals('SuperAdmin', $options['SuperAdmin']);
    }
}
