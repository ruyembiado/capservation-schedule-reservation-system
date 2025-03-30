@extends('layouts.app') <!-- Extend the main layout -->
@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Activity Logs</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            {{-- <form action="{{ url()->current() }}" method="GET" class="mb-4">
                <div class="filter-container d-flex gap-2 align-items-end">
                    <div class="program-filter d-flex align-items-center gap-2">
                        <label for="program" class="form-label">Program: </label>
                        <select id="program" class="form-control select2" name="program">
                            <option value="">-- Select Program --</option>
                            <option value="BSIT" {{ request('program') == 'BSIT' ? 'selected' : '' }}>BSIT</option>
                            <option value="BSCS" {{ request('program') == 'BSCS' ? 'selected' : '' }}>BSCS</option>
                            <option value="BSIS" {{ request('program') == 'BSIS' ? 'selected' : '' }}>BSIS</option>
                        </select>
                    </div>

                    <div class="status-filter d-flex align-items-center gap-2">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" class="form-control select2" name="status">
                            <option value="">-- Select Status --</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ url()->current() }}" class="btn btn-outline-danger">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </div>
            </form> --}}
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>User Type</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>Date Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activity_logs as $activity_log)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ Str::ucfirst($activity_log->user_type) }}</td>
                                <td>{{ $activity_log->action }}</td>
                                <td>{{ $activity_log->description }}</td>
                                <td>{{ \Carbon\Carbon::parse($activity_log['created_at'])->format('Y-m-d g:i A') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->
