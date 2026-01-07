<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;
use App\Models\Attendance;
use Database\Seeders\AttendancesTableSeeder;
use Database\Seeders\RestsTableSeeder;
use function PHPUnit\Framework\assertEquals;

class Case10AttendanceDetailGeneralUserTest extends DuskTestCase
{
    use DatabaseMigrations;
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $faker = Factory::create('ja_JP');
        $this->seed(AttendancesTableSeeder::class);
        $this->attendance = Attendance::inRandomOrder()->first();
    }
    public function test_勤怠詳細画面の「名前」がログインユーザーの氏名になっている()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit('/attendance/detail/' . $this->attendance->id);
            $name = $browser->text('@name');
            assertEquals($this->user->name, $name);
        });
    }
    public function test_勤怠詳細画面の「日付」が選択した日付になっている()
    {
        $this->date = Carbon::parse($this->attendance->start);
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit('/attendance/detail/' . $this->attendance->id);
            $dateText = $browser->text('@date');
            assertEquals($this->date->isoFormat('YYYY年MM月DD日'), $dateText);
        });
    }
    public function test_「出勤・退勤」にて記されている時間がログインユーザーの打刻と一致している()
    {
        $start = Carbon::parse($this->attendance->start);
        $this->startHour = $start->hour;
        $this->startMinute = $start->minute;
        $end = Carbon::parse($this->attendance->end);
        $this->endHour = $end->hour;
        $this->endMinute = $end->minute;
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit('/attendance/detail/' . $this->attendance->id)
                ->assertSelected('#attendance_start_hour', $this->startHour)
                ->assertSelected('#attendance_start_minute', $this->startMinute)
                ->assertSelected('#attendance_end_hour', $this->endHour)
                ->assertSelected('#attendance_end_minute', $this->endMinute);
        });
    }
    public function test_「休憩」にて記されている時間がログインユーザーの打刻と一致している()
    {
        $this->seed(RestsTableSeeder::class);
        while (count($this->attendance->rests) <= 0) {
            $this->attendance = Attendance::inRandomOrder()->first();
        }
        $this->rests = $this->attendance->rests->sortBy('start');
        $this->browse(function (Browser $browser) {
            $i = 0;
            $browser
                ->loginAs($this->user)
                ->visit('/attendance/detail/' . $this->attendance->id);
            foreach ($this->rests as $rest) {
                $i++;
                $start = Carbon::parse($rest->start);
                $startHour = $start->hour;
                $startMinute = $start->minute;
                $end = Carbon::parse($rest->end);
                $endHour = $end->hour;
                $endMinute = $end->minute;
                $browser
                    ->assertSelected('#rest_start_hour_' . $i, $startHour)
                    ->assertSelected('#rest_start_minute_' . $i, $startMinute)
                    ->assertSelected('#rest_end_hour_' . $i, $endHour)
                    ->assertSelected('#rest_end_minute_' . $i, $endMinute);
            }
        });
    }
}
