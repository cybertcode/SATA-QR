<?php

namespace App\Livewire\Sata;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Tenant;
use App\Services\UserService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\On;
use Livewire\Component;

class UserManager extends Component
{
    // ─── Modal state ───
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public ?int $editingUserId = null;

    // ─── Form fields ───
    public string $name = '';
    public string $email = '';
    public string $dni = '';
    public string $role = 'Auxiliar';
    public ?string $tenant_id = null;
    public string $cargo = '';
    public string $password = '';

    // ─── Computed ───
    public array $stats = [];

    protected function rules(): array
    {
        $validRoles = implode(',', UserRole::values());

        $rules = [
            'name' => ['required', 'string', 'min:3', 'max:255', 'regex:/^[\pL\s\.]+$/u'],
            'role' => ['required', 'string', 'in:' . $validRoles],
            'dni' => ['required', 'string', 'regex:/^[0-9]{8}$/'],
            'tenant_id' => ['nullable', 'exists:tenants,id'],
            'cargo' => ['nullable', 'string', 'max:100'],
        ];

        if ($this->editingUserId) {
            $rules['dni'][] = 'unique:users,dni,' . $this->editingUserId;
            $rules['password'] = ['nullable', 'string', 'min:8', Password::defaults()];
        } else {
            $rules['email'] = ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users,email'];
            $rules['dni'][] = 'unique:users,dni';
            $rules['password'] = ['required', 'string', 'min:8', Password::defaults()];
        }

        return $rules;
    }

    protected $messages = [
        'name.required' => 'El nombre es obligatorio.',
        'name.min' => 'El nombre debe tener al menos 3 caracteres.',
        'name.regex' => 'El nombre solo debe contener letras y espacios.',
        'email.required' => 'El correo es obligatorio.',
        'email.email' => 'Ingrese un correo electrónico válido.',
        'email.unique' => 'Este correo ya está registrado.',
        'dni.required' => 'El DNI es obligatorio.',
        'dni.regex' => 'El DNI debe tener exactamente 8 dígitos numéricos.',
        'dni.unique' => 'Este DNI ya está registrado en otro usuario.',
        'role.required' => 'Debe seleccionar un rol.',
        'role.in' => 'El rol seleccionado no es válido.',
        'cargo.max' => 'El cargo no debe exceder 100 caracteres.',
        'tenant_id.exists' => 'La institución seleccionada no es válida.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
    ];

    public function mount(UserService $userService): void
    {
        $this->refreshStats($userService);
    }

    public function computeStats(): void
    {
        $this->refreshStats(app(UserService::class));
    }

    private function refreshStats(UserService $userService): void
    {
        $this->stats = $userService->getStats();
    }

    // ─── CREAR ───
    public function openCreate(): void
    {
        Gate::authorize('create', User::class);

        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function store(UserService $userService): void
    {
        Gate::authorize('create', User::class);
        $this->validate();

        $userService->create($this->getFormData());

        $this->showCreateModal = false;
        $this->resetForm();
        $this->computeStats();
        $this->dispatch('refreshDatatable');
        $this->dispatch('swal', icon: 'success', title: 'Usuario registrado exitosamente.');
    }

    // ─── EDITAR ───
    #[On('editUser')]
    public function openEdit(int $userId): void
    {
        $user = User::findOrFail($userId);
        Gate::authorize('update', $user);

        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->dni = $user->dni ?? '';
        $this->role = $user->role;
        $this->tenant_id = $user->tenant_id ? (string) $user->tenant_id : null;
        $this->cargo = $user->cargo ?? '';
        $this->password = '';
        $this->resetValidation();
        $this->showEditModal = true;
    }

    public function update(UserService $userService): void
    {
        $user = User::findOrFail($this->editingUserId);
        Gate::authorize('update', $user);
        $this->validate();

        $userService->update($user, $this->getFormData());

        $this->showEditModal = false;
        $this->resetForm();
        $this->computeStats();
        $this->dispatch('refreshDatatable');
        $this->dispatch('swal', icon: 'success', title: 'Usuario actualizado correctamente.');
    }

    // ─── TOGGLE STATUS ───
    #[On('toggleStatus')]
    public function toggleStatus(int $userId, UserService $userService): void
    {
        $user = User::findOrFail($userId);

        if (Gate::denies('toggleStatus', $user)) {
            $this->dispatch('swal', icon: 'error', title: 'No tiene permisos para cambiar el estado de este usuario.');
            return;
        }

        $userService->toggleStatus($user);

        $this->computeStats();
        $this->dispatch('refreshDatatable');
        $this->dispatch('swal', icon: 'success', title: $user->is_active ? 'Usuario activado.' : 'Usuario desactivado.');
    }

    // ─── ELIMINAR (Soft Delete) ───
    #[On('deleteUser')]
    public function destroy(int $userId, UserService $userService): void
    {
        $user = User::findOrFail($userId);

        if (Gate::denies('delete', $user)) {
            $this->dispatch('swal', icon: 'error', title: 'No tiene permisos para eliminar este usuario.');
            return;
        }

        $userService->delete($user);

        $this->computeStats();
        $this->dispatch('refreshDatatable');
        $this->dispatch('swal', icon: 'success', title: 'Usuario movido a la papelera.');
    }

    // ─── RESTAURAR ───
    #[On('restoreUser')]
    public function restore(int $userId, UserService $userService): void
    {
        $user = User::onlyTrashed()->findOrFail($userId);

        if (Gate::denies('restore', $user)) {
            $this->dispatch('swal', icon: 'error', title: 'No tiene permisos para restaurar este usuario.');
            return;
        }

        $userService->restore($user);

        $this->computeStats();
        $this->dispatch('refreshDatatable');
        $this->dispatch('swal', icon: 'success', title: 'Usuario restaurado exitosamente.');
    }

    // ─── ELIMINAR PERMANENTE ───
    #[On('forceDeleteUser')]
    public function forceDestroy(int $userId, UserService $userService): void
    {
        $user = User::onlyTrashed()->findOrFail($userId);

        if (Gate::denies('forceDelete', $user)) {
            $this->dispatch('swal', icon: 'error', title: 'No tiene permisos para eliminar permanentemente.');
            return;
        }

        $userService->forceDelete($user);

        $this->computeStats();
        $this->dispatch('refreshDatatable');
        $this->dispatch('swal', icon: 'success', title: 'Usuario eliminado permanentemente.');
    }

    // ─── HELPERS ───
    private function resetForm(): void
    {
        $this->editingUserId = null;
        $this->name = '';
        $this->email = '';
        $this->dni = '';
        $this->role = UserRole::Auxiliar->value;
        $this->tenant_id = null;
        $this->cargo = '';
        $this->password = '';
        $this->resetValidation();
    }

    private function getFormData(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'dni' => $this->dni,
            'role' => $this->role,
            'tenant_id' => $this->tenant_id,
            'cargo' => $this->cargo,
            'password' => $this->password,
        ];
    }

    public function updatedDni(): void
    {
        if (!$this->editingUserId && strlen($this->dni) === 8) {
            $this->password = $this->dni;
        }
    }

    public function updatedRole(): void
    {
        $role = UserRole::tryFrom($this->role);
        if ($role && !$role->requiresTenant()) {
            $this->tenant_id = null;
        }
    }

    public function render()
    {
        return view('livewire.sata.user-manager', [
            'tenants' => Tenant::orderBy('nombre')->get(),
            'roleOptions' => UserRole::cases(),
        ]);
    }
}
