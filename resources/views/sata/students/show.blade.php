@extends('layouts.vertical', ['title' => 'Ficha del Estudiante'])

@section('content')
    @include('layouts.partials/page-title', [
        'subtitle' => 'Alumnado',
        'title' => 'Ficha Integral de Seguimiento',
    ])

    <div class="grid lg:grid-cols-12 grid-cols-1 gap-6">
        {{-- COLUMNA IZQUIERDA: INFORMACIÓN BÁSICA --}}
        <div class="lg:col-span-4 col-span-1">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="text-center mb-6">
                        <div
                            class="size-24 rounded-full bg-primary/10 flex items-center justify-center text-primary text-3xl font-bold mx-auto mb-4">
                            {{ substr($student->nombres, 0, 1) }}{{ substr($student->apellido_paterno, 0, 1) }}
                        </div>
                        <h5 class="text-xl font-bold text-default-800">{{ $student->nombre_completo }}</h5>
                        <p class="text-sm text-default-500 font-mono">DNI: {{ $student->dni }}</p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 bg-default-50 rounded-md">
                            <span class="text-xs text-default-500 uppercase font-bold">Estado Académico</span>
                            @if ($student->matriculaActual)
                                <span
                                    class="px-2 py-0.5 rounded {{ $student->matriculaActual->estado === 'Activo' ? 'bg-success/10 text-success' : 'bg-warning/10 text-warning' }} text-[10px] font-bold">{{ strtoupper($student->matriculaActual->estado) }}</span>
                            @else
                                <span class="px-2 py-0.5 rounded bg-default-100 text-default-500 text-[10px] font-bold">SIN
                                    MATRÍCULA</span>
                            @endif
                        </div>
                        <div class="flex justify-between items-center p-3 bg-default-50 rounded-md">
                            <span class="text-xs text-default-500 uppercase font-bold">Género</span>
                            <span class="px-2 py-0.5 rounded bg-primary/10 text-primary text-[10px] font-bold">
                                {{ $student->genero === 'M' ? 'MASCULINO' : 'FEMENINO' }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h6 class="text-xs text-default-400 uppercase font-bold mb-4 border-b pb-2">Datos de la Matrícula
                        </h6>
                        @php $m = $student->matriculaActual; @endphp
                        @if ($m)
                            <ul class="space-y-3">
                                <li class="flex items-center gap-3">
                                    <i data-lucide="building" class="size-4 text-primary"></i>
                                    <span class="text-sm text-default-700">{{ $student->tenant->nombre }}</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <i data-lucide="graduation-cap" class="size-4 text-primary"></i>
                                    <span class="text-sm text-default-700">{{ $m->seccion->nivelEducativo->nivel }}</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <i data-lucide="layout-grid" class="size-4 text-primary"></i>
                                    <span class="text-sm text-default-700">{{ $m->seccion->grado }}°
                                        "{{ $m->seccion->letra }}"</span>
                                </li>
                            </ul>
                        @endif
                    </div>

                    <div class="mt-8 pt-6 border-t border-default-200">
                        <a href="{{ route('students.qr', $student->id) }}" target="_blank"
                            class="btn bg-primary text-white w-full py-2.5 font-bold">
                            <i data-lucide="qr-code" class="size-4 mr-2"></i> Imprimir Carnet QR
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA: HISTORIAL Y GRÁFICOS --}}
        <div class="lg:col-span-8 col-span-1 space-y-6">
            {{-- Resumen de Asistencia --}}
            @if ($student->matriculaActual)
                @php $asistencias = $student->matriculaActual->asistencias; @endphp
                <div class="card">
                    <div class="card-header border-b border-default-200 bg-default-50">
                        <h6 class="card-title text-sm">Resumen de Asistencia (Últimos 30 días)</h6>
                    </div>
                    <div class="card-body">
                        <div class="grid md:grid-cols-4 gap-4 mb-6">
                            <div class="p-4 rounded-md border border-success/20 bg-success/5 text-center">
                                <h4 class="text-xl font-bold text-success">
                                    {{ $asistencias->where('estado', 'P')->count() }}</h4>
                                <p class="text-[10px] text-success/80 font-bold uppercase">Presentes</p>
                            </div>
                            <div class="p-4 rounded-md border border-warning/20 bg-warning/5 text-center">
                                <h4 class="text-xl font-bold text-warning">
                                    {{ $asistencias->where('estado', 'T')->count() }}</h4>
                                <p class="text-[10px] text-warning/80 font-bold uppercase">Tardanzas</p>
                            </div>
                            <div class="p-4 rounded-md border border-danger/20 bg-danger/5 text-center">
                                <h4 class="text-xl font-bold text-danger">
                                    {{ $asistencias->where('estado', 'FI')->count() }}</h4>
                                <p class="text-[10px] text-danger/80 font-bold uppercase">Faltas</p>
                            </div>
                            <div class="p-4 rounded-md border border-primary/20 bg-primary/5 text-center">
                                <h4 class="text-xl font-bold text-primary">
                                    {{ $asistencias->count() > 0 ? round(($asistencias->where('estado', 'P')->count() / $asistencias->count()) * 100) : 0 }}%
                                </h4>
                                <p class="text-[10px] text-primary/80 font-bold uppercase">Asistencia</p>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-xs">
                                <thead class="bg-default-100 text-default-600">
                                    <tr>
                                        <th class="px-3 py-2 text-start">Fecha</th>
                                        <th class="px-3 py-2 text-start">Hora Ingreso</th>
                                        <th class="px-3 py-2 text-center">Estado</th>
                                        <th class="px-3 py-2 text-start">Registrado por</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-default-100">
                                    @foreach ($asistencias as $asistencia)
                                        <tr>
                                            <td class="px-3 py-2 font-medium">{{ $asistencia->fecha->format('d/m/Y') }}
                                            </td>
                                            <td class="px-3 py-2 font-mono">{{ $asistencia->hora_ingreso ?? '--:--' }}</td>
                                            <td class="px-3 py-2 text-center">
                                                <span
                                                    class="px-2 py-0.5 rounded font-bold
                                            {{ $asistencia->estado == 'P' ? 'bg-success/10 text-success' : ($asistencia->estado == 'T' ? 'bg-warning/10 text-warning' : 'bg-danger/10 text-danger') }}">
                                                    {{ $asistencia->estado }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2 text-default-500">
                                                {{ $asistencia->registrador->name ?? 'Sistema' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Línea de Tiempo de Intervenciones (Si tiene alertas) --}}
                @php
                    $alertas = \App\Models\AlertaTemprana::where('matricula_id', $student->matriculaActual->id)
                        ->with('intervenciones')
                        ->latest()
                        ->get();
                @endphp
                @if ($alertas->count() > 0)
                    <div class="card">
                        <div class="card-header border-b border-default-200 bg-danger/5">
                            <h6 class="card-title text-danger font-bold">Alertas y Seguimiento Multisectorial</h6>
                        </div>
                        <div class="card-body p-6">
                            @foreach ($alertas as $alerta)
                                <div class="mb-6 p-4 border rounded-md bg-default-50">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <span
                                                class="px-2 py-0.5 rounded bg-danger text-white text-[10px] font-bold uppercase">ALERTA
                                                {{ $alerta->nivel_riesgo }}</span>
                                            <h6 class="text-sm font-bold mt-2">{{ $alerta->motivo_acumulado }}</h6>
                                        </div>
                                        <span
                                            class="text-xs text-default-400 italic">{{ $alerta->fecha_emision->format('d M, Y') }}</span>
                                    </div>

                                    <div class="space-y-4 ml-4 border-l-2 border-default-200 pl-4">
                                        @foreach ($alerta->intervenciones as $int)
                                            <div>
                                                <p class="text-xs font-bold text-default-800">
                                                    {{ $int->fecha_intervencion->format('d/m/Y') }} -
                                                    {{ $int->aliado->nombre ?? 'UGEL' }}</p>
                                                <p class="text-xs text-default-600 italic mt-1">
                                                    "{{ $int->descripcion_accion }}"</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @else
                <div class="card">
                    <div class="card-body text-center py-10 text-default-500">
                        <i data-lucide="inbox" class="size-10 mx-auto mb-3 opacity-20"></i>
                        <p>Este estudiante no tiene matrícula activa para el año en curso.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
