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
        'date',
        'start',
        'end',
        'note',
    ];
    public function rests()
    {
        return $this->hasMany('App\Models\Rest');
    }
    public function requests()
    {
        return $this->hasMany('App\Models\Request');
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
    public function restAllMinutes()
    {
        $restAllMinutes = 0;
        if (!is_null($this->end)) {
            $rests = $this->rests->all();
            foreach ($rests as $restRecord) {
                $restAllMinutes += $restRecord->minutes();
            }
        }
        return $restAllMinutes;
    }
}
