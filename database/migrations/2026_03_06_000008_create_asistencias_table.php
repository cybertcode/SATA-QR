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
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id'); // Añadido para consultas directas de dashboard
            $table->foreignId('matricula_id')->constrained('matriculas')->onDelete('cascade');
            $table->foreignId('registrado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->date('fecha');
            $table->time('hora_ingreso')->nullable();
            $table->enum('estado', ['P', 'T', 'FJ', 'FI'])->default('FI'); 
            
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['matricula_id', 'fecha']); 
            $table->index(['tenant_id', 'fecha', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
