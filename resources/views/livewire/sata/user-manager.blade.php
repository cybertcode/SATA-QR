<div x-data="{
    showCreate: @entangle('showCreateModal'),
    showEdit: @entangle('showEditModal')
}">
    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- BARRA DE CARGA GLOBAL                                  --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="fixed top-0 inset-x-0 z-[99] pointer-events-none" wire:loading.delay.longer>
        <div class="h-0.5 w-full bg-primary/30 overflow-hidden">
            <div class="h-full bg-primary w-1/3" style="animation: lw-loading-bar 1.5s ease-in-out infinite"></div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- INDICADOR SIN CONEXIÓN                                 --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div wire:offline
        class="fixed bottom-4 left-1/2 -translate-x-1/2 z-[99] bg-warning text-white px-4 py-2 rounded-lg shadow-lg text-sm font-medium flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" class="size-4">
            <path d="M12 20h.01" />
            <path d="M8.5 16.429a5 5 0 0 1 7 0" />
            <path d="M5 12.859a10 10 0 0 1 5.17-2.69" />
            <path d="M13.83 10.17A10 10 0 0 1 19 12.86" />
            <path d="M2 8.82a15 15 0 0 1 4.17-2.65" />
            <path d="M10.66 5c4.01-.36 8.14.9 11.34 3.76" />
            <line x1="2" x2="22" y1="2" y2="22" />
        </svg>
        Sin conexión a internet
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- TARJETAS DE ESTADÍSTICAS                               --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="grid xl:grid-cols-5 md:grid-cols-3 grid-cols-2 gap-5 mb-5">
        <div class="card cursor-pointer hover:shadow-md transition-shadow" wire:loading.class="animate-pulse"
            wire:target="store, update, toggleStatus, destroy"
            x-on:click="Livewire.dispatch('setStatusFilter', { status: '' })">
            <div class="card-body flex items-center gap-3">
                <div class="size-12 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-6 text-primary">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </div>
                <div>
                    <p class="text-default-500 text-xs uppercase tracking-wide">Total</p>
                    <h4 class="text-xl font-bold text-default-800">{{ $stats['total'] }}</h4>
                </div>
            </div>
        </div>
        <div class="card cursor-pointer hover:shadow-md transition-shadow" wire:loading.class="animate-pulse"
            wire:target="store, update, toggleStatus, destroy"
            x-on:click="Livewire.dispatch('setStatusFilter', { status: 'active' })">
            <div class="card-body flex items-center gap-3">
                <div class="size-12 rounded-lg bg-success/10 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-6 text-success">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <polyline points="16 11 18 13 22 9" />
                    </svg>
                </div>
                <div>
                    <p class="text-default-500 text-xs uppercase tracking-wide">Activos</p>
                    <h4 class="text-xl font-bold text-success">{{ $stats['active'] }}</h4>
                </div>
            </div>
        </div>
        <div class="card cursor-pointer hover:shadow-md transition-shadow" wire:loading.class="animate-pulse"
            wire:target="store, update, toggleStatus, destroy"
            x-on:click="Livewire.dispatch('setStatusFilter', { status: 'inactive' })">
            <div class="card-body flex items-center gap-3">
                <div class="size-12 rounded-lg bg-danger/10 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-6 text-danger">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <line x1="17" x2="22" y1="8" y2="13" />
                        <line x1="22" x2="17" y1="8" y2="13" />
                    </svg>
                </div>
                <div>
                    <p class="text-default-500 text-xs uppercase tracking-wide">Inactivos</p>
                    <h4 class="text-xl font-bold text-danger">{{ $stats['inactive'] }}</h4>
                </div>
            </div>
        </div>
        <div class="card" wire:loading.class="animate-pulse" wire:target="store, update, toggleStatus, destroy">
            <div class="card-body flex items-center gap-3">
                <div class="size-12 rounded-lg bg-info/10 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-6 text-info">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                        <polyline points="10 17 15 12 10 7" />
                        <line x1="15" x2="3" y1="12" y2="12" />
                    </svg>
                </div>
                <div>
                    <p class="text-default-500 text-xs uppercase tracking-wide">Accesos Hoy</p>
                    <h4 class="text-xl font-bold text-info">{{ $stats['last_login_today'] }}</h4>
                </div>
            </div>
        </div>
        <div class="card cursor-pointer hover:shadow-md transition-shadow" wire:loading.class="animate-pulse"
            wire:target="store, update, toggleStatus, destroy"
            x-on:click="Livewire.dispatch('setTrashedFilter', { filter: 'trashed' })">
            <div class="card-body flex items-center gap-3">
                <div class="size-12 rounded-lg bg-warning/10 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-6 text-warning">
                        <path d="M3 6h18" />
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                    </svg>
                </div>
                <div>
                    <p class="text-default-500 text-xs uppercase tracking-wide">Papelera</p>
                    <h4 class="text-xl font-bold text-warning">{{ $stats['trashed'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- TABLA + HEADER                                         --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="card">
        <div class="card-header flex-wrap gap-3">
            <h6 class="card-title">Directorio de Personal</h6>
            <div class="flex items-center gap-2 ms-auto flex-wrap">
                {{-- Filtro papelera --}}
                <div x-data="{ filter: '' }" class="inline-flex rounded-md shadow-sm" role="group">
                    <button type="button"
                        class="btn btn-sm border border-default-200 rounded-s-md rounded-e-none transition-colors"
                        :class="filter === '' ? 'bg-primary text-white' :
                            'bg-white text-default-600 hover:bg-default-50'"
                        x-on:click="filter = ''; Livewire.dispatch('setTrashedFilter', { filter: '' })">
                        Activos
                    </button>
                    <button type="button"
                        class="btn btn-sm border-y border-default-200 rounded-none transition-colors"
                        :class="filter === 'trashed' ? 'bg-warning text-white' :
                            'bg-white text-default-600 hover:bg-default-50'"
                        x-on:click="filter = 'trashed'; Livewire.dispatch('setTrashedFilter', { filter: 'trashed' })">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="size-3.5 me-1">
                            <path d="M3 6h18" />
                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                        </svg>
                        Papelera
                    </button>
                    <button type="button"
                        class="btn btn-sm border border-default-200 rounded-e-md rounded-s-none transition-colors"
                        :class="filter === 'all' ? 'bg-default-700 text-white' :
                            'bg-white text-default-600 hover:bg-default-50'"
                        x-on:click="filter = 'all'; Livewire.dispatch('setTrashedFilter', { filter: 'all' })">
                        Todos
                    </button>
                </div>
                {{-- Nuevo usuario --}}
                <button class="btn btn-sm bg-primary text-white inline-flex items-center gap-1" type="button"
                    x-on:click="showCreate = true; $wire.openCreate()" wire:loading.attr="disabled"
                    wire:target="openCreate">
                    <span wire:loading.remove wire:target="openCreate">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="size-4">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <line x1="19" x2="19" y1="8" y2="14" />
                            <line x1="22" x2="16" y1="11" y2="11" />
                        </svg>
                    </span>
                    <span wire:loading wire:target="openCreate">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="size-4 animate-spin">
                            <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                        </svg>
                    </span>
                    Nuevo Usuario
                </button>
            </div>
        </div>
        <div class="card-body">
            <livewire:sata.users-table />
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- MODAL: Crear Usuario                                   --}}
    {{-- x-show + x-transition = apertura/cierre INSTANTÁNEO    --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div x-show="showCreate" x-cloak class="fixed inset-0 z-80 overflow-y-auto" role="dialog" aria-modal="true"
        aria-labelledby="modal-create-title" x-on:keydown.escape.window="if(showCreate) showCreate = false">

        {{-- Backdrop con fade --}}
        <div class="fixed inset-0 bg-black/50" x-show="showCreate"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            x-on:click="showCreate = false">
        </div>

        {{-- Panel con scale + fade --}}
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="card relative w-full max-w-lg shadow-xl border border-default-200 rounded-xl"
                x-show="showCreate" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4" x-on:click.stop>

                <div class="card-header">
                    <h3 id="modal-create-title" class="font-semibold text-base text-default-800">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="size-4 me-1 align-text-bottom inline-block">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <line x1="19" x2="19" y1="8" y2="14" />
                            <line x1="22" x2="16" y1="11" y2="11" />
                        </svg>
                        Registrar Nuevo Usuario
                    </h3>
                    <button type="button" class="size-5 text-default-800 hover:text-danger transition-colors"
                        x-on:click="showCreate = false">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="size-5">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>
                <form wire:submit="store">
                    <div class="card-body space-y-4">
                        {{-- Nombre --}}
                        <div>
                            <label class="inline-block mb-2 text-sm text-default-800 font-medium">Nombre Completo
                                <span class="text-danger">*</span></label>
                            <input type="text" wire:model="name"
                                class="form-input @error('name') border-danger @enderror"
                                placeholder="Ej: Juan Pérez López" autocomplete="name" minlength="3" autofocus>
                            @error('name')
                                <p class="text-danger text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        {{-- Email + DNI --}}
                        <div class="grid sm:grid-cols-2 grid-cols-1 gap-4">
                            <div>
                                <label class="inline-block mb-2 text-sm text-default-800 font-medium">Correo <span
                                        class="text-danger">*</span></label>
                                <input type="email" wire:model.blur="email"
                                    class="form-input @error('email') border-danger @enderror"
                                    placeholder="correo@ejemplo.com" autocomplete="email" inputmode="email">
                                @error('email')
                                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="inline-block mb-2 text-sm text-default-800 font-medium">DNI <span
                                        class="text-danger">*</span></label>
                                <input type="text" wire:model.blur="dni"
                                    class="form-input font-mono tracking-widest @error('dni') border-danger @enderror"
                                    maxlength="8" minlength="8" pattern="[0-9]{8}" inputmode="numeric"
                                    placeholder="00000000" required
                                    x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0, 8)">
                                @error('dni')
                                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        {{-- Rol + Cargo --}}
                        <div class="grid sm:grid-cols-2 grid-cols-1 gap-4">
                            <div>
                                <label class="inline-block mb-2 text-sm text-default-800 font-medium">Rol <span
                                        class="text-danger">*</span></label>
                                <select wire:model.live="role"
                                    class="form-input @error('role') border-danger @enderror">
                                    <option value="" disabled>Seleccione un rol...</option>
                                    @foreach ($roleOptions as $roleOption)
                                        <option value="{{ $roleOption->value }}">{{ $roleOption->label() }}</option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="inline-block mb-2 text-sm text-default-800 font-medium">Cargo</label>
                                <input type="text" wire:model="cargo"
                                    class="form-input @error('cargo') border-danger @enderror"
                                    placeholder="Ej: Especialista" maxlength="100" autocomplete="organization-title">
                                @error('cargo')
                                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        {{-- Tenant --}}
                        @if (\App\Enums\UserRole::tryFrom($role)?->requiresTenant() ?? true)
                            <div>
                                <label class="inline-block mb-2 text-sm text-default-800 font-medium">Institución
                                    Educativa</label>
                                <select wire:model="tenant_id"
                                    class="form-input @error('tenant_id') border-danger @enderror">
                                    <option value="">Seleccione una I.E...</option>
                                    @foreach ($tenants as $tenant)
                                        <option value="{{ $tenant->id }}">{{ $tenant->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('tenant_id')
                                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                        {{-- Password --}}
                        <div>
                            <label class="inline-block mb-2 text-sm text-default-800 font-medium">Contraseña <span
                                    class="text-danger">*</span></label>
                            <div x-data="{ showPw: false }" class="relative">
                                <input :type="showPw ? 'text' : 'password'" wire:model="password"
                                    class="form-input pe-10 @error('password') border-danger @enderror"
                                    autocomplete="new-password" minlength="8">
                                <button type="button"
                                    class="absolute inset-y-0 end-0 flex items-center pe-3 text-default-400 hover:text-default-600"
                                    x-on:click="showPw = !showPw" tabindex="-1"
                                    aria-label="Mostrar u ocultar contraseña">
                                    <svg x-show="!showPw" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="size-4">
                                        <path
                                            d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    <svg x-show="showPw" x-cloak xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="size-4">
                                        <path
                                            d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49" />
                                        <path d="M14.084 14.158a3 3 0 0 1-4.242-4.242" />
                                        <path
                                            d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143" />
                                        <path d="m2 2 20 20" />
                                    </svg>
                                </button>
                            </div>
                            <p class="text-default-400 text-[11px] mt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="size-3 inline-block align-text-bottom">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M12 16v-4" />
                                    <path d="M12 8h.01" />
                                </svg>
                                Predeterminada: su número de DNI
                            </p>
                            @error('password')
                                <p class="text-danger text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer flex gap-2 justify-end">
                        <button type="button" class="bg-transparent text-danger btn border-0 hover:bg-danger/10"
                            x-on:click="showCreate = false">Cancelar</button>
                        <button type="submit" class="btn bg-primary text-white inline-flex items-center gap-1.5"
                            wire:loading.attr="disabled" wire:target="store">
                            <svg wire:loading.remove wire:target="store" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="size-4">
                                <path d="M5 12h14" />
                                <path d="M12 5v14" />
                            </svg>
                            <svg wire:loading wire:target="store" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" class="size-4 animate-spin">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="store">Crear Usuario</span>
                            <span wire:loading wire:target="store">Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- MODAL: Editar Usuario                                  --}}
    {{-- x-show + x-transition = apertura/cierre INSTANTÁNEO    --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div x-show="showEdit" x-cloak class="fixed inset-0 z-80 overflow-y-auto" role="dialog" aria-modal="true"
        aria-labelledby="modal-edit-title" x-on:keydown.escape.window="if(showEdit) showEdit = false">

        {{-- Backdrop con fade --}}
        <div class="fixed inset-0 bg-black/50" x-show="showEdit"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-on:click="showEdit = false">
        </div>

        {{-- Panel con scale + fade --}}
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="card relative w-full max-w-lg shadow-xl border border-default-200 rounded-xl"
                x-show="showEdit" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4" x-on:click.stop>

                <div class="card-header">
                    <h3 id="modal-edit-title" class="font-semibold text-base text-default-800">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="size-4 me-1 align-text-bottom inline-block">
                            <circle cx="18" cy="15" r="3" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M10 15H6a4 4 0 0 0-4 4v2" />
                            <path d="m21.7 16.4-.9-.3" />
                            <path d="m15.2 13.9-.9-.3" />
                            <path d="m16.6 18.7.3-.9" />
                            <path d="m19.1 12.2.3-.9" />
                            <path d="m19.6 18.7-.4-.9" />
                            <path d="m16.8 12.3-.4-.9" />
                            <path d="m14.3 16.6.9-.3" />
                            <path d="m20.7 13.8.9-.3" />
                        </svg>
                        Editar Usuario
                    </h3>
                    <button type="button" class="size-5 text-default-800 hover:text-danger transition-colors"
                        x-on:click="showEdit = false">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="size-5">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>
                <form wire:submit="update">
                    <div class="card-body space-y-4">
                        {{-- Nombre --}}
                        <div>
                            <label class="inline-block mb-2 text-sm text-default-800 font-medium">Nombre Completo
                                <span class="text-danger">*</span></label>
                            <input type="text" wire:model="name"
                                class="form-input @error('name') border-danger @enderror"
                                placeholder="Ej: Juan Pérez López" autocomplete="name" minlength="3" autofocus>
                            @error('name')
                                <p class="text-danger text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        {{-- Email (disabled) + DNI --}}
                        <div class="grid sm:grid-cols-2 grid-cols-1 gap-4">
                            <div>
                                <label
                                    class="inline-block mb-2 text-sm text-default-800 font-medium opacity-60">Email</label>
                                <input type="email" value="{{ $email }}"
                                    class="form-input bg-default-100 text-default-500 cursor-not-allowed" disabled>
                            </div>
                            <div>
                                <label class="inline-block mb-2 text-sm text-default-800 font-medium">DNI <span
                                        class="text-danger">*</span></label>
                                <input type="text" wire:model.blur="dni"
                                    class="form-input font-mono tracking-widest @error('dni') border-danger @enderror"
                                    maxlength="8" minlength="8" pattern="[0-9]{8}" inputmode="numeric"
                                    placeholder="00000000" required
                                    x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0, 8)">
                                @error('dni')
                                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        {{-- Rol + Cargo --}}
                        <div class="grid sm:grid-cols-2 grid-cols-1 gap-4">
                            <div>
                                <label class="inline-block mb-2 text-sm text-default-800 font-medium">Rol <span
                                        class="text-danger">*</span></label>
                                <select wire:model.live="role"
                                    class="form-input @error('role') border-danger @enderror">
                                    <option value="" disabled>Seleccione un rol...</option>
                                    @foreach ($roleOptions as $roleOption)
                                        <option value="{{ $roleOption->value }}">{{ $roleOption->label() }}</option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="inline-block mb-2 text-sm text-default-800 font-medium">Cargo</label>
                                <input type="text" wire:model="cargo"
                                    class="form-input @error('cargo') border-danger @enderror"
                                    placeholder="Ej: Especialista" maxlength="100" autocomplete="organization-title">
                                @error('cargo')
                                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        {{-- Tenant --}}
                        @if (\App\Enums\UserRole::tryFrom($role)?->requiresTenant() ?? true)
                            <div>
                                <label class="inline-block mb-2 text-sm text-default-800 font-medium">Institución
                                    Educativa</label>
                                <select wire:model="tenant_id"
                                    class="form-input @error('tenant_id') border-danger @enderror">
                                    <option value="">Sin asignación (UGEL)</option>
                                    @foreach ($tenants as $tenant)
                                        <option value="{{ $tenant->id }}">{{ $tenant->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('tenant_id')
                                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                        {{-- Password --}}
                        <div>
                            <label class="inline-block mb-2 text-sm text-default-800 font-medium">Nueva
                                Contraseña</label>
                            <div x-data="{ showPw: false }" class="relative">
                                <input :type="showPw ? 'text' : 'password'" wire:model="password"
                                    class="form-input pe-10 @error('password') border-danger @enderror"
                                    placeholder="Dejar vacío para mantener" autocomplete="new-password"
                                    minlength="8">
                                <button type="button"
                                    class="absolute inset-y-0 end-0 flex items-center pe-3 text-default-400 hover:text-default-600"
                                    x-on:click="showPw = !showPw" tabindex="-1"
                                    aria-label="Mostrar u ocultar contraseña">
                                    <svg x-show="!showPw" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="size-4">
                                        <path
                                            d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    <svg x-show="showPw" x-cloak xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="size-4">
                                        <path
                                            d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49" />
                                        <path d="M14.084 14.158a3 3 0 0 1-4.242-4.242" />
                                        <path
                                            d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143" />
                                        <path d="m2 2 20 20" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-danger text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer flex gap-2 justify-end">
                        <button type="button" class="bg-transparent text-danger btn border-0 hover:bg-danger/10"
                            x-on:click="showEdit = false">Cancelar</button>
                        <button type="submit" class="btn bg-primary text-white inline-flex items-center gap-1.5"
                            wire:loading.attr="disabled" wire:target="update">
                            <svg wire:loading.remove wire:target="update" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="size-4">
                                <path
                                    d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z" />
                                <path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7" />
                                <path d="M7 3v4a1 1 0 0 0 1 1h7" />
                            </svg>
                            <svg wire:loading wire:target="update" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" class="size-4 animate-spin">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="update">Guardar Cambios</span>
                            <span wire:loading wire:target="update">Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- Los hooks de Lucide y SweetAlert2 están en el layout  --}}
    {{-- ═══════════════════════════════════════════════════════ --}}

    <style>
        [x-cloak] {
            display: none !important;
        }

        @keyframes lw-loading-bar {
            0% {
                transform: translateX(-100%);
            }

            50% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(300%);
            }
        }
    </style>
</div>
