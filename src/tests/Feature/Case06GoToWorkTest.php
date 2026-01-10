<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use Faker\Factory;
use Carbon\Carbon;
use App\Models\Attendance;
class Case06GoToWorkTest extends TestCase
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
    public function test_出勤ボタンが正しく機能する()
    {
        $this->actingAs($this->user)
            ->post('/attendance');
        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->user->id,
            'date' => $this->date,
            'start' => $this->dateTime,
            'end' => null,
            'note' => null,
        ]);
    }
    public function test_出勤時刻が勤怠一覧画面で確認できる()
    {
        $this->actingAs($this->user)
            ->post('/attendance');
        $attendanceId = Attendance::first()->id;
        $searchDay = $this->dateTime->copy()->startOfMonth();
        while ($searchDay <= $this->dateTime->copy()->lastOfMonth()) {
            $viewList[] = $searchDay->isoFormat('MM月DD日(ddd)');
            if ($searchDay->copy()->startOfDay() == $this->dateTime->copy()->startOfDay()) {
                $viewList[] = $this->dateTime->format('H:i');
                $dayList[] = [
                    'day' => $searchDay->isoFormat('MM月DD日(ddd)'),
                    'start' => $this->dateTime,
                    'end' => null,
                    'restHours' => 0,
                    'restMinutes' => 0,
                    'workHours' => 0,
                    'workMinutes' => 0,
                    'pending' => false,
                    'sendAttendanceId' => $attendanceId,
                    'hasRests' => false,
                ];
            } else {
                $dayList[] = [
                    'day' => $searchDay->isoFormat('MM月DD日(ddd)'),
                    'start' => null,
                    'end' => null,
                    'restHours' => 0,
                    'restMinutes' => 0,
                    'workHours' => 0,
                    'workMinutes' => 0,
                    'pending' => false,
                    'sendAttendanceId' => null,
                    'hasRests' => false,
                ];
            }
            $searchDay->addDay();
        }
        $this->actingAs($this->user)
            ->post('/attendance');
        $this->actingAs($this->user)
            ->get('/attendance/list')
            ->assertViewHas('dayList', $dayList)
            ->assertSeeInOrder($viewList);
    }
}
