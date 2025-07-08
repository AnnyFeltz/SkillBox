<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'canvas_projeto_id',
        'titulo',
        'descricao',
        'status',
    ];

    public function canvasProjeto()
    {
        return $this->belongsTo(CanvasProjeto::class);
    }
}
