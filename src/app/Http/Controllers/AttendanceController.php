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
        $preYear = Carbon::parse($year . '-' . $month . '-1')->subMonth()->year;
        $preMonth = Carbon::parse($year . '-' . $month . '-1')->subMonth()->month;
        $nextYear = Carbon::parse($year . '-' . $month . '-1')->addMonth()->year;
        $nextMonth = Carbon::parse($year . '-' . $month . '-1')->addMonth()->month;
        // 一覧対応
        $searchDay = Carbon::parse($year . '-' . $month . '-1');
        $dayList = [];
        // $listLineCount = 0;
        while ($searchDay <= Carbon::parse($year . '-' . $month . '-1')->endOfMonth()) {
            // $listLineCount++;
            if (Auth::user()->attendances()->where('status', 0)->where('start', $searchDay)->exists()) {
                $start = Auth::user()->attendances()->where('status', 0)->where('start', $searchDay)->start->format('HH:ii');
            } else {
                $start = null;
            }
            ;
            $dayList += array(
                // $listLineCount,
                [
                    'day' => $searchDay->isoFormat('MM月DD日(ddd)'),
                ]
            );
            // \Log::info('search day is ' . $searchDay->isoFormat('MM月DD日(ddd)'));
            $searchDay->addDay();
        }
        ;
        \Log::info('day List is ' . $dayList[0]['day']);
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
