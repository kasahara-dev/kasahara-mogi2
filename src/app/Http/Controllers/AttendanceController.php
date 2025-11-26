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
            $workHours = 0;
            $workMinutes = 0;
            $restTimeSeconds = 0;
            $attendanceId = null;
            if (Auth::user()->attendances()->where('status', 0)->whereDate('start', $searchDay)->exists()) {
                $attendanceId = Auth::user()->attendances()->where('status', 0)->whereDate('start', $searchDay)->first()->id;
                $start = Carbon::parse(Auth::user()->attendances()->where('status', 0)->whereDate('start', $searchDay)->first()->start);
                // 退勤済み判定
                if (Auth::user()->attendances()->where('status', 0)->whereDate('start', $searchDay)->first()->end) {
                    $end = Carbon::parse(Auth::user()->attendances()->where('status', 0)->whereDate('start', $searchDay)->first()->end);
                    // 休憩を分単位で計算
                    $rests = Auth::user()->attendances()->where('status', 0)->whereDate('start', $searchDay)->first()->rests->all();
                    foreach ($rests as $restRecord) {
                        $startTime = Carbon::parse($restRecord->start)->second(0);
                        $endTime = Carbon::parse($restRecord->end)->second(0);
                        $diffInSeconds = $startTime->diffInSeconds($endTime);
                        $restTimeSeconds += $diffInSeconds;
                    }
                    // 合計勤務時間計算
                    $startTime = $start->second(0);
                    $endTime = $end->second(0);
                    $diffInSeconds = $startTime->diffInSeconds($endTime);
                    $diffInSeconds -= $restTimeSeconds;
                    $workHours = floor($diffInSeconds / 3600);
                    $workMinutes = floor($diffInSeconds % 3600) / 60;
                    $restHours = floor($restTimeSeconds / 3600);
                    $restMinutes = floor($restTimeSeconds % 3600) / 60;
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
                'attendanceId' => $attendanceId,
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
