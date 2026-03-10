<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Demo\RoutingController;

// Prefijo /demo para acceder a la plantilla original sin interferir con el sistema real
Route::group(['prefix' => 'demo', 'middleware' => 'auth'], function () {
    Route::get('', [RoutingController::class, 'index'])->name('demo.root');
    Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('demo.third');
    Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('demo.second');
    Route::get('{any}', [RoutingController::class, 'root'])->name('demo.any');
});
