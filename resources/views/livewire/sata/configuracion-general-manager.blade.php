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
                    <i data-lucide="settings" class="size-6 text-primary"></i>
                </div>
                <div>
                    <p class="text-default-500 text-xs uppercase tracking-wide">Parámetros</p>
                    <h4 class="text-xl font-bold text-default-800">{{ $stats['total'] }}</h4>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body flex items-center gap-3">
                <div class="size-12 rounded-lg bg-success/10 flex items-center justify-center shrink-0">
                    <i data-lucide="layers" class="size-6 text-success"></i>
                </div>
                <div>
                    <p class="text-default-500 text-xs uppercase tracking-wide">Categorías</p>
                    <h4 class="text-xl font-bold text-success">{{ $stats['grupos'] }}</h4>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body flex items-center gap-3">
                <div class="size-12 rounded-lg bg-warning/10 flex items-center justify-center shrink-0">
                    <i data-lucide="pen-line" class="size-6 text-warning"></i>
                </div>
                <div>
                    <p class="text-default-500 text-xs uppercase tracking-wide">Modificadas</p>
                    <h4 class="text-xl font-bold text-warning">{{ $stats['modificadas'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- LAYOUT DOS COLUMNAS: TABS + FORMULARIO                 --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="grid lg:grid-cols-4 grid-cols-1 gap-5">
        {{-- SIDEBAR DE GRUPOS --}}
        <div class="lg:col-span-1">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title text-sm">Categorías</h6>
                </div>
                <div class="card-body p-2">
                    <nav class="flex flex-col gap-1">
                        @foreach ($gruposMeta as $grupoKey => $meta)
                            <button type="button" wire:click="switchGroup('{{ $grupoKey }}')"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-start transition-all
                                    {{ $activeGroup === $grupoKey
                                        ? 'bg-' . $meta['color'] . '/10 text-' . $meta['color'] . ' font-semibold'
                                        : 'text-default-600 hover:bg-default-100' }}">
                                <div
                                    class="size-8 rounded-md flex items-center justify-center shrink-0
                                    {{ $activeGroup === $grupoKey ? 'bg-' . $meta['color'] . '/20' : 'bg-default-100' }}">
                                    <i data-lucide="{{ $meta['icon'] }}" class="size-4"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-sm block truncate">{{ $meta['label'] }}</span>
                                    <span
                                        class="text-[10px] text-default-400 block truncate">{{ $meta['descripcion'] }}</span>
                                </div>
                            </button>
                        @endforeach
                    </nav>
                </div>
            </div>
        </div>

        {{-- FORMULARIO DE CONFIGURACIÓN --}}
        <div class="lg:col-span-3">
            <form wire:submit="save">
                <div class="card">
                    <div class="card-header flex-wrap gap-3">
                        <div class="flex items-center gap-2">
                            @php $meta = $gruposMeta[$activeGroup] ?? ['label' => $activeGroup, 'icon' => 'settings', 'color' => 'primary']; @endphp
                            <div class="size-9 rounded-lg bg-{{ $meta['color'] }}/10 flex items-center justify-center">
                                <i data-lucide="{{ $meta['icon'] }}" class="size-5 text-{{ $meta['color'] }}"></i>
                            </div>
                            <div>
                                <h6 class="card-title text-base">{{ $meta['label'] }}</h6>
                                <p class="text-default-400 text-xs">{{ $meta['descripcion'] }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 ms-auto">
                            <span class="text-default-400 text-xs" wire:loading wire:target="save">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="size-4 animate-spin inline-block me-1">
                                    <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                                </svg>
                                Guardando...
                            </span>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="space-y-5">
                            @foreach ($configs as $config)
                                <div class="flex flex-col sm:flex-row sm:items-start gap-2">
                                    <div class="sm:w-1/3 shrink-0">
                                        <label for="cfg-{{ $config->id }}"
                                            class="text-sm font-medium text-default-800 block">
                                            {{ $config->etiqueta }}
                                        </label>
                                        @if ($config->descripcion)
                                            <p class="text-[11px] text-default-400 mt-0.5">{{ $config->descripcion }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="sm:w-2/3">
                                        @if ($config->tipo === 'boolean')
                                            <label class="inline-flex items-center cursor-pointer gap-2.5">
                                                <input type="checkbox" wire:model="valores.{{ $config->id }}"
                                                    id="cfg-{{ $config->id }}" class="toggle toggle-primary">
                                                <span class="text-sm text-default-600">
                                                    {{ $valores[$config->id] ?? false ? 'Activado' : 'Desactivado' }}
                                                </span>
                                            </label>
                                        @elseif ($config->tipo === 'integer')
                                            <input type="number" wire:model="valores.{{ $config->id }}"
                                                id="cfg-{{ $config->id }}"
                                                class="form-input max-w-40 @error('valores.' . $config->id) border-danger @enderror"
                                                min="0">
                                        @else
                                            <input type="text" wire:model="valores.{{ $config->id }}"
                                                id="cfg-{{ $config->id }}"
                                                class="form-input @error('valores.' . $config->id) border-danger @enderror">
                                        @endif
                                        @error('valores.' . $config->id)
                                            <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                @if (!$loop->last)
                                    <hr class="border-default-100">
                                @endif
                            @endforeach

                            @if ($configs->isEmpty())
                                <div class="text-center py-8 text-default-400">
                                    <i data-lucide="inbox" class="size-12 mx-auto mb-2 opacity-30"></i>
                                    <p class="text-sm">No hay configuraciones en esta categoría.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if ($configs->isNotEmpty())
                        <div class="card-footer flex items-center justify-between gap-2">
                            <button type="button"
                                class="btn bg-default-100 text-default-600 inline-flex items-center gap-1"
                                wire:click="resetGroup" wire:loading.attr="disabled">
                                <i data-lucide="rotate-ccw" class="size-4"></i>
                                Restaurar
                            </button>
                            <button type="submit" class="btn bg-primary text-white inline-flex items-center gap-1"
                                wire:loading.attr="disabled" wire:target="save">
                                <span wire:loading.remove wire:target="save">
                                    <i data-lucide="save" class="size-4"></i>
                                </span>
                                <span wire:loading wire:target="save">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="size-4 animate-spin">
                                        <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                                    </svg>
                                </span>
                                Guardar Configuración
                            </button>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
