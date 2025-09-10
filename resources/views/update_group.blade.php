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
                                    @foreach (json_decode($group->members) as $member)
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
                        <div class="col-12 mb-2">
                            <label for="capacity" class="form-label">Required Panelists</label>
                            <div class="col-2">
                                <input type="number" name="capacity"
                                    class="form-control @error('capacity') is-invalid @enderror" id="capacity"
                                    value="{{ $group->capacity }}">
                            </div>
                            @error('capacity')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="col-12 mb-2">
                            <label for="program" class="form-label">Program</label>
                            <select name="program" id="program"
                                class="form-control @error('program') is-invalid @enderror">
                                <option value="">-- Select Program --</option>
                                <option value="BSIT" {{ $group->program == 'BSIT' ? 'selected' : '' }}>BSIT
                                </option>
                                <option value="BSCS" {{ $group->program == 'BSCS' ? 'selected' : '' }}>BSCS
                                </option>
                                <option value="BSIS" {{ $group->program == 'BSIS' ? 'selected' : '' }}>BSIS
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
                            <!-- Hidden actual select input -->
                            <select name="instructor" class="form-control select2" id="select_instructor"
                                class="">
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

                        <div class="col-12 mb-2">
                            <label for="credentials" class="form-label">Topic Tags</label>
                            <div id="credentialsRepeater">
                                @php
                                    $credentials = old('credentials', json_decode($group->credentials) ?? ['']);
                                @endphp
                                @foreach ($credentials as $index => $credential)
                                    <div class="input-group mb-2 credential-item">
                                        <input type="text" name="credentials[]" class="form-control"
                                            value="{{ $credential }}" placeholder="Enter Topic Tag">
                                        <button type="button" class="btn btn-danger remove-credential">x</button>
                                        @error("credentials.$index")
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" id="addCredentialBtn">Add
                                Topic Tag</button>
                        </div>

                        <div class="col-12 mb-2">
                            <label for="vacant_time" class="form-label">Defense Time</label>
                            <input type="time" name="vacant_time" placeholder="Enter passsword" value="{{ $group->vacant_time }}"
                                class="form-control @error('vacant_time') is-invalid @enderror" id="vacant_time">
                            @error('vacant_time')
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
