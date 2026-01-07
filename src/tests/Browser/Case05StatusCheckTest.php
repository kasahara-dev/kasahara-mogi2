<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;
use App\Models\Attendance;
use App\Models\Rest;
use function PHPUnit\Framework\assertEquals;

class Case05StatusCheckTest extends DuskTestCase
{
    use DatabaseMigrations;
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }
    public function test_勤務外の場合、勤怠ステータスが正しく表示される()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit('/attendance');
            $statusText = $browser->text('@status');
            assertEquals('勤務外', $statusText);
        });
    }
    public function test_出勤中の場合、勤怠ステータスが正しく表示される()
    {
        Attendance::create([
            'user_id' => $this->user->id,
            'date' => today(),
            'start' => now(),
        ]);
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit('/attendance');
            $statusText = $browser->text('@status');
            assertEquals('出勤中', $statusText);
        });
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
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit('/attendance');
            $statusText = $browser->text('@status');
            assertEquals('休憩中', $statusText);
        });
    }
    public function test_退勤済の場合、勤怠ステータスが正しく表示される()
    {
        Attendance::create([
            'user_id' => $this->user->id,
            'date' => today(),
            'start' => now(),
            'end' => now(),
        ]);
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit('/attendance');
            $statusText = $browser->text('@status');
            assertEquals('退勤済', $statusText);
        });
    }
}
