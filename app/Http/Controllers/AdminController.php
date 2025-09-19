<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\User;
use App\Models\Panelist;
use App\Models\Schedule;
use App\Models\Reservation;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function SmartScheduler()
    {
        return view('smartscheduler', ['formatted' => []]);
    }

    // public function runSmartScheduler(Request $request)
    // {
    //     $offsetOptionInput = $request->input('offsetOption', 'weeks:1');
    //     [$unit, $value] = explode(':', $offsetOptionInput);
    //     $offsetOption = [$unit => (int)$value];

    //     // --- Get Instructors from Users ---
    //     $instructorRecords = User::where('user_type', 'instructor')->get();

    //     $instructors = [];

    //     foreach ($instructorRecords as $inst) {
    //         $expertise = json_decode($inst->credentials, true) ?? [];
    //         $availabilityRaw = json_decode($inst->vacant_time, true) ?? [];

    //         $availability = [];
    //         foreach ($availabilityRaw as $slot) {
    //             $availability[] = $slot['day'] . ' ' . $slot['start_time'];
    //         }

    //         $instructors[$inst->id] = [
    //             'id' => $inst->id,
    //             'type' => 'instructor',
    //             'name' => $inst->name,
    //             'capacity' => $inst->capacity,
    //             'expertise' => $expertise,
    //             'availability' => $availability,
    //         ];
    //     }

    //     // --- Get Students / Groups ---
    //     $studentRecords = User::where('user_type', 'student')
    //         ->whereHas('reservations', function ($query) {
    //             $query->where('status', 'pending');
    //         })
    //         ->with('instructor')
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     $groups = [];
    //     foreach ($studentRecords as $student) {
    //         $instructor = $student->instructor;
    //         if (!$instructor) continue;

    //         $vacantTime = json_decode($instructor->vacant_time, true) ?? [];
    //         $timeSlot = count($vacantTime) ? $vacantTime[0]['start_time'] : '09:00';

    //         $conflictId = null;
    //         foreach ($instructors as $instId => $inst) {
    //             if ($inst['id'] === $instructor->id) {
    //                 $conflictId = $instId;
    //                 break;
    //             }
    //         }

    //         $groups[$student->id] = [
    //             'name' => $student->username,
    //             'required_panelists' => $student->capacity, // maybe rename later
    //             'topic_tags' => json_decode($student->credentials, true) ?? [],
    //             'time_slot' => $timeSlot,
    //             'conflicts' => $conflictId !== null ? [$conflictId] : [],
    //             'reservation_id' => $student->reservations->first()->id ?? null,
    //         ];
    //     }

    //     // --- Run Scheduler ---
    //     $result = $this->balancedExpertMatch($instructors, $groups, $offsetOption);

    //     // --- Format Output ---
    //     $formatted = [];
    //     foreach ($groups as $groupId => $group) {
    //         $assigned = $result[$groupId] ?? [];

    //         $conflictNames = [];
    //         foreach ($group['conflicts'] as $conflictInstructorId) {
    //             if (isset($instructors[$conflictInstructorId])) {
    //                 $conflictNames[] = $instructors[$conflictInstructorId]['name'];
    //             }
    //         }

    //         $formatted[] = [
    //             'groupId' => $groupId,
    //             'group' => [
    //                 'name' => $group['name'],
    //                 'required_panelists' => $group['required_panelists'],
    //                 'topic_tags' => $group['topic_tags'],
    //                 'time_slot' => $group['time_slot'],
    //                 'conflicts' => $conflictNames,
    //                 'reservation_id' => $group['reservation_id'],
    //             ],
    //             'panelists' => $assigned, // still named panelists for compatibility
    //             'conflict_note' => count($assigned) < $group['required_panelists']
    //                 ? "Conflict: Needed {$group['required_panelists']}, but only " . count($assigned) . " assigned"
    //                 : null,
    //         ];
    //     }

    //     return view('smartscheduler', compact('formatted'));
    // }

    public function runSmartScheduler(Request $request)
    {
        $offsetOptionInput = $request->input('offsetOption', 'weeks:1');
        [$unit, $value] = explode(':', $offsetOptionInput);
        $offsetOption = [$unit => (int)$value];

        // --- Get Instructors from Users ---
        $instructorRecords = User::where('user_type', 'instructor')->get();

        $instructors = [];

        foreach ($instructorRecords as $inst) {
            $expertise = json_decode($inst->credentials, true) ?? [];
            $availabilityRaw = json_decode($inst->vacant_time, true) ?? [];

            $availability = [];
            foreach ($availabilityRaw as $slot) {
                $availability[] = $slot['day'] . ' ' . $slot['start_time'];
            }

            $instructors[$inst->id] = [
                'id' => $inst->id,
                'type' => 'instructor',
                'name' => $inst->name,
                'capacity' => $inst->capacity,
                'expertise' => $expertise,
                'availability' => $availability,
            ];
        }

        // --- Get Students / Groups ---
        $studentRecords = User::where('user_type', 'student')
            ->whereHas('reservations', function ($query) {
                $query->where('status', 'pending');
            })
            ->with('instructor')
            ->orderBy('created_at', 'desc')
            ->get();

        $groups = [];
        foreach ($studentRecords as $student) {
            $instructor = $student->instructor;
            if (!$instructor) continue;

            $vacantTime = json_decode($instructor->vacant_time, true) ?? [];
            $timeSlot = count($vacantTime) ? $vacantTime[0]['start_time'] : '09:00';

            $conflictId = null;
            foreach ($instructors as $instId => $inst) {
                if ($inst['id'] === $instructor->id) {
                    $conflictId = $instId;
                    break;
                }
            }

            $groups[$student->id] = [
                'name' => $student->username,
                'required_panelists' => $student->capacity, // maybe rename later
                'topic_tags' => json_decode($student->credentials, true) ?? [],
                'time_slot' => $timeSlot,
                'conflicts' => $conflictId !== null ? [$conflictId] : [],
                'reservation_id' => $student->reservations->first()->id ?? null,
            ];
        }

        // --- Run Scheduler ---
        $result = $this->balancedExpertMatch($instructors, $groups, $offsetOption);

        // --- Format Output ---
        $formatted = [];
        foreach ($groups as $groupId => $group) {
            $assigned = $result[$groupId] ?? [];

            $conflictNames = [];
            foreach ($group['conflicts'] as $conflictInstructorId) {
                if (isset($instructors[$conflictInstructorId])) {
                    $conflictNames[] = $instructors[$conflictInstructorId]['name'];
                }
            }

            $formatted[] = [
                'groupId' => $groupId,
                'group' => [
                    'name' => $group['name'],
                    'required_panelists' => $group['required_panelists'],
                    'topic_tags' => $group['topic_tags'],
                    'time_slot' => $group['time_slot'],
                    'conflicts' => $conflictNames,
                    'reservation_id' => $group['reservation_id'],
                ],
                'panelists' => $assigned, // still named panelists for compatibility
                'conflict_note' => count($assigned) < $group['required_panelists']
                    ? "The group requires at least {$group['required_panelists']} panelists, but only " . count($assigned) . " were suggested."
                    : null,

            ];
        }

        return view('smartscheduler', compact('formatted'));
    }

    private function balancedExpertMatch($instructors, $groups, $offsetOption)
    {
        $daySchedule = []; // track slots per date
        $assignments = [];

        // Define fixed defense slots per day
        $defenseSlots = [
            '08:00',
            '09:00',
            '13:00',
            '14:00',
        ];

        foreach ($groups as $groupId => $group) {
            $required = $group['required_panelists'];
            $bestCandidates = [];

            // Find candidate instructors for this group
            foreach ($instructors as $instId => $inst) {
                foreach ($inst['availability'] as $slot) {
                    [$slotDay, $slotTime] = explode(' ', $slot);

                    if ($inst['capacity'] <= 0) continue;
                    if (in_array($instId, $group['conflicts'])) continue;

                    // Only consider valid defense slots
                    if (!in_array($slotTime, $defenseSlots)) continue;

                    $score = count(array_intersect($group['topic_tags'], $inst['expertise']));
                    $bestCandidates[] = [
                        'instructor_id' => $instId,
                        'instructor' => $inst['name'],
                        'score' => $score,
                        'day' => $slotDay,
                        'expertise' => $inst['expertise'],
                        'availability' => $inst['availability'],
                    ];
                }
            }

            $assignments[$groupId] = [];
            if (!empty($bestCandidates)) {
                // Sort candidates by score
                usort($bestCandidates, fn($a, $b) => $b['score'] <=> $a['score']);
                $assigned = array_slice($bestCandidates, 0, $required);

                // Group’s defense time (from vacant_time)
                $preferredTime = date('H:i', strtotime($group['time_slot']));
                if (!in_array($preferredTime, $defenseSlots)) {
                    // fallback to first valid slot
                    $preferredTime = $defenseSlots[0];
                }

                // Start at base date for this group
                $scheduleDate = $this->getScheduleDate($assigned[0]['day'], null, $offsetOption);

                $slotAssigned = null;
                while (!$slotAssigned) {
                    // Try all defense slots for this date (start from preferred)
                    $slotsToCheck = array_merge(
                        [$preferredTime],
                        array_diff($defenseSlots, [$preferredTime])
                    );

                    foreach ($slotsToCheck as $slotTime) {
                        // Skip if already booked in memory
                        if (isset($daySchedule[$scheduleDate][$slotTime])) {
                            continue;
                        }

                        // Skip if already booked in DB
                        $existsInDb = DB::table('schedules')
                            ->whereDate('schedule_date', $scheduleDate)
                            ->whereTime('schedule_time', $slotTime)
                            ->exists();

                        if ($existsInDb) {
                            continue;
                        }

                        // Validate if all assigned panelists are available at this time
                        $allAvailable = true;
                        foreach ($assigned as $cand) {
                            $available = false;
                            foreach ($cand['availability'] as $avail) {
                                [$availDay, $availTime] = explode(' ', $avail);
                                if (
                                    strtolower($availDay) === strtolower(date('l', strtotime($scheduleDate))) &&
                                    $availTime === $slotTime
                                ) {
                                    $available = true;
                                    break;
                                }
                            }
                            if (!$available) {
                                $allAvailable = false;
                                break;
                            }
                        }

                        if ($allAvailable) {
                            $slotAssigned = $slotTime;
                            $daySchedule[$scheduleDate][$slotTime] = true;
                            break 2; // break out of foreach + while
                        }
                    }

                    // If no slot free this day, move to next week
                    $scheduleDate = date('Y-m-d', strtotime($scheduleDate . ' +7 days'));
                }

                // Save group assignment
                foreach ($assigned as $cand) {
                    $cand['schedule_date'] = $scheduleDate;
                    $cand['time'] = $slotAssigned;
                    $assignments[$groupId][] = $cand;

                    $instructors[$cand['instructor_id']]['capacity']--;
                }
            }
        }

        return $assignments;
    }

    private function getScheduleDate($dayName, $baseDate = null, $offsetOption = [])
    {
        $base = $baseDate ? new DateTime($baseDate) : new DateTime();

        if (isset($offsetOption['weeks'])) {
            $base->modify("+{$offsetOption['weeks']} week");
        } elseif (isset($offsetOption['days'])) {
            $base->modify("+{$offsetOption['days']} day");
        } elseif (isset($offsetOption['months'])) {
            $base->modify("+{$offsetOption['months']} month");
        }

        $date = clone $base;
        $date->modify("next $dayName");

        return $date->format('Y-m-d');
    }

    public function CreatePanelistSchedule(Request $request)
    {
        foreach ($request->reservation_id as $i => $reservationId) {
            $reservation = Reservation::findOrFail($reservationId);

            $groupId     = $request->group_id[$i] ?? null;
            $defenseDate = $request->defense_date[$i] ?? null;
            $defenseTime = $request->defense_time[$i] ?? null;

            // Get panelists for this group
            $panelists = $request->panelist_id[$groupId] ?? [];
            $panelists = array_map('intval', $panelists);

            $formattedDateTime = Carbon::parse("{$defenseDate} {$defenseTime}")
                ->format('l, F j, Y \\a\\t h:i A');
            // Example: Monday, September 29, 2025 at 01:00 PM

            // --- Step 1: Assign panelists ---
            $reservation->update([
                'panelist_id' => json_encode($panelists),
                'status'      => 'approved',
            ]);

            Notification::create([
                'user_id'              => $reservation->group_id,
                '_link_id'             => $reservation->id,
                'notification_type'    => 'system_alert',
                'notification_title'   => 'Panelist Assigned',
                'notification_message' => ucwords($reservation->user->username) .
                    "'s reservation has assigned panelists.",
            ]);

            // --- Step 2: Create defense schedule ---
            $schedule = Schedule::create([
                'group_id'          => $reservation->group_id,
                'reservation_id'    => $reservation->id,
                'schedule_date'     => $defenseDate,
                'schedule_time'     => $defenseTime,
                'schedule_category' => '',
                'schedule_remarks'  => '',
            ]);

            // --- Step 3: Update reservation status if schedule was created ---
            if ($schedule) {
                $reservation->update([
                    'status' => 'reserved',
                ]);

                Notification::create([
                    'user_id'              => $reservation->group_id,
                    '_link_id'             => $reservation->id,
                    'notification_type'    => 'system_alert',
                    'notification_title'   => 'Schedule Created',
                    'notification_message' => ucfirst($reservation->user->username) .
                        "'s reservation has been scheduled for defense on {$formattedDateTime}.",
                ]);
            }
        }

        return redirect()->back()->with('success', 'Panelists assigned and defense schedules created successfully.');
    }

    public function groups()
    {
        if (Auth::user()->user_type === 'instructor') {
            $groups = User::where('user_type', 'student')->where('instructor_id', Auth::user()->id)->with('instructor')->orderBy('created_at', 'desc')->get();
        } else {
            $groups = User::where('user_type', 'student')->with('instructor')->orderBy('created_at', 'desc')->get();
        }
        return view('group', compact('groups'));
    }

    public function instructors()
    {
        $instructors = User::where('user_type', 'instructor')->orderBy('created_at', 'desc')->get();
        return view('instructor', compact('instructors'));
    }

    public function transactions()
    {
        return view('transaction');
    }
}
