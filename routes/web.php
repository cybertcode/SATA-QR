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
use App\Http\Controllers\Sata\User\UserController;
use App\Http\Controllers\Sata\User\RoleController;
use App\Http\Controllers\Sata\Admin\ConfiguracionGeneralController;
use App\Http\Controllers\Demo\RoutingController;

/*
|--------------------------------------------------------------------------
| RUTAS REALES - SATA-QR
|--------------------------------------------------------------------------
*/

// Autenticación (Público)
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Sistema Protegido
Route::group(['middleware' => 'auth'], function () {

    // ─── ESCÁNER QR (Todos los roles autenticados) ───
    Route::get('/', [ScannerController::class, 'index'])->name('root');
    Route::post('/scan/process', [ScannerController::class, 'process'])->name('scan.process');

    // ─── PERFIL PERSONAL (Todos los roles) ───
    Route::get('/perfil', [UserController::class, 'profile'])->name('profile');
    Route::post('/perfil/update', [UserController::class, 'updateProfile'])->name('profile.update');

    // ─── DASHBOARDS ───
    Route::get('/dashboard', function () {
        if (auth()->user()->isSuperAdmin())
            return redirect()->route('dashboard.admin');
        return redirect()->route('dashboard.director');
    })->name('dashboard');

    Route::get('/dashboard/ugel', [DashboardController::class, 'index'])
        ->middleware('role:SuperAdmin')
        ->name('dashboard.admin');

    Route::get('/dashboard/ie', [DirectorDashboardController::class, 'index'])
        ->middleware('role:Director,Docente,Auxiliar')
        ->name('dashboard.director');

    // ─── MÓDULO USUARIOS (Solo SuperAdmin/Administrador) ───
    Route::middleware('role:SuperAdmin,Administrador')->group(function () {
        Route::get('/usuarios', [UserController::class, 'index'])->name('users.index');
    });

    // ─── MÓDULO ROLES Y PERMISOS (Solo SuperAdmin) ───
    Route::middleware('role:SuperAdmin')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/configuracion-general', [ConfiguracionGeneralController::class, 'index'])->name('config.general');
    });

    // ─── MÓDULO INSTITUCIÓN (SuperAdmin + Director) ───
    Route::middleware('role:SuperAdmin,Director')->group(function () {
        Route::get('/institucion/configuracion', [SettingsController::class, 'index'])->name('institution.settings');
        Route::post('/institucion/cierre-asistencia', [SettingsController::class, 'closeDay'])->name('institution.close-day');
    });

    // ─── MÓDULO ALUMNADO (SuperAdmin + Director + Docente) ───
    Route::middleware('role:SuperAdmin,Director,Docente')->group(function () {
        Route::get('/estudiantes', [StudentController::class, 'index'])->name('students.index');
        Route::get('/estudiantes/siagie', [SiagieController::class, 'import'])->name('students.import');
        Route::post('/estudiantes/siagie', [SiagieController::class, 'process'])->name('students.import.process');
        Route::get('/estudiantes/{id}', [StudentController::class, 'show'])->name('students.show');
        Route::get('/estudiantes/{id}/qr', [StudentController::class, 'generateQr'])->name('students.qr');
    });

    // ─── MÓDULO ALERTAS Y DESERCIÓN (SuperAdmin + Director) ───
    Route::middleware('role:SuperAdmin,Director')->group(function () {
        Route::get('/alertas', [AlertController::class, 'index'])->name('alerts.index');
        Route::get('/intervenciones', [InterventionController::class, 'index'])->name('interventions.index');
    });

    // ─── REFERENCIA VISUAL: Plantilla Demo (solo bajo /demo) ───
    Route::prefix('demo')->group(function () {
        Route::get('/', [RoutingController::class, 'index'])->name('demo.root');
        Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('demo.third');
        Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('demo.second');
        Route::get('{any}', [RoutingController::class, 'root'])->name('demo.any');
    });
});
