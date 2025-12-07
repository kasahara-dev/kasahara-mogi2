<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Http\Requests\AttendanceRequest;
use App\Models\Rest;
use App\Models\Request as RequestModel;
use App\Models\RequestedAttendance;
use App\Models\RequestedRest;
use Carbon\Carbon;

class RequestedAttendanceController extends Controller
{
    public function show($id)
    {
        $name = Auth::user()->name;
        $requestedAttendance = RequestedAttendance::find($id);
        // 申請中判定
        if ($requestedAttendance->request->status == 1) {
            $pending = true;
        } else {
            $pending = false;
        }
        $requestedAttendanceId = $requestedAttendance->id;
        $start = $requestedAttendance->start;
        $end = $requestedAttendance->end;
        $rests = $requestedAttendance->rests()->orderBy('start')->get();
        $restsCount = $requestedAttendance->rests()->count();
        $note = $requestedAttendance->note;
        return view('requested_attendance/detail', compact(['requestedAttendanceId', 'name', 'start', 'end', 'rests', 'restsCount', 'pending', 'note']));
    }
    public function create(Request $request, $id)
    {
        $name = Auth::user()->name;
        $attendance = Attendance::find($id);
        $attendanceId = $attendance->id;
        $start = $attendance->start;
        $end = $attendance->end;
        $rests = $attendance->rests()->orderBy('start')->get();
        $restsCount = $attendance->rests()->count();
        $note = $attendance->note;
        return view('attendance/detail', compact(['attendanceId', 'name', 'start', 'end', 'rests', 'restsCount', 'note']));
    }
    public function store(AttendanceRequest $request, $id)
    {
        // 修正元情報
        $oldAttendance = Attendance::find($id);
        $oldDate = Carbon::parse($oldAttendance->start);
        // 日付作成
        $start = new Carbon();
        $end = new Carbon();
        $start->year($oldDate->year)->month($oldDate->month)->day($oldDate->day)->startOfDay()->hour($request->attendance_start_hour)->minute($request->attendance_start_minute);
        $end->year($oldDate->year)->month($oldDate->month)->day($oldDate->day)->startOfDay();
        // 24時終了の場合は翌日0時を終了日時とする
        if ($request->attendance_end_hour == '24') {
            $end->addDay();
        } else {
            $end->hour($request->attendance_end_hour)->minute($request->attendance_end_minute);
        }
        // 申請
        $forRequest = RequestModel::create([
            'attendance_id' => $request->id,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $requestedAttendance = requestedAttendance::create([
            'request_id' => $forRequest->id,
            'start' => $start,
            'end' => $end,
            'note' => $request->note,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        // requestedRestsテーブルに登録
        $restStart = new Carbon();
        $restEnd = new Carbon();
        // 24時処理
        foreach ($request->rest_start_hour as $key => $restStartHour) {
            if ($restStartHour <> '') {
                $restStart->year($oldDate->year)->month($oldDate->month)->day($oldDate->day)->startOfDay()->hour($request->rest_start_hour[$key])->minute($request->rest_start_minute[$key]);
                $restEnd->year($oldDate->year)->month($oldDate->month)->day($oldDate->day)->startOfDay();
                if ($request->rest_end_hour[$key] == '24') {
                    $restEnd->addDay();
                } else {
                    $restEnd->hour($request->rest_end_hour[$key])->minute($request->rest_end_minute[$key]);
                }
                RequestedRest::create([
                    'requested_attendance_id' => $requestedAttendance->id,
                    'start' => $restStart,
                    'end' => $restEnd,
                ]);
            }
        }
        return back();
    }
}
