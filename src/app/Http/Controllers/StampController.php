<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class StampController extends Controller
{
    public function create($id)
    {
        // 申請中有無判定
        if (Auth::user()->attendances()->where('status', 1)->whereDate('start', Auth::user()->attendances()->find($id)->start)) {
        } else {
        }
        return view('attendance/detail');
        ;
    }
}
