<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorLog extends Model
{
    protected $fillable = ['user_id', 'ip_address', 'user_agent', 'method', 'url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
