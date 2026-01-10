<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class RequestedAttendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'request_id',
        'date',
        'start',
        'end',
        'note',
    ];
    public function request()
    {
        return $this->belongsTo('App\Models\Request');
    }
    public function rests()
    {
        return $this->hasMany('App\Models\RequestedRest');
    }
}
