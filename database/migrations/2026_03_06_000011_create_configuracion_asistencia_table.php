<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('configuracion_asistencia', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->time('hora_entrada_regular')->default('07:45:00');
            $table->tinyInteger('minutos_tolerancia')->default(15);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique('tenant_id'); // Una config por colegio
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracion_asistencia');
    }
};
