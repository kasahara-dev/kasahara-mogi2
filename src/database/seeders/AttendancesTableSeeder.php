<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
        $userIds = User::pluck('id');
        $faker = Factory::create('ja_JP');
        foreach ($userIds as $userId) {
            $start = Carbon::parse(now());
            $end = Carbon::parse(now());
            for ($i = 0; $i < 50; $i++) {
                $start->subDay();
                $end->subDay();
                // 1/2の確率でデータ作成
                $randNum = rand(0, 100);
                if ($randNum > 50) {
                    $param = [
                        'user_id' => $userId,
                        'date' => $setStart = $start->hour(rand(0, 11))->minute(rand(0, 59)),
                        'start' => $setStart,
                        'end' => $setEnd = $end->hour(rand(12, 23))->minute(rand(0, 59)),
                        'note' => $faker->optional()->sentence(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    DB::table('attendances')->insert($param);
                }
            }
        }
    }
}
