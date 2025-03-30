<?php

namespace App\Http\Controllers;

use App\Models\Capstone;
use App\Models\Panelist;
use App\Models\Reservation;
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
        $panelists = Panelist::all();

        $capstoneIds = (array) json_decode($reservation->capstone_title_id, true);
        $capstones = Capstone::whereIn('id', $capstoneIds)->get();

        return view('assign_panelist_form', compact('reservation', 'panelists', 'capstones'));
    }


    public function updateForm($id)
    {
        $panelist = Panelist::where('id', $id)->get()->first();
        return view('update_panelist', compact('panelist'));
    }

    public function viewPanelist($id)
    {
        $reservation = Reservation::findOrFail($id);
        $panelists = Panelist::all();

        $capstoneIds = (array) json_decode($reservation->capstone_title_id, true);
        $capstones = Capstone::whereIn('id', $capstoneIds)->get();

        return view('view_panelist', compact('reservation', 'panelists', 'capstones'));
    }

    public function updatePanelist(Request $request, $id)
    {
        $rules = [
            'name' => 'required|unique:panelists,name,' . $id,
            'email' => 'required|email|unique:panelists,email,' . $id,
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

        $panelist->save();

        return redirect('/panelists')->with('success', 'Panelist updated successfully!');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Remove empty credentials before validation
        $request->merge([
            'credentials' => array_filter($request->credentials, function ($credential) {
                return !empty($credential);
            })
        ]);

        $messages = [];
        if ($request->credentials) {
            foreach ($request->credentials as $index => $credential) {
                $messages["credentials.$index.required"] = "Credential " . ($index + 1) . " is required.";
            }
        }

        if ($request->vacant_time['start_time']) {
            foreach ($request->vacant_time['start_time'] as $index => $startTime) {
                $messages["vacant_time.end_time.$index.after"] = "The vacant time end time " . ($index + 1) . " must be a time after start time " . ($index + 1) . ".";
            }
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'vacant_time.day.*' => 'required',
            'vacant_time.start_time.*' => 'required|date_format:H:i',
            'vacant_time.end_time.*' => 'required|date_format:H:i|after:vacant_time.start_time.*',
            'credentials.*' => 'required|string|max:255',
        ], $messages);

        if ($validator->fails()) {
            // dd($validator->errors());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->all());
        }

        $panelist = Panelist::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

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
            $panelist->save();
        }

        if ($request->has('credentials')) {
            $panelist->credentials = json_encode($request->credentials);
            $panelist->save();
        }

        return redirect()->back()->with('success', 'Panelist added successfully!');
    }

    public function assignPanelists(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'panelists' => 'required|array|size:4',
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

        return redirect('/reservations')->with('success', 'Panelists assigned successfully!');
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
