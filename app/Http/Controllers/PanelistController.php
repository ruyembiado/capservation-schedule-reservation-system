<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Capstone;
use App\Models\Panelist;
use App\Models\Reservation;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PanelistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $panelists = Panelist::orderBy('created_at', 'desc')->get();
        return view('panelist', compact('panelists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('add_panelist');
    }

    public function showForm($id)
    {
        $reservation = Reservation::findOrFail($id);

        $group = User::where('id', $reservation->group_id)->first();
        $tags = json_decode($group->credentials, true);

        // $panelists = Panelist::where(function ($query) use ($tags) {
        //     foreach ($tags as $tag) {
        //         $query->orWhereJsonContains('credentials', $tag);
        //     }
        // })->get();

        $panelists = User::whereNot('id', $group->instructor_id )->where('user_type', 'instructor')->where(function ($query) use ($tags) {
            foreach ($tags as $tag) {
                $query->orWhereJsonContains('credentials', $tag);
            }
        })->get();

        $capstoneIds = (array) json_decode($reservation->capstone_title_id, true);
        $capstones = Capstone::whereIn('id', $capstoneIds)->get();

        return view('assign_panelist_form', compact('reservation', 'panelists', 'capstones', 'group'));
    }

    public function updateForm($id)
    {
        $panelist = Panelist::where('id', $id)->get()->first();
        return view('update_panelist', compact('panelist'));
    }

    public function viewSinglePanelist($id)
    {
        $panelist = Panelist::where('id', $id)->get()->first();
        return view('view_single_panelist', compact('panelist'));
    }

    public function viewPanelist($id)
    {
        $reservation = Reservation::findOrFail($id);
        // $panelists = Panelist::all();
        $panelists = User::where('user_type', 'instructor')->get();

        $capstoneIds = (array) json_decode($reservation->capstone_title_id, true);
        $capstones = Capstone::whereIn('id', $capstoneIds)->get();

        return view('view_panelist', compact('reservation', 'panelists', 'capstones'));
    }

    public function updatePanelist(Request $request, $id)
    {
        $rules = [
            'name' => 'required|unique:panelists,name,' . $id,
            'email' => 'required|email|unique:panelists,email,' . $id,
            'vacant_time' => 'nullable|array',
            'credentials.*' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $panelist = Panelist::findOrFail($id);

        $panelist->name = $request->name;
        $panelist->email = $request->email;
        $panelist->capacity = $request->capacity;
        if ($request->has('credentials')) {
            $panelist->credentials = json_encode($request->credentials);
        }

        if ($request->has('vacant_time')) {
            $vacantTimes = [];
            foreach ($request->vacant_time['day'] as $index => $day) {
                $vacantTimes[] = [
                    'day' => $day,
                    'start_time' => $request->vacant_time['start_time'][$index],
                    'end_time' => $request->vacant_time['end_time'][$index],
                ];
            }
            $panelist->vacant_time = json_encode($vacantTimes);
        }

        $panelist->save();

        return redirect('/panelists')->with('success', 'Panelist updated successfully!');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Clean up empty credentials
        if (is_array($request->credentials)) {
            $request->merge([
                'credentials' => array_filter($request->credentials, function ($credential) {
                    return !empty($credential);
                })
            ]);
        } else {
            $request->merge(['credentials' => []]);
        }

        // Custom messages
        $messages = [];

        if ($request->credentials) {
            foreach ($request->credentials as $index => $credential) {
                $messages["credentials.$index.required"] = "Credential " . ($index + 1) . " is required.";
            }
        }

        if (isset($request->vacant_time['start_time'])) {
            foreach ($request->vacant_time['start_time'] as $index => $startTime) {
                $messages["vacant_time.end_time.$index.after"] = "The vacant time end time " . ($index + 1) . " must be a time after start time " . ($index + 1) . ".";
            }
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:panelists,email',
            'vacant_time.day.*' => 'required',
            'vacant_time.start_time.*' => 'required|date_format:H:i',
            'vacant_time.end_time.*' => 'required|date_format:H:i',
            'credentials.*' => 'required|string|max:255',
        ], $messages);

        // Manual time comparison before checking `fails()`
        if (isset($request->vacant_time['start_time'])) {
            foreach ($request->vacant_time['start_time'] as $index => $startTime) {
                $endTime = $request->vacant_time['end_time'][$index] ?? null;
                if ($endTime && strtotime($endTime) <= strtotime($startTime)) {
                    $validator->after(function ($validator) use ($index) {
                        $validator->errors()->add(
                            "vacant_time.end_time.$index",
                            "The vacant time end time " . ($index + 1) . " must be a time after start time " . ($index + 1) . "."
                        );
                    });
                }
            }
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Save Panelist
        $panelist = new Panelist();
        $panelist->name = $request->name;
        $panelist->email = $request->email;

        // Save vacant times if provided
        if ($request->has('vacant_time')) {
            $vacantTimes = [];
            foreach ($request->vacant_time['day'] as $index => $day) {
                $vacantTimes[] = [
                    'day' => $day,
                    'start_time' => $request->vacant_time['start_time'][$index],
                    'end_time' => $request->vacant_time['end_time'][$index],
                ];
            }
            $panelist->vacant_time = json_encode($vacantTimes);
        }

        // Save credentials
        if ($request->has('credentials')) {
            $panelist->credentials = json_encode($request->credentials);
        }

        $panelist->save();

        return redirect()->back()->with('success', 'Panelist added successfully!');
    }

    public function assignPanelists(Request $request)
    {
        $reservation = Reservation::findOrFail($request->reservation_id);
        $user = User::where('id', $reservation->group_id)->first();

        $validator = Validator::make($request->all(), [
            'panelists' => 'required|array|min:' . $user->capacity . '|max:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->all());
        }

        $reservation = Reservation::findOrFail($request->reservation_id);

        $panelists = array_map('intval', $request->panelists);

        $reserved = $reservation->update([
            'panelist_id' => json_encode($panelists),
            'status' => 'approved'
        ]);

        if (!$reserved) {
            return redirect()->back()->with('error', 'Failed to assign panelists.');
        }

        if ($request->type_of_action == 'add_panelists') {
            Notification::create([
                'user_id' => $reservation->group_id,
                '_link_id' => $reservation->id,
                'notification_type' => 'system_alert',
                'notification_title' => 'Panelist Assigned',
                'notification_message' => ucwords($reservation->user->username) . '\'s reservation has been approved for scheduling and panelists have been assigned.',
            ]);

            return redirect('/reservations')->with('success', 'Panelists assigned successfully!');
        } else if ($request->type_of_action == 'update_panelists') {
            Notification::create([
                'user_id' => $reservation->group_id,
                '_link_id' => $reservation->id,
                'notification_type' => 'status_update',
                'notification_title' => 'Panelist Updated',
                'notification_message' => ucwords($reservation->user->username) . '\'s reservation has been updated and panelists have been re-assigned.',
            ]);

            return redirect('/reservations')->with('success', 'Panelists updated successfully!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Panelist $panelist)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Panelist $panelist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Panelist $panelist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $panelist = Panelist::findOrFail($id);
        $panelist->delete();

        return redirect('/panelists')->with('success', 'Panelist deleted successfully!');
    }
}
