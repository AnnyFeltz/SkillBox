<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    protected $fillable = ['nome', 'user_id', 'is_global'];

    public function canvasProjetos()
    {
        return $this->belongsToMany(CanvasProjeto::class, 'canvas_projeto_tool');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function projetos()
    {
        return $this->belongsToMany(CanvasProjeto::class, 'canvas_projeto_tool');
    }   
}
