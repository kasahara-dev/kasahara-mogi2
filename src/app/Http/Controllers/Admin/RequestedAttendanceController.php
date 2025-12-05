<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use App\Models\RequestedAttendance;
use App\Models\Attendance;

class RequestedAttendanceController extends Controller
{
    public function show($id)
    {
        $oldAttendanceId = RequestedAttendance::find($id)->request->attendance_id;
        $name = Attendance::find($oldAttendanceId)->user->name;
        $requestedAttendance = RequestedAttendance::find($id);
        $requestedAttendanceId = $requestedAttendance->id;
        $start = $requestedAttendance->start;
        $end = $requestedAttendance->end;
        $rests = $requestedAttendance->rests()->orderBy('start')->get();
        $restsCount = $requestedAttendance->rests()->count();
        $note = $requestedAttendance->note;
        return view('admin.requested_attendance.detail', compact(['requestedAttendanceId', 'name', 'start', 'end', 'rests', 'restsCount', 'note']));
    }
}
