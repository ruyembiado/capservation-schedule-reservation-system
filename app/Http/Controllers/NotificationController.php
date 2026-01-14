<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Reservation;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\CustomReminder;
use App\Models\NotificationUser;

class NotificationController extends Controller
{
    public function index()
    {
        if (auth()->user()->user_type == 'admin') {
            $notifications = Notification::orderBy('id', 'desc')->get();
            $customReminders = CustomReminder::orderBy('id', 'desc')->get();
        } else if (auth()->user()->user_type == 'student') {
            $notifications = Notification::where('user_id', auth()->user()->id)
                ->orderBy('id', 'desc')
                ->get();
            $customReminders = CustomReminder::where('group_id', auth()->user()->id)
                ->orderBy('id', 'desc')
                ->get();
        } else if (auth()->user()->user_type == 'instructor') {
            $studentIds = User::where('instructor_id', auth()->user()->id)->pluck('id');
            $notifications = Notification::whereIn('user_id', $studentIds)
                ->orderBy('id', 'desc')
                ->get();
            $customReminders = [];
        } elseif (auth()->user()->user_type == 'panelist') {
            $notifications = Notification::where('user_id', auth()->user()->id)
                ->orderBy('id', 'desc')
                ->get();
            $customReminders = [];
        } else {
            $notifications = [];
            $customReminders = [];
        }

        $readNotifications = NotificationUser::where('status', 'read')
            ->where('user_id', auth()->user()->id)
            ->pluck('notification_id')
            ->toArray();

        $groups = User::where('user_type', 'student')->get();

        return view('notification', compact('notifications', 'customReminders', 'groups', 'readNotifications'));
    }

    public function createScheduleReminder()
    {
        $today = Carbon::today();
        $reservations = Reservation::with('latestSchedule', 'user')->get();

        foreach ($reservations as $reservation) {
            // Check if latestSchedule exists
            if ($reservation->latestSchedule) {
                $scheduleDate = Carbon::parse($reservation->latestSchedule->schedule_date);
                $reminderDate = $scheduleDate->copy()->subDay();

                if ($reminderDate->isSameDay($today)) {
                    Notification::create([
                        'user_id' => $reservation->group_id,
                        '_link_id' => $reservation->id,
                        'notification_type' => 'reminder',
                        'notification_title' => 'Upcoming Defense Schedule',
                        'notification_message' => ucwords($reservation->user->username)
                            . '\'s reservation is scheduled on '
                            . $scheduleDate->format('F j, Y \a\t h:i A')
                            . '. Please be prepared.',
                        'status' => 'unread',
                    ]);

                    $panelistIds = json_decode($reservation->panelist_id, true);
                    if (is_array($panelistIds)) {
                        foreach ($panelistIds as $panelistId) {
                            Notification::create([
                                'user_id' => $panelistId,
                                '_link_id' => $reservation->id,
                                'notification_type' => 'reminder',
                                'notification_title' => 'Upcoming Defense Schedule',
                                'notification_message' => ucwords($reservation->user->username)
                                    . '\'s reservation is scheduled on '
                                    . $scheduleDate->format('F j, Y \a\t h:i A')
                                    . '. Please be prepared.',
                                'status' => 'unread',
                            ]);
                        }
                    }
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

    public function bellNotifications()
    {
        $user = auth()->user();
        $notifications = collect();

        if ($user->user_type === 'admin') {
            $notif = Notification::select('id', '_link_id', 'notification_message as message', 'created_at')
                ->latest('created_at')
                ->get();

            $reminders = CustomReminder::select('id', 'message', 'schedule_datetime as created_at')
                ->latest('created_at')
                ->get();

            $notifications = $this->formatNotifications($notif, $reminders);
        } elseif ($user->user_type === 'student') {
            $notif = Notification::where('user_id', $user->id)
                ->select('id', '_link_id', 'notification_message as message', 'created_at')
                ->latest('created_at')
                ->get();

            $reminders = CustomReminder::where('group_id', $user->id)
                ->select('id', 'message', 'schedule_datetime as created_at')
                ->latest('created_at')
                ->get();

            $notifications = $this->formatNotifications($notif, $reminders);
        } elseif ($user->user_type === 'instructor') {
            $studentIds = User::where('instructor_id', $user->id)->pluck('id');

            $notif = Notification::whereIn('user_id', $studentIds)
                ->select('id', '_link_id', 'notification_message as message', 'created_at')
                ->latest('created_at')
                ->get();

            $notifications = $this->formatNotifications($notif);
        } elseif ($user->user_type === 'panelist') {
            $notif = Notification::where('user_id', $user->id)
                ->select('id', '_link_id', 'notification_message as message', 'created_at')
                ->latest('created_at')
                ->get();

            $reminders = [];

            $notifications = $this->formatNotifications($notif, $reminders);
        }

        $notifications = collect($notifications)->sortByDesc('created_at')->values();

        $readNotifications = NotificationUser::where('status', 'read')
            ->where('user_id', $user->id)
            ->pluck('notification_id')
            ->toArray();

        return response()->json([
            'notifications' => $notifications,
            'readNotifications' => $readNotifications,
        ]);
    }

    private function formatNotifications($notif, $reminders = null)
    {
        $formattedNotif = $notif->map(function ($n) {
            return [
                'id'          => $n->id,
                'type'        => 'notification',
                'message'     => $n->message,
                'link_id'     => $n->_link_id,
                'created_at'  => $n->created_at,
                'time_ago'    => $n->created_at->diffForHumans(),
            ];
        });

        if ($reminders) {
            $formattedReminders = $reminders->map(function ($r) {
                $created = \Carbon\Carbon::parse($r->created_at);
                return [
                    'id'          => $r->id,
                    'type'        => 'reminder',
                    'message'     => $r->message,
                    'link_id'     => null,
                    'created_at'  => $created, // keep raw Carbon
                    'time_ago'    => $created->diffForHumans(),
                ];
            });

            return $formattedNotif->merge($formattedReminders)
                ->sortByDesc('id')
                ->values();
        }

        return $formattedNotif->sortByDesc('created_at')->values();
    }
}
