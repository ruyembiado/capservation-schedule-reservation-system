<?php

namespace App\Http\Controllers;

use App\Models\Capstone;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->user_type === 'student') {
            $reservations = Reservation::where('reserve_by', Auth::user()->id)
                ->with('user', 'reserveBy', 'capstone')
                ->get();
        } else {
            $reservations = Reservation::with('capstone', 'user', 'reserveBy')
                ->get()
                ->groupBy('group_id')
                ->map(function ($group) {
                    return [
                        'group_id' => $group->first()->group_id,
                        'user' => $group->first()->user,
                        'reserveBy' => $group->first()->reserveBy, 
                        'titles' => $group->pluck('capstone.title')->toArray(), 
                        'status' => $group->first()->status, 
                        'created_at' => $group->first()->created_at,
                    ];
                })->values(); 
        }

        return view('reservation', compact('reservations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $selectedGroup = session('selected_group');
        $reservation = Reservation::where('group_id', $selectedGroup)->latest()->first();

        $groups = User::where('user_type', 'student')->select('id', 'username')->get();

        return view('reserve', compact('groups', 'reservation', 'selectedGroup'));
    }

    public function storeGroup(Request $request)
    {
        session(['selected_group' => $request->group]);

        return redirect()->route('reservation.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title_1' => 'required',
            'title_2' => 'required',
            'title_3' => 'required',
            'group_id' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->all());
        }

        $titles = [
            'title_1' => $request->title_1,
            'title_2' => $request->title_2,
            'title_3' => $request->title_3,
        ];

        foreach ($titles as $title) {
            if (!empty($title)) {
                $capstone = Capstone::create([
                    'group_id' => $request->group_id,
                    'title' => $title,
                ]);

                Reservation::create([
                    'group_id' => $request->group_id,
                    'capstone_title_id' => $capstone->id,
                    'reserve_by' => auth()->id(),
                ]);
            }
        }

        session()->forget('selected_group');

        return redirect()->back()->with('success', 'Reserved successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reservation $reservation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reservation $reservation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        //
    }
}
