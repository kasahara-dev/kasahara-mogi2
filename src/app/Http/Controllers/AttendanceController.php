<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function create()
    {
        // 勤務中判定
        $working = Auth::user()->attendances()->where('end', null)->exists();
        return view('attendance.attendance', compact(['working']));
    }
}
