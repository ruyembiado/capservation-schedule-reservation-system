<?php

namespace App\Http\Controllers;

use App\Models\Capstone;
use App\Models\User;
use App\Models\Reservation;
use App\Models\Transaction;
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
                ->with(['user', 'reserveBy'])
                ->get()
                ->map(function ($reservation) {
                    return [
                        'id' => $reservation->id,
                        'group_id' => $reservation->group_id,
                        'user' => $reservation->user,
                        'reserveBy' => $reservation->reserveBy,
                        'titles' => Capstone::whereIn('id', json_decode($reservation->capstone_title_id, true))
                            ->pluck('title')
                            ->toArray(),
                        'status' => $reservation->status,
                        'created_at' => $reservation->created_at,
                    ];
                });
        } else {
            $reservations = Reservation::with(['user', 'reserveBy'])
                ->get()
                ->groupBy('group_id')
                ->map(function ($group) {
                    return [
                        'id' => $group->first()->id,
                        'group_id' => $group->first()->group_id,
                        'user' => $group->first()->user,
                        'reserveBy' => $group->first()->reserveBy,
                        'titles' => Capstone::whereIn('id', json_decode($group->first()->capstone_title_id, true))
                            ->pluck('title')
                            ->toArray(),
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

        $capstoneIds = [];
        foreach ($titles as $title) {
            if (!empty($title)) {
                $capstone = Capstone::create([
                    'group_id' => $request->group_id,
                    'title' => $title,
                ]);

                $capstoneIds[] = $capstone->id;
            }
        }

        Reservation::create([
            'group_id' => $request->group_id,
            'capstone_title_id' => json_encode($capstoneIds),
            'reserve_by' => auth()->id(),
        ]);

        $transactionCode = $this->generateTransactionCode();

        $user = User::find($request->group_id);

        if (!$user) {
            return redirect()->back()->withErrors(['group_id' => 'Group not found.']);
        }

        $type_of_defense = match ($request->type_of_defense ?? 'title_defense') {
            'pre_oral' => 'pre_oral',
            'final_defense' => 'final_defense',
            default => 'title_defense',
        };

        Transaction::create([
            'group_id' => $request->group_id,
            'group_name' => $user->username,
            'members' => $user->members,
            'program' => $user->program,
            'type_of_defense' => $type_of_defense,
            'transaction_code' => $transactionCode
        ]);

        session()->forget('selected_group');

        return redirect()->back()->with('success', 'Reserved successfully.');
    }

    private function generateTransactionCode()
    {
        $lastTransaction = Transaction::latest('id')->first();

        $nextNumber = $lastTransaction
            ? ((int) str_replace('CAP-', '', $lastTransaction->transaction_code) + 1)
            : 1;

        return 'CAP-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
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
    public function destroy($id)
    {
        $reservation = Reservation::find($id);

        if (!$reservation) {
            return redirect()->back()->with('error', 'Reservation not found.');
        }

        $reservation->delete();

        return redirect()->back()->with('success', 'Reservation deleted successfully.');
    }
}
