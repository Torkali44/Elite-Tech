<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cv extends Model
{
    protected $fillable = ['user_id', 'data', 'visibility'];

    protected $casts = [
        'data'       => 'array',
        'visibility' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
