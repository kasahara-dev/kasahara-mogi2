<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
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
            $pending = true;
            $start = Attendance::find($id)->user->attendances()->where('status', 1)->whereDate('start', $attendanceDay)->first()->start;
            $end = Attendance::find($id)->user->attendances()->where('status', 1)->whereDate('start', $attendanceDay)->first()->end;
            $rests = Attendance::find($id)->user->attendances()->where('status', 1)->whereDate('start', $attendanceDay)->first()->rests()->orderBy('start')->get();
            $restsCount = Attendance::find($id)->user->attendances()->where('status', 1)->whereDate('start', $attendanceDay)->first()->rests()->count();
            $note = Attendance::find($id)->user->attendances()->where('status', 1)->whereDate('start', $attendanceDay)->first()->note;
        } else {
            $pending = false;
            $start = Attendance::find($id)->start;
            $end = Attendance::find($id)->end;
            $rests = Attendance::find($id)->rests()->orderBy('start')->get();
            $restsCount = Attendance::find($id)->rests()->count();
            $note = Attendance::find($id)->note;
        }
        return view('attendance/detail', compact(['name', 'start', 'end', 'rests', 'restsCount', 'pending', 'note']));
    }
}
