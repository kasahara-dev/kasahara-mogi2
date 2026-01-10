<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use Faker\Factory;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\Rest;
class Case07RestTest extends TestCase
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
        $this->attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => $this->date,
            'start' => $this->dateTime,
        ]);
    }
    protected function tearDown(): void
    {
        Carbon::setTestNow();
    }
    public function test_休憩ボタンが正しく機能する()
    {
        $this->actingAs($this->user)
            ->post('/attendance/rest');
        $this->assertDatabaseHas('rests', [
            'attendance_id' => $this->attendance->id,
            'start' => $this->dateTime,
            'end' => null,
        ]);
    }
    public function test_休憩戻ボタンが正しく機能する()
    {
        $this->actingAs($this->user)
            ->post('/attendance/rest');
        $restId = Rest::where('attendance_id', $this->attendance->id)->first()->id;
        $this->actingAs($this->user)
            ->patch('/attendance/rest/' . $restId);
        $this->assertDatabaseHas('rests', [
            'attendance_id' => $this->attendance->id,
            'start' => $this->dateTime,
            'end' => $this->dateTime,
        ]);
    }
    public function test_休憩時刻が勤怠一覧画面で確認できる()
    {
        $afterDateTime = $this->dateTime->copy()->addMinutes(rand(0, 200));
        if ($afterDateTime->copy()->startOfDay()->gt($this->date)) {
            $afterDateTime = $this->dateTime->copy()->endOfDay();
        }
        $diffInMinutes = $afterDateTime->copy()->second(0)->diffInMinutes($this->dateTime->copy()->second(0));
        $dispHours = sprintf('%02d', floor($diffInMinutes / 60));
        $dispMinutes = sprintf('%02d', floor($diffInMinutes % 60));
        $this->actingAs($this->user)
            ->post('/attendance/rest');
        $restId = Rest::where('attendance_id', $this->attendance->id)->first()->id;
        Carbon::setTestNow($afterDateTime);
        $this->actingAs($this->user)
            ->patch('/attendance/rest/' . $restId);
        $searchDay = $this->dateTime->copy()->startOfMonth();
        while ($searchDay <= $this->dateTime->copy()->lastOfMonth()) {
            $viewList[] = $searchDay->isoFormat('MM月DD日(ddd)');
            if ($searchDay->copy()->startOfDay() == $this->dateTime->copy()->startOfDay()) {
                $viewList[] = $this->dateTime->format('H:i');
                $dayList[] = [
                    'day' => $searchDay->isoFormat('MM月DD日(ddd)'),
                    'start' => $this->dateTime,
                    'end' => null,
                    'restHours' => $dispHours,
                    'restMinutes' => $dispMinutes,
                    'workHours' => 0,
                    'workMinutes' => 0,
                    'pending' => false,
                    'sendAttendanceId' => $this->attendance->id,
                    'hasRests' => true,
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
            ->assertSeeInOrder($viewList)
            ->assertViewHas('dayList', $dayList);
    }
}
