<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Sata\Admin\DashboardController;
use App\Http\Controllers\Sata\Director\DashboardController as DirectorDashboardController;
use App\Http\Controllers\Sata\Student\StudentController;
use App\Http\Controllers\Sata\Student\SiagieController;
use App\Http\Controllers\Sata\Alert\AlertController;
use App\Http\Controllers\Sata\Alert\InterventionController;
use App\Http\Controllers\Sata\Institution\SettingsController;
use App\Http\Controllers\Sata\Attendance\ScannerController;
use App\Http\Controllers\Demo\RoutingController;

/*
|--------------------------------------------------------------------------
| RUTAS REALES - SATA-QR
|--------------------------------------------------------------------------
*/

// Autenticación
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('debug-camera', function() { return view('sata.debug.camera'); });
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Sistema Protegido
Route::group(['middleware' => 'auth'], function () {
    
    // Raíz: Escáner QR
    Route::get('/', [ScannerController::class, 'index'])->name('root');
    Route::post('/scan/process', [ScannerController::class, 'process'])->name('scan.process');

    // Dashboards
    Route::get('/dashboard/ugel', [DashboardController::class, 'index'])->name('dashboard.admin');
    Route::get('/dashboard/ie', [DirectorDashboardController::class, 'index'])->name('dashboard.director');
    Route::get('/dashboard', function () {
        if (auth()->user()->isSuperAdmin()) return redirect()->route('dashboard.admin');
        return redirect()->route('dashboard.director');
    })->name('dashboard');

    // Módulo Institución
    Route::get('/institucion/configuracion', [SettingsController::class, 'index'])->name('institution.settings');
    Route::post('/institucion/cierre-asistencia', [SettingsController::class, 'closeDay'])->name('institution.close-day');

    // Módulo Alumnado
    Route::get('/estudiantes', [StudentController::class, 'index'])->name('students.index');
    Route::get('/estudiantes/{id}', [StudentController::class, 'show'])->name('students.show');
    Route::get('/estudiantes/{id}/qr', [StudentController::class, 'generateQr'])->name('students.qr');
    Route::get('/estudiantes/siagie', [SiagieController::class, 'import'])->name('students.import');
    Route::post('/estudiantes/siagie', [SiagieController::class, 'process'])->name('students.import.process');

    // Módulo Alertas y Deserción
    Route::get('/alertas', [AlertController::class, 'index'])->name('alerts.index');
    Route::get('/intervenciones', [InterventionController::class, 'index'])->name('interventions.index');

    // MÓDULO USUARIOS Y PERFIL
    Route::get('/usuarios', function() { return view('sata.users.index'); })->name('users.index');
    Route::get('/perfil', function() { return view('sata.users.profile'); })->name('profile');

    // Rutas de compatibilidad con la plantilla (CLONADO TOTAL)
    Route::get('demo', [RoutingController::class, 'index'])->name('demo.root');
    Route::get('demo/{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('demo.third');
    Route::get('demo/{first}/{second}', [RoutingController::class, 'secondLevel'])->name('demo.second');
    Route::get('demo/{any}', [RoutingController::class, 'root'])->name('demo.any');
    
    // Alias para compatibilidad de la plantilla original (si algún archivo interno usa 'second')
    Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('third');
    Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('second');
    Route::get('{any}', [RoutingController::class, 'root'])->name('any');
});
