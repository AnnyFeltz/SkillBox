<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CanvasImagem extends Model
{
    protected $table = 'canvas_imagems';

    protected $fillable = [
        'canvas_projeto_id',
        'url',
        'pagina',
    ];

    public function canvasProjeto()
    {
        return $this->belongsTo(CanvasProjeto::class);
    }
}
