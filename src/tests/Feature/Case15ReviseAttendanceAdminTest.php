<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Faker\Factory;
use App\Models\User;
use App\Models\Admin;
use App\Models\Request as RequestModel;
use App\Models\RequestedAttendance;
use Database\Seeders\AdminsTableSeeder;
use Database\Seeders\AttendancesTableSeeder;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\RequestedAttendancesTableSeeder;
use Database\Seeders\RequestsTableSeeder;
class Case15ReviseAttendanceAdminTest extends TestCase
{
    use DatabaseMigrations;
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            AdminsTableSeeder::class,
        ]);
        $this->admin = Admin::first();
        $this->faker = Factory::create('ja_JP');
    }
    public function test_承認待ちの修正申請が全て表示されている()
    {
        $this->seed([
            UsersTableSeeder::class,
            AttendancesTableSeeder::class,
            RequestsTableSeeder::class,
            RequestedAttendancesTableSeeder::class,
        ]);
        $paginate = 10;
        $requestIds = RequestModel::where('status', 1)->pluck('id');
        for ($page = 1; $page <= ceil(count($requestIds) / $paginate); $page++) {
            $expectedAttendances = RequestedAttendance::whereIn('request_id', $requestIds)
                ->join('requests', 'requested_attendances.request_id', '=', 'requests.id')
                ->orderBy('requests.created_at', 'asc')
                ->orderBy('requests.id', 'asc')
                ->paginate($paginate, ['*'], 'page', $page);
            foreach ($expectedAttendances as $expectedAttendance) {
                $this->actingAs($this->admin, 'admin')
                    ->get('/stamp_correction_request/list?page=' . $page)
                    ->assertViewHas('requestedAttendances', function ($requestedAttendances) use ($expectedAttendance) {
                        return $requestedAttendances->contains($expectedAttendance);
                    });
            }
        }
    }
    public function test_承認済みの修正申請が全て表示されている()
    {
        $this->seed([
            UsersTableSeeder::class,
            AttendancesTableSeeder::class,
            RequestsTableSeeder::class,
            RequestedAttendancesTableSeeder::class,
        ]);
        $paginate = 10;
        $requestIds = RequestModel::where('status', 2)->pluck('id');
        for ($page = 1; $page <= ceil(count($requestIds) / $paginate); $page++) {
            $expectedAttendances = RequestedAttendance::whereIn('request_id', $requestIds)
                ->join('requests', 'requested_attendances.request_id', '=', 'requests.id')
                ->orderBy('requests.created_at', 'desc')
                ->orderBy('requests.id', 'desc')
                ->paginate($paginate, ['*'], 'page', $page);
            foreach ($expectedAttendances as $expectedAttendance) {
                $this->actingAs($this->admin, 'admin')
                    ->get('/stamp_correction_request/list?tab=approved&page=' . $page)
                    ->assertViewHas('requestedAttendances', function ($requestedAttendances) use ($expectedAttendance) {
                        return $requestedAttendances->contains($expectedAttendance);
                    });
            }
        }
    }
    public function test_修正申請の詳細内容が正しく表示されている()
    {
        $user = User::factory()->create();
        $this->seed([
            AttendancesTableSeeder::class,
            RequestsTableSeeder::class,
            RequestedAttendancesTableSeeder::class,
        ]);
        $requestedAttendance = RequestModel::where('status', 1)->inRandomOrder()->first()->RequestedAttendance;
        $this->actingAs($this->admin, 'admin')
            ->get('/stamp_correction_request/approve/' . $requestedAttendance->id)
            ->assertViewIs('admin.stamp.detail')
            ->assertViewHasAll([
                'id' => $requestedAttendance->id,
                'name' => $user->name,
                'start' => $requestedAttendance->start,
                'end' => $requestedAttendance->end,
            ]);
    }
    public function test_修正申請の承認処理が正しく行われる()
    {
        $user = User::factory()->create();
        $this->seed([
            AttendancesTableSeeder::class,
            RequestsTableSeeder::class,
            RequestedAttendancesTableSeeder::class,
        ]);
        $request = RequestModel::where('status', 1)->inRandomOrder()->first();
        $requestedAttendance = $request->RequestedAttendance;
        $this->assertDatabaseHas('requests', [
            'id' => $request->id,
            'status' => 1,
            'approver' => null,
        ]);
        $this->actingAs($this->admin, 'admin')
            ->put('/stamp_correction_request/approve/' . $requestedAttendance->id);
        $this->assertDatabaseHas('requests', [
            'id' => $request->id,
            'status' => 2,
            'approver' => $this->admin->id,
        ]);
    }
}
