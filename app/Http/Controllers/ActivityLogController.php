<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        if (auth()->user()->user_type == 'admin') {
            $activity_logs = ActivityLog::orderBy('created_at', 'desc')->get();
        } else {
            $activity_logs = ActivityLog::where('instructor_id', auth()->user()->id)->orderBy('created_at', 'desc')->get();
        }

        return view('activity_log', compact('activity_logs'));
    }

}