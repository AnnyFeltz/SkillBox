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
}
