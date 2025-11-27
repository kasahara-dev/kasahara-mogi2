<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'start',
        'end',
        'note',
        'status',
    ];
    public function rests()
    {
        return $this->hasMany('App\Models\Rest');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function minutes()
    {
        if (is_null($this->end)) {
            $minutes = null;
        } else {
            $startTime = Carbon::parse($this->start)->second(0);
            $endTime = Carbon::parse($this->end)->second(0);
            $diffInSeconds = $startTime->diffInSeconds($endTime);
            $minutes = floor($diffInSeconds / 60);
        }
        return $minutes;
    }
}
