<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Faker\Factory;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Database\Seeders\AttendancesTableSeeder;
use Database\Seeders\RestsTableSeeder;

class Case11ReviseAttendanceGeneralUserTest extends TestCase
{
    use DatabaseMigrations;
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->faker = Factory::create('ja_JP');
        $this->dateTime = Carbon::parse($this->faker->dateTime());
        $this->date = $this->dateTime->toDateString();
        Carbon::setTestNow($this->dateTime);
        $this->seed(AttendancesTableSeeder::class);
        $this->attendance = Attendance::inRandomOrder()->first();
    }
    protected function tearDown(): void
    {
        Carbon::setTestNow();
    }
    public function test_出勤時間が退勤時刻より後になっている場合、エラーメッセージが表示される()
    {
        $dateTime1 = Carbon::parse($this->faker->dateTimeBetween($this->date, Carbon::parse($this->date)->endOfDay()));
        $dateTime2 = Carbon::parse($this->faker->dateTimeBetween($this->date, Carbon::parse($this->date)->endOfDay()));
        while ($dateTime1->hour == $dateTime2->hour && $dateTime1->minute == $dateTime2->minute) {
            $dateTime2 = Carbon::parse($this->faker->dateTimeBetween($this->date, Carbon::parse($this->date)->endOfDay()));
        }
        if ($dateTime1 < $dateTime2) {
            $beforeDateTime = $dateTime1;
            $afterDateTime = $dateTime2;
        } else {
            $beforeDateTime = $dateTime2;
            $afterDateTime = $dateTime1;
        }
        $exceptRest = [1 => '-1'];
        $this->actingAs($this->user)
            ->from('/attendance/detail/' . $this->attendance->id)
            ->post('/attendance/detail/' . $this->attendance->id, [
                'attendance_start_hour' => $afterDateTime->hour,
                'attendance_start_minute' => $afterDateTime->minute,
                'attendance_end_hour' => $beforeDateTime->hour,
                'attendance_end_minute' => $beforeDateTime->minute,
                'rest_start_hour' => $exceptRest,
                'rest_start_minute' => $exceptRest,
                'rest_end_hour' => $exceptRest,
                'rest_end_minute' => $exceptRest,
                'note' => $this->faker->realText(),
            ])
            ->assertSessionHasErrors(['attendance_start_num' => '出勤時間が不適切な値です']);
    }
}
