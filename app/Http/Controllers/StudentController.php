<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function studentDashboard()
    {
        return view('student.dashboard');
    }
}
