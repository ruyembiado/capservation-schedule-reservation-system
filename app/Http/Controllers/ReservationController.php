<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Capstone;
use Mockery\Matcher\Not;
use App\Models\Reservation;
use App\Models\Transaction;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\NotificationUser;
use Illuminate\Notifications\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->user_type === 'student') {
            $reservations = Reservation::where('group_id', Auth::user()->id)
                ->with(['user', 'reserveBy', 'schedule'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($reservation) {
                    $latestSchedule = $reservation->schedule->sortByDesc('schedule_date')->first();
                    $titles = $this->getTitlesForReservation($reservation);
                    return [
                        'id' => $reservation->id,
                        'group_id' => $reservation->group_id,
                        'user' => $reservation->user,
                        'reserveBy' => $reservation->reserveBy,
                        'titles' => $titles,
                        'status' => $reservation->status,
                        'created_at' => $reservation->created_at,
                        'schedule_date' => $latestSchedule ? $latestSchedule->schedule_date : 'No date available',
                        'schedule_time' => $latestSchedule && $latestSchedule->schedule_time
                            ? \Carbon\Carbon::parse($latestSchedule->schedule_time)->format('h:i A')
                            : 'No time available',
                    ];
                });
        } elseif (Auth::user()->user_type === 'instructor') {
            $studentIds = User::where('instructor_id', Auth::user()->id)->pluck('id');
            $reservations = Reservation::whereIn('group_id', $studentIds)
                ->with(['user', 'reserveBy', 'schedule'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($reservation) {
                    $latestSchedule = $reservation->schedule->sortByDesc('schedule_date')->first();
                    $titles = $this->getTitlesForReservation($reservation);
                    return [
                        'id' => $reservation->id,
                        'group_id' => $reservation->group_id,
                        'user' => $reservation->user,
                        'reserveBy' => $reservation->reserveBy,
                        'titles' => $titles,
                        'status' => $reservation->status,
                        'created_at' => $reservation->created_at,
                        'schedule_date' => $latestSchedule ? $latestSchedule->schedule_date : 'No date available',
                        'schedule_time' => $latestSchedule && $latestSchedule->schedule_time
                            ? \Carbon\Carbon::parse($latestSchedule->schedule_time)->format('h:i A')
                            : 'No time available',
                    ];
                });
        } else {
            $reservations = Reservation::with(['user', 'reserveBy', 'schedule'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($reservation) {
                    $latestSchedule = $reservation->schedule->sortByDesc('schedule_date')->first();
                    $titles = $this->getTitlesForReservation($reservation);
                    return [
                        'id' => $reservation->id,
                        'group_id' => $reservation->group_id,
                        'user' => $reservation->user,
                        'reserveBy' => $reservation->reserveBy,
                        'titles' => $titles,
                        'status' => $reservation->status,
                        'created_at' => $reservation->created_at,
                        'schedule_date' => $latestSchedule ? $latestSchedule->schedule_date : 'No date available',
                        'schedule_time' => $latestSchedule && $latestSchedule->schedule_time
                            ? \Carbon\Carbon::parse($latestSchedule->schedule_time)->format('h:i A')
                            : 'No time available',
                    ];
                });
        }

        return view('reservation', compact('reservations'));
    }

    /**
     * Helper method to get titles for a reservation based on the type of defense.
     *
     * @param Reservation $reservation
     * @return array
     */
    private function getTitlesForReservation($reservation)
    {
        $transaction = Transaction::where('reservation_id', $reservation->id)->first();
        if ($transaction && in_array($transaction->type_of_defense, ['pre_oral_defense', 'final_defense'])) {
            $capstone = Capstone::find($reservation->capstone_title_id);
            return $capstone ? [$capstone->title] : [];
        } else {
            $capstoneIds = json_decode($reservation->capstone_title_id, true);
            return Capstone::whereIn('id', $capstoneIds)->pluck('title')->toArray();
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $selectedGroup = session('selected_group', null);

        // if (Auth::user()->user_type === 'student') {
        //     $reservation = Reservation::with('capstone')->where('group_id', Auth::user()->id)->latest()->first();
        // } else {
        //     $reservation = Reservation::with('capstone')->where('group_id', $selectedGroup)->latest()->first();
        // }

        $reservation = Reservation::with('capstone')->where('group_id', $selectedGroup)->latest()->first();

        $transaction = Transaction::where('group_id', $reservation->group_id ?? null)
            ->latest()
            ->first();

        $groups = User::where('user_type', 'student')->select('id', 'username')->get();

        $defendedCapstones = [];
        if ($reservation) {
            if ($transaction && in_array($transaction->type_of_defense, ['pre_oral_defense', 'final_defense'])) {
                $capstoneId = $reservation->capstone_title_id;
                if ($capstoneId) {
                    $defendedCapstones = Capstone::where('id', $capstoneId)
                        ->where('title_status', 'defended')
                        ->get();
                }
            } else {
                $capstoneIds = json_decode($reservation->capstone_title_id ?? '[]', true);
                if (is_array($capstoneIds)) {
                    $defendedCapstones = Capstone::whereIn('id', $capstoneIds)
                        ->whereIn('title_status', ['defended', 'pending'])
                        ->get();
                }
            }
        }

        return view('reserve', compact('groups', 'reservation', 'selectedGroup', 'transaction', 'defendedCapstones'));
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
        $notTitleDefense = $request->has('type_of_defense') && $request->has('capstone_title_id');

        if ($notTitleDefense) {
            $validator = Validator::make($request->all(), [
                'group_id' => 'required',
                'type_of_defense' => 'required',
                'capstone_title_id' => 'required'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput($request->all());
            }

            $reservation = Reservation::create([
                'group_id' => $request->group_id,
                'capstone_title_id' => $request->capstone_title_id,
                'reserve_by' => Auth::user()->id,
            ]);

            Capstone::where('id', $reservation->capstone_title_id)
                ->update(['title_status' => 'pending', 'capstone_status' => $request->type_of_defense]);

            $transactionCode = $this->generateTransactionCode();

            $user = User::find($request->group_id);

            if (!$user) {
                return redirect()->back()->withErrors(['group_id' => 'Group not found.']);
            }

            Transaction::create([
                'group_id' => $request->group_id,
                'reservation_id' => $reservation->id,
                'group_name' => $user->username,
                'members' => $user->members,
                'program' => $user->program,
                'type_of_defense' => $request->type_of_defense,
                'transaction_code' => $transactionCode
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'title_1' => 'required|string|max:255',
                'title_2' => 'required|string|max:255',
                'title_3' => 'required|string|max:255',

                // optional file validation rules
                'attachment_1' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx',
                'attachment_2' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx',
                'attachment_3' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx',
            ])->after(function ($validator) use ($request) {
                $titles = [
                    'title_1' => $request->title_1,
                    'title_2' => $request->title_2,
                    'title_3' => $request->title_3,
                ];

                $filteredTitles = array_filter($titles, fn($value) => !empty($value));

                // Check for duplicates within the request
                $counts = array_count_values($filteredTitles);
                foreach ($counts as $fieldValue => $count) {
                    if ($count > 1) {
                        foreach ($titles as $key => $value) {
                            if ($value === $fieldValue) {
                                $validator->errors()->add($key, 'Duplicate titles are not allowed.');
                            }
                        }
                    }
                }

                // Check for duplicates in the database
                foreach ($filteredTitles as $key => $value) {
                    if (DB::table('capstones')->where('title', $value)->exists()) {
                        $validator->errors()->add($key, "The title '{$value}' already exists.");
                    }
                }
            });

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

            foreach ($titles as $key => $title) {
                if (!empty($title)) {
                    $capstone = Capstone::create([
                        'group_id' => $request->group_id,
                        'title'    => $title,
                    ]);

                    // Match attachment key (attachment_1, attachment_2, ...)
                    $attachmentKey = str_replace('title', 'attachment', $key);

                    if ($request->hasFile($attachmentKey)) {
                        $file     = $request->file($attachmentKey);
                        $filename = time() . '_' . $file->getClientOriginalName();

                        // Move to public/capstones
                        $file->move(public_path('capstones'), $filename);

                        // Save relative path (optional: you can store just filename)
                        $capstone->attachment = 'capstones/' . $filename;
                        $capstone->save();
                    }

                    $capstoneIds[] = $capstone->id;
                }
            }

            $reservation = Reservation::create([
                'group_id' => $request->group_id,
                'capstone_title_id' => json_encode($capstoneIds),
                'reserve_by' => Auth::user()->id,
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
                'reservation_id' => $reservation->id,
                'group_name' => $user->username,
                'members' => $user->members,
                'program' => $user->program,
                'type_of_defense' => $type_of_defense,
                'transaction_code' => $transactionCode
            ]);

            Notification::create([
                'user_id' => $request->group_id,
                '_link_id' => $reservation->id,
                'notification_type' => 'system_alert',
                'notification_title' => 'Reservation Created',
                'notification_message' => ucwords($reservation->user->username) . ' has reserved a ' . ucwords(str_replace('_', ' ', $type_of_defense)) . '.',
            ]);
        }

        session()->forget('selected_group');
        if (auth()->user()->user_type === 'admin') {
            return redirect()->back()->with('success', 'Reserved successfully.');
        } else {
            return redirect('/transactions')->with('success', 'Reserved successfully.');
        }
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
    public function show($id, $action = null, $notif_id = null)
    {
        $reservation = Reservation::with(['user', 'reserveBy', 'latestSchedule'])->find($id);
        if ($notif_id) {
            $notification = Notification::where('_link_id', $id)->first();
            if ($notification && $action == 'read') {
                $notification_user = NotificationUser::updateOrCreate([
                    'notification_id' => $notif_id,
                    'user_id' => auth()->user()->id,
                    'status' => 'read',
                ]);
            }
        }

        if (!$reservation) {
            return redirect()->route('reservations.index')->with('error', 'Reservation not found.');
        }

        $titles = $this->getTitlesForReservation($reservation);
        $reservations = [
            [
                'id' => $reservation->id,
                'group_id' => $reservation->group_id,
                'user' => $reservation->user,
                'reserveBy' => $reservation->reserveBy,
                'titles' => $titles,
                'status' => $reservation->status,
                'created_at' => $reservation->created_at,
                'schedule_date' => $reservation->schedule ? $reservation->latestSchedule->schedule_date : 'No date available',
                'schedule_time' => $reservation->schedule && $reservation->latestSchedule->schedule_time
                    ? \Carbon\Carbon::parse($reservation->latestSchedule->schedule_time)->format('h:i A')
                    : 'No time available',
            ]
        ];

        return view('reservation', compact('reservations'));
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
