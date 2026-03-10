<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AnioLectivoSeeder::class,
            AliadoSeeder::class,
            TenantSeeder::class,
            UserSeeder::class,
            RolesAndPermissionsSeeder::class,
            SataSeeder::class,
            ConfiguracionGeneralSeeder::class,
        ]);
    }
}
