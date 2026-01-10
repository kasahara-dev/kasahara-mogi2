<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use App\Models\Attendance;
use function PHPUnit\Framework\assertEquals;

class Case06GoToWorkTest extends DuskTestCase
{
    use DatabaseMigrations;
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }
    public function test_出勤ボタンが正しく機能する()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit('/attendance')
                ->assertVisible('@go-to-work');
            $btnText = $browser->text('@go-to-work');
            assertEquals('出勤', $btnText);
            $browser
                ->loginAs($this->user)
                ->visit('/attendance')
                ->click('@go-to-work');
            $statusText = $browser->text('@status');
            assertEquals('出勤中', $statusText);
        });
    }
    public function test_出勤は一日一回のみできる()
    {
        Attendance::create([
            'user_id' => $this->user->id,
            'date' => now(),
            'start' => now(),
        ]);
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit('/attendance')
                ->assertMissing('@go-to-work');
        });
    }
}
