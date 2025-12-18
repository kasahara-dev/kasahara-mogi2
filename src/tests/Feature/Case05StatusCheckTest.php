<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Faker\Factory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Attendance;
use App\Models\Rest;
class Case05StatusCheckTest extends TestCase
{
    use DatabaseMigrations;
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }
    public function test_勤務外の場合、勤怠ステータスが正しく表示される()
    {
        $this->actingAs($this->user)->get('/attendance')
            ->assertSee('勤務外')
            ->assertDontSee('出勤中')
            ->assertDontSee('休憩中')
            ->assertDontSee('退勤済');
    }
    public function test_出勤中の場合、勤怠ステータスが正しく表示される()
    {
        Attendance::create([
            'user_id' => $this->user->id,
            'date' => today(),
            'start' => now(),
        ]);
        $this->actingAs($this->user)->get('/attendance')
            ->assertDontSee('勤務外')
            ->assertSee('出勤中')
            ->assertDontSee('休憩中')
            ->assertDontSee('退勤済');
    }
    public function test_休憩中の場合、勤怠ステータスが正しく表示される()
    {
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => today(),
            'start' => now(),
        ]);
        Rest::create([
            'attendance_id' => $attendance->id,
            'start' => now(),
        ]);
        $this->actingAs($this->user)->get('/attendance')
            ->assertDontSee('勤務外')
            ->assertDontSee('出勤中')
            ->assertSee('休憩中')
            ->assertDontSee('退勤済');
    }
    public function test_退勤済の場合、勤怠ステータスが正しく表示される()
    {
        Attendance::create([
            'user_id' => $this->user->id,
            'date' => today(),
            'start' => now(),
            'end' => now(),
        ]);
        $this->actingAs($this->user)->get('/attendance')
            ->assertDontSee('勤務外')
            ->assertDontSee('出勤中')
            ->assertDontSee('休憩中')
            ->assertSee('退勤済');
    }
}
