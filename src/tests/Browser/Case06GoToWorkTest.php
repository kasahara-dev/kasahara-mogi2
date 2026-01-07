<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;
use App\Models\Attendance;

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
