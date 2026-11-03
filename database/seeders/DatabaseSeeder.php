<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        $this->crearUsuarioMaestro();
    }

    private function crearUsuarioMaestro(): void
    {
        $email = env('MASTER_USER_EMAIL', 'admin@ruleta.local');
        $user  = User::where('email', $email)->first();

        if (! $user) {
            $password = env('MASTER_USER_PASSWORD');

            if (empty($password)) {
                if (app()->environment('production')) {
                    throw new \RuntimeException(
                        'MASTER_USER_PASSWORD debe estar definido en producción.'
                    );
                }

                $password = 'password';
                $this->command?->warn(
                    'Usando contraseña de desarrollo "password". Define MASTER_USER_PASSWORD en .env.'
                );
            }

            $user = User::create([
                'name'              => 'Administrador Maestro',
                'email'             => $email,
                'password'          => Hash::make($password),
                'email_verified_at' => now(),
            ]);

            $this->command?->info("Master user creado: {$email}");
        } else {
            $this->command?->info("Master user ya existe: {$email}");
        }

        if (! $user->hasRole('admin')) {
            $user->assignRole('admin');
            $this->command?->info("Rol 'admin' asignado a {$email}");
        }
    }
}
