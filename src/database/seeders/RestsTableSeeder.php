<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class RestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // テストユーザー1は固定
        for ($i = 1; $i <= 5; $i++) {
            $start = Carbon::parse(Attendance::find($i)->start)->hour(12)->min(0);
            $end = Carbon::parse(Attendance::find($i)->start)->hour(13)->min(0);
            $param = [
                'attendance_id' => $i,
                'start' => $start,
                'end' => $end,
            ];
            DB::table('rests')->insert($param);
        }
        // 通常＆承認後
        // 9時前から勤務と15時以降勤務に休憩付与
        $attendances = Attendance::whereNotIn('id', [1, 2, 3, 4, 5])->where('status', 0)->get();
        foreach ($attendances as $attendance) {
            $earlyTime = Carbon::parse($attendance->start)->hour(9)->minute(0);
            $lateTime = Carbon::parse($attendance->start)->hour(15)->minute(0);
            $start = Carbon::parse($attendance->start);
            $end = Carbon::parse($attendance->end);
            $setStart = Carbon::parse($attendance->start)->addMinutes(rand(0, 100));
            $setEnd = Carbon::parse($setStart)->addMinutes(rand(0, 100));
            // 承認後がある場合は同じ値をセットしたいので、承認後有無判定
            if (Attendance::where('status', 2)->whereDate('start', $start)->where('user_id', $attendance->user_id)->exists()) {
                $status2 = true;
                $status2AttendanceId = Attendance::where('status', 2)->whereDate('start', $start)->where('user_id', $attendance->user_id)->first()->id;
            } else {
                $status2 = false;
                $status2AttendanceId = null;
            }
            // 9時前
            if ($start->lt($earlyTime)) {
                $param = [
                    'attendance_id' => $attendance->id,
                    'start' => $setStart,
                    'end' => $setEnd,
                ];
                DB::table('rests')->insert($param);
                if ($status2) {
                    $param = [
                        'attendance_id' => $status2AttendanceId,
                        'start' => $setStart,
                        'end' => $setEnd,
                    ];
                    DB::table('rests')->insert($param);
                }
            }
            // 15時後
            $setStart = Carbon::parse($attendance->start)->hour(12)->minute(0)->addMinutes(rand(0, 100));
            $setEnd = Carbon::parse($setStart)->addMinutes(rand(0, 100));
            if ($end->gt($lateTime)) {
                $param = [
                    'attendance_id' => $attendance->id,
                    'start' => $setStart,
                    'end' => $setEnd,
                ];
                DB::table('rests')->insert($param);
                if ($status2) {
                    $param = [
                        'attendance_id' => $status2AttendanceId,
                        'start' => $setStart,
                        'end' => $setEnd,
                    ];
                    DB::table('rests')->insert($param);
                }
            }
        }
        // 承認前
        // 9時前から勤務と15時以降勤務に休憩付与
        $attendances = Attendance::whereNotIn('id', [1, 2, 3, 4, 5])->where('status', 1)->get();
        foreach ($attendances as $attendance) {
            $earlyTime = Carbon::parse($attendance->start)->hour(9)->minute(0);
            $lateTime = Carbon::parse($attendance->start)->hour(15)->minute(0);
            $start = Carbon::parse($attendance->start);
            $end = Carbon::parse($attendance->end);
            $setStart = Carbon::parse($attendance->start)->addMinutes(rand(0, 100));
            $setEnd = Carbon::parse($setStart)->addMinutes(rand(0, 100));
            // 9時前
            if ($start->lt($earlyTime)) {
                $param = [
                    'attendance_id' => $attendance->id,
                    'start' => $setStart,
                    'end' => $setEnd,
                ];
                DB::table('rests')->insert($param);
            }
            // 15時後
            $setStart = Carbon::parse($attendance->start)->hour(12)->minute(0)->addMinutes(rand(0, 100));
            $setEnd = Carbon::parse($setStart)->addMinutes(rand(0, 100));
            if ($end->gt($lateTime)) {
                $param = [
                    'attendance_id' => $attendance->id,
                    'start' => $setStart,
                    'end' => $setEnd,
                ];
                DB::table('rests')->insert($param);
            }
        }
    }
}
