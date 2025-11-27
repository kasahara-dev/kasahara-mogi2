<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\AttendanceRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;

class StampController extends Controller
{
    public function create($id)
    {
        $name = Auth::user()->name;
        $attendanceDay = Carbon::parse(Attendance::find($id)->start);
        // 申請中有無判定
        if (Auth::user()->attendances()->where('status', 1)->whereDate('start', $attendanceDay)->exists()) {
            // 申請中がある場合は申請中の情報を渡す
            $attendance = Attendance::find($id)->user->attendances()->where('status', 1)->whereDate('start', $attendanceDay)->first();
            $pending = true;
        } else {
            // 申請中がない場合はそのままの情報を渡す
            $attendance = Attendance::find($id);
            $pending = false;
        }
        $attendanceId = $attendance->id;
        $start = $attendance->start;
        $end = $attendance->end;
        $rests = $attendance->rests()->orderBy('start')->get();
        $restsCount = $attendance->rests()->count();
        $note = $attendance->note;
        return view('attendance/detail', compact(['attendanceId', 'name', 'start', 'end', 'rests', 'restsCount', 'pending', 'note']));
    }
    public function store(AttendanceRequest $request, $id)
    {
        return redirect('/attendance/detail/' . $id);
    }
}
