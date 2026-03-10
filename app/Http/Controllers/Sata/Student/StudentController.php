<?php

namespace App\Http\Controllers\Sata\Student;

use App\Exports\StudentsExport;
use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use App\Models\Matricula;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Estudiante::with(['matriculaActual.seccion.nivelEducativo', 'tenant']);

        // Filtro por IE (solo SuperAdmin puede filtrar)
        if ($request->filled('ie') && $user->isSuperAdmin()) {
            $query->where('tenant_id', $request->ie);
        }

        // Búsqueda por DNI o nombre
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('dni', 'like', "%{$search}%")
                    ->orWhere('nombres', 'like', "%{$search}%")
                    ->orWhere('apellido_paterno', 'like', "%{$search}%")
                    ->orWhere('apellido_materno', 'like', "%{$search}%");
            });
        }

        // Filtro por nivel educativo
        if ($request->filled('nivel')) {
            $nivel = $request->nivel;
            $query->whereHas('matriculaActual.seccion.nivelEducativo', function ($q) use ($nivel) {
                $q->where('nivel', $nivel);
            });
        }

        // Filtro por grado
        if ($request->filled('grado')) {
            $grado = $request->grado;
            $query->whereHas('matriculaActual.seccion', function ($q) use ($grado) {
                $q->where('grado', $grado);
            });
        }

        // Filtro por estado de matrícula
        if ($request->filled('estado')) {
            $estado = $request->estado;
            if ($estado === 'sin_matricula') {
                $query->doesntHave('matriculaActual');
            } else {
                $query->whereHas('matriculaActual', function ($q) use ($estado) {
                    $q->where('estado', $estado);
                });
            }
        }

        $students = $query->latest()->paginate(15)->withQueryString();

        // Estadísticas
        $baseQuery = Estudiante::query();
        if ($request->filled('ie') && $user->isSuperAdmin()) {
            $baseQuery->where('tenant_id', $request->ie);
        }

        $totalEstudiantes = (clone $baseQuery)->count();
        $conMatricula = (clone $baseQuery)->has('matriculaActual')->count();
        $sinMatricula = $totalEstudiantes - $conMatricula;
        $masculinos = (clone $baseQuery)->where('genero', 'M')->count();
        $femeninos = (clone $baseQuery)->where('genero', 'F')->count();

        $totalInstituciones = Tenant::count();
        $stats = compact('totalEstudiantes', 'conMatricula', 'sinMatricula', 'masculinos', 'femeninos', 'totalInstituciones');

        $tenants = $user->isSuperAdmin() ? Tenant::orderBy('nombre')->get() : collect();

        return view('sata.students.index', compact('students', 'tenants', 'stats'));
    }

    public function show($id)
    {
        $student = Estudiante::with([
            'tenant',
            'matriculaActual.seccion.nivelEducativo',
            'matriculaActual.asistencias' => fn($q) => $q->latest('fecha')->limit(30),
            'matriculaActual.asistencias.registrador',
        ])->findOrFail($id);

        return view('sata.students.show', compact('student'));
    }

    public function generateQr($id)
    {
        $student = Estudiante::with(['tenant', 'matriculaActual.seccion.nivelEducativo'])->findOrFail($id);

        return view('sata.students.qr', compact('student'));
    }

    public function massQr(Request $request, string $tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);

        $query = Estudiante::with(['matriculaActual.seccion.nivelEducativo'])
            ->where('tenant_id', $tenantId)
            ->has('matriculaActual')
            ->whereHas('matriculaActual', fn($q) => $q->where('estado', 'Activo'));

        if ($request->filled('nivel')) {
            $nivel = $request->nivel;
            $query->whereHas('matriculaActual.seccion.nivelEducativo', fn($q) => $q->where('nivel', $nivel));
        }

        if ($request->filled('grado')) {
            $grado = $request->grado;
            $query->whereHas('matriculaActual.seccion', fn($q) => $q->where('grado', $grado));
        }

        $students = $query->orderBy('apellido_paterno')->orderBy('apellido_materno')->orderBy('nombres')->get();

        return view('sata.students.qr-mass', compact('tenant', 'students'));
    }

    public function export(Request $request)
    {
        return Excel::download(
            new StudentsExport(
                tenantId: $request->ie,
                nivel: $request->nivel,
                grado: $request->grado,
                estado: $request->estado,
                search: $request->search,
            ),
            'estudiantes-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
