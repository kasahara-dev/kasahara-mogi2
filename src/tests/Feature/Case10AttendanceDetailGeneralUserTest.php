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

class Case10AttendanceDetailGeneralUserTest extends TestCase
{
    use DatabaseMigrations;
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $faker = Factory::create('ja_JP');
        $this->dateTime = Carbon::parse($faker->dateTime());
        $this->date = $this->dateTime->toDateString();
        Carbon::setTestNow($this->dateTime);
        $this->seed(AttendancesTableSeeder::class);
        $this->attendance = Attendance::inRandomOrder()->first();
    }
    protected function tearDown(): void
    {
        Carbon::setTestNow();
    }

    public function test_勤怠詳細画面の「名前」がログインユーザーの氏名になっている()
    {
        $this->actingAs($this->user)
            ->get('/attendance/detail/' . $this->attendance->id)
            ->assertSee('<dt class="list-line-title">名前</dt>
                    <dd class="list-line-data">
                        ' . $this->user->name . '
                        <div class="list-line-errors-area">', false);
    }
    public function test_勤怠詳細画面の「日付」が選択した日付になっている()
    {
        $date = Carbon::parse($this->attendance->start);
        $this->actingAs($this->user)
            ->get('/attendance/detail/' . $this->attendance->id)
            ->assertSee('<dt class="list-line-title">日付</dt>
                    <dd class="list-line-data">
                        ' . $date->isoFormat('YYYY年MM月DD日') . '
                        <div class="list-line-errors-area"></div>', false);
    }
    public function test_「出勤・退勤」にて記されている時間がログインユーザーの打刻と一致している()
    {
        $start = Carbon::parse($this->attendance->start);
        $startHour = $start->hour;
        $startMinute = $start->minute;
        $end = Carbon::parse($this->attendance->end);
        $endHour = $end->hour;
        $endMinute = $end->minute;
        $this->actingAs($this->user)
            ->get('/attendance/detail/' . $this->attendance->id)
            ->assertSeeInOrder([
                '<select name="attendance_start_hour" id="attendance_start_hour" class="list-line-selector">',
                '<option value="' . $startHour . '"
                                                                                    selected',
                '<select name="attendance_start_minute" id="attendance_start_minute" class="list-line-selector">',
                '<option value="' . $startMinute . '"
                                                                                    selected
                                                                                >',
                '<select name="attendance_end_hour" id="attendance_end_hour" class="list-line-selector">',
                '<option value="' . $endHour . '"
                                                                                    selected
                                                                                >',
                '<select name="attendance_end_minute" id="attendance_end_minute" class="list-line-selector">',
                '<option value="' . $endMinute . '"
                                                                                    selected
                                                                                >'
            ], false);
    }
    public function test_「休憩」にて記されている時間がログインユーザーの打刻と一致している()
    {
        $this->seed(RestsTableSeeder::class);
        while (count($this->attendance->rests) <= 0) {
            $this->attendance = Attendance::inRandomOrder()->first();
        }
        $rests = $this->attendance->rests->sortBy('start');
        $i = 0;
        foreach ($rests as $rest) {
            $i++;
            $start = Carbon::parse($rest->start);
            $startHour = $start->hour;
            $startMinute = $start->minute;
            $end = Carbon::parse($rest->end);
            $endHour = $end->hour;
            $endMinute = $end->minute;
            $exceptTexts[] = 'id="rest_start_hour_' . $i . '"';
            $exceptTexts[] = '<option value="' . $startHour . '"
                                                                                    selected
                                                                                >';
            $exceptTexts[] = 'id="rest_start_minute_' . $i . '"';
            $exceptTexts[] = '<option value="' . $startMinute . '"
                                                                                    selected
                                                                                >';
            $exceptTexts[] = 'id="rest_end_hour_' . $i . '"';
            $exceptTexts[] = '<option value="' . $endHour . '"
                                                                                    selected
                                                                                >';
            $exceptTexts[] = 'id="rest_end_minute_' . $i . '"';
            $exceptTexts[] = '<option value="' . $endMinute . '"
                                                                                    selected
                                                                                >';
        }
        $this->actingAs($this->user)
            ->get('/attendance/detail/' . $this->attendance->id)
            ->assertSeeInOrder($exceptTexts, false);
    }
}
