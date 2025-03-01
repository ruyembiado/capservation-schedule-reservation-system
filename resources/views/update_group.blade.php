@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Edit Group</h1>
    </div>
    <div class="card shadow col-12 mb-4">
        <div class="card-body">
            <form action="{{ route('group.update', $group->id) }}" method="POST">
                @csrf
                <div class="d-flex align-items-start justify-content-center gap-5">
                    <div class="col-6">
                        <div class="col-12 mb-2">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" name="email" placeholder="Enter email address"
                                class="form-control @error('email') is-invalid @enderror" id="email"
                                value="{{ $group->email }}">
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label for="username" class="form-label">Username/Group Name</label>
                            <input type="username" name="username" placeholder="Enter username/group name"
                                class="form-control @error('username') is-invalid @enderror" id="username"
                                value="{{ $group->username }}">
                            @error('username')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
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
                        <div class="col-12 mb-2">
                            <label for="members" class="form-label">Members</label>
                            <div class="member-input-container border rounded p-2">
                                <input type="text" id="members"
                                    class="form-control @error('members') is-invalid @enderror"
                                    placeholder="Type and press Enter">
                                <!-- Hidden inputs for old members -->
                                <div id="membersContainer" class="d-flex flex-wrap gap-1 mt-2">
                                    @if ($group->members)
                                        @foreach (json_decode($group->members, true) as $member)
                                            <span class="badge bg-light text-dark">
                                                {{ $member }}
                                            </span>
                                            <input type="hidden" name="members[]" value="{{ $member }}">
                                        @endforeach
                                    @endif
                                </div>
                                @error('members')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="col-12 mb-2">
                            <label for="program" class="form-label">Program</label>
                            <input type="program" name="program" placeholder="Enter program"
                                class="form-control @error('program') is-invalid @enderror" id="program"
                                value="{{ $group->program }}">
                            @error('program')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label for="yearsection" class="form-label">Year & Section</label>
                            <input type="yearsection" name="yearsection" placeholder="Enter year and section"
                                class="form-control @error('yearsection') is-invalid @enderror" id="yearsection"
                                value="{{ $group->year_section }}">
                            @error('yearsection')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label for="capstone_adviser" class="form-label">Capstone Adviser</label>
                            <input type="capstone_adviser" name="capstone_adviser" placeholder="Enter capstone adviser"
                                class="form-control @error('capstone_adviser') is-invalid @enderror" id="capstone_adviser"
                                value="{{ $group->capstone_adviser }}">
                            @error('capstone_adviser')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label for="instructor" class="form-label">Instructor</label>
                            <div class="custom-select-container">
                                <!-- Custom select display -->
                                <div class="custom-select" id="custom-select">
                                    @if (old('instructor', $group->instructor_id))
                                        @php
                                            $selectedInstructor = $instructors->firstWhere(
                                                'id',
                                                old('instructor', $group->instructor_id),
                                            );
                                        @endphp
                                        {{ $selectedInstructor ? $selectedInstructor->name : '-- Select an instructor --' }}
                                    @else
                                        -- Select an instructor --
                                    @endif
                                </div>
                                <!-- Custom dropdown options -->
                                <div class="custom-dropdown" id="custom-dropdown">
                                    @foreach ($instructors as $instructor)
                                        <div data-value="{{ $instructor->id }}"
                                            class="custom-dropdown-option {{ old('instructor', $group->instructor_id) == $instructor->id ? 'selected' : '' }}">
                                            {{ $instructor->name }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Hidden actual select input -->
                            <select name="instructor" id="instructor" class="hidden-select">
                                <option value="" disabled>-- Select an instructor --</option>
                                @foreach ($instructors as $instructor)
                                    <option value="{{ $instructor->id }}"
                                        {{ old('instructor', $group->instructor_id) == $instructor->id ? 'selected' : '' }}>
                                        {{ $instructor->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('instructor')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="login-logo-container m-auto text-center pt-3">
                            <div class="mt-3">
                                <div class="d-flex gap-1">
                                    <button class="btn btn-primary w-100" type="submit">Update</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->
