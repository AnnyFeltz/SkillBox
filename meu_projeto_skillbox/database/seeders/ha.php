<?php

namespace Database\Seeders;

use App\Models\CanvasProjeto;
use App\Models\Tool;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ha extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $tools = [
            ['nome' => 'Tesoura de Ponta Fina', 'user_id' => 1, 'is_global' => true],
            ['nome' => 'Agulha de Costura', 'user_id' => 1, 'is_global' => true],
            ['nome' => 'Agulha de Crochê', 'user_id' => 1, 'is_global' => true],
            ['nome' => 'Agulha de Tricô', 'user_id' => 1, 'is_global' => true],
            ['nome' => 'Alfinete', 'user_id' => 1, 'is_global' => true],
            ['nome' => 'Dedal', 'user_id' => 1, 'is_global' => true],
            ['nome' => 'Fita Métrica', 'user_id' => 1, 'is_global' => true],
            ['nome' => 'Cortador Circular', 'user_id' => 1, 'is_global' => true],
            ['nome' => 'Base de Corte', 'user_id' => 1, 'is_global' => true],
            ['nome' => 'Cola Quente', 'user_id' => 1, 'is_global' => true],
            ['nome' => 'Cola Branca', 'user_id' => 1, 'is_global' => true],
            ['nome' => 'Cola de Silicone', 'user_id' => 1, 'is_global' => true],
            ['nome' => 'Espátula para EVA', 'user_id' => 1, 'is_global' => true],
            ['nome' => 'Furador de Papel', 'user_id' => 1, 'is_global' => true],
            ['nome' => 'Régua de Patchwork', 'user_id' => 1, 'is_global' => true],
            ['nome' => 'Pincel para Artesanato', 'user_id' => 1, 'is_global' => true],
            ['nome' => 'Estilete de Precisão', 'user_id' => 1, 'is_global' => true],
            ['nome' => 'Dobradeira de Papel', 'user_id' => 1, 'is_global' => true],
            ['nome' => 'Marcador de Tecido', 'user_id' => 1, 'is_global' => true],
            ['nome' => 'Pistola de Cola Quente', 'user_id' => 1, 'is_global' => true],
        ];

        foreach ($tools as $tool) {
            Tool::create($tool);
        }
    }
}
