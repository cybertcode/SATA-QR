<div class="flex items-center justify-center gap-1">
    @if ($isTrashed ?? false)
        {{-- Restaurar --}}
        <button type="button" class="btn size-7.5 bg-success/10 hover:bg-success hover:text-white text-success"
            aria-label="Restaurar usuario {{ $user->name }}"
            x-on:click="
                Swal.fire({
                    title: '¿Restaurar a {{ e($user->name) }}?',
                    text: 'El usuario volverá al listado activo.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#22c55e',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Sí, restaurar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) Livewire.dispatch('restoreUser', { userId: {{ $user->id }} });
                })
            "
            title="Restaurar">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                <path d="M3 3v5h5" />
            </svg>
        </button>
        {{-- Eliminar permanente --}}
        <button type="button" class="btn size-7.5 bg-danger/10 hover:bg-danger hover:text-white text-danger"
            aria-label="Eliminar permanentemente {{ $user->name }}"
            x-on:click="
                Swal.fire({
                    title: '¿Eliminar permanentemente?',
                    html: '<b>{{ e($user->name) }}</b><br><span class=\'text-sm text-gray-500\'>Esta acción NO se puede deshacer.</span>',
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Eliminar para siempre',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) Livewire.dispatch('forceDeleteUser', { userId: {{ $user->id }} });
                })
            "
            title="Eliminar permanentemente">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 6h18" />
                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                <line x1="10" x2="10" y1="11" y2="17" />
                <line x1="14" x2="14" y1="11" y2="17" />
            </svg>
        </button>
    @else
        {{-- Editar --}}
        <button type="button"
            class="btn size-7.5 bg-default-200 hover:bg-primary hover:text-white text-default-500 relative"
            wire:click="$dispatch('editUser', { userId: {{ $user->id }} })" title="Editar"
            aria-label="Editar usuario {{ $user->name }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path
                    d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                <path d="m15 5 4 4" />
            </svg>
        </button>
        {{-- Eliminar (soft) --}}
        @if (!$isSelf)
            <button type="button" class="btn size-7.5 bg-default-200 hover:bg-danger hover:text-white text-default-500"
                aria-label="Eliminar usuario {{ $user->name }}"
                x-on:click="
                    Swal.fire({
                        title: '¿Eliminar a {{ e($user->name) }}?',
                        text: 'El usuario será movido a la papelera.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) Livewire.dispatch('deleteUser', { userId: {{ $user->id }} });
                    })
                "
                title="Eliminar">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h18" />
                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                    <line x1="10" x2="10" y1="11" y2="17" />
                    <line x1="14" x2="14" y1="11" y2="17" />
                </svg>
            </button>
        @endif
    @endif
</div>
