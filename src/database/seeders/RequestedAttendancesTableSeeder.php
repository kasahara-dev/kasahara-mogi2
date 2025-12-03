<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use Faker\Factory;
use Carbon\Carbon;
use App\Models\Request;
class RequestedAttendancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $requests = Request::get();
        $faker = Factory::create('ja_JP');
        foreach ($requests as $request) {
            $start = Carbon::parse($request->attendance->start)->startOfDay();
            $end = Carbon::parse($request->attendance->start)->startOfDay();
            $param = [
                'request_id' => $request->id,
                'start' => $start->hour(rand(0, 11))->minute(rand(0, 59)),
                'end' => $end->hour(rand(12, 23))->minute(rand(0, 59)),
                'note' => $faker->realText(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            DB::table('requested_attendances')->insert($param);
        }
    }
}

