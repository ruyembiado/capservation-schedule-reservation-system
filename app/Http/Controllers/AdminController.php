<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function groups()
    {
        if (Auth::user()->user_type === 'instructor') {
            $groups = User::where('user_type', 'student')->where('instructor_id', Auth::user()->id)->with('instructor')->orderBy('created_at', 'desc')->get();
        } else {
            $groups = User::where('user_type', 'student')->with('instructor')->orderBy('created_at', 'desc')->get();
        }
        return view('group', compact('groups'));
    }

    public function instructors()
    {
        $instructors = User::where('user_type', 'instructor')->orderBy('created_at', 'desc')->get();
        return view('instructor', compact('instructors'));
    }

    public function transactions()
    {
        return view('transaction');
    }
}
