<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function testScheduler()
    {
        // --------------------------
        // Static offset option (choose one)
        // --------------------------
        $offsetOption = ['weeks' => 1];   // schedule 1 week later
        // $offsetOption = ['weeks' => 2];   // schedule 2 weeks later
        // $offsetOption = ['days' => 10];   // schedule 10 days later
        // $offsetOption = ['months' => 1];  // schedule 1 month later

        // --------------------------
        // Static Instructor Data (10 instructors)
        // --------------------------
        $instructors = [
            'Alice' => [
                'capacity' => 3,
                'expertise' => ['AI', 'Networks'],
                'availability' => [
                    'Monday 9:00 AM',
                    'Wednesday 2:00 PM',
                    'Friday 9:00 AM',
                ],
                'conflicts' => []
            ],
            'Bob' => [
                'capacity' => 3,
                'expertise' => ['Security', 'Networks'],
                'availability' => [
                    'Monday 9:00 AM',
                    'Tuesday 2:00 PM',
                    'Thursday 9:00 AM',
                ],
                'conflicts' => []
            ],
            'Charlie' => [
                'capacity' => 3,
                'expertise' => ['AI', 'Data Science'],
                'availability' => [
                    'Monday 9:00 AM',
                    'Tuesday 2:00 PM',
                    'Friday 9:00 AM',
                ],
                'conflicts' => []
            ],
            'Diana' => [
                'capacity' => 3,
                'expertise' => ['AI', 'Security'],
                'availability' => [
                    'Wednesday 9:00 AM',
                    'Monday 9:00 AM',
                    'Friday 2:00 PM',
                ],
                'conflicts' => []
            ],
            'Ethan' => [
                'capacity' => 3,
                'expertise' => ['Networks', 'Data Science'],
                'availability' => [
                    'Monday 9:00 AM',
                    'Wednesday 2:00 PM',
                    'Thursday 9:00 AM',
                ],
                'conflicts' => []
            ],
            'Faye' => [
                'capacity' => 3,
                'expertise' => ['AI', 'Security'],
                'availability' => [
                    'Monday 9:00 AM',
                    'Wednesday 9:00 AM',
                    'Friday 2:00 PM',
                ],
                'conflicts' => [1]
            ],
            'George' => [
                'capacity' => 3,
                'expertise' => ['Data Science', 'AI'],
                'availability' => [
                    'Tuesday 2:00 PM',
                    'Monday 9:00 AM',
                    'Friday 9:00 AM',
                ],
                'conflicts' => [4]
            ],
            'Hannah' => [
                'capacity' => 3,
                'expertise' => ['Security', 'Networks'],
                'availability' => [
                    'Monday 9:00 AM',
                    'Thursday 2:00 PM',
                    'Friday 9:00 AM',
                ],
                'conflicts' => [2]
            ],
            'Ian' => [
                'capacity' => 3,
                'expertise' => ['AI', 'Security'],
                'availability' => [
                    'Wednesday 9:00 AM',
                    'Monday 9:00 AM',
                    'Friday 2:00 PM',
                ],
                'conflicts' => [3]
            ],
            'Jane' => [
                'capacity' => 3,
                'expertise' => ['Networks', 'Data Science'],
                'availability' => [
                    'Monday 9:00 AM',
                    'Wednesday 2:00 PM',
                    'Thursday 9:00 AM',
                ],
                'conflicts' => [5]
            ],
        ];

        // --------------------------
        // Static Group Data (5 groups, 3 panelists each)
        // --------------------------
        $groups = [
            1 => [
                'required_panelists' => 3,
                'topic_tags' => ['AI', 'Networks'],
                'time_slot' => '9:00 AM',
            ],
            2 => [
                'required_panelists' => 3,
                'topic_tags' => ['Networks', 'Security'],
                'time_slot' => '9:00 AM',
            ],
            3 => [
                'required_panelists' => 3,
                'topic_tags' => ['Security', 'AI'],
                'time_slot' => '2:00 PM',
            ],
            4 => [
                'required_panelists' => 3,
                'topic_tags' => ['Data Science', 'AI'],
                'time_slot' => '2:00 PM',
            ],
            5 => [
                'required_panelists' => 3,
                'topic_tags' => ['AI', 'Data Science', 'Security'],
                'time_slot' => '9:00 AM',
            ],
        ];

        // Run scheduler
        $result = $this->balancedExpertMatch($instructors, $groups, $offsetOption);

        // Build structured output
        $formatted = [];

        foreach ($groups as $groupId => $group) {
            $required = $group['required_panelists'];
            $assigned = isset($result[$groupId]) ? count($result[$groupId]) : 0;

            // Find adviser conflicts for this group
            $conflicts = [];
            foreach ($instructors as $instName => $instData) {
                if (in_array($groupId, $instData['conflicts'])) {
                    $conflicts[] = $instName;
                }
            }

            $formatted[] = [
                "groupId" => $groupId,
                "group" => [
                    "required_panelists" => $required,
                    "topic_tags" => $group['topic_tags'],
                    "time_slot" => !empty($result[$groupId]) ? [$result[$groupId][0]['day'] . " " . $result[$groupId][0]['time']] : [],
                    "conflicts" => $conflicts,
                ],
                "panelists" => $result[$groupId] ?? [],
                "conflict_note" => $assigned < $required
                    ? "Conflict: Needed {$required}, but only {$assigned} assigned"
                    : null,
            ];
        }

        // Format schedule for display
        foreach ($formatted as $entry) {
            echo "<h3>Group {$entry['groupId']}</h3>";

            if (!empty($entry['panelists'])) {
                $scheduleDate = $entry['panelists'][0]['schedule_date'];
                $dayTime = $entry['panelists'][0]['day'] . " " . $entry['panelists'][0]['time'];
                echo "<b>Schedule:</b> {$scheduleDate} {$dayTime}<br>";
            } else {
                echo "<b>Schedule:</b> <span style='color:red'>No schedule</span><br>";
            }

            echo "<b>Tags:</b> " . implode(', ', $entry['group']['topic_tags']) . "<br>";

            $conflicts = !empty($entry['group']['conflicts'])
                ? implode(', ', $entry['group']['conflicts'])
                : 'None';
            echo "<b>Adviser (Conflict):</b> {$conflicts}<br>";

            echo "<b>Panelists:</b><ul>";
            foreach ($entry['panelists'] as $panel) {
                echo "<li><b>{$panel['instructor']}</b> 
                (Score: {$panel['score']}, 
                Time: {$panel['day']} {$panel['time']}, 
                Date: {$panel['schedule_date']})<br>
                <u>Expertise:</u> " . implode(', ', $panel['expertise']) . "<br>
                <u>Availability:</u> " . implode(', ', $panel['availability']) . "
                </li>";
            }
            echo "</ul>";

            if ($entry['conflict_note']) {
                echo "<span style='color:red'>{$entry['conflict_note']}</span><br>";
            }

            echo "<hr>";
        }
    }

    private function balancedExpertMatch($instructors, $groups, $offsetOption)
    {
        $daySchedule = []; // Track how many groups per session
        $assignments = [];

        foreach ($groups as $groupId => $group) {
            $required = $group['required_panelists'];
            $bestSlot = null;
            $bestCandidates = [];

            // Step 1: collect possible slots from all instructors
            $possibleSlots = [];
            foreach ($instructors as $instId => $inst) {
                foreach ($inst['availability'] as $slot) {
                    $possibleSlots[$slot][] = $instId;
                }
            }

            // Step 2: evaluate each slot
            foreach ($possibleSlots as $slot => $instList) {
                [$day, $time, $ampm] = explode(' ', $slot, 3);
                $timeSlot = $time . ' ' . $ampm;
                $key = $day . ' ' . $timeSlot;

                // max 2 groups per session
                if (isset($daySchedule[$key]) && $daySchedule[$key] >= 2) {
                    continue;
                }

                // Collect valid instructors for this slot
                $candidates = [];
                foreach ($instList as $instId) {
                    $inst = $instructors[$instId];

                    if ($inst['capacity'] <= 0) continue;

                    if (in_array($groupId, $instructors[$instId]['conflicts'])) continue;

                    // Match time
                    $slotParts = explode(' ', $slot, 3);
                    $slotTime = $slotParts[1] . ' ' . $slotParts[2];
                    if ($slotTime !== $group['time_slot']) continue;

                    $score = count(array_intersect($group['topic_tags'], $inst['expertise']));

                    $candidates[] = [
                        'instructor'   => $instId,
                        'score'        => $score,
                        'day'          => $day,
                        'time'         => $timeSlot,
                        'expertise'    => $inst['expertise'],
                        'availability' => $inst['availability'],
                    ];
                }

                // Keep slot if enough candidates exist
                if (count($candidates) >= $required) {
                    $totalScore = array_sum(array_column($candidates, 'score'));
                    if ($bestSlot === null || $totalScore > array_sum(array_column($bestCandidates, 'score'))) {
                        $bestSlot = $slot;
                        $bestCandidates = $candidates;
                    }
                }
            }

            // Step 3: assign chosen slot
            $assignments[$groupId] = [];
            if ($bestSlot) {
                [$dayName, $time, $ampm] = explode(' ', $bestSlot, 3);
                $timeSlot = $time . ' ' . $ampm;

                // Generate real date ONCE for this slot
                $scheduleDate = $this->getScheduleDate($dayName, null, $offsetOption);

                // Sort and assign
                usort($bestCandidates, fn($a, $b) => $b['score'] <=> $a['score']);
                $assigned = array_slice($bestCandidates, 0, $required);

                foreach ($assigned as $cand) {
                    $cand['schedule_date'] = $scheduleDate; // Add schedule date
                    $assignments[$groupId][] = $cand;

                    $instructors[$cand['instructor']]['capacity']--;
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
