<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;
use App\Models\Attendance;

class Case08LeaveWorkTest extends DuskTestCase
{
    use DatabaseMigrations;
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->afterDateTime = Carbon::parse(now())->copy()->addMinutes(rand(0, 200));
        if ($this->afterDateTime->copy()->startOfDay()->gt(Carbon::parse(now())->copy()->startOfDay())) {
            $this->afterDateTime = Carbon::parse(now())->copy()->endOfDay();
        }
    }
    public function test_退勤ボタンが正しく機能する()
    {
        $this->attendance = $this->attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => now(),
            'start' => now(),
        ]);
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit('/attendance')
                ->assertSee('退勤')
                ->assertVisible('@work-end-btn');
        });
    }
}
