<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Usuario Administrador
        User::create([
            'dpi' => '1234567890123',
            'nombre' => 'Administrador',
            'apellido' => 'EcoBici',
            'email' => 'admin@ecobici.gt',
            'password' => Hash::make('admin123'),
            'telefono' => '50241234567',
            'fecha_nacimiento' => '1990-01-01',
            'foto' => null,
            'rol' => 'administrador',
            'activo' => true,
            'puntos_verdes' => 0,
            'co2_reducido_total' => 0,
            'email_verified_at' => now()
        ]);

        // Usuarios de prueba
        $usuarios = [
            [
                'dpi' => '9876543210987',
                'nombre' => 'María',
                'apellido' => 'González',
                'email' => 'maria@example.com',
                'password' => Hash::make('password123'),
                'telefono' => '50242345678',
                'fecha_nacimiento' => '1995-03-15',
                'rol' => 'usuario',
                'puntos_verdes' => 250,
                'co2_reducido_total' => 15.50
            ],
            [
                'dpi' => '1122334455667',
                'nombre' => 'Carlos',
                'apellido' => 'Morales',
                'email' => 'carlos@example.com',
                'password' => Hash::make('password123'),
                'telefono' => '50243456789',
                'fecha_nacimiento' => '1988-07-22',
                'rol' => 'usuario',
                'puntos_verdes' => 180,
                'co2_reducido_total' => 8.75
            ],
            [
                'dpi' => '5544332211009',
                'nombre' => 'Ana',
                'apellido' => 'Rodríguez',
                'email' => 'ana@example.com',
                'password' => Hash::make('password123'),
                'telefono' => '50244567890',
                'fecha_nacimiento' => '1992-11-08',
                'rol' => 'usuario',
                'puntos_verdes' => 420,
                'co2_reducido_total' => 22.30
            ]
        ];

        foreach ($usuarios as $userData) {
            $userData['activo'] = true;
            $userData['foto'] = null;
            $userData['email_verified_at'] = now();
            User::create($userData);
        }
    }
}
