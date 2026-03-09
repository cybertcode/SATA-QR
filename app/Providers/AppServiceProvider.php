<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
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

        // Registrar Policy para modelo de Spatie (no se auto-descubre)
        Gate::policy(\Spatie\Permission\Models\Role::class, \App\Policies\RolePolicy::class);

        // Registrar Policy para ConfiguracionGeneral
        Gate::policy(\App\Models\ConfiguracionGeneral::class, \App\Policies\ConfiguracionGeneralPolicy::class);

        // Compartir configuración del sistema con todas las vistas
        View::composer('*', function ($view) {
            static $siteConfig = null;
            if ($siteConfig === null && Schema::hasTable('configuraciones_generales')) {
                $siteConfig = \App\Models\ConfiguracionGeneral::pluck('valor', 'clave')->toArray();
            }
            $view->with('siteConfig', $siteConfig ?? []);
        });
    }
}
