<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('canvas_projeto_id');
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->enum('status', ['pendente', 'concluida'])->default('pendente');
            $table->timestamps();

            $table->foreign('canvas_projeto_id')->references('id')->on('canvas_projetos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
