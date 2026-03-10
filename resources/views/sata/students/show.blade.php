@extends('layouts.vertical', ['title' => 'Ficha del Estudiante'])

@section('content')
    @include('layouts.partials/page-title', [
        'subtitle' => 'Alumnado',
        'title' => 'Ficha Integral de Seguimiento',
    ])

    {{-- Botón Volver --}}
    <div class="mb-5">
        <a href="{{ route('students.index') }}"
            class="btn btn-sm bg-default-200 text-default-600 hover:bg-default-300 inline-flex items-center gap-1.5">
            <i data-lucide="arrow-left" class="size-3.5"></i> Volver al Listado
        </a>
    </div>

    <div class="grid lg:grid-cols-12 grid-cols-1 gap-6">
        {{-- COLUMNA IZQUIERDA: INFORMACIÓN BÁSICA --}}
        <div class="lg:col-span-4 col-span-1 space-y-6">
            {{-- Tarjeta Perfil --}}
            <div class="card overflow-hidden">
                @php
                    $avatarColor = $student->genero === 'M' ? 'bg-info/10 text-info' : 'bg-pink-500/10 text-pink-500';
                    $headerBg =
                        $student->genero === 'M' ? 'from-info/5 to-transparent' : 'from-pink-500/5 to-transparent';
                @endphp
                <div class="bg-gradient-to-b {{ $headerBg }} pt-8 pb-4 px-6 text-center">
                    <div
                        class="size-24 rounded-full {{ $avatarColor }} flex items-center justify-center text-3xl font-bold mx-auto mb-4 ring-4 ring-white shadow-lg">
                        {{ substr($student->nombres, 0, 1) }}{{ substr($student->apellido_paterno, 0, 1) }}
                    </div>
                    <h5 class="text-lg font-bold text-default-800">{{ $student->apellido_paterno }}
                        {{ $student->apellido_materno }}</h5>
                    <p class="text-sm text-default-600 font-medium">{{ $student->nombres }}</p>
                    <div class="flex items-center justify-center gap-2 mt-2">
                        <span class="font-mono text-xs text-default-500 bg-default-100 px-2 py-0.5 rounded">DNI:
                            {{ $student->dni }}</span>
                        @if ($student->genero === 'M')
                            <span
                                class="inline-flex items-center gap-1 py-0.5 px-2 rounded text-[10px] font-bold bg-info/10 text-info">MASCULINO</span>
                        @else
                            <span
                                class="inline-flex items-center gap-1 py-0.5 px-2 rounded text-[10px] font-bold bg-pink-500/10 text-pink-500">FEMENINO</span>
                        @endif
                    </div>
                </div>

                <div class="card-body pt-4">
                    {{-- Estado Académico --}}
                    <div class="space-y-3">
                        <div
                            class="flex justify-between items-center p-3 bg-default-50 rounded-lg border border-default-100">
                            <span class="text-[10px] text-default-500 uppercase font-bold tracking-wide">Estado
                                Académico</span>
                            @if ($student->matriculaActual)
                                @php
                                    $estadoColor = match ($student->matriculaActual->estado) {
                                        'Activo' => 'bg-success/10 text-success',
                                        'Retirado' => 'bg-danger/10 text-danger',
                                        'Trasladado' => 'bg-warning/10 text-warning',
                                        default => 'bg-default-100 text-default-500',
                                    };
                                @endphp
                                <span
                                    class="px-2 py-0.5 rounded {{ $estadoColor }} text-[10px] font-bold">{{ strtoupper($student->matriculaActual->estado) }}</span>
                            @else
                                <span class="px-2 py-0.5 rounded bg-default-100 text-default-500 text-[10px] font-bold">SIN
                                    MATRÍCULA</span>
                            @endif
                        </div>

                        @if ($student->fecha_nacimiento)
                            <div
                                class="flex justify-between items-center p-3 bg-default-50 rounded-lg border border-default-100">
                                <span class="text-[10px] text-default-500 uppercase font-bold tracking-wide">Fecha
                                    Nac.</span>
                                <span
                                    class="text-xs text-default-700 font-medium">{{ $student->fecha_nacimiento->format('d/m/Y') }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Datos de Matrícula --}}
                    @php $m = $student->matriculaActual; @endphp
                    @if ($m)
                        <div class="mt-6">
                            <h6
                                class="text-[10px] text-default-400 uppercase font-bold tracking-wider mb-3 flex items-center gap-2">
                                <i data-lucide="book-open" class="size-3"></i>
                                Datos de Matrícula {{ now()->year }}
                            </h6>
                            <div class="space-y-2.5">
                                <div class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-default-50 transition-colors">
                                    <div class="size-8 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                                        <i data-lucide="building" class="size-4 text-primary"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-default-400 uppercase">Institución</p>
                                        <p class="text-sm text-default-700 font-medium">{{ $student->tenant->nombre }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-default-50 transition-colors">
                                    <div class="size-8 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                                        <i data-lucide="graduation-cap" class="size-4 text-primary"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-default-400 uppercase">Nivel Educativo</p>
                                        <p class="text-sm text-default-700 font-medium">
                                            {{ $m->seccion->nivelEducativo->nivel }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-default-50 transition-colors">
                                    <div class="size-8 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                                        <i data-lucide="layout-grid" class="size-4 text-primary"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-default-400 uppercase">Grado y Sección</p>
                                        <p class="text-sm text-default-700 font-medium">{{ $m->seccion->grado }}°
                                            "{{ $m->seccion->letra }}"</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Acciones --}}
                    <div class="mt-6 pt-4 border-t border-default-200 space-y-2">
                        <a href="{{ route('students.qr', $student->id) }}" target="_blank"
                            class="btn bg-primary text-white w-full py-2.5 font-bold inline-flex items-center justify-center gap-2">
                            <i data-lucide="qr-code" class="size-4"></i> Imprimir Carnet QR
                        </a>
                        <a href="{{ route('students.index') }}"
                            class="btn bg-default-100 text-default-600 w-full py-2 text-sm inline-flex items-center justify-center gap-2 hover:bg-default-200">
                            <i data-lucide="arrow-left" class="size-3.5"></i> Regresar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA: HISTORIAL Y GRÁFICOS --}}
        <div class="lg:col-span-8 col-span-1 space-y-6">
            @if ($student->matriculaActual)
                @php $asistencias = $student->matriculaActual->asistencias; @endphp

                {{-- Resumen de Asistencia --}}
                <div class="card">
                    <div class="card-header border-b border-default-200 bg-default-50/50 flex items-center gap-2">
                        <i data-lucide="calendar-check" class="size-4 text-primary"></i>
                        <h6 class="card-title text-sm">Resumen de Asistencia (Últimos 30 días)</h6>
                    </div>
                    <div class="card-body">
                        {{-- Stat Cards --}}
                        <div class="grid md:grid-cols-4 gap-4 mb-6">
                            @php
                                $presentes = $asistencias->where('estado', 'P')->count();
                                $tardanzas = $asistencias->where('estado', 'T')->count();
                                $faltas = $asistencias->where('estado', 'FI')->count();
                                $porcentaje =
                                    $asistencias->count() > 0 ? round(($presentes / $asistencias->count()) * 100) : 0;
                            @endphp
                            <div class="p-4 rounded-lg border border-success/20 bg-success/5 text-center">
                                <h4 class="text-2xl font-bold text-success">{{ $presentes }}</h4>
                                <p class="text-[10px] text-success/80 font-bold uppercase tracking-wide mt-1">Presentes</p>
                            </div>
                            <div class="p-4 rounded-lg border border-warning/20 bg-warning/5 text-center">
                                <h4 class="text-2xl font-bold text-warning">{{ $tardanzas }}</h4>
                                <p class="text-[10px] text-warning/80 font-bold uppercase tracking-wide mt-1">Tardanzas</p>
                            </div>
                            <div class="p-4 rounded-lg border border-danger/20 bg-danger/5 text-center">
                                <h4 class="text-2xl font-bold text-danger">{{ $faltas }}</h4>
                                <p class="text-[10px] text-danger/80 font-bold uppercase tracking-wide mt-1">Faltas</p>
                            </div>
                            <div class="p-4 rounded-lg border border-primary/20 bg-primary/5 text-center">
                                <h4 class="text-2xl font-bold text-primary">{{ $porcentaje }}%</h4>
                                <p class="text-[10px] text-primary/80 font-bold uppercase tracking-wide mt-1">Asistencia</p>
                            </div>
                        </div>

                        {{-- Barra de Progreso Visual --}}
                        @if ($asistencias->count() > 0)
                            <div class="mb-6">
                                <div class="flex gap-0.5 h-3 rounded-full overflow-hidden bg-default-100">
                                    @if ($presentes > 0)
                                        <div class="bg-success transition-all"
                                            style="width: {{ ($presentes / $asistencias->count()) * 100 }}%"
                                            title="{{ $presentes }} Presentes"></div>
                                    @endif
                                    @if ($tardanzas > 0)
                                        <div class="bg-warning transition-all"
                                            style="width: {{ ($tardanzas / $asistencias->count()) * 100 }}%"
                                            title="{{ $tardanzas }} Tardanzas"></div>
                                    @endif
                                    @if ($faltas > 0)
                                        <div class="bg-danger transition-all"
                                            style="width: {{ ($faltas / $asistencias->count()) * 100 }}%"
                                            title="{{ $faltas }} Faltas"></div>
                                    @endif
                                </div>
                                <div class="flex items-center justify-center gap-4 mt-2 text-[10px] text-default-500">
                                    <span class="flex items-center gap-1"><span
                                            class="size-2 rounded-full bg-success"></span> Presente</span>
                                    <span class="flex items-center gap-1"><span
                                            class="size-2 rounded-full bg-warning"></span> Tardanza</span>
                                    <span class="flex items-center gap-1"><span
                                            class="size-2 rounded-full bg-danger"></span> Falta</span>
                                </div>
                            </div>
                        @endif

                        {{-- Tabla de Asistencia --}}
                        @if ($asistencias->count() > 0)
                            <div class="overflow-x-auto rounded-lg border border-default-200">
                                <table class="w-full text-xs">
                                    <thead class="bg-default-100/50">
                                        <tr>
                                            <th
                                                class="px-3 py-2.5 text-start text-[10px] font-medium text-default-500 uppercase tracking-wider">
                                                Fecha</th>
                                            <th
                                                class="px-3 py-2.5 text-start text-[10px] font-medium text-default-500 uppercase tracking-wider">
                                                Hora Ingreso</th>
                                            <th
                                                class="px-3 py-2.5 text-center text-[10px] font-medium text-default-500 uppercase tracking-wider">
                                                Estado</th>
                                            <th
                                                class="px-3 py-2.5 text-start text-[10px] font-medium text-default-500 uppercase tracking-wider">
                                                Registrado por</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-default-100">
                                        @foreach ($asistencias as $asistencia)
                                            <tr class="hover:bg-default-50 transition-colors">
                                                <td class="px-3 py-2 font-medium text-default-700">
                                                    {{ $asistencia->fecha->format('d/m/Y') }}</td>
                                                <td class="px-3 py-2 font-mono text-default-600">
                                                    {{ $asistencia->hora_ingreso ?? '--:--' }}</td>
                                                <td class="px-3 py-2 text-center">
                                                    @php
                                                        $estConf = match ($asistencia->estado) {
                                                            'P' => [
                                                                'bg' => 'bg-success/10 text-success',
                                                                'label' => 'Presente',
                                                            ],
                                                            'T' => [
                                                                'bg' => 'bg-warning/10 text-warning',
                                                                'label' => 'Tardanza',
                                                            ],
                                                            default => [
                                                                'bg' => 'bg-danger/10 text-danger',
                                                                'label' => 'Falta',
                                                            ],
                                                        };
                                                    @endphp
                                                    <span
                                                        class="px-2 py-0.5 rounded font-bold {{ $estConf['bg'] }}">{{ $estConf['label'] }}</span>
                                                </td>
                                                <td class="px-3 py-2 text-default-500">
                                                    {{ $asistencia->registrador->name ?? 'Sistema' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div
                                    class="size-12 rounded-full bg-default-100 flex items-center justify-center mx-auto mb-3">
                                    <i data-lucide="calendar-x" class="size-6 text-default-300"></i>
                                </div>
                                <p class="text-sm text-default-500">Sin registros de asistencia en los últimos 30 días.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Alertas Tempranas --}}
                @php
                    $alertas = \App\Models\AlertaTemprana::where('matricula_id', $student->matriculaActual->id)
                        ->with('intervenciones')
                        ->latest()
                        ->get();
                @endphp
                @if ($alertas->count() > 0)
                    <div class="card">
                        <div class="card-header border-b border-default-200 bg-danger/5 flex items-center gap-2">
                            <i data-lucide="shield-alert" class="size-4 text-danger"></i>
                            <h6 class="card-title text-sm text-danger font-bold">Alertas y Seguimiento Multisectorial</h6>
                            <span
                                class="ms-auto inline-flex items-center py-0.5 px-2 rounded-full text-[10px] font-bold bg-danger/10 text-danger">
                                {{ $alertas->count() }} {{ $alertas->count() === 1 ? 'alerta' : 'alertas' }}
                            </span>
                        </div>
                        <div class="card-body p-6">
                            @foreach ($alertas as $alerta)
                                <div class="mb-6 last:mb-0 p-4 border border-default-200 rounded-lg bg-default-50/50">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            @php
                                                $riesgoColor = match ($alerta->nivel_riesgo) {
                                                    'ALTO' => 'bg-danger text-white',
                                                    'MEDIO' => 'bg-warning text-white',
                                                    default => 'bg-info text-white',
                                                };
                                            @endphp
                                            <span
                                                class="px-2 py-0.5 rounded {{ $riesgoColor }} text-[10px] font-bold uppercase">
                                                ALERTA {{ $alerta->nivel_riesgo }}
                                            </span>
                                            <h6 class="text-sm font-bold mt-2 text-default-800">
                                                {{ $alerta->motivo_acumulado }}</h6>
                                        </div>
                                        <span
                                            class="text-xs text-default-400 italic shrink-0 ms-4">{{ $alerta->fecha_emision->format('d M, Y') }}</span>
                                    </div>

                                    @if ($alerta->intervenciones->count() > 0)
                                        <div class="space-y-3 ml-3 border-l-2 border-primary/20 pl-4">
                                            @foreach ($alerta->intervenciones as $int)
                                                <div class="relative">
                                                    <div
                                                        class="absolute -left-[21px] top-1 size-2.5 rounded-full bg-primary border-2 border-white">
                                                    </div>
                                                    <p class="text-xs font-bold text-default-800">
                                                        {{ $int->fecha_intervencion->format('d/m/Y') }} —
                                                        <span
                                                            class="text-primary">{{ $int->aliado->nombre ?? 'UGEL' }}</span>
                                                    </p>
                                                    <p class="text-xs text-default-600 italic mt-0.5">
                                                        "{{ $int->descripcion_accion }}"</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-xs text-default-400 italic ml-3">Sin intervenciones registradas aún.
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @else
                <div class="card">
                    <div class="card-body text-center py-12">
                        <div class="size-16 rounded-full bg-default-100 flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="inbox" class="size-8 text-default-300"></i>
                        </div>
                        <h6 class="text-default-600 font-medium mb-1">Sin matrícula activa</h6>
                        <p class="text-default-400 text-xs">Este estudiante no tiene matrícula activa para el año en curso
                            ({{ now()->year }}).</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
