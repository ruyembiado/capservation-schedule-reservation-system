@extends('layouts.app')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Capstone History</h1>
    </div>

    <style>
        /* Only show timeline line if there are items */
        .capstone-timeline::before {
            @if ($reservations->isNotEmpty())
                height: calc(100% - 60px);
            @else
                height: 0;
            @endif
        }
    </style>

    <div class="card shadow mb-4">
        <div class="card-body">
            @if ($reservations->isEmpty())
                <div class="alert alert-warning">No capstone history found.</div>
            @else
                <div class="capstone-timeline">
                    @foreach ($reservations as $key => $reservation)
                        <div class="timeline-item">
                            <div class="timeline-dot">
                                {{ $loop->iteration }}
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex align-items-center gap-2">
                                    <h4 class="timeline-title">
                                        @switch($loop->iteration)
                                            @case(1)
                                                Title Defense
                                            @break

                                            @case(2)
                                                Pre-Oral Defense
                                            @break

                                            @case(3)
                                                Final Defense
                                            @break

                                            @default
                                                Additional Defense
                                        @endswitch
                                    </h4>
                                    <h4 class="badge {{ $reservation->status == 'done' ? 'bg-success' : 'bg-warning' }}">
                                        {{ $reservation->status == 'done' ? 'Defended' : 'Pending' }}
                                    </h4>
                                    <h4
                                        class="badge {{ $reservation->transaction->status == 'paid' ? 'bg-success' : 'bg-warning' }}">
                                        {{ $reservation->transaction->status == 'paid' ? 'Paid' : 'Pending' }}
                                    </h4>
                                    @if ($reservation->reservationHistory->isNotEmpty())
                                        <h4 class="badge bg-danger position-relative">
                                            @foreach ($reservation->reservationHistory as $history)
                                                Re-Defense
                                                <span
                                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary text-light">
                                                    {{ $loop->iteration }}
                                                </span>
                                            @endforeach
                                        </h4>
                                    @endif
                                </div>
                                <div class="timeline-date">
                                    <p>Date of Defense: {{ $reservation->schedule->schedule_date ?? 'N/A' }}
                                        @if ($reservation->schedule->schedule_time ?? false)
                                            at {{ $reservation->schedule->schedule_time }}
                                        @endif
                                    </p>
                                </div>
                                <div class="timeline-meta">
                                    <ul>
                                        @if ($reservation->capstones && $reservation->capstones->isNotEmpty())
                                            @foreach ($reservation->capstones as $capstone)
                                                <li>
                                                    <h6>
                                                        <strong>{{ $capstone->title }}</strong>
                                                        @if ($reservation->status == 'done')
                                                            @if ($capstone->title_status == 'pending')
                                                                <span class="badge bg-success">Defended</span>
                                                            @else
                                                                <span
                                                                    class="badge bg-danger">{{ Str::ucfirst($capstone->title_status) }}</span>
                                                            @endif
                                                        @else
                                                            @if ($loop->parent->last)
                                                                <span
                                                                    class="badge {{ $capstone->title_status == 'pending'
                                                                        ? 'bg-warning'
                                                                        : ($capstone->title_status == 'defended'
                                                                            ? 'bg-success'
                                                                            : 'bg-danger') }}">
                                                                    {{ ucfirst($capstone->title_status) }}
                                                                </span>
                                                            @else
                                                                <span class="badge bg-success">Defended</span>
                                                            @endif
                                                        @endif
                                                    </h6>
                                                </li>
                                            @endforeach
                                        @else
                                            <li class="text-warning">
                                                No capstone data available
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
