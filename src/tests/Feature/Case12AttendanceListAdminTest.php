<?php

namespace Tests\Feature;

use Database\Seeders\UsersTableSeeder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Faker\Factory;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use Carbon\Carbon;
use Database\Seeders\AdminsTableSeeder;


class Case12AttendanceListAdminTest extends TestCase
{
    use DatabaseMigrations;
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminsTableSeeder::class);
        $this->admin = Admin::first();
        $this->faker = Factory::create('ja_JP');
        $this->dateTime = Carbon::parse($this->faker->dateTime());
        $this->date = $this->dateTime->toDateString();
        Carbon::setTestNow($this->dateTime);
    }
    protected function tearDown(): void
    {
        Carbon::setTestNow();
    }
    public function test_その日になされた全ユーザーの勤怠情報が正確に確認できる()
    {
        $this->seed(UsersTableSeeder::class);
        $users = User::orderBy('created_at')->orderBy('id')->get();
        $paginate = 10;
        $page = 1;
        $line = 1;
        $expectArray = [];
        foreach ($users as $user) {
            $expectArray[] = $user->name;
            $randNum = rand(0, 1);
            $startDateTime = null;
            $endDateTime = null;
            $setStartDateTime = null;
            $setEndDateTime = null;
            if ($randNum > 0) {
                $startDateTime = Carbon::parse($this->faker->dateTimeBetween($this->dateTime->copy()->startOfDay(), now()));
                $setStartDateTime = $startDateTime->format('H:i');
                $calcEndDateTime = Carbon::parse($this->faker->dateTimeBetween($startDateTime, $this->dateTime->copy()->endOfDay()));
                $expectArray[] = $setStartDateTime;
                if ($calcEndDateTime->lte(now())) {
                    $endDateTime = $calcEndDateTime;
                    $setEndDateTime = $endDateTime->format('H:i');
                    $expectArray[] = $setEndDateTime;
                }
                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'date' => $startDateTime,
                    'start' => $startDateTime,
                    'end' => $endDateTime,
                ]);
            }
            $line++;
            if ($line > 10) {
                $line = 1;
                $page++;
                unset($expectArray);
            }
        }
        $this->actingAs($this->admin, 'admin')
            ->get('/admin/attendance/list?page=' . $page)
            ->assertSeeInOrder($expectArray, false);
    }
    public function test_遷移した際に現在の日付が表示される()
    {
        $this->actingAs($this->admin, 'admin')
            ->get('/admin/attendance/list')
            ->assertSee($this->dateTime->format('Y年n月j日の勤怠'));
    }
    public function test_「前日」を押下した時に前の日の勤怠情報が表示される()
    {
        $yesterday = $this->dateTime->copy()->subDay();
        $this->actingAs($this->admin, 'admin')
            ->get('/admin/attendance/list?year=' . $yesterday->year . '&month=' . $yesterday->month . '&day=' . $yesterday->day)
            ->assertSee($yesterday->format('Y年n月j日の勤怠'));
    }
    public function test_「翌日」を押下した時に次の日の勤怠情報が表示される()
    {
        $tomorrow = $this->dateTime->copy()->addDay();
        $this->actingAs($this->admin, 'admin')
            ->get('/admin/attendance/list?year=' . $tomorrow->year . '&month=' . $tomorrow->month . '&day=' . $tomorrow->day)
            ->assertSee($tomorrow->format('Y年n月j日の勤怠'));
    }
}