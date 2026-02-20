<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CreateAdminUser extends Command
{
    protected $signature = 'make:admin-user {email? : Email del usuario} {name? : Nombre del usuario}';

    protected $description = 'Crea un usuario administrador';

    public function handle()
    {
        $email = $this->argument('email') ?? 'admin@test.com';
        $name = $this->argument('name') ?? 'Admin Test';

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => bcrypt('password'),
                'is_admin' => 1
            ]
        );

        $this->info("Usuario admin creado: {$user->email} / password");
        return 0;
    }
}
