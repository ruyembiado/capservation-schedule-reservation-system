<?php

namespace App\Http\Controllers;

use App\Models\Capstone;
use App\Models\Schedule;
use App\Models\Reservation;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CapstoneHistory;
use App\Models\ReservationHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CapstoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $capstones = Capstone::with('user', 'histories')
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
            'title.*' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($idsArray) {
                    if (DB::table('capstones')
                        ->where('title', $value)
                        ->whereNotIn('id', $idsArray)
                        ->exists()
                    ) {
                        $fail("The $attribute must be unique.");
                    }
                }
            ],
            'title_status.*' => 'required|in:defended,pending,rejected,redefense',
            // Optional file validation rules
            'attachment_1' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx',
            'attachment_2' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx',
            'attachment_3' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx',
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
                $newTitle = $request->title[$index];
                $newTitleStatus = $request->title_status[$index];

                if ($capstone->title !== $newTitle) {
                    CapstoneHistory::create([
                        'capstone_id' => $capstone->id,
                        'user_id' => auth()->id(), 
                        'old_capstone_name' => $capstone->title,
                    ]);
                }

                // Update Capstone
                $capstone->title = $newTitle;
                $capstone->title_status = $newTitleStatus;

                $attachmentKey = 'attachment_' . ($index + 1); 
                if ($request->hasFile($attachmentKey)) {
                    $file = $request->file($attachmentKey);
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('capstones'), $filename);
                    $capstone->attachment = 'capstones/' . $filename;
                }

                $capstone->save();
            }
        }

        if ($groupId) {
            $reservation = Reservation::where('group_id', $groupId)
                ->latest()
                ->first();

            if ($reservation) {
                if (in_array('redefense', $request->title_status)) {
                    $reservation->status = 'pending';
                } elseif (in_array('pending', $request->title_status)) {
                    $reservation->status = 'pending';
                } else {
                    $reservation->status = 'done';
                }

                if ($request->title_status == 'redefense') {
                    ReservationHistory::create([
                        'reservation_id' => $reservation->id,
                    ]);
                }

                $reservation->save();
            }
        }

        return redirect('/capstones-list')->with('success', 'Capstone(s) updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Capstone $capstone)
    {
        //
    }

    public function history($id = '')
    {
        if (auth()->user()->user_type == 'student') {
            $reservations = Reservation::with(['schedule', 'transaction', 'reservationHistory'])
                ->where('group_id', auth()->id())
                ->orderBy('created_at', 'asc')
                ->get();
        } else {
            $reservations = Reservation::with(['schedule', 'transaction', 'reservationHistory'])
                ->where('group_id', $id)
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return view('capstone_history', compact('reservations'));
    }
}
