@extends('layouts.vertical', ['title' => 'Panel de Monitoreo Regional'])

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'SATA-QR', 'title' => 'Dashboard Administrativo'] )

    {{-- FILA 1: WIDGETS RECICLADOS (Estilo Ecommerce) --}}
    <div class="grid grid-cols-12 gap-5 mb-5">
        <div class="col-span-12 md:col-span-6 lg:col-span-3">
            <div class="card p-5">
                <div class="flex items-center gap-4">
                    <div class="size-12 rounded-md bg-primary/10 flex items-center justify-center text-primary">
                        <i data-lucide="school" class="size-6"></i>
                    </div>
                    <div class="flex-grow">
                        <h4 class="text-xl font-bold mb-1">{{ $stats['total_instituciones'] }}</h4>
                        <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Colegios Monitoreados</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-12 md:col-span-6 lg:col-span-3">
            <div class="card p-5">
                <div class="flex items-center gap-4">
                    <div class="size-12 rounded-md bg-info/10 flex items-center justify-center text-info">
                        <i data-lucide="users" class="size-6"></i>
                    </div>
                    <div class="flex-grow">
                        <h4 class="text-xl font-bold mb-1">{{ $stats['total_estudiantes'] }}</h4>
                        <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Población Estudiantil</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-12 md:col-span-6 lg:col-span-3">
            <div class="card p-5 border-l-4 border-danger">
                <div class="flex items-center gap-4">
                    <div class="size-12 rounded-md bg-danger/10 flex items-center justify-center text-danger">
                        <i data-lucide="alert-triangle" class="size-6"></i>
                    </div>
                    <div class="flex-grow">
                        <h4 class="text-xl font-bold mb-1 text-danger">{{ $stats['alertas_criticas'] }}</h4>
                        <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Riesgos Críticos</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-12 md:col-span-6 lg:col-span-3">
            <div class="card p-5">
                <div class="flex items-center gap-4">
                    <div class="size-12 rounded-md bg-success/10 flex items-center justify-center text-success">
                        <i data-lucide="check-circle" class="size-6"></i>
                    </div>
                    <div class="flex-grow">
                        <h4 class="text-xl font-bold mb-1">{{ $stats['intervenciones_activas'] }}</h4>
                        <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Casos en Seguimiento</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILA 2: GRÁFICOS RECICLADOS (Estilo Analytics) --}}
    <div class="grid grid-cols-12 gap-5 mb-5">
        <div class="col-span-12 lg:col-span-8">
            <div class="card">
                <div class="card-header flex justify-between items-center bg-slate-50/50">
                    <h6 class="card-title text-13">Tendencia de Asistencia Provincial</h6>
                    <div class="flex items-center gap-2">
                        <span class="size-2 rounded-full bg-primary animate-pulse"></span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase">En Tiempo Real</span>
                    </div>
                </div>
                <div class="card-body">
                    <div id="mainChart" class="apex-charts"></div>
                </div>
            </div>
        </div>
        <div class="col-span-12 lg:col-span-4">
            <div class="card h-full">
                <div class="card-header bg-slate-50/50">
                    <h6 class="card-title text-13">Distribución de Alertas por Riesgo</h6>
                </div>
                <div class="card-body flex flex-col justify-center">
                    <div id="donutChart" class="apex-charts"></div>
                    <div class="mt-6 grid grid-cols-3 gap-2 text-center">
                        @foreach($riskData as $risk)
                        <div class="p-2 rounded bg-slate-50 border border-slate-100">
                            <h6 class="text-sm font-bold {{ $risk->nivel_riesgo == 'Crítico' ? 'text-danger' : ($risk->nivel_riesgo == 'Moderado' ? 'text-warning' : 'text-primary') }}">
                                {{ $risk->count }}
                            </h6>
                            <p class="text-[9px] text-slate-400 uppercase font-black tracking-tighter">{{ $risk->nivel_riesgo }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILA 3: TABLA DE RANKING (Reciclamos el diseño de Top Sellers) --}}
    <div class="grid grid-cols-12 gap-5">
        <div class="col-span-12 lg:col-span-6">
            <div class="card h-full">
                <div class="card-header border-b border-slate-200 py-3 flex justify-between items-center">
                    <h6 class="card-title text-13">I.E. con Mayor Índice de Inasistencia</h6>
                    <i data-lucide="info" class="size-4 text-slate-300" data-bs-toggle="tooltip" title="Basado en faltas injustificadas del mes actual"></i>
                </div>
                <div class="card-body p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-50">
                                <tr class="text-[10px] font-black text-slate-500 uppercase">
                                    <th class="px-4 py-3 text-start">Institución Educativa</th>
                                    <th class="px-4 py-3 text-center">Faltas FI</th>
                                    <th class="px-4 py-3 text-end">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($topInasistencias as $item)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="size-8 rounded bg-danger/10 text-danger flex items-center justify-center font-bold text-xs uppercase">
                                                {{ substr($item->tenant->nombre, 0, 2) }}
                                            </div>
                                            <h6 class="text-xs font-bold text-slate-700 uppercase tracking-tight">{{ $item->tenant->nombre }}</h6>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-1 rounded bg-danger/10 text-danger text-[10px] font-black">{{ $item->faltas }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <button class="btn btn-sm btn-icon text-slate-400 hover:text-primary"><i data-lucide="external-link" class="size-4"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-6">
            <div class="card h-full">
                <div class="card-header bg-slate-50/50 py-3 flex justify-between items-center">
                    <h6 class="card-title text-13">Últimas Intervenciones Registradas</h6>
                    <a href="{{ route('interventions.index') }}" class="text-primary text-[10px] font-black uppercase">Ver Historial Completo</a>
                </div>
                <div class="card-body p-0">
                    <div class="p-4 space-y-4">
                        @foreach(\App\Models\IntervencionMultisectorial::with(['alerta.matricula.estudiante', 'aliado'])->latest()->limit(3)->get() as $int)
                        <div class="flex gap-4 p-3 rounded-lg border border-slate-100 hover:shadow-sm transition-all bg-white">
                            <div class="size-10 rounded-lg bg-slate-100 flex items-center justify-center shrink-0">
                                <i data-lucide="handshake" class="size-5 text-slate-400"></i>
                            </div>
                            <div class="flex-grow">
                                <div class="flex justify-between items-start mb-1">
                                    <h6 class="text-xs font-black text-slate-800 uppercase leading-none">{{ $int->alerta->matricula->estudiante->nombre_completo }}</h6>
                                    <span class="text-[9px] font-bold text-slate-400">{{ $int->fecha_intervencion->diffForHumans() }}</span>
                                </div>
                                <p class="text-[10px] text-slate-500 italic line-clamp-1">"{{ $int->descripcion_accion }}"</p>
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="px-1.5 py-0.5 rounded bg-info/10 text-info text-[8px] font-black uppercase tracking-tighter">{{ $int->aliado->nombre ?? 'UGEL' }}</span>
                                    <span class="px-1.5 py-0.5 rounded bg-success/10 text-success text-[8px] font-black uppercase tracking-tighter">Exitosa</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // 1. Gráfico de Tendencia (Reciclamos estilo Analytics Area)
    new ApexCharts(document.querySelector("#mainChart"), {
        series: [{ name: 'Asistencia (%)', data: @json($attendanceTrend->pluck('total')) }],
        chart: { type: 'area', height: 350, toolbar: {show: false}, fontFamily: 'Inter, sans-serif' },
        colors: ['#3b82f6'],
        stroke: { curve: 'smooth', width: 3 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1 } },
        xaxis: { categories: @json($attendanceTrend->pluck('dia')) },
        grid: { borderColor: '#f1f5f9' }
    }).render();

    // 2. Gráfico de Riesgo (Reciclamos estilo Donut Pro)
    new ApexCharts(document.querySelector("#donutChart"), {
        series: @json($riskData->pluck('count')),
        chart: { type: 'donut', height: 280 },
        labels: @json($riskData->pluck('nivel_riesgo')),
        colors: ['#ef4444', '#f59e0b', '#3b82f6'],
        legend: { position: 'bottom', fontSize: '11px', fontWeight: 700 },
        plotOptions: { pie: { donut: { size: '75%', labels: { show: true, total: { show: true, label: 'TOTAL ALERTAS', fontSize: '10px', fontWeight: 900 } } } } }
    }).render();
</script>
@endsection
