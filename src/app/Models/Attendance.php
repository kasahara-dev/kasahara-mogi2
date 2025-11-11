<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'date',
        'start',
        'end',
        'note',
        'status',
    ];
    public function rests()
    {
        return $this->hasMany('App\Models\Rests');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
