<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('configuraciones_generales', function (Blueprint $table) {
            $table->id();
            $table->string('grupo', 50)->index();
            $table->string('clave', 100)->unique();
            $table->text('valor')->nullable();
            $table->string('tipo', 20)->default('string'); // string, integer, boolean, json
            $table->string('etiqueta');
            $table->string('descripcion')->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();

            $table->index(['grupo', 'orden']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuraciones_generales');
    }
};
