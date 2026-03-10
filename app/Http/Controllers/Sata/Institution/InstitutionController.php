<?php

namespace App\Http\Controllers\Sata\Institution;

use App\Http\Controllers\Controller;
use App\Models\Tenant;

class InstitutionController extends Controller
{
    public function index()
    {
        $institutions = Tenant::with([
            'niveles',
            'users' => fn($q) => $q->where('role', 'Director'),
            'configuracionAsistencia',
        ])
            ->withCount('estudiantes')
            ->orderBy('nombre')
            ->get();

        return view('sata.institutions.index', compact('institutions'));
    }
}
