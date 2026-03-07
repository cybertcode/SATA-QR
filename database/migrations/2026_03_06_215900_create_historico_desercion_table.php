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
        Schema::create('historico_desercion', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('anio_lectivo_id')->constrained('anios_lectivos');
            $table->integer('total_estudiantes');
            $table->integer('total_desercion');
            $table->decimal('porcentaje_desercion', 5, 2);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'anio_lectivo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historico_desercion');
    }
};
