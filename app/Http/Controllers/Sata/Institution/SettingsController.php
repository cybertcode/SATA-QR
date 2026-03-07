<?php

namespace App\Http\Controllers\Sata\Institution;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    public function index()
    {
        return view('sata.institution.settings');
    }

    public function closeDay(Request $request)
    {
        // Ejecutar el comando para hoy
        Artisan::call('sata:close-day');
        
        return back()->with('success', 'La asistencia del día ha sido cerrada correctamente. Los alumnos inasistentes fueron marcados con FI.');
    }
}
