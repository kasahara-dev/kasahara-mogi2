<?php

namespace Tests\Feature;

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
use Database\Seeders\AttendancesTableSeeder;
use Database\Seeders\UsersTableSeeder;
class Case14UserListTest extends TestCase
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
    public function test_管理者ユーザーが全一般ユーザーの「氏名」「メールアドレス」を確認できる()
    {
        $this->seed(UsersTableSeeder::class);
        $paginate = 10;
        $maxPage = ceil(User::count() / $paginate);
        for ($page = 1; $page <= $maxPage; $page++) {
            $users = User::orderBy('created_at', 'asc')->orderBy('id', 'asc')->paginate($paginate, ['*'], 'page', $page);
            foreach ($users as $user) {
                $this->actingAs($this->admin, 'admin')
                    ->get('/admin/staff/list?page=' . $page)
                    ->assertSeeInOrder([$user->name, $user->email]);
            }
        }
    }
    public function test_ユーザーの勤怠情報が正しく表示される()
    {
        $user = User::factory()->create();
        $this->seed(AttendancesTableSeeder::class);
        $attendances = Attendance::whereYear('start', $this->dateTime->copy()->subMonth()->year)
            ->whereMonth('start', $this->dateTime->copy()->subMonth()->month)
            ->orderBy('start')
            ->get();
        foreach ($attendances as $attendance) {
            $this->actingAs($this->admin, 'admin')
                ->get('/admin/attendance/staff/' . $user->id . '/?year=' . $this->dateTime->copy()->subMonth()->year . '&month=' . $this->dateTime->copy()->subMonth()->month)
                ->assertSeeInOrder([
                    Carbon::parse($attendance->start)->isoFormat('MM月DD日(ddd)'),
                    Carbon::parse($attendance->start)->format('H:i'),
                    Carbon::parse($attendance->end)->format('H:i'),
                ], false);
        }
    }
    public function test_「前月」を押下した時に表示月の前月の情報が表示される()
    {
        $user = User::factory()->create();
        $preDateTime = Carbon::parse($this->dateTime->copy()->subMonth());
        $this->actingAs($this->admin, 'admin')
            ->get('/admin/attendance/staff/' . $user->id . '?year=' . $preDateTime->year . '&month=' . $preDateTime->month)
            ->assertSee($preDateTime->format('Y/m'));
    }
    public function test_「翌月」を押下した時に表示月の前月の情報が表示される()
    {
        $user = User::factory()->create();
        $nextDateTime = Carbon::parse($this->dateTime->copy()->addMonth());
        $this->actingAs($this->admin, 'admin')
            ->get('/admin/attendance/staff/' . $user->id . '?year=' . $nextDateTime->year . '&month=' . $nextDateTime->month)
            ->assertSee($nextDateTime->format('Y/m'));
    }
    public function test_「詳細」を押下すると、その日の勤怠詳細画面に遷移する()
    {
        $user = User::factory()->create();
        $this->seed(AttendancesTableSeeder::class);
        $attendance = Attendance::inRandomOrder()->first();
        $this->actingAs($this->admin, 'admin')
            ->get('/admin/attendance/' . $attendance->id)
            ->assertViewIs('admin.attendance.detail')
            ->assertViewHas('attendanceId', $attendance->id);
    }
}
