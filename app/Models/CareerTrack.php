<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CareerTrack extends Model { protected $fillable=['user_id','slug','status','admin_notes','github']; }
