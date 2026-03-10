<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('configuracion_asistencia', function (Blueprint $table) {
            $table->tinyInteger('dias_inasistencia_riesgo')->default(3)->after('minutos_tolerancia');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion_asistencia', function (Blueprint $table) {
            $table->dropColumn('dias_inasistencia_riesgo');
        });
    }
};
