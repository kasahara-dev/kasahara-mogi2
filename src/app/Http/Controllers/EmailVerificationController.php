<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class EmailVerificationController extends Controller
{
    public function show()
    {
        return view('auth.verify');
    }
}