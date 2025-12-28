<?php

namespace Tests\Feature;

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
        $exceptRest = [1 => '-1'];
        $this->actingAs($this->user)
            ->from('/attendance/detail/' . $this->attendance->id)
            ->post('/attendance/detail/' . $this->attendance->id, [
                'attendance_start_hour' => $afterDateTime->hour,
                'attendance_start_minute' => $afterDateTime->minute,
                'attendance_end_hour' => $beforeDateTime->hour,
                'attendance_end_minute' => $beforeDateTime->minute,
                'rest_start_hour' => $exceptRest,
                'rest_start_minute' => $exceptRest,
                'rest_end_hour' => $exceptRest,
                'rest_end_minute' => $exceptRest,
                'note' => $this->faker->realText(),
            ])
            ->assertSessionHasErrors(['attendance_start_num' => '出勤時間が不適切な値です']);
        $this->actingAs($this->user)
            ->get('/attendance/detail/' . $this->attendance->id)
            ->assertSee('出勤時間が不適切な値です');

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
        $exceptRest = [1 => '-1'];
        $this->actingAs($this->user)
            ->from('/attendance/detail/' . $this->attendance->id)
            ->post('/attendance/detail/' . $this->attendance->id, [
                'attendance_start_hour' => $beforeDateTime->hour,
                'attendance_start_minute' => $beforeDateTime->minute,
                'attendance_end_hour' => $afterDateTime->hour,
                'attendance_end_minute' => $afterDateTime->minute,
                'rest_start_hour' => $exceptRest,
                'rest_start_minute' => $exceptRest,
                'rest_end_hour' => $exceptRest,
                'rest_end_minute' => $exceptRest,
                'note' => '',
            ])
            ->assertSessionHasErrors(['note' => '備考を記入してください']);
        $this->actingAs($this->user)
            ->get('/attendance/detail/' . $this->attendance->id)
            ->assertSee('備考を記入してください');
    }
    public function test_修正申請処理が実行される()
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
        $lastPage = $expectedAttendances = RequestedAttendance::whereIn('request_id', $requestIds)
            ->join('requests', 'requested_attendances.request_id', '=', 'requests.id')
            ->orderBy('requests.created_at', 'asc')
            ->orderBy('requests.id', 'asc')
            ->paginate(10)
            ->lastPage();
        $lineCount = 1;
        $page = 1;
        for ($i = 0; $i < $lastPage; $i++) {
            $expectedAttendances = RequestedAttendance::whereIn('request_id', $requestIds)
                ->join('requests', 'requested_attendances.request_id', '=', 'requests.id')
                ->orderBy('requests.created_at', 'asc')
                ->orderBy('requests.id', 'asc')
                ->paginate(10, ['*'], 'page', $page);
            foreach ($expectedAttendances as $expectedAttendance) {
                $this->actingAs($this->user)
                    ->get('/stamp_correction_request/list?page=' . $page)
                    ->assertViewHas('requestedAttendances', function ($requestedAttendances) use ($expectedAttendance) {
                        return $requestedAttendances->contains($expectedAttendance);
                    });
            }
            $lineCount++;
            if ($lineCount > 10) {
                $lineCount = 0;
                $page++;
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
        $lastPage = $expectedAttendances = RequestedAttendance::whereIn('request_id', $requestIds)
            ->join('requests', 'requested_attendances.request_id', '=', 'requests.id')
            ->orderBy('requests.created_at', 'desc')
            ->orderBy('requests.id', 'desc')
            ->paginate(10)
            ->lastPage();
        $lineCount = 1;
        $page = 1;
        for ($i = 0; $i < $lastPage; $i++) {
            $expectedAttendances = RequestedAttendance::whereIn('request_id', $requestIds)
                ->join('requests', 'requested_attendances.request_id', '=', 'requests.id')
                ->orderBy('requests.created_at', 'desc')
                ->orderBy('requests.id', 'desc')
                ->paginate(10, ['*'], 'page', $page);
            foreach ($expectedAttendances as $expectedAttendance) {
                $this->actingAs($this->user)
                    ->get('/stamp_correction_request/list?tab=approved&page=' . $page)
                    ->assertViewHas('requestedAttendances', function ($requestedAttendances) use ($expectedAttendance) {
                        return $requestedAttendances->contains($expectedAttendance);
                    });
            }
            $lineCount++;
            if ($lineCount > 10) {
                $lineCount = 0;
                $page++;
            }
        }
    }
}
