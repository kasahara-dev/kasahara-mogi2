<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use Carbon\Carbon;
class Case04GetDateTimeTest extends DuskTestCase
{
    use DatabaseMigrations;
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }
    public function test_現在の日時がUIと同じ形式で表示されている()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit('/attendance')
                ->pause(1000);
            $carbonNow = Carbon::now()->setTimezone('Asia/Tokyo');
            $year = $carbonNow->isoFormat('YYYY');
            $month = $carbonNow->format('n');
            $day = $carbonNow->format('j');
            $weekDay = $carbonNow->isoFormat('ddd');
            $value = $browser->script("return document.getElementById('date').textContent;");
            $innerHTML1 = $value[0];
            $value = $browser->script("return document.getElementById('time').textContent;");
            $innerHTML2 = $value[0];
            $this->assertEquals($year . '年' . $month . '月' . $day . '日(' . $weekDay . ')', $innerHTML1);
            $this->assertEquals($carbonNow->format('H:i'), $innerHTML2);
        });
    }
}
