<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        if (auth()->user()->user_type == 'admin') {
            $notifications = Notification::orderBy('created_at', 'desc')->get();
        } else if (auth()->user()->user_type == 'student') {
            $notifications = Notification::where('user_id', auth()->user()->id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else if (auth()->user()->user_type == 'instructor') {
            $studentIds = User::where('instructor_id', auth()->user()->id)->pluck('id');
            $notifications = Notification::whereIn('user_id', $studentIds)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $notifications = [];
        }

        return view('notification', compact('notifications'));
    }
}
