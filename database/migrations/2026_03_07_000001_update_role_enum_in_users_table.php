<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Ampliar el enum para soportar todos los roles del plan de desarrollo
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('SuperAdmin', 'Director', 'Docente', 'Auxiliar') DEFAULT 'Auxiliar'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('SuperAdmin', 'Director', 'Auxiliar') DEFAULT 'Director'");
    }
};
