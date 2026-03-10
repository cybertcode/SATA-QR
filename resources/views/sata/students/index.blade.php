@extends('layouts.vertical', ['title' => 'Gestión de Estudiantes'])

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Alumnado', 'title' => 'Listado General'])

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- TARJETAS DE ESTADÍSTICAS                               --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="grid xl:grid-cols-6 md:grid-cols-3 grid-cols-2 gap-5 mb-5">
        <a href="{{ route('institutions.index') }}" class="card hover:shadow-md transition-shadow group cursor-pointer">
            <div class="card-body flex items-center gap-3">
                <div
                    class="size-12 rounded-lg bg-violet-500/10 flex items-center justify-center shrink-0 group-hover:bg-violet-500/20 transition-colors">
                    <i data-lucide="school" class="size-6 text-violet-500"></i>
                </div>
                <div>
                    <p class="text-default-500 text-xs uppercase tracking-wide">Instituciones</p>
                    <h4 class="text-xl font-bold text-violet-600">{{ $stats['totalInstituciones'] }}</h4>
                </div>
            </div>
        </a>
        <div class="card hover:shadow-md transition-shadow">
            <div class="card-body flex items-center gap-3">
                <div class="size-12 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                    <i data-lucide="users" class="size-6 text-primary"></i>
                </div>
                <div>
                    <p class="text-default-500 text-xs uppercase tracking-wide">Total</p>
                    <h4 class="text-xl font-bold text-default-800">{{ $stats['totalEstudiantes'] }}</h4>
                </div>
            </div>
        </div>
        <div class="card hover:shadow-md transition-shadow">
            <div class="card-body flex items-center gap-3">
                <div class="size-12 rounded-lg bg-success/10 flex items-center justify-center shrink-0">
                    <i data-lucide="user-check" class="size-6 text-success"></i>
                </div>
                <div>
                    <p class="text-default-500 text-xs uppercase tracking-wide">Matriculados</p>
                    <h4 class="text-xl font-bold text-success">{{ $stats['conMatricula'] }}</h4>
                </div>
            </div>
        </div>
        <div class="card hover:shadow-md transition-shadow">
            <div class="card-body flex items-center gap-3">
                <div class="size-12 rounded-lg bg-warning/10 flex items-center justify-center shrink-0">
                    <i data-lucide="user-x" class="size-6 text-warning"></i>
                </div>
                <div>
                    <p class="text-default-500 text-xs uppercase tracking-wide">Sin Matrícula</p>
                    <h4 class="text-xl font-bold text-warning">{{ $stats['sinMatricula'] }}</h4>
                </div>
            </div>
        </div>
        <div class="card hover:shadow-md transition-shadow">
            <div class="card-body flex items-center gap-3">
                <div class="size-12 rounded-lg bg-info/10 flex items-center justify-center shrink-0">
                    <i data-lucide="circle-user" class="size-6 text-info"></i>
                </div>
                <div>
                    <p class="text-default-500 text-xs uppercase tracking-wide">Masculino</p>
                    <h4 class="text-xl font-bold text-info">{{ $stats['masculinos'] }}</h4>
                </div>
            </div>
        </div>
        <div class="card hover:shadow-md transition-shadow">
            <div class="card-body flex items-center gap-3">
                <div class="size-12 rounded-lg bg-pink-500/10 flex items-center justify-center shrink-0">
                    <i data-lucide="circle-user-round" class="size-6 text-pink-500"></i>
                </div>
                <div>
                    <p class="text-default-500 text-xs uppercase tracking-wide">Femenino</p>
                    <h4 class="text-xl font-bold text-pink-500">{{ $stats['femeninos'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- FLASH MESSAGE                                          --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    @if (session('success'))
        <div class="bg-success/10 border border-success/20 text-success p-4 rounded-md mb-5 text-sm flex items-center gap-3"
            x-data="{ show: true }" x-show="show" x-transition>
            <i data-lucide="check-circle" class="size-5 shrink-0"></i>
            <p class="flex-1">{{ session('success') }}</p>
            <button x-on:click="show = false" class="shrink-0 hover:text-success/70"><i data-lucide="x"
                    class="size-4"></i></button>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- TABLA PRINCIPAL                                        --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="card">
        <div class="card-header flex-wrap gap-3">
            <h6 class="card-title">Directorio de Estudiantes</h6>
            <div class="flex items-center gap-2 ms-auto flex-wrap">
                <a href="{{ route('students.export', request()->query()) }}"
                    class="btn btn-sm bg-success/10 text-success hover:bg-success hover:text-white inline-flex items-center gap-1">
                    <i data-lucide="download" class="size-3.5"></i> Excel
                </a>
                <a href="{{ route('students.import') }}"
                    class="btn btn-sm bg-info/10 text-info hover:bg-info hover:text-white inline-flex items-center gap-1">
                    <i data-lucide="upload" class="size-3.5"></i> Importar SIAGIE
                </a>
            </div>
        </div>

        {{-- Barra de Filtros --}}
        <div class="card-body border-b border-default-200 bg-default-50/50 py-3">
            <form method="GET" action="{{ route('students.index') }}" class="flex flex-wrap items-end gap-3">
                {{-- Búsqueda --}}
                <div class="flex-1 min-w-[200px]">
                    <label class="text-[10px] text-default-500 uppercase font-bold mb-1 block">Buscar</label>
                    <div class="relative">
                        <input class="ps-9 form-input form-input-sm w-full" placeholder="DNI o nombre..." type="text"
                            name="search" value="{{ request('search') }}" />
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3">
                            <i class="size-3.5 text-default-400" data-lucide="search"></i>
                        </div>
                    </div>
                </div>

                {{-- Filtro IE (SuperAdmin) --}}
                @if (auth()->user()->isSuperAdmin())
                    <div class="min-w-[180px]">
                        <label class="text-[10px] text-default-500 uppercase font-bold mb-1 block">Institución</label>
                        <select class="form-input form-input-sm w-full" name="ie">
                            <option value="">Todas las I.E.</option>
                            @foreach ($tenants as $ie)
                                <option value="{{ $ie->id }}" {{ request('ie') == $ie->id ? 'selected' : '' }}>
                                    {{ $ie->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Filtro Nivel --}}
                <div class="min-w-[130px]">
                    <label class="text-[10px] text-default-500 uppercase font-bold mb-1 block">Nivel</label>
                    <select class="form-input form-input-sm w-full" name="nivel">
                        <option value="">Todos</option>
                        <option value="Primaria" {{ request('nivel') == 'Primaria' ? 'selected' : '' }}>Primaria</option>
                        <option value="Secundaria" {{ request('nivel') == 'Secundaria' ? 'selected' : '' }}>Secundaria
                        </option>
                    </select>
                </div>

                {{-- Filtro Grado --}}
                <div class="min-w-[100px]">
                    <label class="text-[10px] text-default-500 uppercase font-bold mb-1 block">Grado</label>
                    <select class="form-input form-input-sm w-full" name="grado">
                        <option value="">Todos</option>
                        @for ($g = 1; $g <= 6; $g++)
                            <option value="{{ $g }}" {{ request('grado') == $g ? 'selected' : '' }}>
                                {{ $g }}°</option>
                        @endfor
                    </select>
                </div>

                {{-- Filtro Estado --}}
                <div class="min-w-[130px]">
                    <label class="text-[10px] text-default-500 uppercase font-bold mb-1 block">Estado</label>
                    <select class="form-input form-input-sm w-full" name="estado">
                        <option value="">Todos</option>
                        <option value="Activo" {{ request('estado') == 'Activo' ? 'selected' : '' }}>Activo</option>
                        <option value="Retirado" {{ request('estado') == 'Retirado' ? 'selected' : '' }}>Retirado</option>
                        <option value="Trasladado" {{ request('estado') == 'Trasladado' ? 'selected' : '' }}>Trasladado
                        </option>
                        <option value="sin_matricula" {{ request('estado') == 'sin_matricula' ? 'selected' : '' }}>Sin
                            Matrícula</option>
                    </select>
                </div>

                {{-- Botones --}}
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-sm bg-primary text-white">
                        <i data-lucide="filter" class="size-3.5 me-1"></i> Filtrar
                    </button>
                    @if (request()->hasAny(['search', 'ie', 'nivel', 'grado', 'estado']))
                        <a href="{{ route('students.index') }}" class="btn btn-sm bg-default-200 text-default-600"
                            title="Limpiar filtros">
                            <i data-lucide="x" class="size-3.5"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-default-200">
                <thead class="bg-default-100/50">
                    <tr>
                        <th class="px-3.5 py-3 text-start text-xs font-medium text-default-500 uppercase tracking-wider">
                            Estudiante</th>
                        <th class="px-3.5 py-3 text-start text-xs font-medium text-default-500 uppercase tracking-wider">
                            DNI</th>
                        @if (auth()->user()->isSuperAdmin())
                            <th
                                class="px-3.5 py-3 text-start text-xs font-medium text-default-500 uppercase tracking-wider hidden lg:table-cell">
                                Institución</th>
                        @endif
                        <th class="px-3.5 py-3 text-start text-xs font-medium text-default-500 uppercase tracking-wider">
                            Nivel</th>
                        <th class="px-3.5 py-3 text-start text-xs font-medium text-default-500 uppercase tracking-wider">
                            Grado/Sección</th>
                        <th class="px-3.5 py-3 text-start text-xs font-medium text-default-500 uppercase tracking-wider">
                            Género</th>
                        <th class="px-3.5 py-3 text-start text-xs font-medium text-default-500 uppercase tracking-wider">
                            Estado</th>
                        <th class="px-3.5 py-3 text-start text-xs font-medium text-default-500 uppercase tracking-wider">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-default-100">
                    @forelse($students as $student)
                        @php
                            $matricula = $student->matriculaActual;
                            $avatarColor =
                                $student->genero === 'M' ? 'bg-info/10 text-info' : 'bg-pink-500/10 text-pink-500';
                        @endphp
                        <tr class="transition-colors hover:bg-default-50">
                            {{-- Estudiante (Avatar + Nombre) --}}
                            <td class="px-3.5 py-2.5 whitespace-nowrap text-sm">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <div
                                            class="size-10 rounded-full {{ $avatarColor }} flex items-center justify-center font-bold text-sm uppercase">
                                            {{ substr($student->nombres, 0, 1) }}{{ substr($student->apellido_paterno, 0, 1) }}
                                        </div>
                                        @if ($matricula)
                                            <span
                                                class="absolute size-2.5 rounded-full border-2 border-white end-0 bottom-0 {{ $matricula->estado === 'Activo' ? 'bg-success' : 'bg-warning' }}"></span>
                                        @else
                                            <span
                                                class="absolute size-2.5 rounded-full border-2 border-white end-0 bottom-0 bg-default-300"></span>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="text-default-800 font-medium text-sm mb-0.5">
                                            {{ $student->apellido_paterno }} {{ $student->apellido_materno }},
                                            {{ $student->nombres }}</h6>
                                        @if (!auth()->user()->isSuperAdmin())
                                            <p class="text-[10px] text-default-500 uppercase">
                                                {{ $student->tenant->nombre }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- DNI --}}
                            <td class="px-3.5 py-2.5 whitespace-nowrap text-sm">
                                <span class="font-mono text-primary font-medium">{{ $student->dni }}</span>
                            </td>

                            {{-- Institución (solo SuperAdmin) --}}
                            @if (auth()->user()->isSuperAdmin())
                                <td class="px-3.5 py-2.5 whitespace-nowrap text-sm text-default-600 hidden lg:table-cell">
                                    <span class="truncate max-w-[200px] block"
                                        title="{{ $student->tenant->nombre }}">{{ $student->tenant->nombre }}</span>
                                </td>
                            @endif

                            {{-- Nivel --}}
                            <td class="px-3.5 py-2.5 whitespace-nowrap text-sm">
                                @if ($matricula)
                                    @php
                                        $nivel = $matricula->seccion->nivelEducativo->nivel;
                                        $nivelColor =
                                            $nivel === 'Primaria'
                                                ? 'bg-amber-500/10 text-amber-600 border-amber-200'
                                                : 'bg-indigo-500/10 text-indigo-600 border-indigo-200';
                                    @endphp
                                    <span
                                        class="inline-flex items-center gap-1 py-0.5 px-2 rounded text-[11px] font-semibold border {{ $nivelColor }}">
                                        <i data-lucide="{{ $nivel === 'Primaria' ? 'book-open' : 'graduation-cap' }}"
                                            class="size-3"></i>
                                        {{ $nivel }}
                                    </span>
                                @else
                                    <span class="text-default-400 text-xs italic">—</span>
                                @endif
                            </td>

                            {{-- Grado/Sección --}}
                            <td class="px-3.5 py-2.5 whitespace-nowrap text-sm">
                                @if ($matricula)
                                    <span
                                        class="inline-flex items-center py-0.5 px-2.5 rounded text-xs font-bold bg-default-100 border border-default-200 text-default-700">
                                        {{ $matricula->seccion->grado }}° "{{ $matricula->seccion->letra }}"
                                    </span>
                                @else
                                    <span class="text-default-400 text-xs italic">—</span>
                                @endif
                            </td>

                            {{-- Género --}}
                            <td class="px-3.5 py-2.5 whitespace-nowrap text-sm">
                                @if ($student->genero === 'M')
                                    <span
                                        class="inline-flex items-center gap-1 py-0.5 px-2 rounded text-[11px] font-semibold bg-info/10 text-info">
                                        M
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 py-0.5 px-2 rounded text-[11px] font-semibold bg-pink-500/10 text-pink-500">
                                        F
                                    </span>
                                @endif
                            </td>

                            {{-- Estado --}}
                            <td class="px-3.5 py-2.5 whitespace-nowrap">
                                @if ($matricula)
                                    @php
                                        $estadoConfig = match ($matricula->estado) {
                                            'Activo' => ['color' => 'success', 'icon' => 'check-circle'],
                                            'Retirado' => ['color' => 'danger', 'icon' => 'x-circle'],
                                            'Trasladado' => ['color' => 'warning', 'icon' => 'arrow-right-circle'],
                                            default => ['color' => 'default-400', 'icon' => 'minus-circle'],
                                        };
                                    @endphp
                                    <span
                                        class="inline-flex items-center gap-1.5 text-xs font-medium text-{{ $estadoConfig['color'] }}">
                                        <i data-lucide="{{ $estadoConfig['icon'] }}" class="size-3.5"></i>
                                        {{ $matricula->estado }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-default-400">
                                        <i data-lucide="minus-circle" class="size-3.5"></i>
                                        Sin matrícula
                                    </span>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td class="px-3.5 py-2.5">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('students.show', $student->id) }}"
                                        class="btn size-7.5 bg-info/10 text-info hover:bg-info hover:text-white transition-colors"
                                        title="Ver Ficha">
                                        <i data-lucide="eye" class="size-3.5"></i>
                                    </a>
                                    <a href="{{ route('students.qr', $student->id) }}" target="_blank"
                                        class="btn size-7.5 bg-primary/10 text-primary hover:bg-primary hover:text-white transition-colors"
                                        title="Carnet QR">
                                        <i data-lucide="qr-code" class="size-3.5"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isSuperAdmin() ? 8 : 7 }}" class="text-center py-12">
                                <div class="flex flex-col items-center">
                                    <div class="size-16 rounded-full bg-default-100 flex items-center justify-center mb-4">
                                        <i data-lucide="inbox" class="size-8 text-default-300"></i>
                                    </div>
                                    <h6 class="text-default-600 font-medium mb-1">No se encontraron estudiantes</h6>
                                    <p class="text-default-400 text-xs mb-4">
                                        @if (request()->hasAny(['search', 'ie', 'nivel', 'grado', 'estado']))
                                            Intente modificar los filtros de búsqueda.
                                        @else
                                            Importe estudiantes desde SIAGIE para comenzar.
                                        @endif
                                    </p>
                                    @if (!request()->hasAny(['search', 'ie', 'nivel', 'grado', 'estado']))
                                        <a href="{{ route('students.import') }}"
                                            class="btn btn-sm bg-primary text-white">
                                            <i data-lucide="upload" class="size-3.5 me-1"></i> Importar SIAGIE
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if ($students->hasPages())
            <div
                class="card-footer border-t border-default-200 px-4 py-3 flex flex-wrap items-center justify-between gap-3">
                <div class="text-xs text-default-500">
                    Mostrando <strong>{{ $students->firstItem() }}</strong> a <strong>{{ $students->lastItem() }}</strong>
                    de <strong>{{ $students->total() }}</strong> estudiantes
                </div>
                <div>
                    {{ $students->links('pagination::tailwind') }}
                </div>
            </div>
        @endif
    </div>
@endsection
