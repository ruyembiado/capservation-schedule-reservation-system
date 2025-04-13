<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Reservation;
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

    public function createScheduleReminder()
    {
        $today = Carbon::today();
        $reservations = Reservation::with('schedule', 'user')->get();

        foreach ($reservations as $reservation) {
            if ($reservation->schedule) {
                $scheduleDate = Carbon::parse($reservation->schedule->schedule_date);
                $reminderDate = $scheduleDate->copy()->subDay();

                if ($reminderDate->isSameDay($today)) {
                    Notification::create([
                        'user_id' => $reservation->group_id,
                        '_link_id' => $reservation->id,
                        'notification_type' => 'reminder',
                        'notification_title' => 'Upcoming Defense Schedule',
                        'notification_message' => ucwords($reservation->user->username) . '\'s reservation is scheduled on ' . $scheduleDate->toFormattedDateString() . '. Please be prepared.',
                        'status' => 'unread',
                    ]);
                }
            }
        }
    }
}
