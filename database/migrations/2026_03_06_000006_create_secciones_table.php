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
        Schema::create('secciones', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('tenant_nivel_id')->constrained('tenant_niveles')->onDelete('cascade');
            $table->string('grado', 50); // ej: "1", "2", "3 Años"
            $table->char('letra', 1); // A, B, C...
            $table->string('tutor_id')->nullable(); // FK opcional a users
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['tenant_nivel_id', 'grado', 'letra']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secciones');
    }
};
