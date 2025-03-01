<?php

namespace App\Http\Controllers;

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
        $panelists = Panelist::all();
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

        return view('assign_panelist_form', compact('reservation', 'panelists'));
    }

    public function updateForm($id)
    {
        $panelist = Panelist::where('id', $id)->get()->first();
        return view('update_panelist', compact('panelist'));
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:panelists',
            'email' => 'required|email|unique:panelists',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->all());
        }

        Panelist::create($request->all());

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
