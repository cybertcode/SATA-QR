@extends('layouts.vertical', ['title' => 'Dashboard I.E.'])

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Mi Institución', 'title' => 'Panel de Control del Director'] )

    <div class="grid lg:grid-cols-4 md:grid-cols-2 grid-cols-1 gap-5 mb-5">
        <div class="card border-b-4 border-primary">
            <div class="card-body">
                <div class="flex items-center gap-3">
                    <div class="size-12 rounded bg-primary/10 flex items-center justify-center text-primary">
                        <i class="size-6" data-lucide="users"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 text-xl font-bold text-default-800">
                            {{ \App\Models\Estudiante::where('tenant_id', auth()->user()->tenant_id)->count() }}
                        </h5>
                        <p class="text-default-500 text-sm font-medium">Alumnos Matriculados</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-b-4 border-success">
            <div class="card-body">
                <div class="flex items-center gap-3">
                    <div class="size-12 rounded bg-success/10 flex items-center justify-center text-success">
                        <i class="size-6" data-lucide="check-square"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 text-xl font-bold text-default-800">
                            {{ \App\Models\Asistencia::where('tenant_id', auth()->user()->tenant_id)->where('fecha', date('Y-m-d'))->where('estado', 'P')->count() }}
                        </h5>
                        <p class="text-default-500 text-sm font-medium">Presentes Hoy</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-b-4 border-warning">
            <div class="card-body">
                <div class="flex items-center gap-3">
                    <div class="size-12 rounded bg-warning/15 flex items-center justify-center text-warning">
                        <i class="size-6" data-lucide="clock"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 text-xl font-bold text-default-800">
                            {{ \App\Models\Asistencia::where('tenant_id', auth()->user()->tenant_id)->where('fecha', date('Y-m-d'))->where('estado', 'T')->count() }}
                        </h5>
                        <p class="text-default-500 text-sm font-medium">Tardanzas Hoy</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-b-4 border-danger">
            <div class="card-body">
                <div class="flex items-center gap-3">
                    <div class="size-12 rounded bg-danger/10 flex items-center justify-center text-danger">
                        <i class="size-6" data-lucide="x-circle"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 text-xl font-bold text-default-800">
                            {{ \App\Models\Asistencia::where('tenant_id', auth()->user()->tenant_id)->where('fecha', date('Y-m-d'))->where('estado', 'FI')->count() }}
                        </h5>
                        <p class="text-default-500 text-sm font-medium">Faltas Injustificadas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-5 mb-5">
        <div class="card">
            <div class="card-header border-b border-default-200">
                <h6 class="card-title">Tendencia de Asistencia (Mi I.E.)</h6>
            </div>
            <div class="card-body">
                <div id="schoolAttendanceChart" class="apex-charts"></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header border-b border-default-200">
                <h6 class="card-title">Alumnos con Más Inasistencias (Top 5)</h6>
            </div>
            <div class="card-body p-0">
                <table class="min-w-full divide-y divide-default-200">
                    <thead class="bg-default-50">
                        <tr>
                            <th class="px-4 py-3 text-start text-xs font-semibold text-default-500 uppercase">Estudiante</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-default-500 uppercase">Faltas Mes</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-default-500 uppercase">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-default-200">
                        {{-- Ejemplo visual basado en la base de datos --}}
                        @foreach(\App\Models\Asistencia::where('tenant_id', auth()->user()->tenant_id)->where('estado', 'FI')->select('matricula_id', \Illuminate\Support\Facades\DB::raw('count(*) as total'))->groupBy('matricula_id')->orderBy('total', 'desc')->limit(5)->get() as $falta)
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium">{{ $falta->matricula->estudiante->nombre_completo }}</td>
                            <td class="px-4 py-3 text-center text-sm text-danger font-bold">{{ $falta->total }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($falta->total >= 3)
                                    <span class="px-2 py-1 bg-danger/10 text-danger rounded text-[10px] font-bold">ALERTA</span>
                                @else
                                    <span class="px-2 py-1 bg-warning/10 text-warning rounded text-[10px] font-bold">PREVENCIÓN</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    new ApexCharts(document.querySelector("#schoolAttendanceChart"), {
        series: [{ name: 'Porcentaje Asistencia', data: [95, 92, 98, 85, 90, 96, 99] }],
        chart: { type: 'line', height: 300, toolbar: {show: false}, fontFamily: 'Inter, sans-serif' },
        colors: ['#3b82f6'],
        stroke: { curve: 'smooth', width: 3 },
        markers: { size: 4 },
        xaxis: { categories: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] },
        yaxis: { max: 100, min: 0 }
    }).render();
</script>
@endsection