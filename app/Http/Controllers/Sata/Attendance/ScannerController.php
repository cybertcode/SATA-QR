<?php

namespace App\Http\Controllers\Sata\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Estudiante;
use App\Models\Matricula;
use App\Models\Asistencia;
use App\Models\ConfiguracionAsistencia;
use Carbon\Carbon;

class ScannerController extends Controller
{
    public function index()
    {
        return view('sata.kiosk.index');
    }

    public function process(Request $request)
    {
        $request->validate(['qr_uuid' => 'required|uuid']);

        // 1. Buscar Estudiante y su Matrícula Activa
        $student = Estudiante::where('qr_uuid', $request->qr_uuid)->first();
        if (!$student) return response()->json(['success' => false, 'message' => 'Código QR no reconocido.'], 404);

        $matricula = Matricula::where('estudiante_id', $student->id)
            ->where('estado', 'Activo')
            ->first();
        
        if (!$matricula) return response()->json(['success' => false, 'message' => 'Estudiante sin matrícula activa.'], 403);

        // 2. Determinar Estado (Puntual/Tarde)
        $config = ConfiguracionAsistencia::where('tenant_id', $student->tenant_id)->first();
        $horaEntrada = Carbon::createFromFormat('H:i:s', $config->hora_entrada_regular ?? '07:45:00');
        $tolerancia = $config->minutos_tolerancia ?? 15;
        $ahora = Carbon::now();

        $estado = ($ahora->lte($horaEntrada->addMinutes($tolerancia))) ? Asistencia::PRESENTE : Asistencia::TARDE;

        // 3. Registrar Asistencia
        $asistencia = Asistencia::updateOrCreate(
            ['matricula_id' => $matricula->id, 'fecha' => $ahora->toDateString()],
            [
                'tenant_id' => $student->tenant_id,
                'registrado_por' => auth()->id(),
                'hora_ingreso' => $ahora->toTimeString(),
                'estado' => $estado
            ]
        );

        return response()->json([
            'success' => true,
            'student' => $student->nombre_completo,
            'dni' => $student->dni,
            'hora' => $ahora->format('H:i A'),
            'estado' => $estado == 'P' ? 'PRESENTE' : 'TARDE',
            'vulnerabilidad' => $student->vulnerabilidad
        ]);
    }
}
