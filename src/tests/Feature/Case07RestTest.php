<?php

namespace Tests\Feature;

use Date;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
            ->get('/attendance')
            ->assertDontSee('休憩中')
            ->assertSee('class="rest-btn">休憩入</button>', false);
        $this->actingAs($this->user)
            ->post('/attendance/rest');
        $this->actingAs($this->user)
            ->get('/attendance')
            ->assertSee('休憩中');
        $this->assertDatabaseHas('rests', [
            'attendance_id' => $this->attendance->id,
            'start' => $this->dateTime,
            'end' => null,
        ]);
    }
    public function test_休憩は一日に何回でもできる()
    {
        $this->actingAs($this->user)
            ->get('/attendance')
            ->assertDontSee('休憩中')
            ->assertSee('class="rest-btn">休憩入</button>', false);
        $this->actingAs($this->user)
            ->post('/attendance/rest');
        $restId = Rest::where('attendance_id', $this->attendance->id)->first()->id;
        $this->actingAs($this->user)
            ->patch('/attendance/rest/' . $restId);
        $this->actingAs($this->user)
            ->get('/attendance')
            ->assertSee('class="rest-btn">休憩入</button>', false);
    }
    public function test_休憩戻ボタンが正しく機能する()
    {
        $this->actingAs($this->user)
            ->post('/attendance/rest');
        $this->actingAs($this->user)
            ->get('/attendance')
            ->assertDontSee('出勤中')
            ->assertSee('class="rest-btn">休憩戻</button>', false)
            ->assertDontSee('class="rest-btn">休憩入</button>', false);
        $restId = Rest::where('attendance_id', $this->attendance->id)->first()->id;
        $this->actingAs($this->user)
            ->patch('/attendance/rest/' . $restId);
        $this->actingAs($this->user)
            ->get('/attendance')
            ->assertSee('出勤中')
            ->assertDontSee('class="rest-btn">休憩戻</button>', false)
            ->assertSee('class="rest-btn">休憩入</button>', false);
        $this->assertDatabaseHas('rests', [
            'attendance_id' => $this->attendance->id,
            'start' => $this->dateTime,
            'end' => $this->dateTime,
        ]);
    }
    public function test_休憩戻は一日に何回でもできる()
    {
        $this->actingAs($this->user)
            ->post('/attendance/rest');
        $restId = Rest::where('attendance_id', $this->attendance->id)->first()->id;
        $this->actingAs($this->user)
            ->patch('/attendance/rest/' . $restId);
        $this->actingAs($this->user)
            ->post('/attendance/rest');
        $this->actingAs($this->user)
            ->get('/attendance')
            ->assertSee('class="rest-btn">休憩戻</button>', false);
    }
    public function test_休憩時刻が勤怠一覧画面で確認できる()
    {
        $afterDateTime = $this->dateTime->copy()->addMinutes(rand(0, 200));
        while($afterDateTime->copy()->startOfDay()->gt($this->date)){
            $afterDateTime = $this->dateTime->copy()->addMinutes(rand(0, 200));
        }
        $diffInMinutes = $afterDateTime->copy()->second(0)->diffInMinutes($this->dateTime->copy()->second(0));
        $dispHours = sprintf('%02d', floor($diffInMinutes / 60));
        $dispMinutes = sprintf('%02d', floor($diffInMinutes % 60));
        $this->actingAs($this->user)
            ->post('/attendance/rest');
        if ($afterDateTime->copy()->startOfDay()->gt($this->dateTime->copy()->startOfDay())) {
            $afterDateTime->hour(0)->minute(0)->second(0);
        }
        $restId = Rest::where('attendance_id', $this->attendance->id)->first()->id;
        Carbon::setTestNow($afterDateTime);
        $this->actingAs($this->user)
            ->patch('/attendance/rest/' . $restId);
        $this->actingAs($this->user)
            ->get('/attendance/list')
            ->assertSee('<td class="table__data">' . $this->dateTime->isoFormat('MM月DD日(ddd)') . '</td>
                        <td class="table__data">' . $this->dateTime->format('H:i') . '</td>
                        <td class="table__data">
                                                    </td>
                        <td class="table__data">
                            ' . $dispHours . ':' . $dispMinutes . '                        </td>', false);
    }
}
