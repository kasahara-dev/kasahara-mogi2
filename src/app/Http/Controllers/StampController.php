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
        $attendance = Attendance::find($id);
        // 申請中有無判定
        if ($attendance->status == '1') {
            $pending = true;
        } else {
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
        $attendance = Attendance::create([
            'user_id' => $oldAttendance->user_id,
            'start' => $start,
            'end' => $end,
            'status' => 1,
            'note' => $request->note,
        ]);
        // restsテーブルに登録
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
                Rest::create([
                    'attendance_id' => $attendance->id,
                    'start' => $restStart,
                    'end' => $restEnd,
                ]);
            }
        }
        return redirect('/stamp_correction_request/list');
    }
    public function show(Request $request)
    {
        $pending = true;
        if (isset($request->tab)) {
            if ($request->tab == 'approved') {
                $pending = false;
            }
        }
        if ($pending) {
            $attendances = Auth::user()->attendances()->where('status', '1')->get();
        } else {
            $attendances = Auth::user()->attendances()->where('status', '2')->get();
        }
        return view('/stamp/list', compact('pending', 'attendances'));
    }
}
