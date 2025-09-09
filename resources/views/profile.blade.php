@extends('layouts.app') <!-- Extend the main layout -->
@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Profile</h1>
    </div>
    <div class="card shadow col-12 mb-4">
        <div class="card-body">
            <form action="{{ route('profile.update', $profile->id) }}" method="POST">
                @csrf
                <div class="d-flex align-items-start justify-content-start gap-5">
                    <div class="col-6">
                        <div class="col-12 mb-2">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" name="email" placeholder="Enter email address"
                                class="form-control @error('email') is-invalid @enderror" id="email"
                                value="{{ $profile->email }}">
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label for="username" class="form-label">Username
                                @if (auth()->user()->user_type == 'student')
                                    /Group Name
                                @endif
                            </label>
                            <input type="username" name="username" placeholder="Enter username/group name"
                                class="form-control @error('username') is-invalid @enderror" id="username"
                                value="{{ $profile->username }}">
                            @error('username')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        @if (auth()->user()->user_type == 'instructor')
                            <div class="col-12 mb-2">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" name="name" placeholder="Enter full name"
                                    class="form-control @error('name') is-invalid @enderror" id="name"
                                    value="{{ $profile->name }}">
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                        <div class="col-12 mb-2">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" placeholder="Enter passsword"
                                class="form-control @error('password') is-invalid @enderror" id="password">
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" placeholder="Confirm passsword"
                                class="form-control @error('password') is-invalid @enderror" id="password_confirmation">
                        </div>
                        @if (auth()->user()->user_type == 'admin')
                            <div class="login-logo-container m-auto text-center">
                                <div class="mt-3">
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-primary w-100" type="submit">Update</button>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (auth()->user()->user_type != 'admin' && auth()->user()->user_type != 'instructor')
                            <div class="col-12 mb-2">
                                <label for="members" class="form-label">Members</label>
                                <div id="membersRepeater">
                                    @if (old('members'))
                                        @foreach (old('members') as $index => $member)
                                            <div class="input-group mb-2 member-item">
                                                <input type="text" name="members[]" value="{{ $member }}"
                                                    class="form-control @error("members.$index") is-invalid @enderror"
                                                    placeholder="Enter member name">
                                                <button type="button" class="btn btn-danger remove-member">x</button>
                                                @error("members.$index")
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        @endforeach
                                    @else
                                        @foreach (json_decode($profile->members) as $member)
                                            <div class="input-group mb-2 member-item">
                                                <input type="text" name="members[]" value="{{ $member }}"
                                                    class="form-control" placeholder="Enter member name">
                                                <button type="button" class="btn btn-danger remove-member">x</button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <!-- Add Member Button Outside the Repeater -->
                                <button type="button" class="btn btn-sm btn-primary" id="addMemberBtn">Add Member</button>
                            </div>
                        @endif
                    </div>
                    @if (auth()->user()->user_type != 'admin')
                        <div class="col-5">
                            @if (auth()->user()->user_type != 'instructor')
                                <div class="col-12 mb-2">
                                    <label for="program" class="form-label">Program</label>
                                    <select name="program" id="program"
                                        class="form-control @error('program') is-invalid @enderror">
                                        <option value="">-- Select Program --</option>
                                        <option value="BSIT" {{ $profile->program == 'BSIT' ? 'selected' : '' }}>BSIT
                                        </option>
                                        <option value="BSCS" {{ $profile->program == 'BSCS' ? 'selected' : '' }}>BSCS
                                        </option>
                                        <option value="BSIS" {{ $profile->program == 'BSIS' ? 'selected' : '' }}>BSIS
                                        </option>
                                    </select>
                                    @error('program')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 mb-2">
                                    <label for="yearsection" class="form-label">Year & Section</label>
                                    <input type="yearsection" name="yearsection" placeholder="Enter year and section"
                                        class="form-control @error('yearsection') is-invalid @enderror" id="yearsection"
                                        value="{{ $profile->year_section }}">
                                    @error('yearsection')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 mb-2">
                                    <label for="capstone_adviser" class="form-label">Capstone Adviser</label>
                                    <input type="capstone_adviser" name="capstone_adviser"
                                        placeholder="Enter capstone adviser"
                                        class="form-control @error('capstone_adviser') is-invalid @enderror"
                                        id="capstone_adviser" value="{{ $profile->capstone_adviser }}">
                                    @error('capstone_adviser')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 mb-2">
                                    <label for="instructor" class="form-label">Instructor</label>
                                    <!-- Hidden actual select input -->
                                    <select name="instructor" class="form-control select2" id="select_instructor"
                                        class="">
                                        <option value="" disabled>-- Select an instructor --</option>
                                        @foreach ($instructors as $instructor)
                                            <option value="{{ $instructor->id }}"
                                                {{ old('instructor', $profile->instructor_id) == $instructor->id ? 'selected' : '' }}>
                                                {{ $instructor->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('instructor')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                            <div class="login-logo-container m-auto text-center">
                                <div class="mt-3">
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-primary w-100" type="submit">Update</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->
