<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Rest extends Model
{
    use HasFactory;
    protected $fillable = [
        'attendance_id',
        'start',
        'end',
    ];
    public function attendance()
    {
        return $this->belongsTo('App\Models\Attendance');
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

