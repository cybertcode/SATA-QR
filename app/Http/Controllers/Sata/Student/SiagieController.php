<?php

namespace App\Http\Controllers\Sata\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\StudentImportService;

class SiagieController extends Controller
{
    protected $importService;

    public function __construct(StudentImportService $importService)
    {
        $this->importService = $importService;
    }

    public function import()
    {
        return view('sata.students.import');
    }

    public function process(Request $request)
    {
        $request->validate([
            'archivo_siagie' => 'required|file|max:5120',
            'anio_lectivo_id' => 'required|exists:anios_lectivos,id'
        ]);

        // Simulación de lectura de Excel para el demo funcional
        $dataSimulada = [
            ['dni' => '77889900', 'nombres' => 'MARIA', 'paterno' => 'ROJAS', 'materno' => 'LUNA', 'genero' => 'F', 'grado' => '3', 'seccion' => 'B'],
            ['dni' => '11223344', 'nombres' => 'CARLOS', 'paterno' => 'SOTO', 'materno' => 'MEZA', 'genero' => 'M', 'grado' => '3', 'seccion' => 'B'],
        ];

        $count = $this->importService->import(
            $dataSimulada, 
            auth()->user()->tenant_id, 
            $request->anio_lectivo_id,
            'Secundaria' // Detectado del Excel en producción
        );

        return redirect()->route('students.index')->with('success', "Se han procesado $count estudiantes correctamente.");
    }
}
