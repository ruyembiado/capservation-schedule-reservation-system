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

                        <div class="mb-3">
                            <label for="offsetOption" class="form-label">Schedule Offset</label>
                            <select name="offsetOption" id="offsetOption" class="form-select">
                                <option value="weeks:1" selected>1 Week Later</option>
                                <option value="weeks:2">2 Weeks Later</option>
                                {{-- <option value="days:10">10 Days Later</option> --}}
                                <option value="months:1">1 Month Later</option>
                                <option value="months:2">2 Month Later</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Run Smart Scheduler
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Results Card -->
        <div class="col-12">
            <div class="card shadow mb-3 mb-lg-0">
                <div class="card-body">
                    <h4 class="card-title mb-4 fw-normal text-dark">Smart Scheduler Results</h4>
                    @if ($formatted)
                        <div id="schedulerResult">
                            @foreach ($formatted as $entry)
                                <div class="mb-3 shadow-sm rounded border px-3 pt-3 bg-white">
                                    <h5 class="fw-semibold mb-1">{{ $entry['group']['name'] }}</h5>
                                    <p class="mb-1"><b>Defense Time:</b>
                                        {{ $entry['group']['time_slot']
                                            ? \Carbon\Carbon::createFromFormat('H:i', $entry['group']['time_slot'])->format('h:i A')
                                            : 'No schedule' }}
                                    </p>
                                    <p class="mb-1"><b>Schedule:</b>
                                        {{ !empty($entry['panelists'])
                                            ? date('Y-m-d h:i A, l', strtotime($entry['panelists'][0]['schedule_date'] . ' ' . $entry['panelists'][0]['time']))
                                            : 'No schedule' }}
                                    </p>
                                    <p class="mb-1"><b>Tags:</b> {{ implode(', ', $entry['group']['topic_tags']) }}</p>
                                    <p class="mb-1"><b>Adviser:</b>
                                        {{ !empty($entry['group']['conflicts']) ? implode(', ', $entry['group']['conflicts']) : 'None' }}
                                    </p>
                                    <p class="mb-1"><b>Panelists:</b></p>
                                    <div class="row">
                                        @foreach ($entry['panelists'] as $panel)
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
                                    @if ($entry['conflict_note'])
                                        <span class="text-danger fw-normal mt-2">{{ $entry['conflict_note'] }}</span>
                                    @endif
                                </div>
                            @endforeach
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
