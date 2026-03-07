<?php

namespace App\Http\Controllers\Sata\Student;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        // Cargamos estudiantes con su matrícula actual y sección (Optimizado)
        $students = Estudiante::with(['matriculas.seccion', 'tenant'])
            ->latest()
            ->paginate(10);

        return view('sata.students.index', compact('students'));
    }

    public function generateQr($id)
    {
        $student = Estudiante::with(['tenant', 'matriculas.seccion'])->findOrFail($id);
        
        return view('sata.students.qr', compact('student'));
    }

    public function show($id)
    {
        $student = Estudiante::with(['tenant', 'matriculas.seccion.nivelEducativo', 'matriculas.asistencias' => function($q) {
            $q->latest()->limit(30);
        }])->findOrFail($id);

        return view('sata.students.show', compact('student'));
    }
}
