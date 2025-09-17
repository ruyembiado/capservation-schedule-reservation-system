<?php

namespace App\Http\Controllers;

use App\Models\Panelist;
use DateTime;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function SmartScheduler()
    {
        return view('smartscheduler', ['formatted' => []]);
    }

    public function runSmartScheduler(Request $request)
    {
        $offsetOptionInput = $request->input('offsetOption', 'weeks:1');
        [$unit, $value] = explode(':', $offsetOptionInput);
        $offsetOption = [$unit => (int)$value];

        // --- Get Instructors from Users ---
        $instructorRecords = User::where('user_type', 'instructor')->get();

        // --- Get Panelists ---
        $panelistRecords = Panelist::all();

        $instructors = [];

        // Process User instructors
        foreach ($instructorRecords as $inst) {
            $expertise = json_decode($inst->credentials, true) ?? [];
            $availabilityRaw = json_decode($inst->vacant_time, true) ?? [];

            $availability = [];
            foreach ($availabilityRaw as $slot) {
                $availability[] = $slot['day'] . ' ' . $slot['start_time'];
            }

            $instructors['i_' . $inst->id] = [
                'id' => $inst->id,
                'type' => 'instructor',
                'name' => $inst->name,
                'capacity' => $inst->capacity,
                'expertise' => $expertise,
                'availability' => $availability,
            ];
        }

        // Process Panelists
        foreach ($panelistRecords as $panel) {
            $expertise = json_decode($panel->credentials, true) ?? [];
            $availabilityRaw = json_decode($panel->vacant_time, true) ?? [];

            $availability = [];
            foreach ($availabilityRaw as $slot) {
                $availability[] = $slot['day'] . ' ' . $slot['start_time'];
            }

            $instructors['p_' . $panel->id] = [
                'id' => $panel->id,
                'type' => 'panelist',
                'name' => $panel->name,
                'capacity' => $panel->capacity,
                'expertise' => $expertise,
                'availability' => $availability,
            ];
        }

        // --- Get Students / Groups ---
        $studentRecords = User::where('user_type', 'student')
            ->whereHas('reservations', function ($query) {
                $query->where('status', 'reserved');
            })
            ->with('instructor')
            ->orderBy('created_at', 'desc')
            ->get();

        $groups = [];
        foreach ($studentRecords as $student) {
            $instructor = $student->instructor;
            if (!$instructor) continue;

            // Convert instructor's vacant_time to first available time only
            $vacantTime = json_decode($instructor->vacant_time, true) ?? [];
            $timeSlot = count($vacantTime) ? $vacantTime[0]['start_time'] : '09:00';

            // Get prefixed instructor ID for conflict
            $conflictId = null;
            foreach ($instructors as $instId => $inst) {
                if ($inst['type'] === 'instructor' && $inst['id'] === $instructor->id) {
                    $conflictId = $instId;
                    break;
                }
            }

            $groups[$student->id] = [
                'name' => $student->username,
                'required_panelists' => $student->capacity,
                'topic_tags' => json_decode($student->credentials, true) ?? [],
                'time_slot' => $timeSlot, // Only time
                'conflicts' => $conflictId !== null ? [$conflictId] : [],
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
                    'time_slot' => $group['time_slot'], // Only time
                    'conflicts' => $conflictNames,
                ],
                'panelists' => $assigned,
                'conflict_note' => count($assigned) < $group['required_panelists']
                    ? "Conflict: Needed {$group['required_panelists']}, but only " . count($assigned) . " assigned"
                    : null,
            ];
        }

        return view('smartscheduler', compact('formatted'));
    }

    private function balancedExpertMatch($instructors, $groups, $offsetOption)
    {
        $daySchedule = [];
        $assignments = [];

        foreach ($groups as $groupId => $group) {
            $required = $group['required_panelists'];
            $bestSlot = null;
            $bestCandidates = [];

            // Collect possible slots (match only time)
            $possibleSlots = [];
            foreach ($instructors as $instId => $inst) {
                foreach ($inst['availability'] as $slot) {
                    $parts = explode(' ', $slot);
                    $slotDay = $parts[0];
                    $slotTime = $parts[1] ?? $parts[0]; // fallback

                    $possibleSlots[$slotTime][] = [
                        'instId' => $instId,
                        'day' => $slotDay,
                    ];
                }
            }

            foreach ($possibleSlots as $slotTime => $instList) {
                $candidates = [];
                foreach ($instList as $info) {
                    $instId = $info['instId'];
                    $instDay = $info['day'];
                    $inst = $instructors[$instId];

                    if ($inst['capacity'] <= 0) continue;
                    if (in_array($instId, $group['conflicts'])) continue;
                    if ($slotTime !== $group['time_slot']) continue;

                    $score = count(array_intersect($group['topic_tags'], $inst['expertise']));
                    $candidates[] = [
                        'instructor_id' => $instId,
                        'instructor' => $inst['name'],
                        'score' => $score,
                        'day' => $instDay,
                        'time' => $slotTime,
                        'expertise' => $inst['expertise'],
                        'availability' => $inst['availability'],
                    ];
                }

                if (count($candidates) >= $required) {
                    $totalScore = array_sum(array_column($candidates, 'score'));
                    if ($bestSlot === null || $totalScore > array_sum(array_column($bestCandidates, 'score'))) {
                        $bestSlot = $slotTime;
                        $bestCandidates = $candidates;
                    }
                }
            }

            $assignments[$groupId] = [];
            if ($bestSlot) {
                $scheduleDate = $this->getScheduleDate($bestCandidates[0]['day'], null, $offsetOption);
                usort($bestCandidates, fn($a, $b) => $b['score'] <=> $a['score']);
                $assigned = array_slice($bestCandidates, 0, $required);

                foreach ($assigned as $cand) {
                    $cand['schedule_date'] = $scheduleDate;
                    $assignments[$groupId][] = $cand;
                    $instructors[$cand['instructor_id']]['capacity']--;
                    $key = $cand['day'] . ' ' . $cand['time'];
                    $daySchedule[$key] = ($daySchedule[$key] ?? 0) + 1;
                }
            }
        }

        return $assignments;
    }

    // Helper: generate actual date for next week's schedule
    private function getScheduleDate($dayName, $baseDate = null, $offsetOption = [])
    {
        $base = $baseDate ? new DateTime($baseDate) : new DateTime();

        // Apply offset first
        if (isset($offsetOption['weeks'])) {
            $base->modify("+{$offsetOption['weeks']} week");
        } elseif (isset($offsetOption['days'])) {
            $base->modify("+{$offsetOption['days']} day");
        } elseif (isset($offsetOption['months'])) {
            $base->modify("+{$offsetOption['months']} month");
        }

        // Jump to the requested day in that week
        $date = clone $base;
        $date->modify("next $dayName");

        return $date->format('Y-m-d');
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
