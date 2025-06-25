<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

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
}
