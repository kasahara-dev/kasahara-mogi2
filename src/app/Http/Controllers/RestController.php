<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RestController extends Controller
{
    public function store()
    {
        DB::transaction(function () {
            $tableDate = Carbon::parse(Auth::user()->attendances()->where('end', null)->value('start'))->startOfDay();
            $today = Carbon::today();
            while ($tableDate < $today) {
                $tableDate->addDay();
                Auth::user()->attendances()->where('end', null)->update([
                    'end' => $tableDate,
                ]);
                Attendance::create([
                    'user_id' => auth()->id(),
                    'date' => $tableDate,
                    'start' => $tableDate,
                ]);
            }
            $attendance = Auth::user()->attendances()->where('end', null)->first();
            Rest::create([
                'attendance_id' => $attendance->id,
                'start' => now(),
            ]);
        });
        return redirect('/attendance');
    }
    public function update($id)
    {
        DB::transaction(function () use ($id) {
            $tableDate = Carbon::parse(Auth::user()->attendances()->where('end', null)->value('start'))->startOfDay();
            $today = Carbon::today();
            $attendance = Auth::user()->attendances()->where('end', null)->first();
            while ($tableDate < $today) {
                $tableDate->addDay();
                $attendance->update([
                    'end' => $tableDate,
                ]);
                Rest::where('attendance_id', $attendance->id)->where('end', null)->first()->update([
                    'end' => $tableDate,
                ]);
                $attendance = Attendance::create([
                    'user_id' => auth()->id(),
                    'date' => $tableDate,
                    'start' => $tableDate,
                ]);
                Rest::create([
                    'attendance_id' => $attendance->id,
                    'start' => $tableDate,
                ]);
            }
            Rest::where('attendance_id', $attendance->id)->where('end', null)->first()->update([
                'end' => now(),
            ]);
        });
        return redirect('/attendance');
    }
}