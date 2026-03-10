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
    {{-- SIN TENANT                                             --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    @if (!$tenantId)
        <div class="card">
            <div class="card-body text-center py-12">
                <i data-lucide="building-2" class="size-16 mx-auto mb-4 text-default-300"></i>
                <h5 class="text-lg font-bold text-default-700 mb-2">Sin Institución Asignada</h5>
                <p class="text-default-500">Su cuenta de usuario no tiene una institución educativa vinculada.</p>
            </div>
        </div>
    @else
        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- SELECTOR DE I.E. (SuperAdmin)                          --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        @if ($isSuperAdmin && $tenants->count() > 1)
            <div class="mb-5">
                <div class="card">
                    <div class="card-body py-3">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-lg bg-violet-500/10 flex items-center justify-center shrink-0">
                                <i data-lucide="building-2" class="size-5 text-violet-500"></i>
                            </div>
                            <div class="flex-1">
                                <label for="tenant-selector" class="text-xs text-default-500 block mb-1">Seleccione la
                                    Institución Educativa a configurar</label>
                                <select id="tenant-selector" wire:model.live="tenantId"
                                    wire:change="selectTenant($event.target.value)"
                                    class="form-select text-sm font-medium">
                                    @foreach ($tenants as $t)
                                        <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- CABECERA DE I.E.                                       --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        <div class="mb-5 flex items-center gap-3">
            <div class="size-10 rounded-lg bg-primary/10 flex items-center justify-center">
                <i data-lucide="school" class="size-5 text-primary"></i>
            </div>
            <div>
                <h5 class="text-base font-bold text-default-800">{{ $tenantNombre }}</h5>
                <p class="text-xs text-default-400">Configuración institucional</p>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- LAYOUT DOS COLUMNAS                                    --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        <div class="grid lg:grid-cols-4 grid-cols-1 gap-5">

            {{-- ── SIDEBAR DE TABS ── --}}
            <div class="lg:col-span-1">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title text-sm">Secciones</h6>
                    </div>
                    <div class="card-body p-2">
                        <nav class="flex flex-col gap-1">
                            @php
                                $tabs = [
                                    'horarios' => [
                                        'icon' => 'clock',
                                        'label' => 'Horarios y Tolerancia',
                                        'desc' => 'Hora de entrada y minutos de gracia',
                                        'color' => 'primary',
                                    ],
                                    'identidad' => [
                                        'icon' => 'palette',
                                        'label' => 'Identidad Visual',
                                        'desc' => 'Color y lema institucional',
                                        'color' => 'success',
                                    ],
                                    'feriados' => [
                                        'icon' => 'calendar-off',
                                        'label' => 'Días Feriados',
                                        'desc' => 'Feriados y días no laborables',
                                        'color' => 'warning',
                                    ],
                                ];
                            @endphp

                            @foreach ($tabs as $tabKey => $meta)
                                <button type="button" wire:click="switchTab('{{ $tabKey }}')"
                                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-start transition-all
                                    {{ $activeTab === $tabKey
                                        ? 'bg-' . $meta['color'] . '/10 text-' . $meta['color'] . ' font-semibold'
                                        : 'text-default-600 hover:bg-default-100' }}">
                                    <div
                                        class="size-8 rounded-md flex items-center justify-center shrink-0
                                    {{ $activeTab === $tabKey ? 'bg-' . $meta['color'] . '/20' : 'bg-default-100' }}">
                                        <i data-lucide="{{ $meta['icon'] }}" class="size-4"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-sm block truncate">{{ $meta['label'] }}</span>
                                        <span
                                            class="text-[10px] text-default-400 block truncate">{{ $meta['desc'] }}</span>
                                    </div>
                                </button>
                            @endforeach
                        </nav>
                    </div>
                </div>
            </div>

            {{-- ── CONTENIDO PRINCIPAL ── --}}
            <div class="lg:col-span-3">

                {{-- ══════════════════════════════════════════════ --}}
                {{-- TAB: HORARIOS Y TOLERANCIA                    --}}
                {{-- ══════════════════════════════════════════════ --}}
                @if ($activeTab === 'horarios')
                    <form wire:submit="saveHorarios">
                        <div class="card">
                            <div class="card-header flex-wrap gap-3">
                                <div class="flex items-center gap-2">
                                    <div class="size-9 rounded-lg bg-primary/10 flex items-center justify-center">
                                        <i data-lucide="clock" class="size-5 text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="card-title text-base">Parámetros de Asistencia y Escáner QR</h6>
                                        <p class="text-default-400 text-xs">Configure el horario de entrada y tolerancia
                                            para el control de asistencia.</p>
                                    </div>
                                </div>
                                <span class="text-default-400 text-xs ms-auto" wire:loading wire:target="saveHorarios">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="size-4 animate-spin inline-block me-1">
                                        <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                                    </svg>
                                    Guardando...
                                </span>
                            </div>

                            <div class="card-body">
                                <div class="space-y-5">
                                    {{-- Hora de entrada --}}
                                    <div class="flex flex-col sm:flex-row sm:items-start gap-2">
                                        <div class="sm:w-1/3 shrink-0">
                                            <label for="hora_entrada"
                                                class="text-sm font-medium text-default-800 block">Hora de Ingreso
                                                Regular</label>
                                            <p class="text-[11px] text-default-400 mt-0.5">A partir de esta hora se
                                                calcula la tardanza.</p>
                                        </div>
                                        <div class="sm:w-2/3">
                                            <input type="time" wire:model="hora_entrada_regular" id="hora_entrada"
                                                class="form-input max-w-48 @error('hora_entrada_regular') border-danger @enderror">
                                            @error('hora_entrada_regular')
                                                <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <hr class="border-default-100">

                                    {{-- Minutos de tolerancia --}}
                                    <div class="flex flex-col sm:flex-row sm:items-start gap-2">
                                        <div class="sm:w-1/3 shrink-0">
                                            <label for="minutos_tol"
                                                class="text-sm font-medium text-default-800 block">Minutos de
                                                Tolerancia</label>
                                            <p class="text-[11px] text-default-400 mt-0.5">Tiempo de gracia antes de
                                                marcar como "Tarde".</p>
                                        </div>
                                        <div class="sm:w-2/3">
                                            <div class="flex items-center gap-2">
                                                <input type="number" wire:model="minutos_tolerancia" id="minutos_tol"
                                                    class="form-input max-w-28 @error('minutos_tolerancia') border-danger @enderror"
                                                    min="0" max="60">
                                                <span class="text-xs text-default-500 font-semibold">minutos</span>
                                            </div>
                                            @error('minutos_tolerancia')
                                                <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <hr class="border-default-100">

                                    {{-- Días para riesgo --}}
                                    <div class="flex flex-col sm:flex-row sm:items-start gap-2">
                                        <div class="sm:w-1/3 shrink-0">
                                            <label for="dias_riesgo"
                                                class="text-sm font-medium text-default-800 block">Inasistencias para
                                                Riesgo</label>
                                            <p class="text-[11px] text-default-400 mt-0.5">Días consecutivos de falta
                                                para generar alerta temprana automática.</p>
                                        </div>
                                        <div class="sm:w-2/3">
                                            <div class="p-3 rounded-md bg-warning/10 border border-warning/20">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <i data-lucide="alert-triangle" class="size-4 text-warning"></i>
                                                    <span class="text-xs font-bold text-warning">Alerta de
                                                        Deserción</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <input type="number" wire:model="dias_inasistencia_riesgo"
                                                        id="dias_riesgo"
                                                        class="form-input max-w-28 border-warning/30 @error('dias_inasistencia_riesgo') border-danger @enderror"
                                                        min="1" max="30">
                                                    <span class="text-xs text-default-500 font-semibold">días
                                                        consecutivos</span>
                                                </div>
                                                @error('dias_inasistencia_riesgo')
                                                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer flex items-center justify-end gap-2">
                                <button type="submit"
                                    class="btn bg-primary text-white inline-flex items-center gap-1"
                                    wire:loading.attr="disabled" wire:target="saveHorarios">
                                    <i data-lucide="save" class="size-4"></i>
                                    Guardar Configuración
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Sección de Cierre de Asistencia --}}
                    <div class="card mt-5 border-danger/30 bg-danger/5">
                        <div class="card-header border-b border-danger/20">
                            <div class="flex items-center gap-2">
                                <div class="size-9 rounded-lg bg-danger/10 flex items-center justify-center">
                                    <i data-lucide="lock" class="size-5 text-danger"></i>
                                </div>
                                <div>
                                    <h6 class="card-title text-danger text-base">Cierre de Asistencia Diario</h6>
                                    <p class="text-xs text-danger/60">Acción administrativa irreversible</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="text-sm text-default-600 mb-4">
                                Esta acción marcará automáticamente como <strong class="text-danger">Falta
                                    Injustificada (FI)</strong>
                                a todos los estudiantes que no registraron su ingreso hoy. Utilice esta opción al
                                finalizar el horario de entrada.
                            </p>
                            <button type="button" wire:click="closeDay"
                                wire:confirm="¿Está seguro de cerrar la asistencia de hoy? Esta acción marcará las inasistencias automáticamente."
                                class="btn bg-danger text-white py-2 px-6 font-bold inline-flex items-center gap-2"
                                wire:loading.attr="disabled" wire:target="closeDay">
                                <span wire:loading.remove wire:target="closeDay"><i data-lucide="lock"
                                        class="size-4"></i></span>
                                <span wire:loading wire:target="closeDay">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="size-4 animate-spin">
                                        <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                                    </svg>
                                </span>
                                Realizar Cierre de Asistencia Hoy
                            </button>
                        </div>
                    </div>
                @endif

                {{-- ══════════════════════════════════════════════ --}}
                {{-- TAB: IDENTIDAD VISUAL                         --}}
                {{-- ══════════════════════════════════════════════ --}}
                @if ($activeTab === 'identidad')
                    <form wire:submit="saveIdentidad">
                        <div class="card">
                            <div class="card-header flex-wrap gap-3">
                                <div class="flex items-center gap-2">
                                    <div class="size-9 rounded-lg bg-success/10 flex items-center justify-center">
                                        <i data-lucide="palette" class="size-5 text-success"></i>
                                    </div>
                                    <div>
                                        <h6 class="card-title text-base">Identidad Visual</h6>
                                        <p class="text-default-400 text-xs">Personalice la apariencia de los carnets y
                                            documentos de su institución.</p>
                                    </div>
                                </div>
                                <span class="text-default-400 text-xs ms-auto" wire:loading
                                    wire:target="saveIdentidad">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="size-4 animate-spin inline-block me-1">
                                        <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                                    </svg>
                                    Guardando...
                                </span>
                            </div>

                            <div class="card-body">
                                <div class="space-y-5">
                                    {{-- Color primario --}}
                                    <div class="flex flex-col sm:flex-row sm:items-start gap-2">
                                        <div class="sm:w-1/3 shrink-0">
                                            <label for="primary_color"
                                                class="text-sm font-medium text-default-800 block">Color
                                                Primario</label>
                                            <p class="text-[11px] text-default-400 mt-0.5">Color principal para los
                                                carnets QR generados.</p>
                                        </div>
                                        <div class="sm:w-2/3">
                                            <div class="flex items-center gap-3">
                                                <input type="color" wire:model.live="primary_color"
                                                    id="primary_color"
                                                    class="size-10 rounded-lg border border-default-200 cursor-pointer p-0.5">
                                                <input type="text" wire:model.live="primary_color"
                                                    class="form-input max-w-32 font-mono text-sm @error('primary_color') border-danger @enderror"
                                                    maxlength="7" placeholder="#000000">
                                                <div class="h-8 w-20 rounded-md border border-default-200"
                                                    style="background-color: {{ $primary_color }}"></div>
                                            </div>
                                            @error('primary_color')
                                                <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <hr class="border-default-100">

                                    {{-- Lema institucional --}}
                                    <div class="flex flex-col sm:flex-row sm:items-start gap-2">
                                        <div class="sm:w-1/3 shrink-0">
                                            <label for="lema"
                                                class="text-sm font-medium text-default-800 block">Lema
                                                Institucional</label>
                                            <p class="text-[11px] text-default-400 mt-0.5">Texto que aparece debajo del
                                                nombre en los carnets.</p>
                                        </div>
                                        <div class="sm:w-2/3">
                                            <input type="text" wire:model="lema" id="lema"
                                                class="form-input @error('lema') border-danger @enderror"
                                                placeholder="Ej: Formando líderes del mañana" maxlength="200">
                                            @error('lema')
                                                <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                            <p class="text-[11px] text-default-400 mt-1">{{ strlen($lema) }}/200
                                                caracteres</p>
                                        </div>
                                    </div>

                                    <hr class="border-default-100">

                                    {{-- Vista previa --}}
                                    <div class="flex flex-col sm:flex-row sm:items-start gap-2">
                                        <div class="sm:w-1/3 shrink-0">
                                            <span class="text-sm font-medium text-default-800 block">Vista
                                                Previa</span>
                                            <p class="text-[11px] text-default-400 mt-0.5">Así se verá la cabecera en
                                                los carnets.</p>
                                        </div>
                                        <div class="sm:w-2/3">
                                            <div
                                                class="rounded-lg overflow-hidden shadow-sm border border-default-200 max-w-xs">
                                                <div class="px-4 py-3 text-white text-center"
                                                    style="background-color: {{ $primary_color }}">
                                                    <p
                                                        class="text-[10px] font-bold uppercase tracking-wider opacity-80">
                                                        {{ $tenantNombre }}</p>
                                                    @if ($lema)
                                                        <p class="text-[8px] mt-0.5 opacity-70">{{ $lema }}
                                                        </p>
                                                    @endif
                                                </div>
                                                <div class="px-4 py-3 bg-white text-center">
                                                    <div
                                                        class="size-12 rounded-full bg-default-100 mx-auto mb-1 flex items-center justify-center">
                                                        <i data-lucide="qr-code" class="size-6 text-default-400"></i>
                                                    </div>
                                                    <p class="text-[10px] text-default-500">Código QR del estudiante
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer flex items-center justify-end gap-2">
                                <button type="submit"
                                    class="btn bg-success text-white inline-flex items-center gap-1"
                                    wire:loading.attr="disabled" wire:target="saveIdentidad">
                                    <i data-lucide="save" class="size-4"></i>
                                    Guardar Identidad Visual
                                </button>
                            </div>
                        </div>
                    </form>
                @endif

                {{-- ══════════════════════════════════════════════ --}}
                {{-- TAB: DÍAS FERIADOS                            --}}
                {{-- ══════════════════════════════════════════════ --}}
                @if ($activeTab === 'feriados')
                    <div class="card">
                        <div class="card-header flex-wrap gap-3">
                            <div class="flex items-center gap-2">
                                <div class="size-9 rounded-lg bg-warning/10 flex items-center justify-center">
                                    <i data-lucide="calendar-off" class="size-5 text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="card-title text-base">Calendario de Feriados</h6>
                                    <p class="text-default-400 text-xs">Los días feriados no generarán faltas
                                        automáticas al ejecutar el cierre diario.</p>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            {{-- Formulario agregar/editar feriado --}}
                            <form wire:submit="saveFeriado"
                                class="mb-6 p-4 rounded-lg bg-default-50 border border-default-200">
                                <h6 class="text-sm font-bold text-default-700 mb-3">
                                    {{ $editingFeriadoId ? 'Editar Feriado' : 'Agregar Nuevo Feriado' }}
                                </h6>
                                <div class="grid sm:grid-cols-12 gap-3">
                                    <div class="sm:col-span-4">
                                        <label class="text-xs font-medium text-default-600 mb-1 block">Fecha</label>
                                        <input type="date" wire:model="feriado_fecha"
                                            class="form-input text-sm @error('feriado_fecha') border-danger @enderror">
                                        @error('feriado_fecha')
                                            <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="sm:col-span-6">
                                        <label
                                            class="text-xs font-medium text-default-600 mb-1 block">Descripción</label>
                                        <input type="text" wire:model="feriado_descripcion"
                                            class="form-input text-sm @error('feriado_descripcion') border-danger @enderror"
                                            placeholder="Ej: Día del Maestro" maxlength="150">
                                        @error('feriado_descripcion')
                                            <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="sm:col-span-2 flex items-end gap-1">
                                        <button type="submit"
                                            class="btn bg-warning text-white w-full inline-flex items-center justify-center gap-1"
                                            wire:loading.attr="disabled" wire:target="saveFeriado">
                                            <i data-lucide="{{ $editingFeriadoId ? 'check' : 'plus' }}"
                                                class="size-4"></i>
                                            {{ $editingFeriadoId ? 'Actualizar' : 'Agregar' }}
                                        </button>
                                    </div>
                                </div>
                                @if ($editingFeriadoId)
                                    <button type="button" wire:click="cancelEditFeriado"
                                        class="text-xs text-default-500 hover:text-danger mt-2 underline">
                                        Cancelar edición
                                    </button>
                                @endif
                            </form>

                            {{-- Tabla de feriados --}}
                            @if ($feriados->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="w-full border-collapse">
                                        <thead>
                                            <tr class="bg-default-50">
                                                <th
                                                    class="px-4 py-2.5 text-start text-xs font-semibold text-default-500 uppercase tracking-wider">
                                                    Fecha</th>
                                                <th
                                                    class="px-4 py-2.5 text-start text-xs font-semibold text-default-500 uppercase tracking-wider">
                                                    Descripción</th>
                                                <th
                                                    class="px-4 py-2.5 text-center text-xs font-semibold text-default-500 uppercase tracking-wider w-28">
                                                    Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-default-100">
                                            @foreach ($feriados as $feriado)
                                                <tr
                                                    class="hover:bg-default-50 transition-colors {{ $editingFeriadoId === $feriado->id ? 'bg-warning/5' : '' }}">
                                                    <td class="px-4 py-2.5">
                                                        <div class="flex items-center gap-2">
                                                            <div
                                                                class="size-8 rounded-md bg-warning/10 flex items-center justify-center shrink-0">
                                                                <i data-lucide="calendar"
                                                                    class="size-3.5 text-warning"></i>
                                                            </div>
                                                            <span
                                                                class="text-sm font-medium text-default-800">{{ $feriado->fecha->format('d/m/Y') }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-2.5 text-sm text-default-600">
                                                        {{ $feriado->descripcion }}</td>
                                                    <td class="px-4 py-2.5 text-center">
                                                        <div class="flex items-center justify-center gap-1">
                                                            <button type="button"
                                                                wire:click="editFeriado({{ $feriado->id }})"
                                                                class="size-7 rounded-md bg-primary/10 text-primary hover:bg-primary/20 inline-flex items-center justify-center transition-colors"
                                                                title="Editar">
                                                                <i data-lucide="pencil" class="size-3.5"></i>
                                                            </button>
                                                            <button type="button"
                                                                wire:click="deleteFeriado({{ $feriado->id }})"
                                                                wire:confirm="¿Eliminar este feriado?"
                                                                class="size-7 rounded-md bg-danger/10 text-danger hover:bg-danger/20 inline-flex items-center justify-center transition-colors"
                                                                title="Eliminar">
                                                                <i data-lucide="trash-2" class="size-3.5"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-8 text-default-400">
                                    <i data-lucide="calendar-x" class="size-12 mx-auto mb-2 opacity-30"></i>
                                    <p class="text-sm">No hay feriados registrados para esta institución.</p>
                                    <p class="text-xs mt-1">Agregue los días no laborables para evitar faltas
                                        automáticas.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

            </div>
        </div>
    @endif
</div>
