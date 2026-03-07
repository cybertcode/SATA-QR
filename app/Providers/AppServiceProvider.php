<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        
        // Configuración Regional Perú
        Carbon::setLocale('es');
        setlocale(LC_TIME, 'es_PE.UTF-8');
        date_default_timezone_set('America/Lima');
    }
}
