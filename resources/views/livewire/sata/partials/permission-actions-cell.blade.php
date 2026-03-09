<div class="flex items-center justify-center">
    <button type="button" class="btn size-7.5 bg-default-200 hover:bg-danger hover:text-white text-default-500"
        aria-label="Eliminar permiso {{ $permission->name }}"
        x-on:click="
            Swal.fire({
                title: '¿Eliminar permiso?',
                html: '<code>{{ e($permission->name) }}</code><br><span class=\'text-sm text-gray-500\'>Se eliminará permanentemente.</span>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) Livewire.dispatch('deletePermission', { permissionId: {{ $permission->id }} });
            })
        "
        title="Eliminar permiso">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" class="size-3.5">
            <path d="M3 6h18" />
            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
        </svg>
    </button>
</div>
