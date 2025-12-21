<?php

namespace Tests\Feature;

use Database\Seeders\AttendancesTableSeeder;
use Database\Seeders\RestsTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Faker\Factory;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
class Case09GeneralUserAttendanceListTest extends TestCase
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
    }
    protected function tearDown(): void
    {
        Carbon::setTestNow();
    }

    public function test_自分の行った勤怠情報が全て表示される()
    {
        $this->seed(AttendancesTableSeeder::class);
        $response = $this->actingAs($this->user)
            ->get('/attendance/list');
        $countDate = $this->dateTime->copy()->startOfMonth();
        $i = 0;
        while ($countDate < $this->dateTime->copy()->lastOfMonth()) {
            $attendance = Attendance::whereDate('start', $countDate)->first();
            if (is_null($attendance)) {
                $workHours = null;
                $workMinutes = null;
                $start = null;
                $end = null;
            } else {
                $workAllMinutes = $attendance->minutes();
                $workHours = floor($workAllMinutes / 60);
                $workMinutes = floor($workAllMinutes % 60);
                $start = $attendance->start;
                $end = $attendance->end;
            }
            $response->assertViewHas('dayList', function ($dayList) use ($i, $start, $end, $countDate, $attendance, $workHours, $workMinutes) {
                return $dayList[$i]['day'] == $countDate->isoFormat('MM月DD日(ddd)')
                    && $dayList[$i]['start'] == $start
                    && $dayList[$i]['end'] == $end
                    && $dayList[$i]['workHours'] == $workHours
                    && $dayList[$i]['workMinutes'] == $workMinutes;
            });
            $i++;
            $countDate->addDay();
        }
    }
    public function test_勤怠一覧画面に遷移した際に現在の月が表示される()
    {
        $this->actingAs($this->user)
            ->get('/attendance/list')
            ->assertSee($this->dateTime->isoFormat('YYYY/MM'));
    }
    public function test_「前月」を押下した時に表示月の前月の情報が表示される()
    {
        $preMonth = $this->dateTime->copy()->subMonth();
        $this->actingAs($this->user)
            ->get('/attendance/list')
            ->assertDontSee('id="monthPicker" value="' . $preMonth->isoFormat('YYYY/MM') . '"', false);
        $this->actingAs($this->user)
            ->get('/attendance/list/?year=' . $preMonth->year . '&month=' . $preMonth->month);
    }
    public function test_「翌月」を押下した時に表示月の翌月の情報が表示される()
    {
        $nextMonth = $this->dateTime->copy()->addMonth();
        $this->actingAs($this->user)
            ->get('/attendance/list')
            ->assertDontSee('id="monthPicker" value="' . $nextMonth->isoFormat('YYYY/MM') . '"', false);
        $this->actingAs($this->user)
            ->get('/attendance/list/?year=' . $nextMonth->year . '&month=' . $nextMonth->month);
    }
    public function test_「詳細」を押下すると、その日の勤怠詳細画面に遷移する()
    {
        $this->seed(AttendancesTableSeeder::class);
        $attendanceId = Attendance::inRandomOrder()->first()->id;
        $this->actingAs($this->user)
            ->get('/attendance/detail/' . $attendanceId)
            ->assertOk()
            ->assertViewIs('attendance.detail');
    }
}
