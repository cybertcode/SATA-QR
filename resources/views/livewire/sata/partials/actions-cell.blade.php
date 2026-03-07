<div class="flex items-center justify-center gap-1">
    <button type="button" class="btn size-7.5 bg-default-200 hover:bg-primary hover:text-white text-default-500 relative"
        wire:click="$dispatch('editUser', { userId: {{ $user->id }} })" title="Editar">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path
                d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
            <path d="m15 5 4 4" />
        </svg>
    </button>
    @if (!$isSelf)
        <button type="button" class="btn size-7.5 bg-default-200 hover:bg-danger hover:text-white text-default-500"
            x-on:click="
                Swal.fire({
                    title: '¿Eliminar a {{ e($user->name) }}?',
                    text: 'Esta acción no se puede deshacer.',
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
</div>
