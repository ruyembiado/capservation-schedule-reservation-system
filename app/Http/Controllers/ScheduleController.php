<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Reservation;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\ReservationHistory;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reservations = Reservation::where('status', 'approved')->with('user')->get();
        return view('calendar', compact('reservations'));
    }

    public function getSchedules()
    {
        $schedules = Schedule::with('user')->get()->map(function ($schedule) {
            return [
                'id' => $schedule->id,
                'title' => ucwords(optional($schedule->user)->username) ?? 'Unknown User',
                'start' => $schedule->schedule_date . 'T' . $schedule->schedule_time,
                'end' => $schedule->schedule_date . 'T' . date('H:i:s', strtotime($schedule->schedule_time . ' +1 hour')),
                'groupId' => $schedule->group_id,
                'allDay' => false,
            ];
        });

        return response()->json($schedules);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group' => 'required|exists:users,id',
            'schedule_date' => 'required|date',
            'schedule_time' => 'required|date_format:H:i',
            'schedule_category' => 'nullable|in:available,occupied,unavailable',
            'schedule_remarks' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->all());
        }

        $reservation = Reservation::where('group_id', $request->group)
            ->where('status', 'approved')
            ->first();

        if ($reservation) {
            $reservation->status = 'reserved';
            $reservation->save();

            Schedule::create([
                'group_id' => $request->group,
                'reservation_id' => $reservation->id,
                'schedule_date' => $request->schedule_date,
                'schedule_time' => $request->schedule_time,
                'schedule_category' => $request->schedule_category ?: '',
                'schedule_remarks' => $request->schedule_remarks ?: '',
            ]);

            Notification::create([
                'user_id' => $request->group,
                '_link_id' => $reservation->id,
                'notification_type' => 'system_alert',
                'notification_title' => 'Schedule Created',
                'notification_message' => ucfirst($reservation->user->username) . '\'s reservation has been scheduled for defense.',
            ]);
        }

        return redirect()->back()->with('success', 'Schedule created successfully.');
    }

    /** 
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $reservation = Reservation::findOrFail($request->id);

        ReservationHistory::create([
            'reservation_id' => $reservation->id,
        ]);

        $reservation->status = 'approved';
        $reservation->save();

        return redirect()->back()->with('success', 'Reservation Re-scheduled.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        //
    }
}
