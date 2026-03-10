<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class MassUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('Sata2026*');
        $tenants = Tenant::pluck('id')->toArray();
        $roles = ['SuperAdmin', 'Administrador', 'Director', 'Docente', 'Auxiliar'];

        // Distribución: 5 SuperAdmin, 10 Administrador, 20 Director, 115 Docente, 100 Auxiliar = 250
        $distribution = [
            'SuperAdmin' => 5,
            'Administrador' => 10,
            'Director' => 20,
            'Docente' => 115,
            'Auxiliar' => 100,
        ];

        $cargos = [
            'SuperAdmin' => ['Especialista UGEL', 'Coordinador TIC', 'Jefe de AGP', 'Especialista Informática', 'Analista de Datos'],
            'Administrador' => ['Secretario Académico', 'Coordinador General', 'Jefe Administrativo', 'Asistente de Dirección'],
            'Director' => ['Director', 'Subdirector Académico', 'Subdirector Administrativo'],
            'Docente' => ['Docente PIP', 'Tutor', 'Docente de Área', 'Coordinador Pedagógico', 'Docente Fortaleza'],
            'Auxiliar' => ['Auxiliar de Educación', 'Auxiliar de Laboratorio', 'Auxiliar de Biblioteca'],
        ];

        $nombres = [
            'Carlos',
            'María',
            'José',
            'Ana',
            'Luis',
            'Rosa',
            'Juan',
            'Carmen',
            'Pedro',
            'Elena',
            'Miguel',
            'Sofía',
            'Ricardo',
            'Laura',
            'Fernando',
            'Patricia',
            'Andrés',
            'Gabriela',
            'Diego',
            'Valentina',
            'Raúl',
            'Daniela',
            'Jorge',
            'Claudia',
            'Roberto',
            'Lucía',
            'Eduardo',
            'Natalia',
            'Héctor',
            'Alejandra',
            'Marco',
            'Verónica',
            'Óscar',
            'Beatriz',
            'César',
            'Teresa',
            'Martín',
            'Adriana',
            'Sergio',
            'Flor',
            'Hugo',
            'Diana',
            'Iván',
            'Lourdes',
            'Pablo',
            'Pilar',
            'Víctor',
            'Irene',
            'Ernesto',
            'Rocío',
        ];

        $apellidos = [
            'García',
            'Rodríguez',
            'Martínez',
            'López',
            'Hernández',
            'Gonzales',
            'Pérez',
            'Sánchez',
            'Ramírez',
            'Torres',
            'Flores',
            'Díaz',
            'Vásquez',
            'Morales',
            'Reyes',
            'Cruz',
            'Ramos',
            'Ortega',
            'Silva',
            'Vargas',
            'Castillo',
            'Mendoza',
            'Jiménez',
            'Ruiz',
            'Herrera',
            'Rojas',
            'Medina',
            'Aguilar',
            'Peña',
            'Chávez',
            'Rivera',
            'Contreras',
            'Guzmán',
            'Espinoza',
            'Huamán',
            'Quispe',
            'Mamani',
            'Condori',
            'Paredes',
            'Salazar',
            'Delgado',
            'Ponce',
            'Cabrera',
            'Figueroa',
            'Mejía',
            'Ríos',
            'Valenzuela',
            'Campos',
            'León',
            'Navarro',
        ];

        $counter = 0;
        $usedDnis = User::whereNotNull('dni')->pluck('dni')->toArray();

        foreach ($distribution as $role => $count) {
            $roleModel = Role::where('name', $role)->first();

            for ($i = 0; $i < $count; $i++) {
                $counter++;
                $nombre = $nombres[array_rand($nombres)];
                $ap1 = $apellidos[array_rand($apellidos)];
                $ap2 = $apellidos[array_rand($apellidos)];
                $fullName = "{$nombre} {$ap1} {$ap2}";

                // Generar DNI único de 8 dígitos
                do {
                    $dni = str_pad(random_int(10000000, 99999999), 8, '0', STR_PAD_LEFT);
                } while (in_array($dni, $usedDnis));
                $usedDnis[] = $dni;

                // Asignar tenant según rol
                $tenantId = null;
                if (!in_array($role, ['SuperAdmin', 'Administrador']) && !empty($tenants)) {
                    $tenantId = $tenants[array_rand($tenants)];
                }

                $cargo = $cargos[$role][array_rand($cargos[$role])];
                $emailSlug = strtolower(str_replace(' ', '.', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', "{$nombre}.{$ap1}")));
                $email = "{$emailSlug}.{$counter}@sata-qr.edu.pe";

                $isActive = $counter % 7 !== 0; // ~86% activos, ~14% inactivos
                $lastLogin = $isActive ? now()->subDays(random_int(0, 60)) : null;

                $user = User::create([
                    'name' => $fullName,
                    'email' => $email,
                    'password' => $password,
                    'tenant_id' => $tenantId,
                    'role' => $role,
                    'dni' => $dni,
                    'cargo' => $cargo,
                    'is_active' => $isActive,
                    'last_login_at' => $lastLogin,
                ]);

                if ($roleModel) {
                    $user->assignRole($roleModel);
                }
            }
        }

        $this->command->info("Se crearon {$counter} usuarios correctamente.");
    }
}
