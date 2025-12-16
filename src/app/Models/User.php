<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function attendances()
    {
        return $this->hasMany('App\Models\Attendance');
    }
    public function workingStatus()
    {
        // 0:出勤前、1:出勤中、2:退勤後
        if ($this->attendances()->where('end', null)->exists()) {
            $workingStatus = 1;
        } elseif ($this->attendances()->whereDate('end', today())->exists()) {
            $workingStatus = 2;
        } else {
            $workingStatus = 0;
        }
        return $workingStatus;
    }
    public function resting()
    {
        $resting = false;
        if ($this->workingStatus() == 1) {
            $resting = $this->attendances()->where('end', null)->first()->rests()->where('end', null)->exists();
        }
        return $resting;
    }
}
