@if ($isSelf)
    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-success">
        <span class="size-1.5 rounded-full bg-success"></span> Tú
    </span>
@else
    <label class="relative inline-flex cursor-pointer" title="{{ $user->is_active ? 'Desactivar' : 'Activar' }}"
        x-data="{ toggling: false }">
        <input type="checkbox" class="sr-only peer" {{ $user->is_active ? 'checked' : '' }}
            x-on:change="toggling = true; Livewire.dispatch('toggleStatus', { userId: {{ $user->id }} }); setTimeout(() => toggling = false, 5000)"
            x-bind:disabled="toggling">
        <div x-show="!toggling"
            class="w-9 h-5 bg-default-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-success">
        </div>
        <div x-show="toggling" x-cloak class="w-9 h-5 flex items-center justify-center">
            <svg class="animate-spin size-4 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        </div>
    </label>
@endif
