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
    public function show(Request $request)
    {
        if ($request->year && $request->month) {
            $year = $request->year;
            $month = $request->month;
        } else {
            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
        }
        // 前月、翌月対応
        $calcMonth = new Carbon();
        $preYear = $calcMonth->year($year)->month($month)->startOfMonth()->subMonth()->year;
        $preMonth = $calcMonth->year($year)->month($month)->startOfMonth()->subMonth()->month;
        $nextYear = $calcMonth->year($year)->month($month)->startOfMonth()->addMonth()->year;
        $nextMonth = $calcMonth->year($year)->month($month)->startOfMonth()->addMonth()->month;
        $searchDay = new Carbon();
        $searchDay->year($year)->month($month)->startOfMonth();
        $dayList = [];
        while ($searchDay <= Carbon::parse($year . '-' . $month . '-1')->endOfMonth()) {
            $start = null;
            $end = null;
            $restHours = 0;
            $restMinutes = 0;
            $restAllMinutes = 0;
            $workHours = 0;
            $workMinutes = 0;
            $workAllMinutes = 0;
            $sendAttendanceId = null;
            $pending = false;
            // 勤務記録有無判定
            if (Auth::user()->attendances()->whereDate('start', $searchDay)->exists()) {
                $attendance = Auth::user()->attendances()->whereDate('start', $searchDay)->first();
                // 詳細について、申請中がある場合は申請中情報へ遷移
                if ($attendance->requests()->where('status', 1)->exists()) {
                    $pending = true;
                    $sendAttendanceId = $attendance->requests()->where('status', 1)->first()->requestedAttendance()->first()->id;
                } else {
                    // 申請中がない場合は一覧に表示されている情報へ遷移
                    $sendAttendanceId = $attendance->id;
                }
                $start = Carbon::parse($attendance->start);
                // 退勤済み判定
                if ($attendance->end) {
                    $end = Carbon::parse($attendance->end);
                    // 休憩を分単位で合計
                    $rests = $attendance->rests->all();
                    foreach ($rests as $restRecord) {
                        $restAllMinutes += $restRecord->minutes();
                    }
                    // 合計勤務時間計算
                    $workAllMinutes = $attendance->minutes() - $restAllMinutes;
                    $workHours = floor($workAllMinutes / 60);
                    $workMinutes = floor($workAllMinutes % 60);
                    $restHours = floor($restAllMinutes / 60);
                    $restMinutes = floor($restAllMinutes % 60);
                }
            }
            ;
            $dayList[] = [
                'day' => $searchDay->isoFormat('MM月DD日(ddd)'),
                'start' => $start,
                'end' => $end,
                'restHours' => $restHours,
                'restMinutes' => $restMinutes,
                'workHours' => $workHours,
                'workMinutes' => $workMinutes,
                'pending' => $pending,
                'sendAttendanceId' => $sendAttendanceId,
            ];
            $searchDay->addDay();
        }
        ;
        return view('/attendance/list', compact(['year', 'month', 'preYear', 'preMonth', 'nextYear', 'nextMonth', 'dayList']));
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
    public function update($id)
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
