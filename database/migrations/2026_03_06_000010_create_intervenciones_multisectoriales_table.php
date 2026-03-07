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
        Schema::create('intervenciones_multisectoriales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alerta_id')->constrained('alertas_tempranas')->onDelete('cascade');
            $table->foreignId('especialista_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('aliado_estrategico_id')->nullable()->constrained('aliados_estrategicos')->onDelete('set null');
            $table->text('descripcion_accion');
            $table->date('fecha_intervencion');
            $table->text('resultado_seguimiento')->nullable();
            $table->enum('estado', ['Abierto', 'Cerrado', 'Seguimiento'])->default('Abierto');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intervenciones_multisectoriales');
    }
};
