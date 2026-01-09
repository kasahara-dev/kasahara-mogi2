<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Faker\Factory;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use Carbon\Carbon;
use Database\Seeders\AdminsTableSeeder;
use Database\Seeders\AttendancesTableSeeder;

class Case13AttendanceDetailReviseTest extends TestCase
{
    use DatabaseMigrations;
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->seed([AdminsTableSeeder::class, AttendancesTableSeeder::class]);
        $this->admin = Admin::first();
        $this->attendance = Attendance::inRandomOrder()->first();
        $this->faker = Factory::create('ja_JP');
    }
    public function test_勤怠詳細画面に表示されるデータが選択したものになっている()
    {
        $this->actingAs($this->admin, 'admin')
            ->get('/admin/attendance/' . $this->attendance->id)
            ->assertViewHasAll([
                'attendanceId' => $this->attendance->id,
                'name' => $this->user->name,
                'start' => $this->attendance->start,
                'end' => $this->attendance->end,
                'note' => $this->attendance->note,
            ]);
    }
    public function test_出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される()
    {
        $dateTime1 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        $dateTime2 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        while ($dateTime1->hour == $dateTime2->hour && $dateTime1->minute == $dateTime2->minute) {
            $dateTime2 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        }
        if ($dateTime1 < $dateTime2) {
            $beforeDateTime = $dateTime1;
            $afterDateTime = $dateTime2;
        } else {
            $beforeDateTime = $dateTime2;
            $afterDateTime = $dateTime1;
        }
        $expectRest = [1 => '-1'];
        $this->actingAs($this->admin, 'admin')
            ->put('/admin/attendance/' . $this->attendance->id, [
                'attendance_start_hour' => $afterDateTime->hour,
                'attendance_start_minute' => $afterDateTime->minute,
                'attendance_end_hour' => $beforeDateTime->hour,
                'attendance_end_minute' => $beforeDateTime->minute,
                'rest_start_hour' => $expectRest,
                'rest_start_minute' => $expectRest,
                'rest_end_hour' => $expectRest,
                'rest_end_minute' => $expectRest,
                'note' => $this->faker->sentence(),
            ])
            ->assertSessionHasErrors(['attendance_start_num' => '出勤時間もしくは退勤時間が不適切な値です']);
        $this->actingAs($this->admin, 'admin')
            ->get('/admin/attendance/' . $this->attendance->id)
            ->assertSee('出勤時間もしくは退勤時間が不適切な値です');
    }
    public function test_休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される()
    {
        $dateTime1 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        $dateTime2 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        $dateTime3 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        while (($dateTime1->hour == $dateTime2->hour && $dateTime1->minute == $dateTime2->minute) or ($dateTime1->hour == $dateTime3->hour && $dateTime1->minute == $dateTime3->minute) or ($dateTime2->hour == $dateTime3->hour && $dateTime2->minute == $dateTime3->minute)) {
            $dateTime2 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
            $dateTime3 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        }
        $timesArray = [$dateTime1, $dateTime2, $dateTime3];
        sort($timesArray);
        $this->actingAs($this->admin, 'admin')
            ->put('/admin/attendance/' . $this->attendance->id, [
                'attendance_start_hour' => $timesArray[0]->hour,
                'attendance_start_minute' => $timesArray[0]->minute,
                'attendance_end_hour' => $timesArray[1]->hour,
                'attendance_end_minute' => $timesArray[1]->minute,
                'rest_start_hour' => [1 => $timesArray[2]->hour],
                'rest_start_minute' => [1 => $timesArray[2]->minute],
                'rest_end_hour' => [1 => $timesArray[2]->hour],
                'rest_end_minute' => [1 => $timesArray[2]->minute],
                'note' => $this->faker->sentence(),
            ])
            ->assertSessionHasErrors(['rest_start_num.1' => '休憩時間が不適切な値です']);
        $this->actingAs($this->admin, 'admin')
            ->get('/admin/attendance/' . $this->attendance->id)
            ->assertSee('休憩時間が不適切な値です');
    }
    public function test_休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される()
    {
        $dateTime1 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        $dateTime2 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        $dateTime3 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        while (($dateTime1->hour == $dateTime2->hour && $dateTime1->minute == $dateTime2->minute) or ($dateTime1->hour == $dateTime3->hour && $dateTime1->minute == $dateTime3->minute) or ($dateTime2->hour == $dateTime3->hour && $dateTime2->minute == $dateTime3->minute)) {
            $dateTime2 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
            $dateTime3 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        }
        $timesArray = [$dateTime1, $dateTime2, $dateTime3];
        sort($timesArray);
        $this->actingAs($this->admin, 'admin')
            ->from('/attendance/detail/' . $this->attendance->id)
            ->post('/attendance/detail/' . $this->attendance->id, [
                'attendance_start_hour' => $timesArray[0]->hour,
                'attendance_start_minute' => $timesArray[0]->minute,
                'attendance_end_hour' => $timesArray[1]->hour,
                'attendance_end_minute' => $timesArray[1]->minute,
                'rest_start_hour' => [1 => $timesArray[0]->hour],
                'rest_start_minute' => [1 => $timesArray[0]->minute],
                'rest_end_hour' => [1 => $timesArray[2]->hour],
                'rest_end_minute' => [1 => $timesArray[2]->minute],
                'note' => $this->faker->sentence(),
            ])
            ->assertSessionHasErrors(['rest_end_num.1' => '休憩時間もしくは退勤時間が不適切な値です']);
        $this->actingAs($this->admin, 'admin')
            ->get('/admin/attendance/' . $this->attendance->id)
            ->assertSee('休憩時間もしくは退勤時間が不適切な値です');
    }
    public function test_備考欄が未入力の場合のエラーメッセージが表示される()
    {
        $dateTime1 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        $dateTime2 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        while ($dateTime1->hour == $dateTime2->hour && $dateTime1->minute == $dateTime2->minute) {
            $dateTime2 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        }
        if ($dateTime1 < $dateTime2) {
            $beforeDateTime = $dateTime1;
            $afterDateTime = $dateTime2;
        } else {
            $beforeDateTime = $dateTime2;
            $afterDateTime = $dateTime1;
        }
        $expectRest = [1 => '-1'];
        $this->actingAs($this->admin, 'admin')
            ->from('/attendance/detail/' . $this->attendance->id)
            ->post('/attendance/detail/' . $this->attendance->id, [
                'attendance_start_hour' => $beforeDateTime->hour,
                'attendance_start_minute' => $beforeDateTime->minute,
                'attendance_end_hour' => $afterDateTime->hour,
                'attendance_end_minute' => $afterDateTime->minute,
                'rest_start_hour' => $expectRest,
                'rest_start_minute' => $expectRest,
                'rest_end_hour' => $expectRest,
                'rest_end_minute' => $expectRest,
                'note' => '',
            ])
            ->assertSessionHasErrors(['note' => '備考を記入してください']);
        $this->actingAs($this->admin, 'admin')
            ->get('/admin/attendance/' . $this->attendance->id)
            ->assertSee('備考を記入してください');
    }
}
