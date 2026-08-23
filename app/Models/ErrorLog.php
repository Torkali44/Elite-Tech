<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    protected $fillable = ['user_id', 'url', 'message', 'stack_trace'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
