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
            'name' => 'Mg. Juan Pérez',
            'password' => Hash::make('director123'),
            'role' => 'Director',
            'dni' => '12345678',
            'cargo' => 'Director',
        ]);

        // Director de I.E. Canchabamba
        User::firstOrCreate([
            'email' => 'director@ie-canchabamba.edu.pe',
        ], [
            'tenant_id' => 'ie-canchabamba',
            'name' => 'Lic. María Sánchez',
            'password' => Hash::make('director123'),
            'role' => 'Director',
            'dni' => '23456789',
            'cargo' => 'Director',
        ]);

        // Director de I.E. Cochabamba
        User::firstOrCreate([
            'email' => 'director@ie-cochabamba.edu.pe',
        ], [
            'tenant_id' => 'ie-cochabamba',
            'name' => 'Prof. Carlos Romero',
            'password' => Hash::make('director123'),
            'role' => 'Director',
            'dni' => '34567890',
            'cargo' => 'Director',
        ]);

        // Director de I.E. Pinra
        User::firstOrCreate([
            'email' => 'director@ie-pinra.edu.pe',
        ], [
            'tenant_id' => 'ie-pinra',
            'name' => 'Mag. Rosa Espinoza',
            'password' => Hash::make('director123'),
            'role' => 'Director',
            'dni' => '45678901',
            'cargo' => 'Director',
        ]);

        // Director de I.E. Arancay
        User::firstOrCreate([
            'email' => 'director@ie-arancay.edu.pe',
        ], [
            'tenant_id' => 'ie-arancay',
            'name' => 'Lic. Pedro Huamán',
            'password' => Hash::make('director123'),
            'role' => 'Director',
            'dni' => '56789012',
            'cargo' => 'Director',
        ]);
    }
}
