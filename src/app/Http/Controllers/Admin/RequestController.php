<?php

namespace App\Http\Controllers\Admin;

use App\Models\Request as RequestModel;
use App\Models\Rest;
use App\Models\Attendance;
use App\Models\RequestedRest;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
Paginator::useBootstrap();
class RequestController extends Controller
{
    public function edit($id)
    {
        $requestModel = RequestModel::find($id);
        $name = $requestModel->attendance->user->name;
        $requestedAttendance = $requestModel->requestedAttendance;
        // 申請中判定
        if ($requestModel->status == 1) {
            $pending = true;
        } else {
            $pending = false;
        }
        $id = $requestModel->id;
        $start = $requestedAttendance->start;
        $end = $requestedAttendance->end;
        $rests = $requestedAttendance->rests()->orderBy('start')->get();
        $restsCount = $requestedAttendance->rests()->count();
        $note = $requestedAttendance->note;
        return view('admin.stamp.detail', compact(['id', 'name', 'start', 'end', 'rests', 'restsCount', 'pending', 'note']));
    }
    public function update($id)
    {
        DB::transaction(function () use ($id) {
            $requestModel = RequestModel::find($id);
            Attendance::find($requestModel->attendance_id)->sharedLock()->get();
            RequestModel::find($id)->sharedLock()->get();
            $this->authorize('update', $requestModel);
            // requestsテーブルのステータス変更
            $requestModel->update([
                'status' => 2,
                'approver' => Auth::id(),
                'updated_at' => now(),
            ]);
            // attendancesテーブル更新
            $attendance = $requestModel->attendance()->update([
                'start' => $requestModel->requestedAttendance->start,
                'end' => $requestModel->requestedAttendance->end,
                'note' => $requestModel->requestedAttendance->note,
            ]);
            $attendanceId = $requestModel->attendance->id;
            // restsテーブル更新
            Rest::where('attendance_id', $requestModel->attendance->id)->delete();
            $newRests = RequestedRest::where('requested_attendance_id', $id)->get();
            foreach ($newRests as $newRest) {
                Rest::create([
                    'attendance_id' => $attendanceId,
                    'start' => $newRest->start,
                    'end' => $newRest->end,
                ]);
            }
        });
        return redirect('/stamp_correction_request/approve/' . $id);
    }
}
