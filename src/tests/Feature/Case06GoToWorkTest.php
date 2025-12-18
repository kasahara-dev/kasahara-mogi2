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
            ->get('/attendance')
            ->assertDontSee('出勤中')
            ->assertSee('class="attendance-btn">出勤</button>', false);
        $this->actingAs($this->user)
            ->post('/attendance');
        $this->actingAs($this->user)
            ->get('/attendance')
            ->assertSee('出勤中');
        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->user->id,
            'date' => $this->date,
            'start' => $this->dateTime,
            'end' => null,
            'note' => null,
        ]);
    }
    public function test_出勤は一日一回のみできる()
    {
        Attendance::create([
            'user_id' => $this->user->id,
            'date' => $this->date,
            'start' => $this->dateTime,
            'end' => $this->dateTime,
        ]);
        $this->actingAs($this->user)
            ->get('/attendance')
            ->assertDontSee('class="attendance-btn">出勤</button>', false);
    }
    public function test_出勤時刻が勤怠一覧画面で確認できる()
    {
        $this->actingAs($this->user)
            ->post('/attendance');
        $this->actingAs($this->user)
            ->get('/attendance/list')
            ->assertSee('<td class="table__data">' . $this->dateTime->isoFormat('MM月DD日(ddd)') . '</td>
                        <td class="table__data">' . $this->dateTime->format('H:i') . '</td>', false);
    }
}
