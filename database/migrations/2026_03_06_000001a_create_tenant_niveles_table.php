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
        Schema::create('tenant_niveles', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->enum('nivel', ['Inicial', 'Primaria', 'Secundaria']);
            $table->char('codigo_modular', 7)->unique();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'nivel']); // Una I.E. no puede tener dos niveles "Primaria"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_niveles');
    }
};
