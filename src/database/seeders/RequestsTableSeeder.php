<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;
use App\Models\Admin;
use Carbon\Carbon;
class RequestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminId = Admin::first()->id;
        $attendances = Attendance::get();
        foreach ($attendances as $attendance) {
            $randNum = rand(0, 100);
            $today = Carbon::parse(now());
            $attendanceStart = Carbon::parse($attendance->start);
            $daysToToday = $attendanceStart->diffInDays($today);
            $daysArray = [$attendanceStart->copy()->addDays(rand(0, $daysToToday)), $attendanceStart->copy()->addDays(rand(0, $daysToToday))];
            sort($daysArray);
            for ($i = 0; $i < rand(0, 4); $i++) {
                if ($i == 0 && $randNum < 50) {
                    $param = [
                        'attendance_id' => $attendance->id,
                        'status' => 1,
                        'created_at' => $daysArray[0],
                        'updated_at' => $daysArray[0],
                    ];
                    DB::table('requests')->insert($param);
                } else {
                    $param = [
                        'attendance_id' => $attendance->id,
                        'status' => 2,
                        'approver' => $adminId,
                        'created_at' => $daysArray[0],
                        'updated_at' => $daysArray[1],
                    ];
                    DB::table('requests')->insert($param);
                }
            }
        }
    }
}
