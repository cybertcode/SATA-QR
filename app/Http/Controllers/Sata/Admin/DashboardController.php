<?php

namespace App\Http\Controllers\Sata\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Estudiante;
use App\Models\AlertaTemprana;
use App\Models\Asistencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // KPIs Globales (UGEL Huacaybamba)
        $stats = [
            'total_instituciones' => Tenant::count(),
            'total_estudiantes' => Estudiante::count(),
            'alertas_criticas' => AlertaTemprana::where('nivel_riesgo', 'Crítico')->count(),
            'intervenciones_activas' => AlertaTemprana::where('estado_atencion', '!=', 'Atendido')->count(),
        ];

        // Tendencia de Asistencia (Últimos 7 días con nombres en Español)
        $attendanceTrend = Asistencia::select('fecha', DB::raw('count(*) as total'))
            ->where('estado', 'P')
            ->groupBy('fecha')
            ->orderBy('fecha', 'desc')
            ->limit(7)
            ->get()
            ->reverse()
            ->map(function($item) {
                return [
                    'dia' => Carbon::parse($item->fecha)->translatedFormat('D d'),
                    'total' => $item->total
                ];
            });

        // Top 5 I.E. con mayor inasistencia (Reciclamos lógica de "Top Products")
        $topInasistencias = Asistencia::select('tenant_id', DB::raw('count(*) as faltas'))
            ->where('estado', 'FI')
            ->groupBy('tenant_id')
            ->with('tenant:id,nombre')
            ->orderBy('faltas', 'desc')
            ->limit(5)
            ->get();

        // Distribución de Riesgo para Donut Chart
        $riskData = AlertaTemprana::select('nivel_riesgo', DB::raw('count(*) as count'))
            ->groupBy('nivel_riesgo')
            ->get();

        return view('sata.admin.dashboard', compact('stats', 'attendanceTrend', 'topInasistencias', 'riskData'));
    }
}
