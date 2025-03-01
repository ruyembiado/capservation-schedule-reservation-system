@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Edit Instructor</h1>
    </div>
    <div class="card shadow col-12 mb-4">
        <div class="card-body">
            <form action="{{ route('instructor.update', $instructor->id) }}" method="POST">
                @csrf
                <div class="d-flex align-items-start justify-content-center gap-5">
                    <div class="col-6">
                        <div class="col-12 mb-2">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" name="email" placeholder="Enter your email address"
                                class="form-control @error('email') is-invalid @enderror" id="email"
                                value="{{ $instructor->email }}">
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="name" name="name" placeholder="Enter your full name"
                                class="form-control @error('name') is-invalid @enderror" id="name"
                                value="{{ $instructor->name }}">
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label for="username" class="form-label">Username</label>
                            <input type="username" name="username" placeholder="Enter your username"
                                class="form-control @error('username') is-invalid @enderror" id="username"
                                value="{{ $instructor->username }}">
                            @error('username')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="col-12 mb-2">
                            <label for="position" class="form-label">Position</label>
                            <input type="position" name="position" placeholder="Enter your position"
                                class="form-control @error('position') is-invalid @enderror" id="position"
                                value="{{ $instructor->position }}">
                            @error('position')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" placeholder="Enter your passsword"
                                class="form-control @error('password') is-invalid @enderror" id="password">
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" placeholder="Confirm your passsword"
                                class="form-control @error('password') is-invalid @enderror" id="password_confirmation">
                        </div>
                        <div class="m-auto text-center">
                            <button class="btn w-100 btn-primary" type="submit">Update</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->
