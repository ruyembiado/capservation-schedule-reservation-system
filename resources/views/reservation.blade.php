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
                            <th>status</th>
                            <th>Date Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reservations as $reservation)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $reservation['user']['name'] ?? $reservation['user']['username'] }}</td>
                                <td>
                                    @foreach ($reservation['titles'] as $title)
                                        <li><i class="fa fa-book"></i> {{ $title }}</li>
                                    @endforeach
                                </td>
                                <td>{{ $reservation['reserveBy']['name']??$reservation['reserveBy']['username'] }}</td>
                                <td>{{ Str::ucfirst($reservation['status']) }}</td>
                                <td>{{ \Carbon\Carbon::parse($reservation['created_at'])->format('Y-m-d g:i A') }}</td>
                                <td>
                                    @if (Auth::user()->user_type == 'admin')
                                        <a href="" class="btn btn-warning btn-sm mb-1">Edit</a>
                                        <form action="{{ route('reservation.destroy', $reservation['id']) }}" method="POST"
                                            style="display: inline;" onsubmit="return confirmDelete(event)">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="id" value="{{ $reservation['id'] }}">
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    @endif
                                    @if (Auth::user()->user_type === 'instructor')
                                        @if ($reservation['status'] != 'done')
                                            <a href="{{ route('assign_panelist.form', ['id' => $reservation['id']]) }}"
                                                class="btn {{ $reservation['status'] == 'pending' ? 'btn-primary' : 'btn-warning' }} btn-sm mb-1">{{ $reservation['status'] == 'pending' ? 'Assign' : 'Update' }}
                                                Panelists</a>
                                        @endif
                                    @endif
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
    function confirmDelete(event) {
        event.preventDefault();
        if (confirm('Are you sure you want to delete this reservation? This action cannot be undone.')) {
            event.target.submit();
        }
    }
</script>
