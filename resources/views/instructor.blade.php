@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Capstone Instructors / Panelists</h1>
        @if (auth()->user()->user_type == 'admin')
            <a href="{{ url('/add-instructor') }}" class="d-sm-inline-block btn btn-primary shadow-sm">Add Instructor / Panelist</a>
        @endif
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th>Date Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($instructors as $instructor)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $instructor->name }}</td>
                                <td>{{ $instructor->username }}</td>
                                <td>{{ $instructor->email }}</td>
                                <td>{{ $instructor->position }}</td>
                                <td>
								    <span class="badge bg-{{ $instructor->status == 1 ? 'success' :
								    'danger' }}">{{ $instructor->status == 1 ? 'Activated' : 'Deactivated'
								    }}</span>
								</td>
                                <td>{{ $instructor->created_at->format('Y-m-d h:i A') }}</td>
                                <td>
								    <div class="d-flex align-items-center gap-1 flex-wrap">
								        <a href="{{ url('/view-instructor/' . $instructor->id) }}"
								            class="btn btn-secondary btn-sm">
								            View
								        </a>
								
								        <form action="{{ route('instructor.status') }}" method="POST" class="d-inline">
								            @csrf
								            <input type="hidden" name="id" value="{{ $instructor->id }}">
								            <input type="hidden" name="status" value="{{ $instructor->status == 1 ? 0 : 1 }}">
								
								            <button type="submit"
								                class="btn btn-sm {{ $instructor->status == 1 ? 'btn-danger' : 'btn-success' }}">
								                {{ $instructor->status == 1 ? 'Deactivate' : 'Activate' }}
								            </button>
								        </form>
								
								        <a href="{{ url('/update-instructor/' . $instructor->id) }}"
								            class="btn btn-warning btn-sm">
								            Edit
								        </a>
								
								        <form action="{{ route('instructor.delete', $instructor->id) }}"
								            method="POST"
								            class="d-inline"
								            onsubmit="return confirmDelete(event)">
								            @csrf
								            @method('DELETE')
								
								            <button type="submit" class="btn btn-danger btn-sm">
								                Delete
								            </button>
								        </form>
								    </div>
								</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </td>
    </td>
    <!-- Content Row -->
@endsection <!-- End the content section -->

<script>
    function confirmDelete(event) {
        event.preventDefault();
        if (confirm('Are you sure you want to delete this instructor? This action cannot be undone.')) {
            event.target.submit();
        }
    }
</script>
