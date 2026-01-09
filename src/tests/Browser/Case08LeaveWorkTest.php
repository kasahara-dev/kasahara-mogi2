<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Attendance;
use function PHPUnit\Framework\assertEquals;

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
                ->assertVisible('@work-end-btn');
            $btnText = $browser->text('@work-end-btn');
            assertEquals('退勤', $btnText);
            $browser
                ->loginAs($this->user)
                ->visit('/attendance')
                ->click('@work-end-btn')
                ->waitFor('@status');
            $statusText = $browser->text('@status');
            assertEquals('退勤済', $statusText);
        });
    }
}
