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
            $this->afterDateTime->hour(0)->minute(0)->second(0);
        }

    }
    protected function tearDown(): void
    {
        Carbon::setTestNow();
    }
    public function test_退勤ボタンが正しく機能する()
    {
        $attendance = $this->attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => $this->date,
            'start' => $this->dateTime,
        ]);
        $this->actingAs($this->user)
            ->get('/attendance')
            ->assertDontSee('退勤済')
            ->assertSee('class="attendance-btn">退勤</button>', false);
        Carbon::setTestNow($this->afterDateTime);
        $this->actingAs($this->user)
            ->patch('/attendance/' . $attendance->id);
        $this->actingAs($this->user)
            ->get('/attendance')
            ->assertSee('退勤済');
        $this->assertDatabaseHas('attendances', [
            'id' => $this->attendance->id,
            'user_id' => $this->user->id,
            'date' => $this->date,
            'start' => $this->dateTime,
            'end' => $this->afterDateTime,
        ]);
    }
    public function test_退勤時刻が勤怠一覧画面で確認できる()
    {
        $this->actingAs($this->user)
            ->post('/attendance');
        Carbon::setTestNow($this->afterDateTime);
        $attendance = Attendance::first();
        $this->actingAs($this->user)
            ->patch('/attendance/' . $attendance->id);
        $this->actingAs($this->user)
            ->get('/attendance/list')
            ->assertSee('<td class="table__data">' . $this->dateTime->isoFormat('MM月DD日(ddd)') . '</td>
                        <td class="table__data">' . $this->dateTime->format('H:i') . '</td>
                        <td class="table__data">
                                                                                                ' . $this->afterDateTime->format('H:i') . '
                                                                                    </td>', false);
    }
}
