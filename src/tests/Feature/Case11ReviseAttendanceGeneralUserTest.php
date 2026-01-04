<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\RequestedAttendance;
use Database\Seeders\AdminsTableSeeder;
use Database\Seeders\RequestedRestsTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Faker\Factory;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Request;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Database\Seeders\AttendancesTableSeeder;
use Database\Seeders\RestsTableSeeder;
use Database\Seeders\RequestsTableSeeder;
use Database\Seeders\RequestedAttendancesTableSeeder;

class Case11ReviseAttendanceGeneralUserTest extends TestCase
{
    use DatabaseMigrations;
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->faker = Factory::create('ja_JP');
        $this->seed(AttendancesTableSeeder::class);
        $this->attendance = Attendance::inRandomOrder()->first();
    }
    public function test_出勤時間が退勤時刻より後になっている場合、エラーメッセージが表示される()
    {
        $dateTime1 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        $dateTime2 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        while ($dateTime1->hour == $dateTime2->hour && $dateTime1->minute == $dateTime2->minute) {
            $dateTime2 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        }
        if ($dateTime1 < $dateTime2) {
            $beforeDateTime = $dateTime1;
            $afterDateTime = $dateTime2;
        } else {
            $beforeDateTime = $dateTime2;
            $afterDateTime = $dateTime1;
        }
        $expectRest = [1 => '-1'];
        $this->actingAs($this->user)
            ->from('/attendance/detail/' . $this->attendance->id)
            ->post('/attendance/detail/' . $this->attendance->id, [
                'attendance_start_hour' => $afterDateTime->hour,
                'attendance_start_minute' => $afterDateTime->minute,
                'attendance_end_hour' => $beforeDateTime->hour,
                'attendance_end_minute' => $beforeDateTime->minute,
                'rest_start_hour' => $expectRest,
                'rest_start_minute' => $expectRest,
                'rest_end_hour' => $expectRest,
                'rest_end_minute' => $expectRest,
                'note' => $this->faker->realText(),
            ])
            ->assertSessionHasErrors(['attendance_start_num' => '出勤時間もしくは退勤時間が不適切な値です']);
        $this->actingAs($this->user)
            ->get('/attendance/detail/' . $this->attendance->id)
            ->assertSee('出勤時間もしくは退勤時間が不適切な値です');

    }
    public function test_休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される()
    {
        $dateTime1 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        $dateTime2 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        $dateTime3 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        while (($dateTime1->hour == $dateTime2->hour && $dateTime1->minute == $dateTime2->minute) or ($dateTime1->hour == $dateTime3->hour && $dateTime1->minute == $dateTime3->minute) or ($dateTime2->hour == $dateTime3->hour && $dateTime2->minute == $dateTime3->minute)) {
            $dateTime2 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
            $dateTime3 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        }
        $timesArray = [$dateTime1, $dateTime2, $dateTime3];
        sort($timesArray);
        $this->actingAs($this->user)
            ->from('/attendance/detail/' . $this->attendance->id)
            ->post('/attendance/detail/' . $this->attendance->id, [
                'attendance_start_hour' => $timesArray[0]->hour,
                'attendance_start_minute' => $timesArray[0]->minute,
                'attendance_end_hour' => $timesArray[1]->hour,
                'attendance_end_minute' => $timesArray[1]->minute,
                'rest_start_hour' => [1 => $timesArray[2]->hour],
                'rest_start_minute' => [1 => $timesArray[2]->minute],
                'rest_end_hour' => [1 => $timesArray[2]->hour],
                'rest_end_minute' => [1 => $timesArray[2]->minute],
                'note' => $this->faker->realText(),
            ])
            ->assertSessionHasErrors(['rest_start_num.1' => '休憩時間が不適切な値です']);
        $this->actingAs($this->user)
            ->get('/attendance/detail/' . $this->attendance->id)
            ->assertSee('休憩時間が不適切な値です');

    }
    public function test_休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される()
    {
        $dateTime1 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        $dateTime2 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        $dateTime3 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        while (($dateTime1->hour == $dateTime2->hour && $dateTime1->minute == $dateTime2->minute) or ($dateTime1->hour == $dateTime3->hour && $dateTime1->minute == $dateTime3->minute) or ($dateTime2->hour == $dateTime3->hour && $dateTime2->minute == $dateTime3->minute)) {
            $dateTime2 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
            $dateTime3 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        }
        $timesArray = [$dateTime1, $dateTime2, $dateTime3];
        sort($timesArray);
        $this->actingAs($this->user)
            ->from('/attendance/detail/' . $this->attendance->id)
            ->post('/attendance/detail/' . $this->attendance->id, [
                'attendance_start_hour' => $timesArray[0]->hour,
                'attendance_start_minute' => $timesArray[0]->minute,
                'attendance_end_hour' => $timesArray[1]->hour,
                'attendance_end_minute' => $timesArray[1]->minute,
                'rest_start_hour' => [1 => $timesArray[0]->hour],
                'rest_start_minute' => [1 => $timesArray[0]->minute],
                'rest_end_hour' => [1 => $timesArray[2]->hour],
                'rest_end_minute' => [1 => $timesArray[2]->minute],
                'note' => $this->faker->realText(),
            ])
            ->assertSessionHasErrors(['rest_end_num.1' => '休憩時間もしくは退勤時間が不適切な値です']);
        $this->actingAs($this->user)
            ->get('/attendance/detail/' . $this->attendance->id)
            ->assertSee('休憩時間もしくは退勤時間が不適切な値です');
    }
    public function test_備考欄が未入力の場合のエラーメッセージが表示される()
    {
        $dateTime1 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        $dateTime2 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        while ($dateTime1->hour == $dateTime2->hour && $dateTime1->minute == $dateTime2->minute) {
            $dateTime2 = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        }
        if ($dateTime1 < $dateTime2) {
            $beforeDateTime = $dateTime1;
            $afterDateTime = $dateTime2;
        } else {
            $beforeDateTime = $dateTime2;
            $afterDateTime = $dateTime1;
        }
        $expectRest = [1 => '-1'];
        $this->actingAs($this->user)
            ->from('/attendance/detail/' . $this->attendance->id)
            ->post('/attendance/detail/' . $this->attendance->id, [
                'attendance_start_hour' => $beforeDateTime->hour,
                'attendance_start_minute' => $beforeDateTime->minute,
                'attendance_end_hour' => $afterDateTime->hour,
                'attendance_end_minute' => $afterDateTime->minute,
                'rest_start_hour' => $expectRest,
                'rest_start_minute' => $expectRest,
                'rest_end_hour' => $expectRest,
                'rest_end_minute' => $expectRest,
                'note' => '',
            ])
            ->assertSessionHasErrors(['note' => '備考を記入してください']);
        $this->actingAs($this->user)
            ->get('/attendance/detail/' . $this->attendance->id)
            ->assertSee('備考を記入してください');
    }
    public function test_修正申請処理が実行される()
    {
        $this->seed(AdminsTableSeeder::class);
        $admin = Admin::inRandomOrder()->first();
        for ($i = 0; $i < 4; $i++) {
            $timesArray[] = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        }
        sort($timesArray);
        $note = $this->faker->realText();
        $this->actingAs($this->user)
            ->from('/attendance/detail/' . $this->attendance->id)
            ->post('/attendance/detail/' . $this->attendance->id, [
                'attendance_start_hour' => $timesArray[0]->hour,
                'attendance_start_minute' => $timesArray[0]->minute,
                'attendance_end_hour' => $timesArray[3]->hour,
                'attendance_end_minute' => $timesArray[3]->minute,
                'rest_start_hour' => [1 => $timesArray[1]->hour],
                'rest_start_minute' => [1 => $timesArray[1]->minute],
                'rest_end_hour' => [1 => $timesArray[2]->hour],
                'rest_end_minute' => [1 => $timesArray[2]->minute],
                'note' => $note,
            ]);
        $this->actingAs($this->user)
            ->post('/logout');
        $this->assertDatabaseHas('requests', [
            'attendance_id' => $this->attendance->id,
            'status' => 1,
            'approver' => null,
        ]);
        $requestId = Request::first()->id;
        $this->assertDatabaseHas('requested_attendances', [
            'request_id' => $requestId,
            'date' => $this->attendance->date,
            'start' => $timesArray[0]->second(0),
            'end' => $timesArray[3]->second(0),
            'note' => $note,
        ]);
        $requestedAttendanceId = RequestedAttendance::first()->id;
        $this->assertDatabaseHas('requested_rests', [
            'requested_attendance_id' => $requestedAttendanceId,
            'start' => $timesArray[1]->second(0),
            'end' => $timesArray[2]->second(0),
        ]);
        $paginate = 10;
        $expectedAttendance = RequestedAttendance::where('request_id', $requestId)
            ->join('requests', 'requested_attendances.request_id', '=', 'requests.id')
            ->paginate($paginate);
        $requestDate = Carbon::parse(Request::first()->created_at);
        // 承認画面
        $this->actingAs($admin, 'admin')
            ->get('/stamp_correction_request/approve/' . $requestId)
            ->assertSeeInOrder(['<dt class="list-line-title">名前</dt>
                <dd class="list-line-data">
                    ' . $this->user->name . '
                </dd>',
                '<dt class="list-line-title">日付</dt>
                <dd class="list-line-data">
                    ' . $timesArray[0]->format('Y年m月d日') . '
                </dd>',
                '<dt class="list-line-title">出勤・退勤</dt>
                <dd class="list-line-data">
                                            ' . $timesArray[0]->format('H:i') . '～' . $timesArray[3]->format('H:i') . '
                                    </dd>',
                '<dt class="list-line-title">休憩</dt>
                    <dd class="list-line-data">
                                                    ' . $timesArray[1]->format('H:i') . '～' . $timesArray[2]->format('H:i') . '
                                            </dd>',
                '<dt class="list-line-title">備考</dt>
                <dd class="list-line-data">
                    <div class="list-line-selectors-area">
                        ' . $note . '
                    </div>',
            ], false);
        // 承認一覧画面
        $this->actingAs($admin, 'admin')
            ->get('/stamp_correction_request/list')
            ->assertSee('<td class="table__data">
                                                    承認待ち
                                            </td>
                    <td class="table__data">' . $this->user->name . '</td>
                    <td class="table__data">' . $timesArray[0]->format('Y/m/d') . '</td>
                    <td class="table__data">' . $note . '</td>
                    <td class="table__data">
                        ' . $requestDate->format('Y/m/d') . '</td>', false);
    }
    public function test_「承認待ち」にログインユーザーが行った申請が全て表示されていること()
    {
        $this->seed([
            RestsTableSeeder::class,
            AdminsTableSeeder::class,
            RequestsTableSeeder::class,
            RequestedAttendancesTableSeeder::class,
            RequestedRestsTableSeeder::class,
        ]);
        $attendanceIds = $this->user->attendances()->pluck('id');
        $searchRequests = Request::whereIn('attendance_id', $attendanceIds)->get();
        $requestIds = $searchRequests->where('status', 1)->pluck('id');
        $paginate = 10;
        $lastPage = ceil(RequestedAttendance::whereIn('request_id', $requestIds)->count() / $paginate);
        for ($page = 1; $page <= $lastPage; $page++) {
            $expectedAttendances = RequestedAttendance::whereIn('request_id', $requestIds)
                ->join('requests', 'requested_attendances.request_id', '=', 'requests.id')
                ->orderBy('requests.created_at', 'asc')
                ->orderBy('requests.id', 'asc')
                ->paginate($paginate, ['*'], 'page', $page);
            foreach ($expectedAttendances as $expectedAttendance) {
                $this->actingAs($this->user)
                    ->get('/stamp_correction_request/list?page=' . $page)
                    ->assertViewHas('requestedAttendances', function ($requestedAttendances) use ($expectedAttendance) {
                        return $requestedAttendances->contains($expectedAttendance);
                    });
            }
        }
    }
    public function test_「承認済み」に管理者が承認した修正申請が全て表示されている()
    {
        $this->seed([
            RestsTableSeeder::class,
            AdminsTableSeeder::class,
            RequestsTableSeeder::class,
            RequestedAttendancesTableSeeder::class,
            RequestedRestsTableSeeder::class,
        ]);
        $attendanceIds = $this->user->attendances()->pluck('id');
        $searchRequests = Request::whereIn('attendance_id', $attendanceIds)->get();
        $requestIds = $searchRequests->where('status', 2)->pluck('id');
        $paginate = 10;
        $lastPage = ceil(RequestedAttendance::whereIn('request_id', $requestIds)->count() / $paginate);
        for ($page = 1; $page <= $lastPage; $page++) {
            $expectedAttendances = RequestedAttendance::whereIn('request_id', $requestIds)
                ->join('requests', 'requested_attendances.request_id', '=', 'requests.id')
                ->orderBy('requests.created_at', 'desc')
                ->orderBy('requests.id', 'desc')
                ->paginate($paginate, ['*'], 'page', $page);
            foreach ($expectedAttendances as $expectedAttendance) {
                $this->actingAs($this->user)
                    ->get('/stamp_correction_request/list?tab=approved&page=' . $page)
                    ->assertViewHas('requestedAttendances', function ($requestedAttendances) use ($expectedAttendance) {
                        return $requestedAttendances->contains($expectedAttendance);
                    });
            }
        }
    }
    public function test_各申請の「詳細」を押下すると勤怠詳細画面に遷移する()
    {
        for ($i = 0; $i < 4; $i++) {
            $timesArray[] = Carbon::parse($this->faker->dateTimeBetween($this->attendance->date, Carbon::parse($this->attendance->date)->endOfDay()));
        }
        sort($timesArray);
        $note = $this->faker->realText();
        $this->actingAs($this->user)
            ->from('/attendance/detail/' . $this->attendance->id)
            ->post('/attendance/detail/' . $this->attendance->id, [
                'attendance_start_hour' => $timesArray[0]->hour,
                'attendance_start_minute' => $timesArray[0]->minute,
                'attendance_end_hour' => $timesArray[3]->hour,
                'attendance_end_minute' => $timesArray[3]->minute,
                'rest_start_hour' => [1 => $timesArray[1]->hour],
                'rest_start_minute' => [1 => $timesArray[1]->minute],
                'rest_end_hour' => [1 => $timesArray[2]->hour],
                'rest_end_minute' => [1 => $timesArray[2]->minute],
                'note' => $note,
            ]);
        $requestId = Request::first()->id;
        $this->actingAs($this->user)
            ->get('/attendance/list?year=' . $timesArray[0]->year . '&month=' . $timesArray[0]->month)
            ->assertSee('<a href="/requested_attendance/detail/' . $requestId . '/?pending=true"
                                        class="table__data--active">詳細</a>', false);
        $this->actingAs($this->user)
            ->get('/requested_attendance/detail/' . $requestId . '/?pending=true')
            ->assertViewHas('requestedAttendanceId',$requestId);
    }
}
