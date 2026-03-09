<?php

namespace App\Livewire\Sata;

use App\Enums\UserRole;
use App\Services\RoleService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleManager extends Component
{
    // ─── Modal state ───
    public bool $showCreateRoleModal = false;
    public bool $showEditRoleModal = false;
    public bool $showCreatePermissionModal = false;
    public ?int $editingRoleId = null;

    // ─── Role form fields ───
    public string $roleName = '';
    public array $selectedPermissions = [];

    // ─── Permission form fields ───
    public string $permissionName = '';

    // ─── Stats ───
    public array $stats = [];

    protected function roleRules(): array
    {
        $rules = [
            'roleName' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[\pL\s\-\.]+$/u'],
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => ['string', 'exists:permissions,name'],
        ];

        if ($this->editingRoleId) {
            $rules['roleName'][] = 'unique:roles,name,' . $this->editingRoleId;
        } else {
            $rules['roleName'][] = 'unique:roles,name';
        }

        return $rules;
    }

    protected $messages = [
        'roleName.required' => 'El nombre del rol es obligatorio.',
        'roleName.min' => 'El nombre debe tener al menos 3 caracteres.',
        'roleName.max' => 'El nombre no debe exceder 50 caracteres.',
        'roleName.regex' => 'El nombre solo debe contener letras, espacios y guiones.',
        'roleName.unique' => 'Este nombre de rol ya existe.',
        'permissionName.required' => 'El nombre del permiso es obligatorio.',
        'permissionName.min' => 'El nombre debe tener al menos 3 caracteres.',
        'permissionName.regex' => 'El nombre debe usar formato modulo.accion (ej: users.manage).',
        'permissionName.unique' => 'Este permiso ya existe.',
    ];

    public function mount(RoleService $roleService): void
    {
        $this->refreshStats($roleService);
    }

    public function computeStats(): void
    {
        $this->refreshStats(app(RoleService::class));
    }

    private function refreshStats(RoleService $roleService): void
    {
        $this->stats = $roleService->getStats();
    }

    // ═══════════════════════════════════════════════
    // CRUD ROLES
    // ═══════════════════════════════════════════════

    public function openCreateRole(): void
    {
        Gate::authorize('create', Role::class);
        $this->resetRoleForm();
        $this->showCreateRoleModal = true;
    }

    public function storeRole(RoleService $roleService): void
    {
        Gate::authorize('create', Role::class);
        $this->validate($this->roleRules());

        $roleService->create([
            'name' => $this->roleName,
            'permissions' => $this->selectedPermissions,
        ]);

        $this->showCreateRoleModal = false;
        $this->resetRoleForm();
        $this->computeStats();
        $this->dispatch('refreshRolesTable');
        $this->dispatch('swal', icon: 'success', title: 'Rol creado exitosamente.');
    }

    #[On('editRole')]
    public function openEditRole(int $roleId): void
    {
        $role = Role::findOrFail($roleId);
        Gate::authorize('update', $role);

        $this->editingRoleId = $role->id;
        $this->roleName = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->resetValidation();
        $this->showEditRoleModal = true;
    }

    public function updateRole(RoleService $roleService): void
    {
        $role = Role::findOrFail($this->editingRoleId);
        Gate::authorize('update', $role);
        $this->validate($this->roleRules());

        $roleService->update($role, [
            'name' => $this->roleName,
            'permissions' => $this->selectedPermissions,
        ]);

        $this->showEditRoleModal = false;
        $this->resetRoleForm();
        $this->computeStats();
        $this->dispatch('refreshRolesTable');
        $this->dispatch('swal', icon: 'success', title: 'Rol actualizado correctamente.');
    }

    #[On('deleteRole')]
    public function destroyRole(int $roleId, RoleService $roleService): void
    {
        $role = Role::findOrFail($roleId);

        if (Gate::denies('delete', $role)) {
            $this->dispatch('swal', icon: 'error', title: 'No se puede eliminar este rol.');
            return;
        }

        $roleService->delete($role);

        $this->computeStats();
        $this->dispatch('refreshRolesTable');
        $this->dispatch('swal', icon: 'success', title: 'Rol eliminado exitosamente.');
    }

    // ═══════════════════════════════════════════════
    // CRUD PERMISOS
    // ═══════════════════════════════════════════════

    public function openCreatePermission(): void
    {
        Gate::authorize('managePermissions', Role::class);
        $this->permissionName = '';
        $this->resetValidation();
        $this->showCreatePermissionModal = true;
    }

    public function storePermission(RoleService $roleService): void
    {
        Gate::authorize('managePermissions', Role::class);

        $this->validate([
            'permissionName' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[a-z0-9]+(\.[a-z0-9]+)+$/', 'unique:permissions,name'],
        ]);

        $roleService->createPermission(['name' => $this->permissionName]);

        $this->showCreatePermissionModal = false;
        $this->permissionName = '';
        $this->computeStats();
        $this->dispatch('refreshRolesTable');
        $this->dispatch('swal', icon: 'success', title: 'Permiso creado exitosamente.');
    }

    #[On('deletePermission')]
    public function destroyPermission(int $permissionId, RoleService $roleService): void
    {
        Gate::authorize('managePermissions', Role::class);

        $permission = Permission::findOrFail($permissionId);

        if ($permission->roles()->count() > 0) {
            $this->dispatch('swal', icon: 'error', title: 'No se puede eliminar un permiso asignado a roles.');
            return;
        }

        $roleService->deletePermission($permission);

        $this->computeStats();
        $this->dispatch('refreshRolesTable');
        $this->dispatch('swal', icon: 'success', title: 'Permiso eliminado exitosamente.');
    }

    // ═══════════════════════════════════════════════
    // HELPERS
    // ═══════════════════════════════════════════════

    private function resetRoleForm(): void
    {
        $this->editingRoleId = null;
        $this->roleName = '';
        $this->selectedPermissions = [];
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.sata.role-manager', [
            'allPermissions' => Permission::orderBy('name')->get(),
            'protectedRoles' => UserRole::values(),
        ]);
    }
}
