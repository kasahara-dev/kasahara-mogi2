<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;
use App\Models\Admin;
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
            for ($i = 0; $i < rand(0, 4); $i++) {
                if ($i == 0 && $randNum < 50) {
                    $param = [
                        'attendance_id' => $attendance->id,
                        'status' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    DB::table('requests')->insert($param);
                } else {
                    $param = [
                        'attendance_id' => $attendance->id,
                        'status' => 2,
                        'approver' => $adminId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    DB::table('requests')->insert($param);
                }
            }
        }
    }
}
