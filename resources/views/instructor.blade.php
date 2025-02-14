@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Instructors</h1>
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
                            <th>Date Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($instructors->isEmpty())
                            <tr>
                                <td colspan="7" class="text-center">No instructor found</td>
                            </tr>
                        @endif
                        @foreach ($instructors as $instructor)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $instructor->name }}</td>
                                <td>{{ $instructor->username }}</td>
                                <td>{{ $instructor->email }}</td>
                                <td>{{ $instructor->position }}</td>
                                <td>{{ $instructor->created_at->format('Y-m-d g:i A') }}</td>
                                <td>
                                    <a href="" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
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
