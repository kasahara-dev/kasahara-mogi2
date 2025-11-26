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
        // 9時前から勤務と15時以降勤務に休憩付与
        $attendances = Attendance::whereNotIn('id', [1, 2, 3, 4, 5])->where('status', 0)->get();
        foreach ($attendances as $attendance) {
            $earlyTime = Carbon::parse($attendance->start)->hour(9)->minute(0);
            $lateTime = Carbon::parse($attendance->start)->hour(15)->minute(0);
            $start = Carbon::parse($attendance->start);
            $setStart = Carbon::parse($attendance->start)->addMinutes(rand(0, 100));
            $setEnd = Carbon::parse($setStart)->addMinutes(rand(0, 100));
            // 9時前判定
            if ($start->lt($earlyTime)) {
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
