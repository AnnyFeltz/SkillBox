<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Canvas extends Model
{
    protected $table = 'canvases';
    protected $fillable = ['user_id', 'title', 'data_json'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
