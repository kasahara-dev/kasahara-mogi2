<?php

namespace App\Http\Controllers;

use App\Models\RequestedAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Request as RequestModel;
use Illuminate\Pagination\Paginator;

Paginator::useBootstrap();
class RequestController extends Controller
{
    public function show(Request $request)
    {
        // 管理者ログインの場合
        if (Auth::guard('admin')->check()) {
            $pending = true;
            if (isset($request->tab)) {
                if ($request->tab == 'approved') {
                    $pending = false;
                }
            }
            if ($pending) {
                $requestIds = RequestModel::where('status', 1)->pluck('id');
                $requestedAttendances = RequestedAttendance::whereIn('request_id', $requestIds)
                    ->join('requests', 'requested_attendances.request_id', '=', 'requests.id')
                    ->orderBy('requests.created_at', 'asc')
                    ->orderBy('requests.id', 'asc')
                    ->paginate(10);
            } else {
                $requestIds = RequestModel::where('status', 2)->pluck('id');
                $requestedAttendances = RequestedAttendance::whereIn('request_id', $requestIds)
                    ->join('requests', 'requested_attendances.request_id', '=', 'requests.id')
                    ->orderBy('requests.created_at', 'desc')
                    ->orderBy('requests.id', 'desc')
                    ->paginate(10);
            }
            return view('admin.stamp.list', compact('pending', 'requestedAttendances'));
            // 一般ユーザーでログインの場合
        } else {
            $pending = true;
            if (isset($request->tab)) {
                if ($request->tab == 'approved') {
                    $pending = false;
                }
            }
            $attendanceIds = Auth::user()->attendances()->pluck('id');
            $searchRequests = RequestModel::whereIn('attendance_id', $attendanceIds)->get();
            if ($pending) {
                $requestIds = $searchRequests->where('status', 1)->pluck('id');
                $requestedAttendances = RequestedAttendance::whereIn('request_id', $requestIds)
                    ->join('requests', 'requested_attendances.request_id', '=', 'requests.id')
                    ->orderBy('requests.created_at', 'asc')
                    ->orderBy('requests.id', 'asc')
                    ->paginate(10);
            } else {
                $requestIds = $searchRequests->where('status', 2)->pluck('id');
                $requestedAttendances = RequestedAttendance::whereIn('request_id', $requestIds)
                    ->join('requests', 'requested_attendances.request_id', '=', 'requests.id')
                    ->orderBy('requests.created_at', 'desc')
                    ->orderBy('requests.id', 'desc')
                    ->paginate(10);
            }
            return view('/requested_attendance/list', compact('pending', 'requestedAttendances'));
        }
    }
}
