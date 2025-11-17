<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function show()
    {
        return view('/attendance/list');
    }
    public function create()
    {
        // 勤務中判定
        // $workingStatus=0:出勤前、1:出勤中、2:退勤後
        if (Auth::user()->attendances()->where('end', null)->exists()) {
            $workingStatus = 1;
        } elseif (Auth::user()->attendances()->whereDate('end', today())->exists()) {
            $workingStatus = 2;
        } else {
            $workingStatus = 0;
        }
        // 休憩中判定
        $resting = false;
        if ($workingStatus == 1) {
            $resting = Auth::user()->attendances()->where('end', null)->first()->rests()->where('end', null)->exists();
        }
        return view('attendance.attendance', compact(['workingStatus', 'resting']));
    }
    public function store()
    {
        Attendance::create([
            'user_id' => auth()->id(),
            'start' => now(),
        ]);
        return redirect('/attendance');
    }
    public function update()
    {
        $tableDate = Carbon::parse(Auth::user()->attendances()->where('end', null)->value('start'))->startOfDay();
        $today = Carbon::today();
        while ($tableDate < $today) {
            $tableDate->addDay();
            Auth::user()->attendances()->where('end', null)->update([
                'end' => $tableDate,
            ]);
            Attendance::create([
                'user_id' => auth()->id(),
                'start' => $tableDate,
            ]);
        }
        Auth::user()->attendances()->where('end', null)->update([
            'end' => now(),
        ]);
        return redirect('/attendance');
    }
}
