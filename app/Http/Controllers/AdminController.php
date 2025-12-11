<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\User;
use App\Models\Setting;
use App\Models\Panelist;
use App\Models\Schedule;
use App\Models\Reservation;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
	public function SmartScheduler($group_id) {
		$group_id = $group_id;
		return view('smartscheduler', compact('group_id') ,['formatted' => []]);
	}
	
	public function runSmartScheduler(Request $request, $group_id)
	{
	    // --- Get Instructors ---
	    $instructorRecords = User::where('user_type', 'instructor')->get();
	    $instructors = [];
	
	    foreach ($instructorRecords as $inst) {
	        $expertise = json_decode($inst->credentials, true) ?? [];
	
	        $instructors[$inst->id] = [
	            'id' => $inst->id,
	            'name' => $inst->name,
	            'expertise' => $expertise, // tags
	        ];
	    }
	
	    // --- Get Students / Groups (Pending Only) ---
	    $studentRecords = User::where('user_type', 'student')
	    	->where('id', $group_id)
	        ->whereHas('reservations', fn($q) => $q->where('status', 'pending'))
	        ->with('instructor', 'reservations')
	        ->orderBy('created_at', 'desc')
	        ->get();
	        
	    $groups = [];
	    foreach ($studentRecords as $student) {
	        $conflictId = $student->instructor->id ?? null;
	        $groups[$student->id] = [
	            'name' => $student->username,
	            'required_panelists' => 3,
	            'topic_tags' => json_decode($student->credentials, true) ?? [],
	            'conflicts' => $conflictId ? [$conflictId] : [],
	            'reservation_id' => $student->reservations->first()->id ?? null,
	        ];
	    }
	
	    // --- Match by Expertise Only ---
	    $result = $this->balancedExpertMatch($instructors, $groups);
	
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
	                'required_panelists' => 3,
	                'topic_tags' => $group['topic_tags'],
	                'conflicts' => $conflictNames,
	                'reservation_id' => $group['reservation_id'],
	            ],
	            'panelists' => $assigned,
	            'conflict_note' =>
	                count($assigned) < $group['required_panelists']
	                    ? "The group requires at least {$group['required_panelists']} panelists, but only " .
	                      count($assigned) .
	                      " were suggested."
	                    : null,
	        ];
	    }
	    
	    $group = User::where('id', $group_id)->first();
        $tags = json_decode($group->credentials, true);
	    
	    $panelists = User::whereNot('id', $group_id )->where('user_type', 'instructor')->where(function ($query) use ($tags) {
            foreach ($tags as $tag) {
                $query->orWhereJsonContains('credentials', $tag);
            }
        })->get();
        
	    $settings = Setting::first();
	    $dean_name = $settings->dean_name ?? "Dean";
	    $program_head_name = null;
	    $program = strtoupper($group->program ?? '');
	
	    switch ($program) {
	        case 'BSIT':
	            $program_head_name = $settings->it_head_name ?? "IT Head";
	            break;
	        case 'BSCS':
	            $program_head_name = $settings->cs_head_name ?? "CS Head";
	            break;
	        case 'BSIS':
	            $program_head_name = $settings->is_head_name ?? "IS Head";
	            break;
	        default:
	            $program_head_name = "Program Head";
	    }
	    
	    return view('smartscheduler', compact('formatted', 'group_id', 'panelists',
	    'dean_name', 'program_head_name'));
	}
	
	private function balancedExpertMatch($instructors, $groups)
	{
	    $assignments = [];
	
	    foreach ($groups as $groupId => $group) {
	        $required = max(1, 3);
	        $topicTags = $group['topic_tags'];
	
	        $candidates = [];
	
	        foreach ($instructors as $instId => $inst) {
	            // Skip conflict instructors
	            if (in_array($instId, $group['conflicts'])) continue;
	
	            $score = count(array_intersect($topicTags, $inst['expertise']));
	
	            $candidates[] = [
	                'instructor_id' => $instId,
	                'name' => $inst['name'],
	                'expertise' => $inst['expertise'],
	                'score' => $score,
	            ];
	        }
	
	        // Sort best match first
	        usort($candidates, fn($a, $b) => $b['score'] <=> $a['score']);
	
	        // Select top N
	        $assignments[$groupId] = array_slice($candidates, 0, $required);
	    }
	    
	    return $assignments;
	}

	public function runSmartSchedulerTest(Request $request, $group_id)
	{
	    $offsetOptionInput = $request->input('offsetOption', 'weeks:1');
	    [$unit, $value] = explode(':', $offsetOptionInput);
	    $offsetOption = [$unit => (int)$value];
	
	    // --- Get Instructors ---
	    $instructorRecords = User::where('user_type', 'instructor')->get();
	    $instructors = [];
	
	    foreach ($instructorRecords as $inst) {
	        $expertise = json_decode($inst->credentials, true) ?? [];
	        $availabilityRaw = json_decode($inst->vacant_time, true) ?? [];
	        $availability = [];
	        $availabilityDisplay = [];
	
	        foreach ($availabilityRaw as $slot) {
	            // expect: { "day": "Monday", "start_time": "09:00", "end_time": "10:00" }
	            $day = $slot['day'] ?? '';
	            $start = $slot['start_time'] ?? '';
	            $end = $slot['end_time'] ?? '';
	
	            if (!$day || !$start || !$end) continue;
	
	            // For logic matching (keep 24h strings)
	            $availability[] = [
	                'day' => $day,
	                'start_time' => $start,
	                'end_time' => $end,
	            ];
	
	            // For frontend display only
	            $availabilityDisplay[] = sprintf(
	                "%s %s - %s",
	                $day,
	                date('g:i A', strtotime($start)),
	                date('g:i A', strtotime($end))
	            );
	        }
	
	        $instructors[$inst->id] = [
	            'id' => $inst->id,
	            'type' => 'instructor',
	            'name' => $inst->name,
	            'capacity' => (int) $inst->capacity,
	            'expertise' => $expertise,
	            'availability' => $availability,
	            'availability_display' => $availabilityDisplay,
	        ];
	    }
	
	    // --- Get Students / Groups ---
	    $studentRecords = User::where('user_type', 'student')
	        ->whereHas('reservations', function ($query) {
	            $query->where('status', 'pending');
	        })
	        ->with('instructor', 'reservations')
	        ->orderBy('created_at', 'desc')
	        ->get();
	
	    $groups = [];
	    foreach ($studentRecords as $student) {
	        // student->vacant_time is a single time string e.g. "13:00"
	        $defenseTime = $student->vacant_time ?? null;
	        $defenseDay = $student->defense_day ?? null; // optional; may be blank
	
	        $conflictId = $student->instructor->id ?? null;
	
	        $groups[$student->id] = [
	            'name' => $student->username,
	            'required_panelists' => (int) $student->capacity,
	            'topic_tags' => json_decode($student->credentials, true) ?? [],
	            'defense_day' => $defenseDay,      // may be null/empty
	            'defense_time' => $defenseTime,    // single time string e.g. "13:00"
	            'conflicts' => $conflictId ? [$conflictId] : [],
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
	                'time_slot' => $group['defense_time'] ? date('g:i A', strtotime($group['defense_time'])) : null,
	                'conflicts' => $conflictNames,
	                'reservation_id' => $group['reservation_id'],
	            ],
	            'panelists' => $assigned,
	            'conflict_note' =>
	                count($assigned) < $group['required_panelists']
	                    ? "The group requires at least {$group['required_panelists']} panelists, but only " .
	                      count($assigned) .
	                      " were suggested."
	                    : null,
	        ];
	    }
	
	    return view('smartscheduler', compact('formatted', 'group_id'));
	}
	
	private function balancedExpertMatchTest($instructors, $groups, $offsetOption)
	{
	    ini_set('max_execution_time', 300);
	    set_time_limit(300);
	
	    $assignments = [];
	    $daySchedule = [];
	
	    // Load all existing booked schedules as "YYYY-MM-DD H:i" or "YYYY-MM-DD 13:00" depending on your schedule_time format.
	    // Adjust the concatenation if your schedule_time stores ranges (e.g., "13:00 - 14:00")
	    $bookedSchedules = DB::table('schedules')
	        ->select('schedule_date', 'schedule_time')
	        ->get()
	        ->map(fn($s) => trim($s->schedule_date . ' ' . $s->schedule_time))
	        ->toArray();
	
	    foreach ($groups as $groupId => $group) {
	        $required = max(1, (int) $group['required_panelists']);
	        $bestCandidates = [];
	
	        $defenseTime = $group['defense_time'] ?? null; // e.g. "13:00"
	        $defenseDay = $group['defense_day'] ?? null;   // optional e.g. "Monday"
	        // normalize
	        if ($defenseDay) $defenseDay = strtolower($defenseDay);
	
	        // Build candidate list: instructors who have capacity, not in conflicts, and whose availability covers the defense_time.
	        foreach ($instructors as $instId => $inst) {
	            if ($inst['capacity'] <= 0) continue;
	            if (in_array($instId, $group['conflicts'])) continue;
	
	            foreach ($inst['availability'] as $avail) {
	                $availDay = strtolower($avail['day'] ?? '');
	                $availStart = $avail['start_time'] ?? null;
	                $availEnd = $avail['end_time'] ?? null;
	
	                if (!$availDay || !$availStart || !$availEnd) continue;
	
	                // If group provided a defense_day, require it to match. Otherwise accept any day that contains defense_time.
	                if ($defenseTime) {
	                    if ($defenseDay && $availDay !== $defenseDay) {
	                        continue;
	                    }
	
	                    // If defenseDay not provided, allow this avail day (we'll schedule on the instructor day)
	                    // Check if defense_time falls inside instructor window.
	                    if ($defenseTime >= $availStart && $defenseTime < $availEnd) {
	                        $score = count(array_intersect($group['topic_tags'], $inst['expertise']));
	                        $bestCandidates[] = [
	                            'instructor_id' => $instId,
	                            'instructor' => $inst['name'],
	                            'score' => $score,
	                            'day' => $avail['day'],
	                            'avail_start' => $availStart,
	                            'avail_end' => $availEnd,
	                            'expertise' => $inst['expertise'],
	                        ];
	
	                        // no need to check other avail slots for same instructor if they match this defense time
	                        break;
	                    }
	                } else {
	                    // if no defense time provided, skip (we require defense_time to schedule)
	                    continue;
	                }
	            }
	        }
	
	        $assignments[$groupId] = [];
	
	        if (!empty($bestCandidates)) {
	            // Sort by best expertise score (descending)
	            usort($bestCandidates, fn($a, $b) => $b['score'] <=> $a['score']);
	
	            // pick top N candidates
	            $assignedCandidates = array_slice($bestCandidates, 0, $required);
	
	            // Determine which day to use for scheduling:
	            // - If group provided defense_day, use that
	            // - Otherwise use the day of the first assigned candidate (their availability day)
	            $scheduleDayName = $group['defense_day'] ?? $assignedCandidates[0]['day']; // e.g., "Monday"
	            $baseDate = $this->getScheduleDate($scheduleDayName, null, $offsetOption); // must return Y-m-d
	
	            $slotAssigned = false;
	            $attempts = 0;
	
	            // format schedule_time key that we will check against bookedSchedules.
	            // Here we assume schedule_time is stored as "13:00" (single start time). If your DB stores ranges, adjust accordingly.
	            $scheduleTime = $defenseTime;
	
	            while (!$slotAssigned && $attempts < 10) {
	                $attempts++;
	                $slotKey = $baseDate . ' ' . $scheduleTime;
	
	                // Skip if already booked (DB or memory)
	                if (in_array($slotKey, $bookedSchedules) || (isset($daySchedule[$baseDate]) && in_array($scheduleTime, $daySchedule[$baseDate]))) {
	                    // try next week
	                    $baseDate = date('Y-m-d', strtotime($baseDate . ' +7 days'));
	                    continue;
	                }
	
	                // Check all assigned instructors are actually available on this baseDate (same weekday) and at the time
	                $allAvailable = true;
	                foreach ($assignedCandidates as $cand) {
	                    $found = false;
	                    $candInstId = $cand['instructor_id'];
	
	                    foreach ($instructors[$candInstId]['availability'] as $avail) {
	                        $availDay = strtolower($avail['day']);
	                        // ensure weekday of baseDate matches avail day
	                        if ($availDay !== strtolower(date('l', strtotime($baseDate)))) {
	                            continue;
	                        }
	
	                        if ($scheduleTime >= $avail['start_time'] && $scheduleTime < $avail['end_time']) {
	                            $found = true;
	                            break;
	                        }
	                    }
	
	                    if (!$found) {
	                        $allAvailable = false;
	                        break;
	                    }
	                }
	
	                if ($allAvailable) {
	                    // Book it (memory)
	                    $daySchedule[$baseDate][] = $scheduleTime;
	                    $slotAssigned = true;
	
	                    foreach ($assignedCandidates as $cand) {
	                        $assignments[$groupId][] = [
													    'instructor_id' => $cand['instructor_id'],
													    'name' => $cand['instructor'],
													    'day' => $cand['day'],
													    'schedule_date' => $baseDate,
													    'time' => date('g:i A', strtotime($scheduleTime)),
													    'expertise_score' => $cand['score'],
													    'expertise' => $cand['expertise'] ?? [],
													    'availability' => $instructors[$cand['instructor_id']]['availability_display'] ?? [],
													];
	
	                        // decrement capacity for fairness
	                        if (isset($instructors[$cand['instructor_id']])) {
	                            $instructors[$cand['instructor_id']]['capacity']--;
	                        }
	                    }
	
	                    // done for this group
	                    break;
	                }
	
	                // try next week
	                $baseDate = date('Y-m-d', strtotime($baseDate . ' +7 days'));
	            } // end while
	        } // end if bestCandidates
	    } // end foreach groups
	
	    return $assignments;
	}

	private function getScheduleDate($dayName, $baseDate = null, $offsetOption = []) {
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

	public function CreatePanelistSchedule(Request $request) {
	    // --- Step 0: Decode JSON inputs if needed ---
	    $group_ids = is_array($request->group_id) ? $request->group_id : json_decode($request->group_id, true);
	    $reservation_ids = is_array($request->reservation_id) ? $request->reservation_id : json_decode($request->reservation_id, true);
	    $panelist_ids_nested = is_array($request->panelist_id) ? $request->panelist_id : json_decode($request->panelist_id, true);
	
	    // Flatten panelists: convert nested arrays to a single flat array
	    $panelist_ids = [];
	    foreach ($panelist_ids_nested as $arr) {
	        $panelist_ids = array_merge($panelist_ids, $arr);
	    }
	    $panelist_ids = array_map('intval', $panelist_ids); // make sure all are integers
	
	    // Loop through each reservation
	    foreach ($reservation_ids as $i => $reservationId) {
	        $reservation = Reservation::findOrFail($reservationId);
	
	        $groupId = $group_ids[$i] ?? $reservation->group_id;
	        $defenseDate = $request->schedule_date;
	        $defenseTime = $request->schedule_time;
	
	        $formattedDateTime = Carbon::parse("{$defenseDate} {$defenseTime}")
	            ->format('l, F j, Y \\a\\t h:i A');
	
	        // --- Step 1: Assign panelists ---
	        $reservation->update([
	            'panelist_id' => json_encode($panelist_ids),
	            'status' => 'approved',
	        ]);
	
	        Notification::create([
	            'user_id' => $reservation->group_id,
	            '_link_id' => $reservation->id,
	            'notification_type' => 'system_alert',
	            'notification_title' => 'Panelist Assigned',
	            'notification_message' => ucwords($reservation->user->username) .
	                "'s reservation has assigned panelists.",
	        ]);
	
	        // --- Step 2: Create defense schedule ---
	        $schedule = Schedule::create([
	            'group_id' => $reservation->group_id,
	            'reservation_id' => $reservation->id,
	            'schedule_date' => $defenseDate,
	            'schedule_time' => $defenseTime,
	            'schedule_category' => '',
	            'schedule_remarks' => '',
	        ]);
	
	        // --- Step 3: Update reservation status if schedule was created ---
	        if ($schedule) {
	            $reservation->update(['status' => 'reserved']);
	
	            Notification::create([
	                'user_id' => $reservation->group_id,
	                '_link_id' => $reservation->id,
	                'notification_type' => 'system_alert',
	                'notification_title' => 'Schedule Created',
	                'notification_message' => ucfirst($reservation->user->username) .
	                    "'s reservation has been scheduled for defense on {$formattedDateTime}.",
	            ]);
	
	            // Send emails to panelists
	            $scheduleController = new ScheduleController;
	            $scheduleController->sendPanelistEmail($reservation, $panelist_ids);
	            $scheduleController->sendDeanHeadEmail($reservation);
	        }
	    }
	
	    return redirect()
	        ->route('awaiting_reservations.index')
	        ->with('success', 'Panelists assigned and defense schedule created successfully.');
	}
	
	public function assignedPanelistsScheduler(Request $request) {
	
	    return view('schedule_calendar', [
	        'group_id' => json_encode($request->group_id),
	        'reservation_id' => json_encode($request->reservation_id),
	        'panelist_id' => json_encode($request->panelist_id)
	    ]);
	}

	public function groups() {
		if (Auth::user()->user_type === 'instructor') {
			$groups = User::where('user_type', 'student')->where('instructor_id', Auth::user()->id)->with('instructor')->orderBy('created_at', 'desc')->get();
		} else {
			$groups = User::where('user_type', 'student')->with('instructor')->orderBy('created_at', 'desc')->get();
		}
		return view('group', compact('groups'));
	}

	public function instructors() {
		$instructors = User::where('user_type', 'instructor')->orderBy('created_at', 'desc')->get();
		return view('instructor', compact('instructors'));
	}

	public function transactions() {
		return view('transaction');
	}
	
	public function settings() {
		$settings = Setting::first();
		return view('settings', compact('settings'));
	}
	
	public function updateSettings(Request $request) {
	    $request->validate([
	        'dean_name' => 'nullable|string|max:255',
	        'dean_email' => 'nullable|email|max:255',
	        'program_head.it.name' => 'nullable|string|max:255',
	        'program_head.it.email' => 'nullable|email|max:255',
	        'program_head.cs.name' => 'nullable|string|max:255',
	        'program_head.cs.email' => 'nullable|email|max:255',
	        'program_head.is.name' => 'nullable|string|max:255',
	        'program_head.is.email' => 'nullable|email|max:255',
	    ]);
	
	    $settings = Setting::first();
	    if (!$settings) {
	        $settings = new Setting();
	    }
	
	    $settings->dean_name = $request->dean_name;
	    $settings->dean_email = $request->dean_email;
	
	    $settings->it_head_name = $request->program_head['it']['name'] ?? '';
	    $settings->it_head_email = $request->program_head['it']['email'] ?? '';
	
	    $settings->cs_head_name = $request->program_head['cs']['name'] ?? '';
	    $settings->cs_head_email = $request->program_head['cs']['email'] ?? '';
	
	    $settings->is_head_name = $request->program_head['is']['name'] ?? '';
	    $settings->is_head_email = $request->program_head['is']['email'] ?? '';
	
	    $settings->save();
	
	    return redirect()->back()->with('success', 'Settings updated successfully!');
	}									

}