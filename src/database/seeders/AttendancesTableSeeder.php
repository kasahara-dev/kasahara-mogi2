<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;




class AttendancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // テストユーザー1は固定で作成
        // 前日通常のみ
        $start = Carbon::parse(now())->subDay()->hour(9)->minute(0);
        $end = Carbon::parse(now())->subDay()->hour(18)->minute(0);
        $param = [
            'user_id' => '1',
            'start' => $start,
            'end' => $end,
            'status' => '0'
        ];
        DB::table('attendances')->insert($param);
        // 2日前承認待ち
        $start->subDay();
        $end->subDay();
        $param = [
            'user_id' => '1',
            'start' => $start,
            'end' => $end,
            'status' => '0'
        ];
        DB::table('attendances')->insert($param);
        $param = [
            'user_id' => '1',
            'start' => $start->subMinutes(30),
            'end' => $end->addMinutes(30),
            'note' => '出勤時間を30分早く、退勤時間を30分遅く変更しました。休憩時間を5分長く変更しました。',
            'status' => '1'
        ];
        DB::table('attendances')->insert($param);
        // 3日前承認済み
        $start->subDay()->hour(9)->minute(0);
        $end->subDay()->hour(19)->minute(30);
        $faker = Factory::create('ja_JP');
        $param = [
            'user_id' => '1',
            'start' => $start,
            'end' => $end,
            'note' => $faker->realText(),
            'status' => '0'
        ];
        DB::table('attendances')->insert($param);
        $param = [
            'user_id' => '1',
            'start' => $start,
            'end' => $end,
            'note' => $faker->realText(),
            'status' => '2'
        ];
        DB::table('attendances')->insert($param);
        // 50日前までランダムで通常作成
        for ($i = 0; $i < 50; $i++) {
            $start->subDay();
            $end->subDay();
            $randNum = rand(0, 100);
            if ($randNum > 50) {
                $param = [
                    'user_id' => '1',
                    'start' => $start->hour(rand(0, 11))->minute(rand(0, 59)),
                    'end' => $end->hour(rand(12, 23))->minute(rand(0, 59)),
                    'note' => $faker->optional()->realText(),
                    'status' => '0'
                ];
                DB::table('attendances')->insert($param);
            }
        }
        // テストユーザー1以外はランダム作成
        $userIds = User::where('id', '<>', '1')->pluck('id');
        foreach ($userIds as $userId) {
            $start = Carbon::parse(now());
            $end = Carbon::parse(now());
            for ($i = 0; $i < 50; $i++) {
                $start->subDay();
                $end->subDay();
                $randNum = rand(0, 100);
                if ($randNum > 50) {
                    $param = [
                        'user_id' => $userId,
                        'start' => $setStart = $start->hour(rand(0, 11))->minute(rand(0, 59)),
                        'end' => $setEnd = $end->hour(rand(12, 23))->minute(rand(0, 59)),
                        'note' => $setNote = $faker->optional()->realText(),
                        'status' => '0'
                    ];
                    DB::table('attendances')->insert($param);
                    $randStatus = (rand(0, 2));
                    if ($randStatus == 1) {
                        $param = [
                            'user_id' => $userId,
                            'start' => $start->hour(rand(0, 11))->minute(rand(0, 59)),
                            'end' => $end->hour(rand(12, 23))->minute(rand(0, 59)),
                            'note' => $faker->optional()->realText(),
                            'status' => '1'
                        ];
                        DB::table('attendances')->insert($param);
                    } elseif ($randStatus == 2) {
                        $param = [
                            'user_id' => $userId,
                            'start' => $setStart,
                            'end' => $setEnd,
                            'note' => $setNote,
                            'status' => '2'
                        ];
                        DB::table('attendances')->insert($param);
                    }
                }
            }
        }
    }
}
