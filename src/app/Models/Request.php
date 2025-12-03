<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;
    protected $fillable = [
        'attendance_id',
        'status',
    ];
    public function attendance()
    {
        return $this->belongsTo('App\Models\Attendance');
    }
    public function requestedAttendance()
    {
        return $this->hasOne('App\Models\RequestedAttendance');
    }
}
