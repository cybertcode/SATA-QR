<?php

namespace App\Http\Controllers\Sata\Student;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use App\Models\Tenant;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Estudiante::with(['matriculaActual.seccion.nivelEducativo', 'tenant']);

        // Filtro por IE (solo SuperAdmin puede filtrar)
        if ($request->filled('ie') && auth()->user()->isSuperAdmin()) {
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

        $students = $query->latest()->paginate(15)->withQueryString();

        $tenants = auth()->user()->isSuperAdmin() ? Tenant::orderBy('nombre')->get() : collect();

        return view('sata.students.index', compact('students', 'tenants'));
    }

    public function generateQr($id)
    {
        $student = Estudiante::with(['tenant', 'matriculaActual.seccion.nivelEducativo'])->findOrFail($id);

        return view('sata.students.qr', compact('student'));
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
}
