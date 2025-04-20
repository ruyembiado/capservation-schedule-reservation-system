<?php

namespace App\Http\Controllers;

use App\Models\Capstone;
use App\Models\Schedule;
use App\Models\Reservation;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CapstoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $capstones = Capstone::with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('group_id');

        return view('capstone', compact('capstones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($ids)
    {
        $idsArray = explode('/', $ids);
        $capstones = Capstone::whereIn('id', $idsArray)->with('user')->get();
        $groupId = $capstones->first()->group_id;
        $reservation = Reservation::where('group_id', $groupId)->latest()->first();
        $schedule = Schedule::where('group_id', $groupId)->latest()->first();

        return view('update_capstone', compact('capstones', 'reservation', 'schedule'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Capstone $capstone)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Capstone $capstone)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $ids)
    {
        $idsArray = explode(',', $ids);

        $validator = Validator::make($request->all(), [
            'title.*' => 'required|string|max:255',
            'title_status.*' => 'required|in:defended,pending,rejected',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $groupId = null;

        foreach ($idsArray as $index => $id) {
            $capstone = Capstone::find($id);
            if ($capstone) {
                $groupId = $capstone->group_id;
                $capstone->title = $request->title[$index];
                $capstone->title_status = $request->title_status[$index];
                $capstone->save();
            }
        }
        if ($groupId) {
            $reservation = Reservation::where('group_id', $groupId)
                ->latest()
                ->first();

            if ($reservation) {
                $reservation->status = 'done';
                $reservation->save();
            }
        }
        return redirect('/capstones')->with('success', 'Capstone(s) updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Capstone $capstone)
    {
        //
    }

    public function history()
    {
        $reservations = Reservation::with(['schedule', 'transaction', 'reservationHistory'])
            ->where('group_id', auth()->id())
            ->orderBy('created_at', 'asc')
            ->get();

        return view('capstone_history', compact('reservations'));
    }
}
