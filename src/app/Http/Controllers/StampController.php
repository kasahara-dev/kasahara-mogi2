<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\AttendanceRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Rest;


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
        // attendancesテーブルに申請中として登録
        // $attendance = Attendance::create([
        //     'user_id' => $oldAttendance->user_id,
        //     'start' => $start,
        //     'end' => $end,
        //     'status' => 1,
        //     'note' => $request->note,
        // ]);
        // 休憩テーブルにレコード追加
        $restStart = new Carbon();
        $restEnd = new Carbon();
        $restStartHours = $request->rest_start_hour;
        $restStartMinutes = $request->rest_start_minute;
        $restEndHours = $request->rest_end_hour;
        $restEndMinutes = $request->rest_end_minute;
        $start->year($oldDate->year)->month($oldDate->month)->day($oldDate->day)->startOfDay()->hour($request->attendance_start_hour)->minute($request->attendance_start_minute);
        $end->year($oldDate->year)->month($oldDate->month)->day($oldDate->day)->startOfDay();
        // 24時で処理した後日付戻し注意
        foreach ($restStartHours as $key => $restStartHour) {
            if ($restStartHour <> '') {
            }
        }
        return redirect('/attendance/list/?year=' . $start->year . '&month=' . $start->month);
    }
}
