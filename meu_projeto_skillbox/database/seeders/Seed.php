<?php

namespace Database\Seeders;

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
        User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin123'),
            'is_admin' => true,
        ]);

        User::create([
            'name' => 'Pessoa',
            'email' => 'pessoa@gmail.com',
            'password' => bcrypt('pessoa123'),
            'is_admin' => false,
        ]);
    }
}
