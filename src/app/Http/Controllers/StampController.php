<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;

class StampController extends Controller
{
    public function create($id)
    {
        $name = Auth::user()->name;
        // 申請中有無判定
        if (Auth::user()->attendances()->where('status', 1)->whereDate('start', Attendance::find($id)->start)->exists()) {
            // 申請中がある場合は申請中の情報を渡す
            $pending = true;
            $start = Attendance::find($id)->user->attendances()->where('status', 1)->whereDate('start', Attendance::find($id)->start)->first()->start;
            $end = Attendance::find($id)->user->attendances()->where('status', 1)->whereDate('start', Attendance::find($id)->start)->first()->end;
            $rests = Attendance::find($id)->user->attendances()->where('status', 1)->whereDate('start', Attendance::find($id)->start)->first()->rests()->orderBy('start')->get();
            $restsCount = Attendance::find($id)->user->attendances()->where('status', 1)->whereDate('start', Attendance::find($id)->start)->first()->rests()->count();
            $note = Attendance::find($id)->user->attendances()->where('status', 1)->whereDate('start', Attendance::find($id)->start)->first()->note;
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
