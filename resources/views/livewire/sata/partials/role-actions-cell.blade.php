<div class="flex items-center justify-center gap-1">
    {{-- Editar (siempre visible) --}}
    <button type="button" class="btn size-7.5 bg-default-200 hover:bg-primary hover:text-white text-default-500 relative"
        wire:click="$dispatch('editRole', { roleId: {{ $role->id }} })" title="Editar permisos"
        aria-label="Editar rol {{ $role->name }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" class="size-3.5">
            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
            <path d="m15 5 4 4" />
        </svg>
    </button>

    {{-- Eliminar (solo si NO es protegido) --}}
    @if (!$isProtected)
        <button type="button" class="btn size-7.5 bg-default-200 hover:bg-danger hover:text-white text-default-500"
            aria-label="Eliminar rol {{ $role->name }}"
            x-on:click="
                Swal.fire({
                    title: '¿Eliminar rol {{ e($role->name) }}?',
                    @if ($role->users_count > 0) html: 'Este rol tiene <strong>{{ $role->users_count }} usuario(s)</strong> asignado(s).<br><span class=\'text-sm text-gray-500\'>Se les retirará este rol automáticamente.</span>',
                    @else
                        text: 'Esta acción no se puede deshacer.', @endif
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) Livewire.dispatch('deleteRole', { roleId: {{ $role->id }} });
                })
            "
            title="Eliminar">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3.5">
                <path d="M3 6h18" />
                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
            </svg>
        </button>
    @else
        <span class="text-default-300 text-[10px] italic" title="Rol del sistema, no se puede eliminar">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
            </svg>
        </span>
    @endif
</div>
