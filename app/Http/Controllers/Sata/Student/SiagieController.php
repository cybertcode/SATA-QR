<?php

namespace App\Http\Controllers\Sata\Student;

use App\Http\Controllers\Controller;
use App\Imports\SiagieStudentImport;
use App\Models\Tenant;
use App\Services\StudentImportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SiagieController extends Controller
{
    protected $importService;

    public function __construct(StudentImportService $importService)
    {
        $this->importService = $importService;
    }

    public function import()
    {
        $user = auth()->user();
        $instituciones = $user->isSuperAdmin()
            ? Tenant::orderBy('nombre')->get()
            : collect();

        return view('sata.students.import', compact('instituciones'));
    }

    public function process(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'archivo_siagie' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'anio_lectivo_id' => 'required|exists:anios_lectivos,id',
            'nivel' => 'required|in:Primaria,Secundaria',
        ];

        // SuperAdmin debe seleccionar la IE destino
        if ($user->isSuperAdmin()) {
            $rules['tenant_id'] = 'required|exists:tenants,id';
        }

        $request->validate($rules);

        $tenantId = $user->isSuperAdmin()
            ? $request->tenant_id
            : $user->tenant_id;

        // Leer Excel real con Maatwebsite\Excel
        $import = new SiagieStudentImport();
        Excel::import($import, $request->file('archivo_siagie'));

        $rows = $import->getRows();

        if (empty($rows)) {
            return back()->withErrors(['archivo_siagie' => 'El archivo no contiene datos válidos.']);
        }

        $count = $this->importService->import(
            $rows,
            $tenantId,
            $request->anio_lectivo_id,
            $request->nivel
        );

        return redirect()->route('students.index')
            ->with('success', "Se han procesado {$count} estudiantes correctamente desde el archivo SIAGIE.");
    }
}
