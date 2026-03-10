<div>
    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- BARRA DE CARGA GLOBAL                                  --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="fixed top-0 inset-x-0 z-[99] pointer-events-none" wire:loading.delay.longer>
        <div class="h-0.5 w-full bg-primary/30 overflow-hidden">
            <div class="h-full bg-primary w-1/3" style="animation: lw-loading-bar 1.5s ease-in-out infinite"></div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- TARJETAS DE ESTADÍSTICAS                               --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="grid md:grid-cols-3 grid-cols-1 gap-5 mb-5">
        <div class="card">
            <div class="card-body flex items-center gap-3">
                <div class="size-12 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-6 text-primary">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10" />
                    </svg>
                </div>
                <div>
                    <p class="text-default-500 text-xs uppercase tracking-wide">Roles</p>
                    <h4 class="text-xl font-bold text-default-800">{{ $stats['total_roles'] }}</h4>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body flex items-center gap-3">
                <div class="size-12 rounded-lg bg-success/10 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-6 text-success">
                        <path
                            d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                </div>
                <div>
                    <p class="text-default-500 text-xs uppercase tracking-wide">Permisos</p>
                    <h4 class="text-xl font-bold text-success">{{ $stats['total_permissions'] }}</h4>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body flex items-center gap-3">
                <div class="size-12 rounded-lg bg-info/10 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-6 text-info">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </div>
                <div>
                    <p class="text-default-500 text-xs uppercase tracking-wide">Roles con Usuarios</p>
                    <h4 class="text-xl font-bold text-info">{{ $stats['roles_with_users'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- TABLA DE ROLES                                         --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="card mb-5">
        <div class="card-header flex-wrap gap-3">
            <h6 class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="size-4 me-1 inline-block align-text-bottom">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10" />
                </svg>
                Roles del Sistema
            </h6>
            <div class="flex items-center gap-2 ms-auto">
                <button class="btn btn-sm bg-primary text-white inline-flex items-center gap-1" type="button"
                    wire:click="openCreateRole" wire:loading.attr="disabled" wire:target="openCreateRole">
                    <span wire:loading.remove wire:target="openCreateRole">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                            <path d="M12 5v14" />
                            <path d="M5 12h14" />
                        </svg>
                    </span>
                    <span wire:loading wire:target="openCreateRole">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 animate-spin">
                            <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                        </svg>
                    </span>
                    Nuevo Rol
                </button>
            </div>
        </div>
        <div class="card-body">
            <livewire:sata.roles-table />
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- TABLA DE PERMISOS                                      --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="card">
        <div class="card-header flex-wrap gap-3">
            <h6 class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="size-4 me-1 inline-block align-text-bottom">
                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
                Permisos del Sistema
            </h6>
            <div class="flex items-center gap-2 ms-auto">
                <button class="btn btn-sm bg-success text-white inline-flex items-center gap-1" type="button"
                    wire:click="openCreatePermission" wire:loading.attr="disabled"
                    wire:target="openCreatePermission">
                    <span wire:loading.remove wire:target="openCreatePermission">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="size-4">
                            <path d="M12 5v14" />
                            <path d="M5 12h14" />
                        </svg>
                    </span>
                    <span wire:loading wire:target="openCreatePermission">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="size-4 animate-spin">
                            <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                        </svg>
                    </span>
                    Nuevo Permiso
                </button>
            </div>
        </div>
        <div class="card-body">
            <livewire:sata.permissions-table />
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- MODAL: Crear Rol                                       --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div x-data="{ show: @entangle('showCreateRoleModal') }">
        <div x-show="show" x-cloak class="fixed inset-0 z-80 overflow-y-auto" role="dialog" aria-modal="true"
            aria-labelledby="modal-create-role-title" x-on:keydown.escape.window="if(show) show = false">

            <div class="fixed inset-0 bg-black/50" x-show="show"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-on:click="show = false">
            </div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="card relative w-full max-w-lg shadow-xl border border-default-200 rounded-xl"
                    x-show="show" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4" x-on:click.stop>

                    <div class="card-header">
                        <h3 id="modal-create-role-title" class="font-semibold text-base text-default-800">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="size-4 me-1 align-text-bottom inline-block">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10" />
                            </svg>
                            Crear Nuevo Rol
                        </h3>
                        <button type="button" class="size-5 text-default-800 hover:text-danger transition-colors"
                            x-on:click="show = false">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="size-5">
                                <path d="M18 6 6 18" />
                                <path d="m6 6 12 12" />
                            </svg>
                        </button>
                    </div>

                    <form wire:submit="storeRole">
                        <div class="card-body space-y-4">
                            {{-- Nombre del Rol --}}
                            <div>
                                <label class="inline-block mb-2 text-sm text-default-800 font-medium">
                                    Nombre del Rol <span class="text-danger">*</span>
                                </label>
                                <input type="text" wire:model="roleName"
                                    class="form-input @error('roleName') border-danger @enderror"
                                    placeholder="Ej: Coordinador" autofocus>
                                @error('roleName')
                                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Permisos --}}
                            <div>
                                <label class="inline-block mb-2 text-sm text-default-800 font-medium">Permisos</label>
                                <div
                                    class="max-h-48 overflow-y-auto border border-default-200 rounded-md p-3 space-y-2">
                                    @foreach ($allPermissions as $permission)
                                        <label
                                            class="flex items-center gap-2 cursor-pointer hover:bg-default-50 px-2 py-1 rounded">
                                            <input type="checkbox" wire:model="selectedPermissions"
                                                value="{{ $permission->name }}"
                                                class="size-4 rounded border-default-300 text-primary focus:ring-primary">
                                            <span class="text-sm font-mono">{{ $permission->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @if ($allPermissions->isEmpty())
                                    <p class="text-default-400 text-xs mt-1">No hay permisos registrados aún.</p>
                                @endif
                            </div>
                        </div>

                        <div class="card-footer flex items-center justify-end gap-2">
                            <button type="button" class="btn bg-default-100 text-default-700"
                                x-on:click="show = false">
                                Cancelar
                            </button>
                            <button type="submit" class="btn bg-primary text-white inline-flex items-center gap-1"
                                wire:loading.attr="disabled" wire:target="storeRole">
                                <span wire:loading wire:target="storeRole">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="size-4 animate-spin">
                                        <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                                    </svg>
                                </span>
                                Crear Rol
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- MODAL: Editar Rol                                      --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div x-data="{ show: @entangle('showEditRoleModal') }">
        <div x-show="show" x-cloak class="fixed inset-0 z-80 overflow-y-auto" role="dialog" aria-modal="true"
            aria-labelledby="modal-edit-role-title" x-on:keydown.escape.window="if(show) show = false">

            <div class="fixed inset-0 bg-black/50" x-show="show"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-on:click="show = false">
            </div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="card relative w-full max-w-lg shadow-xl border border-default-200 rounded-xl"
                    x-show="show" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4" x-on:click.stop>

                    <div class="card-header">
                        <h3 id="modal-edit-role-title" class="font-semibold text-base text-default-800">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="size-4 me-1 align-text-bottom inline-block">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10" />
                            </svg>
                            Editar Rol
                        </h3>
                        <button type="button" class="size-5 text-default-800 hover:text-danger transition-colors"
                            x-on:click="show = false">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="size-5">
                                <path d="M18 6 6 18" />
                                <path d="m6 6 12 12" />
                            </svg>
                        </button>
                    </div>

                    <form wire:submit="updateRole">
                        <div class="card-body space-y-4">
                            {{-- Nombre del Rol --}}
                            <div>
                                <label class="inline-block mb-2 text-sm text-default-800 font-medium">
                                    Nombre del Rol <span class="text-danger">*</span>
                                </label>
                                <input type="text" wire:model="roleName"
                                    class="form-input @error('roleName') border-danger @enderror"
                                    placeholder="Ej: Coordinador">
                                @error('roleName')
                                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Permisos --}}
                            <div>
                                <label class="inline-block mb-2 text-sm text-default-800 font-medium">Permisos</label>
                                <div
                                    class="max-h-48 overflow-y-auto border border-default-200 rounded-md p-3 space-y-2">
                                    @foreach ($allPermissions as $permission)
                                        <label
                                            class="flex items-center gap-2 cursor-pointer hover:bg-default-50 px-2 py-1 rounded">
                                            <input type="checkbox" wire:model="selectedPermissions"
                                                value="{{ $permission->name }}"
                                                class="size-4 rounded border-default-300 text-primary focus:ring-primary">
                                            <span class="text-sm font-mono">{{ $permission->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="card-footer flex items-center justify-end gap-2">
                            <button type="button" class="btn bg-default-100 text-default-700"
                                x-on:click="show = false">
                                Cancelar
                            </button>
                            <button type="submit" class="btn bg-primary text-white inline-flex items-center gap-1"
                                wire:loading.attr="disabled" wire:target="updateRole">
                                <span wire:loading wire:target="updateRole">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="size-4 animate-spin">
                                        <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                                    </svg>
                                </span>
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- MODAL: Crear Permiso                                   --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div x-data="{ show: @entangle('showCreatePermissionModal') }">
        <div x-show="show" x-cloak class="fixed inset-0 z-80 overflow-y-auto" role="dialog" aria-modal="true"
            aria-labelledby="modal-create-permission-title" x-on:keydown.escape.window="if(show) show = false">

            <div class="fixed inset-0 bg-black/50" x-show="show"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-on:click="show = false">
            </div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="card relative w-full max-w-sm shadow-xl border border-default-200 rounded-xl"
                    x-show="show" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4" x-on:click.stop>

                    <form wire:submit="storePermission">
                        <div class="card-body space-y-3 pb-4">
                            <div class="flex items-center justify-between">
                                <h3 id="modal-create-permission-title" class="font-semibold text-sm text-default-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="size-4 me-1 align-text-bottom inline-block">
                                        <rect width="18" height="11" x="3" y="11" rx="2"
                                            ry="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>
                                    Nuevo Permiso
                                </h3>
                                <button type="button"
                                    class="size-5 text-default-400 hover:text-danger transition-colors"
                                    x-on:click="show = false">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="size-4">
                                        <path d="M18 6 6 18" />
                                        <path d="m6 6 12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div>
                                <div class="flex gap-2">
                                    <input type="text" wire:model="permissionName"
                                        class="form-input font-mono text-sm @error('permissionName') border-danger @enderror"
                                        placeholder="modulo.accion" autofocus>
                                    <button type="submit"
                                        class="btn btn-sm bg-success text-white shrink-0 inline-flex items-center gap-1"
                                        wire:loading.attr="disabled" wire:target="storePermission">
                                        <span wire:loading wire:target="storePermission">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="size-4 animate-spin">
                                                <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                                            </svg>
                                        </span>
                                        Crear
                                    </button>
                                </div>
                                @error('permissionName')
                                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-default-400 text-[11px] mt-1">
                                    Formato: <span class="font-mono">modulo.accion</span> — solo minúsculas, números y
                                    puntos.
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
