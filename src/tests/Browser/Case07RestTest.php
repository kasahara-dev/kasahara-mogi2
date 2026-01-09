<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use App\Models\Attendance;
use function PHPUnit\Framework\assertEquals;

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
            $btnText = $browser->text('@rest-in-btn');
            assertEquals('休憩入', $btnText);
        });
    }
    public function test_休憩は一日に何回でもできる()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit('/attendance')
                ->click('@rest-in-btn')
                ->waitFor('@rest-end-btn')
                ->click('@rest-end-btn')
                ->waitFor('@rest-in-btn');
            $btnText = $browser->text('@rest-in-btn');
            assertEquals('休憩入', $btnText);
        });
    }
    public function test_休憩戻ボタンが正しく機能する()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit('/attendance')
                ->waitFor('@rest-in-btn')
                ->click('@rest-in-btn')
                ->waitFor('@rest-end-btn')
                ->click('@rest-end-btn');
            $statusText = $browser->text('@status');
            assertEquals('出勤中', $statusText);
        });
    }
    public function test_休憩戻は一日に何回でもできる()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit('/attendance')
                ->click('@rest-in-btn')
                ->waitFor('@rest-end-btn')
                ->click('@rest-end-btn')
                ->waitFor('@rest-in-btn')
                ->click('@rest-in-btn')
                ->waitFor('@rest-end-btn');
            $btnText = $browser->text('@rest-end-btn');
            assertEquals('休憩戻',$btnText) ;
        });
    }
}