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
        $attendances = Attendance::get();
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
                    'created_at' => now(),
                    'updated_at' => now(),
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
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                DB::table('rests')->insert($param);
            }
        }
    }
}
