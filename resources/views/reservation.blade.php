@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Reservations</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Group</th>
                            <th>Titles</th>
                            <th>Reserve by</th>
                            <th>Status</th>
                            <th>Schedule Date</th>
                            <th>Date Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reservations as $reservation)
                            @php $bg = ''; @endphp
                            @switch($reservation['status'])
                                @case('done')
                                    @php $bg = 'bg-success'; @endphp
                                @break

                                @case('pending')
                                    @php $bg = 'bg-warning'; @endphp
                                @break

                                @case('reserved')
                                    @php $bg = 'bg-primary'; @endphp
                                @break

                                @case('approved')
                                    @php $bg = 'bg-info'; @endphp
                                @break

                                @default
                                    @php $bg = ''; @endphp
                            @endswitch

                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $reservation['user']['name'] ?? $reservation['user']['username'] }}</td>
                                <td>
                                    @foreach ($reservation['titles'] as $title)
                                        <li><i class="fa fa-book"></i> {{ $title }}</li>
                                    @endforeach
                                </td>
                                <td>{{ $reservation['reserveBy']['name'] ?? $reservation['reserveBy']['username'] }}</td>
                                <td>
                                    <span
                                        class="badge {{ $bg }}">{{ Str::ucfirst($reservation['status'] == 'reserved' ? 'Scheduled' : $reservation['status']) }}</span>
                                </td>
                                <td>
                                    {{ $reservation['status'] == 'reserved' ? $reservation['schedule_date'] . ' | ' . $reservation['schedule_time'] : '' }}
                                </td>
                                <td>{{ \Carbon\Carbon::parse($reservation['created_at'])->format('Y-m-d h:i A') }}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @if (auth()->user()->user_type === 'admin' || auth()->user()->user_type === 'instructor')
                                            {{-- @if (Auth::user()->user_type === 'instructor') --}}
                                            <a href="{{ route('view_panelists', $reservation['id']) }}"
                                                class="btn-sm btn btn-secondary">View Panelists</a>
                                            @if ($reservation['status'] != 'done' && $reservation['status'] != 'reserved')
                                                <a href="{{ route('assign_panelist.form', ['id' => $reservation['id']]) }}"
                                                    class="btn {{ $reservation['status'] == 'pending' ? 'btn-primary' : 'btn-warning' }} btn-sm">{{ $reservation['status'] == 'pending' ? 'Assign' : 'Update' }}
                                                    Panelists</a>
                                            @endif
                                            {{-- @endif --}}
                                            @if ($reservation['status'] === 'reserved')
                                                <form action="{{ route('schedule.reschedule', $reservation['id']) }}"
                                                    method="POST" style="display: inline;"
                                                    onsubmit="return confirmReschedule(event)">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger text-light">
                                                        Re-schedule
                                                    </button>
                                                </form>
                                            @endif
                                            @if (Auth::user()->user_type == 'admin')
                                                <form action="{{ route('reservation.destroy', $reservation['id']) }}"
                                                    method="POST" style="display: inline;"
                                                    onsubmit="return confirmDelete(event)">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="id" value="{{ $reservation['id'] }}">
                                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->

<script>
    function confirmReschedule(event) {
        event.preventDefault();
        if (confirm('Are you sure you want to reschedule this reservation? This action cannot be undone.')) {
            event.target.submit();
        }
    }

    function confirmDelete(event) {
        event.preventDefault();
        if (confirm('Are you sure you want to delete this reservation? This action cannot be undone.')) {
            event.target.submit();
        }
    }
</script>
