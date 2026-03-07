@extends('layouts.vertical', ['title' => 'Configuración Institucional'])

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Administración', 'title' => 'Configuración de la I.E.'] )

    <div class="grid lg:grid-cols-12 grid-cols-1 gap-6">
        <div class="lg:col-span-3 col-span-1">
            <div class="card">
                <div class="card-body">
                    <ul class="flex flex-col gap-2">
                        <li>
                            <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-md bg-primary/10 text-primary font-medium">
                                <i data-lucide="clock" class="size-4"></i> Horarios y Tolerancia
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-md text-default-600 hover:bg-default-100 font-medium">
                                <i data-lucide="image" class="size-4"></i> Identidad Visual
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-md text-default-600 hover:bg-default-100 font-medium">
                                <i data-lucide="calendar" class="size-4"></i> Días Feriados
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="lg:col-span-9 col-span-1">
            <div class="card">
                <div class="card-header border-b border-default-200">
                    <h6 class="card-title">Parámetros de Asistencia y Escáner QR</h6>
                </div>
                <div class="card-body">
                    @php
                        $config = \App\Models\ConfiguracionAsistencia::where('tenant_id', auth()->user()->tenant_id)->first();
                    @endphp

                    <form action="#" method="POST">
                        @csrf
                        <div class="grid lg:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block mb-2 text-sm font-semibold text-default-800">Hora de Ingreso Regular</label>
                                <div class="relative">
                                    <input type="time" class="form-input" value="{{ $config->hora_entrada_regular ?? '07:45:00' }}">
                                    <div class="absolute inset-y-0 end-0 flex items-center pe-3 pointer-events-none text-default-400">
                                        <i data-lucide="clock" class="size-4"></i>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-default-500">A partir de esta hora se calcula la tardanza.</p>
                            </div>
                            
                            <div>
                                <label class="block mb-2 text-sm font-semibold text-default-800">Minutos de Tolerancia</label>
                                <div class="relative">
                                    <input type="number" class="form-input" value="{{ $config->minutos_tolerancia ?? 15 }}" min="0" max="60">
                                    <div class="absolute inset-y-0 end-0 flex items-center pe-3 pointer-events-none text-default-400">
                                        <span class="text-xs font-bold">MIN</span>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-default-500">Tiempo de gracia antes de marcar como "Tarde".</p>
                            </div>
                        </div>

                        <div class="mb-6 p-4 rounded-md bg-warning/10 border border-warning/20">
                            <h6 class="text-sm font-bold text-warning mb-1 flex items-center gap-2">
                                <i data-lucide="alert-circle" class="size-4"></i> Generación Automática de Alertas
                            </h6>
                            <p class="text-xs text-warning/80 mb-3">El sistema detectará automáticamente a los alumnos en riesgo de deserción si superan el límite de inasistencias consecutivas.</p>
                            
                            <label class="block mb-1 text-sm font-semibold text-warning">Inasistencias consecutivas para Riesgo Crítico</label>
                            <input type="number" class="form-input border-warning/30 bg-white" value="3" min="1" max="10">
                        </div>

                        <div class="flex justify-end pt-4 border-t border-default-200">
                            <button type="submit" class="btn bg-primary text-white">
                                <i data-lucide="save" class="size-4 mr-2"></i> Guardar Configuración
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-6 border-danger/30 bg-danger/5">
                <div class="card-header border-b border-danger/20">
                    <h6 class="card-title text-danger">Acciones de Control Diario</h6>
                </div>
                <div class="card-body">
                    <p class="text-sm text-default-600 mb-4">Esta acción marcará automáticamente como <strong>Falta Injustificada (FI)</strong> a todos los estudiantes que no registraron su ingreso hoy. Utilice esta opción al finalizar el horario de entrada.</p>
                    <form action="{{ route('institution.close-day') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn bg-danger text-white py-2 px-6 font-bold" onclick="return confirm('¿Está seguro de cerrar la asistencia de hoy? Esta acción marcará las inasistencias automáticamente.')">
                            <i data-lucide="lock" class="size-4 mr-2"></i> Realizar Cierre de Asistencia Hoy
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection