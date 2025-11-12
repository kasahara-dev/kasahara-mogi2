<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminAttendanceController extends Controller
{
    public function show()
    {
        return view('admin.attendance.list');
    }
}
