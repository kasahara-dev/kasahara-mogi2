<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function create()
    {
        // 勤務中判定
        // $workingStatus=0:出勤前、1:出勤中、2:退勤後
        if (Auth::user()->attendances()->where('end', null)->exists()) {
            $workingStatus = 1;
        } elseif (Auth::user()->attendances()->where('end', today())->exists()) {
            $workingStatus = 2;
        } else {
            $workingStatus = 0;
        }
        // 休憩中判定
        $resting = false;
        $restingRecord = '';
        if ($workingStatus == 1) {
            $resting = Auth::user()->attendances()->rests()->where('end', null)->exists();
        }
        return view('attendance.attendance', compact(['workingStatus', 'resting']));
    }
}
