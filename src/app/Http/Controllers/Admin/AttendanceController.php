<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function show(Request $request)
    {
        if ($request->year && $request->month && $request->day) {
            $year = $request->year;
            $month = $request->month;
            $day = $request->day;
        } else {
            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
            $day = Carbon::now()->day;
        }
        // 前日、翌日対応
        $preDate = new Carbon();
        $nextDate = new Carbon();
        $preYear = $preDate->year($year)->month($month)->day($day)->subDay()->year;
        $preMonth = $preDate->year($year)->month($month)->day($day)->subDay()->month;
        $preDay = $preDate->year($year)->month($month)->day($day)->subDay()->day;
        $nextYear = $nextDate->year($year)->month($month)->day($day)->addDay()->year;
        $nextMonth = $nextDate->year($year)->month($month)->day($day)->addDay()->month;
        $nextDay = $nextDate->year($year)->month($month)->day($day)->addDay()->day;
        return view('admin.attendance.list', compact(['year', 'month', 'day', 'preYear', 'preMonth', 'preDay', 'nextYear', 'nextMonth', 'nextDay']));
    }
}
