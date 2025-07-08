<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('canvas_projeto_tool', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('canvas_projeto_id');
            $table->unsignedBigInteger('tool_id');
            $table->timestamps();

            $table->foreign('canvas_projeto_id')
                ->references('id')
                ->on('canvas_projetos')
                ->onDelete('cascade');

            $table->foreign('tool_id')
                ->references('id')
                ->on('tools')
                ->onDelete('cascade');

            $table->unique(['canvas_projeto_id', 'tool_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('canvas_projeto_tool');
    }
};
