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
        'approver',
    ];
    public function attendance()
    {
        return $this->belongsTo('App\Models\Attendance');
    }
    public function requestedAttendance()
    {
        return $this->hasOne('App\Models\RequestedAttendance');
    }
    public function approver()
    {
        return $this->belongsTo('App\Models\Admins');
    }

}
