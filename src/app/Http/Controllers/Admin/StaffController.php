<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Http\Requests\AttendanceRequest;
use App\Models\Rest;
use Illuminate\Pagination\LengthAwarePaginator;
Paginator::useBootstrap();
class StaffController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'asc')->orderBy('id', 'asc')->paginate(10);
        return view('admin.staff.list', compact('users'));
    }
}
