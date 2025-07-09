<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Tool;
use App\Models\Task;

class CanvasProjeto extends Model
{
    protected $table = 'canvas_projetos';

    protected $fillable = [
        'user_id',
        'titulo',
        'data_json',
        'width',
        'height',
        'is_public',
        'preview_url',
    ];

    protected $casts = [
        'data_json' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tools()
    {
        return $this->belongsToMany(Tool::class, 'canvas_projeto_tool');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'canvas_projeto_id');
    }

    public function scopePublicados($query)
    {
        return $query->where('is_public', true);
    }

    public function imagens()
    {
        return $this->hasMany(CanvasImagem::class, 'canvas_projeto_id')->orderBy('pagina');
    }
}
