<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;
use App\Http\Requests\AttendanceRequest;
use App\Models\Rest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Response;

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
        $pending = false;
        $sendAttendanceId = null;
        foreach ($users as $user) {
            $name = $user->name;
            $start = null;
            $end = null;
            $restHours = 0;
            $restMinutes = 0;
            $workHours = 0;
            $workMinutes = 0;
            $pending = false;
            $restAllMinutes = 0;
            $calcWorkMinutes = 0;
            $sendAttendanceId = null;
            $hasRests = false;
            // 出勤判定
            if ($user->attendances()->whereDate('start', $searchDay)->exists()) {
                $start = $user->attendances()->whereDate('start', $searchDay)->first()->start;
                $restAllMinutes = $rests = $user->attendances()->whereDate('start', $searchDay)->first()->restAllMinutes();
                $restHours = floor($restAllMinutes / 60);
                $restMinutes = floor($restAllMinutes % 60);
                $hasRests = $user->attendances()->whereDate('start', $searchDay)->first()->hasRests();
                // 退勤判定
                if (!is_null($user->attendances()->whereDate('start', $searchDay)->first()->end)) {
                    $end = $user->attendances()->whereDate('start', $searchDay)->first()->end;
                    // 時間計算
                    $calcWorkMinutes = $user->attendances()->whereDate('start', $searchDay)->first()->minutes() - $restAllMinutes;
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
                'hasRests' => $hasRests,
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
        $this->authorize('update', $attendance);
        $name = $attendance->user->name;
        $attendanceId = $attendance->id;
        $start = $attendance->start;
        $end = $attendance->end;
        $rests = $attendance->rests()->orderBy('start')->get();
        $restsCount = $attendance->rests()->count();
        $note = $attendance->note;
        // リダイレクト時以外は遷移元画面(=遷移先画面となる)を取得
        if (url()->previous() <> url()->current()) {
            session(['from' => url()->previous()]);
        }
        return view('admin.attendance.detail', compact(['attendanceId', 'name', 'start', 'end', 'rests', 'restsCount', 'note']));
    }
    public function update(AttendanceRequest $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            // 修正元情報
            $oldAttendance = Attendance::find($id);
            $this->authorize('update', $oldAttendance);
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
            // attendanceテーブル更新
            $attendance = Attendance::find($id)->update([
                'start' => $start,
                'end' => $end,
                'note' => $request->note,
            ]);
            // restsテーブル削除
            Rest::where('attendance_id', $id)->delete();
            // restsテーブル登録
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
                        'attendance_id' => $id,
                        'start' => $restStart,
                        'end' => $restEnd,
                    ]);
                }
            }
        });
        return redirect(session('from', 'admin/attendance/list'));
    }
    public function show(Request $request, $id)
    {
        $name = User::find($id)->name;
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
            $hasRests = false;
            // 勤務記録有無判定
            if (User::find($id)->attendances()->whereDate('start', $searchDay)->exists()) {
                $attendance = User::find($id)->attendances()->whereDate('start', $searchDay)->first();
                // 詳細について、申請中がある場合は申請中情報へ遷移
                if ($attendance->requests()->where('status', 1)->exists()) {
                    $pending = true;
                    $sendAttendanceId = $attendance->requests()->where('status', 1)->first()->requestedAttendance()->first()->id;
                } else {
                    // 申請中がない場合は一覧に表示されている情報へ遷移
                    $sendAttendanceId = $attendance->id;
                }
                $start = Carbon::parse($attendance->start);
                // 休憩を分単位で合計
                $restAllMinutes = $attendance->restAllMinutes();
                $restHours = floor($restAllMinutes / 60);
                $restMinutes = floor($restAllMinutes % 60);
                $hasRests = $attendance->hasRests();
                // 退勤済み判定
                if ($attendance->end) {
                    $end = Carbon::parse($attendance->end);
                    // 合計勤務時間計算
                    $workAllMinutes = $attendance->minutes() - $restAllMinutes;
                    $workHours = floor($workAllMinutes / 60);
                    $workMinutes = floor($workAllMinutes % 60);
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
                'hasRests' => $hasRests,
            ];
            $searchDay->addDay();
        }
        ;
        return view('admin.staff.attendance.list', compact(['id', 'name', 'year', 'month', 'preYear', 'preMonth', 'nextYear', 'nextMonth', 'dayList']));
    }
    public function export(Request $request, $id)
    {
        $name = User::find($id)->name;
        if ($request->year && $request->month) {
            $year = $request->year;
            $month = $request->month;
        } else {
            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
        }
        $searchDay = new Carbon();
        $searchDay->year($year)->month($month)->startOfMonth();
        $dayList = [];
        while ($searchDay <= Carbon::parse($year . '-' . $month . '-1')->endOfMonth()) {
            $start = null;
            $end = null;
            $restHours = 0;
            $restMinutes = 0;
            $restAllMinutes = 0;
            $restTimes = '';
            $workTimes = '';
            $workHours = 0;
            $workMinutes = 0;
            $workAllMinutes = 0;
            $sendAttendanceId = null;
            $pending = false;
            $hasRests = false;
            // 勤務記録有無判定
            if (User::find($id)->attendances()->whereDate('start', $searchDay)->exists()) {
                $attendance = User::find($id)->attendances()->whereDate('start', $searchDay)->first();
                $start = Carbon::parse($attendance->start)->format('H:i');
                // 休憩を分単位で合計
                $restAllMinutes = $attendance->restAllMinutes();
                $hasRests = $attendance->hasRests();
                if ($hasRests) {
                    $restHours = sprintf('%02d', floor($restAllMinutes / 60));
                    $restMinutes = sprintf('%02d', floor($restAllMinutes % 60));
                    $restTimes = $restHours . ':' . $restMinutes;
                }
                // 退勤済み判定
                if ($attendance->end) {
                    // 24時判定
                    if (Carbon::parse($attendance->end)->startOfDay()->gt(Carbon::parse($attendance->start)->startOfDay())) {
                        $end = '24:00';
                    } else {
                        $end = Carbon::parse($attendance->end)->format('H:i');
                    }
                    // 合計勤務時間計算
                    $workAllMinutes = $attendance->minutes() - $restAllMinutes;
                    $workHours = sprintf('%02d', floor($workAllMinutes / 60));
                    $workMinutes = sprintf('%02d', floor($workAllMinutes % 60));
                    $workTimes = $workHours . ':' . $workMinutes;
                }
            }
            ;
            $dayList[] = [
                'day' => $searchDay->isoFormat('YYYY/MM/DD'),
                'start' => $start,
                'end' => $end,
                'restTimes' => $restTimes,
                'workTimes' => $workTimes,
            ];
            $searchDay->addDay();
        }
        ;

        $head = ['日付', '出勤', '退勤', '休憩', '合計'];
        $temps = [];
        array_push($temps, $head);
        foreach ($dayList as $dayLine) {
            $temp = [
                $dayLine['day'],
                $dayLine['start'],
                $dayLine['end'],
                $dayLine['restTimes'],
                $dayLine['workTimes'],
            ];
            array_push($temps, $temp);
        }
        $stream = fopen('php://temp', 'r+b');
        foreach ($temps as $temp) {
            fputcsv($stream, $temp);
        }
        rewind($stream);
        $csv = str_replace(PHP_EOL, "\r\n", stream_get_contents($stream));
        $csv = mb_convert_encoding($csv, 'SJIS-win', 'UTF-8');
        $filename = $name . '_' . $year . '年' . $month . '月勤怠.csv';
        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        );
        return Response::make($csv, 200, $headers);
    }
}
