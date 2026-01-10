<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Pagination\Paginator;
Paginator::useBootstrap();
class StaffController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'asc')->orderBy('id', 'asc')->paginate(10);
        return view('admin.staff.list', compact('users'));
    }
}
