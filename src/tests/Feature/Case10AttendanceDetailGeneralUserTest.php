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
        $this->seed(AttendancesTableSeeder::class);
        $this->attendance = Attendance::inRandomOrder()->first();
    }
    public function test_勤怠詳細画面の「名前」がログインユーザーの氏名になっている()
    {
        $this->actingAs($this->user)
            ->get('/attendance/detail/' . $this->attendance->id)
            ->assertViewHas('name', $this->user->name);
    }
    public function test_勤怠詳細画面の「日付」が選択した日付になっている()
    {
        $date = Carbon::parse($this->attendance->start);
        $this->actingAs($this->user)
            ->get('/attendance/detail/' . $this->attendance->id)
            ->assertViewHas('start', $this->attendance->start);
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
            ->assertViewHasAll(['start' => $this->attendance->start, 'end' => $this->attendance->end]);
    }
    public function test_「休憩」にて記されている時間がログインユーザーの打刻と一致している()
    {
        $this->seed(RestsTableSeeder::class);
        while (count($this->attendance->rests) <= 0) {
            $this->attendance = Attendance::inRandomOrder()->first();
        }
        $rests = $this->attendance->rests->sortBy('start');
        $this->actingAs($this->user)
            ->get('/attendance/detail/' . $this->attendance->id)
            ->assertViewHas('rests', $rests);
    }
}
