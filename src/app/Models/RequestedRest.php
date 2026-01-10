<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestedRest extends Model
{
    use HasFactory;
    protected $fillable = [
        'requested_attendance_id',
        'start',
        'end',
    ];
    public function attendance()
    {
        return $this->belongsTo('App\Models\RequestedAttendance');
    }
}
