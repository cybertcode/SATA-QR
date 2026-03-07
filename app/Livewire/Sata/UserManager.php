<?php

namespace App\Livewire\Sata;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
    public string $password = 'Sata2026*';

    // ─── Computed ───
    public array $stats = [];

    protected function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'min:3', 'max:255', 'regex:/^[\pL\s\.]+$/u'],
            'role' => ['required', 'string', 'in:SuperAdmin,Administrador,Director,Docente,Auxiliar'],
            'dni' => ['nullable', 'string', 'regex:/^[0-9]{8}$/'],
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
        'dni.regex' => 'El DNI debe tener exactamente 8 dígitos numéricos.',
        'dni.unique' => 'Este DNI ya está registrado en otro usuario.',
        'role.required' => 'Debe seleccionar un rol.',
        'role.in' => 'El rol seleccionado no es válido.',
        'cargo.max' => 'El cargo no debe exceder 100 caracteres.',
        'tenant_id.exists' => 'La institución seleccionada no es válida.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
    ];

    public function mount(): void
    {
        $this->computeStats();
    }

    public function computeStats(): void
    {
        $this->stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'roles_count' => User::distinct('role')->count('role'),
        ];
    }

    // ─── CREAR ───
    public function openCreate(): void
    {
        $this->resetForm();
        $this->password = 'Sata2026*';
        $this->showCreateModal = true;
    }

    public function store(): void
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'dni' => $this->dni ?: null,
            'role' => $this->role,
            'tenant_id' => in_array($this->role, ['SuperAdmin', 'Administrador']) ? null : $this->tenant_id,
            'cargo' => $this->cargo ?: null,
            'password' => Hash::make($this->password),
            'is_active' => true,
        ]);

        $user->syncRoles([$this->role]);

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

    public function update(): void
    {
        $this->validate();

        $user = User::findOrFail($this->editingUserId);
        $user->name = $this->name;
        $user->dni = $this->dni ?: null;
        $user->role = $this->role;
        $user->tenant_id = in_array($this->role, ['SuperAdmin', 'Administrador']) ? null : $this->tenant_id;
        $user->cargo = $this->cargo ?: null;

        if ($this->password) {
            $user->password = Hash::make($this->password);
        }

        $user->save();
        $user->syncRoles([$this->role]);

        $this->showEditModal = false;
        $this->resetForm();
        $this->computeStats();
        $this->dispatch('refreshDatatable');
        $this->dispatch('swal', icon: 'success', title: 'Usuario actualizado correctamente.');
    }

    // ─── TOGGLE STATUS ───
    #[On('toggleStatus')]
    public function toggleStatus(int $userId): void
    {
        if ($userId === Auth::id()) {
            $this->dispatch('swal', icon: 'error', title: 'No puede desactivar su propia cuenta.');
            return;
        }

        $user = User::findOrFail($userId);
        $user->is_active = !$user->is_active;
        $user->save();

        $this->computeStats();
        $this->dispatch('refreshDatatable');
        $this->dispatch('swal', icon: 'success', title: $user->is_active ? 'Usuario activado.' : 'Usuario desactivado.');
    }

    // ─── ELIMINAR ───
    #[On('deleteUser')]
    public function destroy(int $userId): void
    {
        if ($userId === Auth::id()) {
            $this->dispatch('swal', icon: 'error', title: 'No puede eliminar su propia cuenta.');
            return;
        }

        User::findOrFail($userId)->delete();

        $this->computeStats();
        $this->dispatch('refreshDatatable');
        $this->dispatch('swal', icon: 'success', title: 'Usuario eliminado del sistema.');
    }

    // ─── HELPERS ───
    private function resetForm(): void
    {
        $this->editingUserId = null;
        $this->name = '';
        $this->email = '';
        $this->dni = '';
        $this->role = 'Auxiliar';
        $this->tenant_id = null;
        $this->cargo = '';
        $this->password = '';
        $this->resetValidation();
    }

    public function updatedRole(): void
    {
        if (in_array($this->role, ['SuperAdmin', 'Administrador'])) {
            $this->tenant_id = null;
        }
    }

    public function render()
    {
        return view('livewire.sata.user-manager', [
            'tenants' => Tenant::orderBy('nombre')->get(),
        ]);
    }
}
