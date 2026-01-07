<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use Faker\Factory;
use Carbon\Carbon;
use App\Models\Attendance;
class Case08LeaveWorkTest extends TestCase
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
        $this->afterDateTime = $this->dateTime->copy()->addMinutes(rand(0, 200));
        if ($this->afterDateTime->copy()->startOfDay()->gt($this->dateTime->copy()->startOfDay())) {
            $this->afterDateTime = $this->dateTime->copy()->endOfDay();
        }
    }
    protected function tearDown(): void
    {
        Carbon::setTestNow();
    }
    public function test_退勤ボタンが正しく機能する()
    {
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => $this->date,
            'start' => $this->dateTime,
        ]);
        $this->actingAs($this->user)
            ->get('/attendance')
            ->assertDontSee('退勤済');
        Carbon::setTestNow($this->afterDateTime);
        $this->actingAs($this->user)
            ->patch('/attendance/' . $attendance->id);
        $this->actingAs($this->user)
            ->get('/attendance')
            ->assertSee('退勤済');
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'user_id' => $this->user->id,
            'date' => $this->date,
            'start' => $this->dateTime,
            'end' => $this->afterDateTime,
        ]);
    }
    public function test_退勤時刻が勤怠一覧画面で確認できる()
    {
        $diffInMinutes = $this->afterDateTime->copy()->second(0)->diffInMinutes($this->dateTime->copy()->second(0));
        $dispHours = floor($diffInMinutes / 60);
        $dispMinutes = floor($diffInMinutes % 60);
        $this->actingAs($this->user)
            ->post('/attendance');
        Carbon::setTestNow($this->afterDateTime);
        $attendance = Attendance::first();
        $this->actingAs($this->user)
            ->patch('/attendance/' . $attendance->id);
        $searchDay = $this->dateTime->copy()->startOfMonth();
        while ($searchDay <= $this->dateTime->copy()->lastOfMonth()) {
            $viewList[] = $searchDay->isoFormat('MM月DD日(ddd)');
            if ($searchDay->copy()->startOfDay() == $this->dateTime->copy()->startOfDay()) {
                $viewList[] = $this->dateTime->format('H:i');
                $dayList[] = [
                    'day' => $searchDay->isoFormat('MM月DD日(ddd)'),
                    'start' => $this->dateTime,
                    'end' => $this->afterDateTime,
                    'restHours' => 0,
                    'restMinutes' => 0,
                    'workHours' => $dispHours,
                    'workMinutes' => $dispMinutes,
                    'pending' => false,
                    'sendAttendanceId' => $attendance->id,
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
            ->get('/attendance/list')
            ->assertViewHas('dayList', $dayList)
            ->assertSeeInOrder($viewList);
    }
}
