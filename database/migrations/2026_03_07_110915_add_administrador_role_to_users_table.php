<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('SuperAdmin','Administrador','Director','Docente','Auxiliar') DEFAULT 'Auxiliar'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('SuperAdmin','Director','Docente','Auxiliar') DEFAULT 'Auxiliar'");
    }
};
