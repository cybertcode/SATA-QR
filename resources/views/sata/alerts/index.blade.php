@extends('layouts.vertical', ['title' => 'Alertas Tempranas'])

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Monitoreo', 'title' => 'Gestión de Alertas'] )

    <div class="grid lg:grid-cols-3 grid-cols-1 gap-5 mb-5">
        <div class="col-span-1">
            <div class="grid md:grid-cols-2 grid-cols-1 gap-5">
                <div class="card border-l-4 border-primary">
                    <div class="card-body">
                        <div class="flex items-center gap-3">
                            <div class="size-12 rounded bg-primary/10 flex items-center justify-center text-primary">
                                <i class="size-6" data-lucide="alert-triangle"></i>
                            </div>
                            <div>
                                <h5 class="mb-1 text-xl font-bold text-default-800">{{ \App\Models\AlertaTemprana::count() }}</h5>
                                <p class="text-default-500 text-sm">Total Alertas</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card border-l-4 border-danger">
                    <div class="card-body">
                        <div class="flex items-center gap-3">
                            <div class="size-12 rounded bg-danger/10 flex items-center justify-center text-danger">
                                <i class="size-6" data-lucide="siren"></i>
                            </div>
                            <div>
                                <h5 class="mb-1 text-xl font-bold text-default-800">{{ \App\Models\AlertaTemprana::where('nivel_riesgo', 'Crítico')->count() }}</h5>
                                <p class="text-default-500 text-sm font-medium">Riesgo Crítico</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card border-l-4 border-warning">
                    <div class="card-body">
                        <div class="flex items-center gap-3">
                            <div class="size-12 rounded bg-warning/15 flex items-center justify-center text-warning">
                                <i class="size-6" data-lucide="loader"></i>
                            </div>
                            <div>
                                <h5 class="mb-1 text-xl font-bold text-default-800">{{ \App\Models\AlertaTemprana::where('estado_atencion', 'Pendiente')->count() }}</h5>
                                <p class="text-default-500 text-sm font-medium">Pendientes</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card border-l-4 border-success">
                    <div class="card-body">
                        <div class="flex items-center gap-3">
                            <div class="size-12 rounded bg-success/10 flex items-center justify-center text-success">
                                <i class="size-6" data-lucide="check-circle"></i>
                            </div>
                            <div>
                                <h5 class="mb-1 text-xl font-bold text-default-800">{{ \App\Models\AlertaTemprana::where('estado_atencion', 'Atendido')->count() }}</h5>
                                <p class="text-default-500 text-sm font-medium">Atendidos</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="lg:col-span-2 col-span-1">
            <div class="card h-full">
                <div class="card-header border-b border-default-200">
                    <h6 class="card-title">Evolución Semanal de Alertas</h6>
                </div>
                <div class="card-body">
                    <div id="alertsOverviewChart" class="apex-charts"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header flex justify-between items-center bg-default-50 border-b border-default-200">
            <h6 class="card-title">Listado de Alertas Activas</h6>
            <button class="btn btn-sm bg-primary text-white">
                <i class="size-4 me-1" data-lucide="download"></i>Exportar Informe
            </button>
        </div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-default-200">
                    <thead class="bg-default-100">
                        <tr class="text-xs font-semibold text-default-600 uppercase tracking-wider">
                            <th class="px-4 py-4 text-start">Estudiante / DNI</th>
                            <th class="px-4 py-4 text-start">Institución</th>
                            <th class="px-4 py-4 text-start">Nivel de Riesgo</th>
                            <th class="px-4 py-4 text-start">Motivo Principal</th>
                            <th class="px-4 py-4 text-start">Estado Atención</th>
                            <th class="px-4 py-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-default-200">
                        @forelse(\App\Models\AlertaTemprana::with(['matricula.estudiante', 'matricula.tenant'])->latest()->get() as $alerta)
                        <tr class="hover:bg-default-50 transition-colors">
                            <td class="px-4 py-4">
                                <h6 class="font-bold text-default-800 leading-none mb-1">{{ $alerta->matricula->estudiante->nombre_completo }}</h6>
                                <p class="text-[10px] text-default-500 font-mono">{{ $alerta->matricula->estudiante->dni }}</p>
                            </td>
                            <td class="px-4 py-4 text-sm text-default-600 font-medium">
                                {{ $alerta->matricula->tenant->nombre }}
                            </td>
                            <td class="px-4 py-4">
                                <span class="px-2 py-1 rounded text-xs font-bold border 
                                    {{ $alerta->nivel_riesgo == 'Crítico' ? 'bg-danger/10 text-danger border-danger/20' : ($alerta->nivel_riesgo == 'Moderado' ? 'bg-warning/10 text-warning border-warning/20' : 'bg-info/10 text-info border-info/20') }}">
                                    {{ $alerta->nivel_riesgo }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm text-default-600 max-w-xs truncate" title="{{ $alerta->motivo_acumulado }}">
                                {{ $alerta->motivo_acumulado }}
                            </td>
                            <td class="px-4 py-4">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest border
                                    {{ $alerta->estado_atencion == 'Pendiente' ? 'bg-default-100 text-default-700 border-default-300' : ($alerta->estado_atencion == 'Derivado' ? 'bg-primary/10 text-primary border-primary/30' : 'bg-success/10 text-success border-success/30') }}">
                                    {{ $alerta->estado_atencion }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <a href="{{ route('interventions.index') }}" class="btn btn-icon size-8 bg-info/10 text-info hover:bg-info hover:text-white" title="Ver Intervenciones">
                                    <i class="size-4" data-lucide="activity"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-default-400">
                                <i data-lucide="shield-check" class="size-12 mx-auto mb-3 opacity-30 text-success"></i>
                                <h6 class="text-lg font-medium text-default-600">No hay alertas activas</h6>
                                <p class="text-sm">El sistema de monitoreo no ha detectado riesgo de deserción reciente.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    new ApexCharts(document.querySelector("#alertsOverviewChart"), {
        series: [{ name: 'Alertas Generadas', data: [2, 5, 3, 8, 4, 10, 1] }],
        chart: { type: 'area', height: 260, toolbar: {show: false}, fontFamily: 'Inter, sans-serif' },
        colors: ['#ef4444'],
        stroke: { curve: 'smooth', width: 2 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0 } },
        dataLabels: { enabled: false },
        xaxis: { categories: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] }
    }).render();
</script>
@endsection