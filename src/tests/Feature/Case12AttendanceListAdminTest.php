<?php

namespace Tests\Feature;

use Database\Seeders\AttendancesTableSeeder;
use Database\Seeders\RestsTableSeeder;
use Database\Seeders\UsersTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Faker\Factory;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Database\Seeders\AdminsTableSeeder;


class Case12AttendanceListAdminTest extends TestCase
{
    use DatabaseMigrations;
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([UsersTableSeeder::class, AdminsTableSeeder::class]);
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
        $this->seed(AttendancesTableSeeder::class);
        $users = User::all();
        foreach ($users as $user) {
            $randNum = rand(0, 2);
            if ($randNum > 0) {
                $startDateTime = Carbon::parse($this->faker->dateTimeBetween($this->dateTime->copy()->startOfDay(), now()));
            }
        }
    }
}
