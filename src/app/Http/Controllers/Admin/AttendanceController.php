<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use Illuminate\Pagination\LengthAwarePaginator;
Paginator::useBootstrap();
class AttendanceController extends Controller
{
    public function index(Request $request)
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
        // ユーザー情報取得
        $users = User::orderBy('created_at', 'asc')->orderBy('id', 'asc')->get();
        $searchDay = new Carbon();
        $searchDay->year($year)->month($month)->day($day);
        $usersData = [];
        foreach ($users as $user) {
            $name = $user->name;
            $start = null;
            $end = null;
            $restHours = 0;
            $restMinutes = 0;
            $workHours = 0;
            $workMinutes = 0;
            $pending = false;
            $calcRestMinutes = 0;
            $calcWorkMinutes = 0;
            $sendAttendanceId = null;
            // 出勤判定
            if ($user->attendances()->whereDate('start', $searchDay)->exists()) {
                $start = $user->attendances()->whereDate('start', $searchDay)->first()->start;
                // 退勤判定
                if (!is_null($user->attendances()->whereDate('start', $searchDay)->first()->end)) {
                    $end = $user->attendances()->whereDate('start', $searchDay)->first()->end;
                    // 時間計算
                    $rests = $user->attendances()->whereDate('start', $searchDay)->first()->rests->all();
                    foreach ($rests as $rest) {
                        $calcRestMinutes += $rest->minutes();
                    }
                    $calcWorkMinutes = $user->attendances()->whereDate('start', $searchDay)->first()->minutes() - $calcRestMinutes;
                    $restHours = floor($calcRestMinutes / 60);
                    $restMinutes = floor($calcRestMinutes % 60);
                    $workHours = floor($calcWorkMinutes / 60);
                    $workMinutes = floor($calcWorkMinutes % 60);
                }
                // 申請中判定
                if ($user->attendances()->whereDate('start', $searchDay)->first()->requests()->where('status', 1)->exists()) {
                    $pending = true;
                    $sendAttendanceId = $user->attendances()->whereDate('start', $searchDay)->first()->requests()->where('status', 1)->first()->requestedAttendance->id;
                } else {
                    $pending = false;
                    $sendAttendanceId = $user->attendances()->whereDate('start', $searchDay)->first()->id;
                }
            }
            $usersData[] = [
                'name' => $name,
                'start' => $start,
                'end' => $end,
                'restHours' => $restHours,
                'restMinutes' => $restMinutes,
                'workHours' => $workHours,
                'workMinutes' => $workMinutes,
                'pending' => $pending,
                'sendAttendanceId' => $sendAttendanceId,
            ];
        }
        // ページネーションのための設定
        $page = $request->page;
        $perPage = 10;
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        // 配列をページ単位に分割
        $items = array_slice($usersData, $offset, $perPage);
        // 全体数を手動で指定
        $usersList = new LengthAwarePaginator($items, count($usersData), $perPage, $currentPage, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);
        return view('admin.attendance.list', compact(['year', 'month', 'day', 'preYear', 'preMonth', 'preDay', 'nextYear', 'nextMonth', 'nextDay', 'usersList', 'pending', 'sendAttendanceId', 'page']));
    }
    public function edit($id)
    {
        $attendance = Attendance::find($id);
        $name = $attendance->user->name;
        $attendanceId = $attendance->id;
        $start = $attendance->start;
        $end = $attendance->end;
        $rests = $attendance->rests()->orderBy('start')->get();
        $restsCount = $attendance->rests()->count();
        $note = $attendance->note;
        return view('admin.attendance.detail', compact(['attendanceId', 'name', 'start', 'end', 'rests', 'restsCount', 'note']));
    }
}
