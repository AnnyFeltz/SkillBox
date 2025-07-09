<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCanvasImagemsTable extends Migration
{
    public function up()
    {
        Schema::create('canvas_imagems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('canvas_projeto_id')->constrained('canvas_projetos')->onDelete('cascade');
            $table->string('url');
            $table->integer('pagina')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('canvas_imagems');
    }
}
