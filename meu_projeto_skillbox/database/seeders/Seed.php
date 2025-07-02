<?php

namespace Database\Seeders;

use App\Models\CanvasProjeto;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Seed extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('admin123'),
                'is_admin' => true,
            ],
            [
                'name' => 'Pessoa',
                'email' => 'pessoa@gmail.com',
                'password' => bcrypt('pessoa123'),
                'is_admin' => false,
            ],
            [
                'name' => 'Carol',
                'email' => 'carol@gmail.com',
                'password' => bcrypt('carol123'),
                'is_admin' => false,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
