@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Smart Scheduler</h1>
    </div>

    <!-- Content Row -->
    <div class="row gap-4">
        <!-- Smart Scheduler Button -->
        <div class="col-12 col-lg-4 col-xl-3">
            <div class="card shadow mb-3 mb-lg-0">
                <div class="card-body">
                    <form action="{{ route('SmartScheduler') }}" method="POST">
                        @csrf
                        <input type="hidden" name="runScheduler" value="true">

                        <div class="mb-3 text-center text-md-start">
                            <label for="offsetOption" class="form-label">Schedule Offset</label>
                            <select name="offsetOption" id="offsetOption" class="form-select">
                                <option value="weeks:1" selected>1 Week Later</option>
                                <option value="weeks:2">2 Weeks Later</option>
                                {{-- <option value="days:10">10 Days Later</option> --}}
                                <option value="months:1">1 Month Later</option>
                                <option value="months:2">2 Month Later</option>
                            </select>
                        </div>

                        <div class="col-12 d-grid gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                Run Smart Scheduler
                            </button>
                            <a class="btn btn-danger w-100" href="{{ url('/smart-scheduler') }}">Clear Results</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Results Card -->
        <div class="col-12">
            <div class="card shadow mb-3 mb-lg-0">
                <div class="card-body">
                    <h4 class="card-title mb-4 fw-normal text-dark text-center text-md-start">Smart Scheduler Results</h4>
                    @if ($formatted)
                        <div id="schedulerResult">
                            <form action="{{ route('assign.panelist.schedule') }}" method="POST">
                                @csrf
                                @foreach ($formatted as $entry)
                                    <div class="group-result mb-3 shadow-sm rounded border px-3 pt-3 bg-white">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h5 class="fw-semibold mb-1">{{ $entry['group']['name'] }}</h5>
                                            <button type="button" class="btn btn-sm btn-danger btn-delete-group">×</button>
                                        </div>

                                        <p class="mb-1"><b>Defense Time:</b>
                                            {{ $entry['group']['time_slot']
                                                ? \Carbon\Carbon::createFromFormat('H:i', $entry['group']['time_slot'])->format('h:i A')
                                                : 'No schedule' }}
                                        </p>
                                        <p class="mb-1"><b>Tags:</b> {{ implode(', ', $entry['group']['topic_tags']) }}
                                        </p>
                                        <p class="mb-1"><b>Adviser:</b>
                                            {{ !empty($entry['group']['conflicts']) ? implode(', ', $entry['group']['conflicts']) : 'None' }}
                                        </p>
                                        <p class="mb-1"><b>Schedule:</b>
                                            {{ !empty($entry['panelists'])
                                                ? date('Y-m-d h:i A, l', strtotime($entry['panelists'][0]['schedule_date'] . ' ' . $entry['panelists'][0]['time']))
                                                : 'No schedule' }}
                                        </p>

                                        {{-- Group-level hidden inputs --}}
                                        <input type="hidden" name="group_id[]" value="{{ $entry['groupId'] ?? '' }}">
                                        <input type="hidden" name="reservation_id[]"
                                            value="{{ $entry['group']['reservation_id'] ?? '' }}">
                                        <input type="hidden" name="defense_date[]"
                                            value="{{ $entry['panelists'][0]['schedule_date'] ?? '' }}">
                                        <input type="hidden" name="defense_time[]"
                                            value="{{ $entry['panelists'][0]['time'] ?? '' }}">

                                        <p class="mb-1 p-0"><b>Panelists:</b></p>
                                        <div class="row">
                                            @if ($entry['conflict_note'])
                                                <span
                                                    class="text-danger fw-normal d-block mb-2">{{ $entry['conflict_note'] }}</span>
                                            @endif

                                            @foreach ($entry['panelists'] as $panel)
                                                {{-- Only panelist IDs are per-panelist --}}
                                                <input type="hidden" name="panelist_id[{{ $entry['groupId'] }}][]"
                                                    value="{{ $panel['instructor_id'] ?? '' }}">

                                                <div class="col-12 col-md-4 mb-4">
                                                    <div class="p-3 border rounded shadow-sm h-100">
                                                        <b>{{ $panel['instructor'] }}</b>
                                                        <span class="badge bg-theme-primary text-light ms-2">Score:
                                                            {{ $panel['score'] }}</span>
                                                        <br>
                                                        <small class="text-dark"><u>Time:</u>
                                                            {{ date('h:i A', strtotime($panel['time'])) }},
                                                            Date: {{ $panel['schedule_date'] }}
                                                        </small>
                                                        <br>
                                                        <small><u>Expertise:</u>
                                                            {{ implode(', ', $panel['expertise']) }}</small>
                                                        <br>
                                                        <small><u>Availability:</u>
                                                            {{ implode(
                                                                ', ',
                                                                array_map(function ($t) {
                                                                    [$day, $time] = explode(' ', $t);
                                                                    return $day . ' ' . date('h:i A', strtotime($time));
                                                                }, $panel['availability']),
                                                            ) }}
                                                        </small>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach

                                <div class="col-12 d-flex justify-content-end gap-2 mb-1">
                                    <button type="submit" id="assignButton" class="btn btn-primary col-12 col-md-3">
                                        Assign and Save Schedule
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-warning mt-3 text-center fw-normal">
                            No scheduling results available. Please run the Smart Scheduler.
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const assignButton = document.getElementById('assignButton');

        function checkConflicts() {
            const hasConflict = document.querySelectorAll('.group-result .text-danger').length > 0;
            const hasGroups = document.querySelectorAll('.group-result').length > 0;

            // Enable only if there are groups AND no conflicts
            assignButton.disabled = hasConflict || !hasGroups;
        }

        // Initial check
        checkConflicts();

        // Delete group functionality
        document.addEventListener("click", function(e) {
            if (e.target.classList.contains("btn-delete-group")) {
                e.target.closest(".group-result").remove();
                checkConflicts(); // re-check after deletion
            }
        });
    });
</script>