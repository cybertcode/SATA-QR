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
        Schema::create('alertas_tempranas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matricula_id')->constrained('matriculas')->onDelete('cascade');
            $table->enum('nivel_riesgo', ['Leve', 'Moderado', 'Crítico'])->default('Leve');
            $table->text('motivo_acumulado'); // ej: "3 inasistencias en 1 semana"
            $table->enum('estado_atencion', ['Pendiente', 'En Proceso', 'Atendido', 'Derivado'])->default('Pendiente');
            $table->date('fecha_emision');
            $table->timestamps();

            $table->index(['nivel_riesgo', 'estado_atencion']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alertas_tempranas');
    }
};
