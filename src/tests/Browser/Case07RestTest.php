<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;
use App\Models\Attendance;

class Case07RestTest extends DuskTestCase
{
    use DatabaseMigrations;
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => now(),
            'start' => now(),
        ]);
    }
    public function test_休憩ボタンが正しく機能する()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit('/attendance')
                ->assertVisible('@rest-in-btn');
        });
    }
    public function test_休憩は一日に何回でもできる()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit('/attendance')
                ->click('@rest-in-btn')
                ->assertMissing('@rest-in-btn')
                ->click('@rest-end-btn')
                ->assertSee('休憩入')
                ->assertVisible('@rest-in-btn');
        });
    }
    public function test_休憩戻ボタンが正しく機能する()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit('/attendance')
                ->assertMissing('@rest-end-btn')
                ->click('@rest-in-btn')
                ->assertSee('休憩戻')
                ->assertVisible('@rest-end-btn')
                ->click('@rest-end-btn')
                ->assertSee('出勤中');
        });
    }
    public function test_休憩戻は一日に何回でもできる()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit('/attendance')
                ->click('@rest-in-btn')
                ->click('@rest-end-btn')
                ->click('@rest-in-btn')
                ->assertSee('休憩戻')
                ->assertVisible('@rest-end-btn');
        });
    }
}
