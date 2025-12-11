<?php

namespace App\Http\Controllers\Admin;

use App\Models\RequestedAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Request as RequestModel;
use Illuminate\Pagination\Paginator;

Paginator::useBootstrap();
class RequestController extends Controller
{
    // public function show(Request $request)
    // {
    //     $pending = true;
    //     if (isset($request->tab)) {
    //         if ($request->tab == 'approved') {
    //             $pending = false;
    //         }
    //     }
    //     if ($pending) {
    //         $requestIds = RequestModel::where('status', 1)->pluck('id');
    //         $requestedAttendances = RequestedAttendance::whereIn('request_id', $requestIds)->orderBy('created_at', 'asc')->orderBy('id', 'asc')->paginate(10);
    //     } else {
    //         $requestIds = RequestModel::where('status', 2)->pluck('id');
    //         $requestedAttendances = RequestedAttendance::whereIn('request_id', $requestIds)->orderBy('created_at', 'desc')->orderBy('id', 'desc')->paginate(10);
    //     }
    //     return view('admin.stamp.list', compact('pending', 'requestedAttendances'));
    // }

}
