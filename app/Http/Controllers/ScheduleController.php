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
        $schedules = Schedule::with('user')->orderBy('schedule_date', 'ASC')->get()->map(function ($schedule) {
            $isUnavailable = ($schedule->schedule_category === 'unavailable');

            if ($isUnavailable) {
                $start = $schedule->schedule_date;
                $end   = date('Y-m-d', strtotime($schedule->schedule_date . ' +1 day'));

                return [
                    'id' => $schedule->id,
                    'title' => '',
                    'start' => $start,
                    'end'   => $end,
                    'allDay' => true,
                    'display' => 'background',
                    'backgroundColor' => '#DC3545',
                    'borderColor' => '#DC3545',
                    'textColor' => '#ffffff',
                    'isUnavailable' => true,
                ];
            }

            return [
                'id' => $schedule->id,
                'title' => ucwords(optional($schedule->user)->username ?? 'Unknown User'),
                'start' => $schedule->schedule_date . 'T' . $schedule->schedule_time,
                'end'   => $schedule->schedule_date . 'T' . date('H:i:s', strtotime($schedule->schedule_time . ' +1 hour')),
                'allDay' => true,
                'isUnavailable' => false,
                'backgroundColor' => '#3788d8',
                'borderColor' => '#3788d8',
                'textColor' => '#ffffff',

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
            'group' => 'nullable|exists:users,id',
            'schedule_date' => 'required|date',
            'schedule_time' => 'nullable|date_format:H:i',
            'schedule_category' => 'nullable|in:available,occupied,unavailable',
            'schedule_remarks' => 'nullable|string|max:255',
        ]);

        $validator->sometimes(['group', 'schedule_time'], 'required', function ($input) {
            return empty($input->schedule_category);
        });

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
                'group_id' => $request->group ?: null,
                'reservation_id' => $reservation->id,
                'schedule_date' => $request->schedule_date,
                'schedule_time' => $request->schedule_time ?: null,
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
        } else {
            Schedule::create([
                'group_id' => null,
                'reservation_id' => null,
                'schedule_date' => $request->schedule_date,
                'schedule_time' => null,
                'schedule_category' => $request->schedule_category ?: '',
                'schedule_remarks' => $request->schedule_remarks ?: '',
            ]);

            $schedules = Schedule::where('schedule_date', $request->schedule_date)->get();

            foreach ($schedules as $schedule) {
                if ($schedule->reservation_id) {
                    ReservationHistory::create([
                        'reservation_id' => $schedule->reservation_id,
                    ]);
                }
            }

            Reservation::whereIn('id', $schedules->pluck('reservation_id'))
                ->update(['status' => 'approved']);

            return redirect()->back()->with('success', 'All schedules for ' . $request->schedule_date . ' have been set to re-defense.');
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
