<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function show()
    {
        return view('admin.attendance.list');
    }
}
