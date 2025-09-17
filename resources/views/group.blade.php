@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Groups</h1>
        @if (auth()->user()->user_type == 'instructor')
            <a href="{{ url('/add_group') }}" class="d-sm-inline-block btn btn-primary shadow-sm">Add New Group</a>
        @endif
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Group Name</th>
                            <th>Email</th>
                            <th>Program</th>
                            <th>Capstone Instructor</th>
                            <th>Members</th>
                            <th>Date Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($groups as $group)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $group->name ?: Str::ucfirst($group->username) }}</td>
                                <td>{{ $group->email }}</td>
                                <td>{{ $group->program }}</td>
                                <td>{{ $group->instructor->name }}</td>
                                <td>
                                    @foreach (json_decode($group->members, true) as $member)
                                        <li>{{ $member }}</li>
                                    @endforeach
                                </td>
                                <td>{{ $group->created_at->format('Y-m-d h:i A') }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ url('/view-group/' . $group->id) }}"
                                            class="btn btn-secondary btn-sm">View</a>
                                        <a href="{{ url('/update-group/' . $group->id) }}"
                                            class="btn btn-warning btn-sm">Edit</a>
                                        @if (auth()->user()->user_type == 'admin')
                                            <form action="{{ route('groups.delete', $group->id) }}" method="POST"
                                                onsubmit="return confirmDelete(event)" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
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
    function confirmDelete(event) {
        event.preventDefault();
        if (confirm('Are you sure you want to delete this group? This action cannot be undone.')) {
            event.target.submit();
        }
    }
</script>
