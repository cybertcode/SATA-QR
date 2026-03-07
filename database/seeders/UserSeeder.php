<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Global (SuperAdmin de la UGEL)
        User::firstOrCreate([
            'email' => 'admin@admin.com',
        ], [
            'name' => 'Super Admin SATA',
            'password' => Hash::make('Admin123'),
            'role' => 'SuperAdmin',
            'dni' => '00000000',
            'cargo' => 'Especialista Informática',
        ]);

        // Director de I.E. Huacaybamba
        User::firstOrCreate([
            'email' => 'director@ie-huacaybamba.edu.pe',
        ], [
            'tenant_id' => 'ie-huacaybamba',
            'name' => 'Mg. Juan Perez',
            'password' => Hash::make('director123'),
            'role' => 'Director',
            'dni' => '12345678',
            'cargo' => 'Director',
        ]);
    }
}
