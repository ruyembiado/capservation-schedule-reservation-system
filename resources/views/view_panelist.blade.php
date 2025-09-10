@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">View Panelists</h1>
    </div>

    @php
        $selectedPanelists = json_decode($reservation->panelist_id, true) ?? [];
    @endphp

    <div class="d-flex justify-content-between flex-column">
        <!-- Panelists List -->
        <div class="card shadow col-12 mb-4">
            <div class="card-body">
                <div class="col-12 mb-2">
                    <div class="titles mb-3">
                        <strong>Capstone Title/s:</strong>
                        @foreach ($capstones as $capstone)
                            <li>
                                <i class="fa fa-book"></i> <strong>{{ $capstone->title }}</strong>
                            </li>
                        @endforeach
                        @if ($capstones->isEmpty())
                            <li>No Capstone Assigned</li>
                        @endif
                    </div>

                    <div class="d-flex flex-wrap justify-content-start gap-4">
                        @foreach ($panelists as $panelist)
                            @if (in_array($panelist->id, $selectedPanelists))
                                <div style="width: 31%;"
                                    class="border rounded p-3 panelist-card bg-selected-panelist"
                                    data-id="{{ $panelist->id }}">
                                    <h5 style="font-weight: 600;" class="text-dark">{{ $panelist->name }}</h5>

                                    <!-- Vacant Time -->
                                    <div class="mb-2">
                                        <strong>Vacant Time:</strong>
                                        @php
                                            $vacantTimes = json_decode($panelist->vacant_time, true);
                                        @endphp

                                        @if ($vacantTimes && count($vacantTimes) > 0)
                                            <ul class="list-unstyled mb-0">
                                                @foreach ($vacantTimes as $vacantTime)
                                                    <li>
                                                        {{ $vacantTime['day'] ?? '' }}:
                                                        {{ date('h:i A', strtotime($vacantTime['start_time'])) }} -
                                                        {{ date('h:i A', strtotime($vacantTime['end_time'])) }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-muted">No vacant time available</span>
                                        @endif
                                    </div>

                                    <!-- Credentials -->
                                    <div class="mb-2">
                                        <strong>Expertise Tags:</strong>
                                        @php
                                            $credentials = json_decode($panelist->credentials, true);
                                        @endphp

                                        @if ($credentials && count($credentials) > 0)
                                            <ul class="list-unstyled mb-0">
                                                @foreach ($credentials as $credential)
                                                    <li>{{ $credential }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-muted">No expertise tags available</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
