<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Reservation;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\CustomReminder;

class NotificationController extends Controller
{
    public function index()
    {
        if (auth()->user()->user_type == 'admin') {
            $notifications = Notification::orderBy('created_at', 'desc')->get();
            $customReminders = CustomReminder::orderBy('created_at', 'desc')->get();
        } else if (auth()->user()->user_type == 'student') {
            $notifications = Notification::where('user_id', auth()->user()->id)
                ->orderBy('created_at', 'desc')
                ->get();
            $customReminders = CustomReminder::where('group_id', auth()->user()->id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else if (auth()->user()->user_type == 'instructor') {
            $studentIds = User::where('instructor_id', auth()->user()->id)->pluck('id');
            $notifications = Notification::whereIn('user_id', $studentIds)
                ->orderBy('created_at', 'desc')
                ->get();
            $customReminders = [];
        } else {
            $notifications = [];
        }

        $groups = User::where('user_type', 'student')->get();

        return view('notification', compact('notifications', 'customReminders', 'groups'));
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
                        'notification_message' => ucwords($reservation->user->username) . '\'s reservation is scheduled on ' . $scheduleDate->format('F j, Y \a\t h:i A') . '. Please be prepared.',
                        'status' => 'unread',
                    ]);
                }
            }
        }
    }

    public function storeCustomReminder(Request $request)
    {
        $request->validate([
            'title_status' => 'required|string',
            'message' => 'required|string',
            'group_id' => 'required|exists:users,id',
            'defense_stage' => 'nullable|string',
            'schedule_datetime' => 'required|date',
        ]);

        CustomReminder::create([
            'title_status' => $request->title_status,
            'message' => $request->message,
            'group_id' => $request->group_id,
            'defense_stage' => $request->defense_stage,
            'schedule_datetime' => $request->schedule_datetime,
        ]);

        return redirect()->back()->with('success', 'Custom reminder created successfully!');
    }
}
