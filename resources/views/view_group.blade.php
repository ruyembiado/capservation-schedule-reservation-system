@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Group Details</h1>
    </div>
    <div class="card shadow col-12 mb-4">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-center gap-5">
                <div class="col-6">
                    <div class="col-12 mb-2">
                        <label for="email" class="form-label">Email</label>
                        <input readonly type="text" name="email" placeholder="Enter email address"
                            class="form-control @error('email') is-invalid @enderror" id="email"
                            value="{{ $group->email }}">
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 mb-2">
                        <label for="username" class="form-label">Username/Group Name</label>
                        <input readonly type="username" name="username" placeholder="Enter username/group name"
                            class="form-control @error('username') is-invalid @enderror" id="username"
                            value="{{ $group->username }}">
                        @error('username')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 mb-2">
                        <label for="members" class="form-label">Members</label>
                        <div id="membersRepeater">
                            @if (old('members'))
                                @foreach (old('members') as $index => $member)
                                    <div class="input-group mb-2 member-item">
                                        <input readonly type="text" name="members[]" value="{{ $member }}"
                                            class="form-control @error("members.$index") is-invalid @enderror"
                                            placeholder="Enter member name">
                                        @error("members.$index")
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach
                            @else
                                @foreach (json_decode($group->members) as $member)
                                    <div class="input-group mb-2 member-item">
                                        <input readonly type="text" name="members[]" value="{{ $member }}"
                                            class="form-control" placeholder="Enter member name">
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <!-- <div class="col-12 mb-2">
                        <label for="capacity" class="form-label">Required Panelists</label>
                        <div class="col-2">
                            <input readonly type="number" name="capacity"
                                class="form-control @error('capacity') is-invalid @enderror" id="capacity"
                                value="{{ $group->capacity }}">
                        </div>
                        @error('capacity')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div> -->
                    <!-- <div class="col-12 mb-2">
                        <label for="vacant_time" class="form-label">Defense Time</label>
                        <input readonly type="time" name="vacant_time" placeholder="Enter passsword"
                            value="{{ $group->vacant_time }}"
                            class="form-control @error('vacant_time') is-invalid @enderror" id="vacant_time">
                        @error('vacant_time')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div> -->
                </div>
                <div class="col-5">
                    <div class="col-12 mb-2">
                        <label for="program" class="form-label">Program</label>
                        <input type="text" readonly name="program" placeholder="Enter program"
                            class="form-control @error('program') is-invalid @enderror" id="program"
                            value="{{ $group->program }}">
                        @error('program')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 mb-2">
                        <label for="yearsection" class="form-label">Year & Section</label>
                        <input readonly type="yearsection" name="yearsection" placeholder="Enter year and section"
                            class="form-control @error('yearsection') is-invalid @enderror" id="yearsection"
                            value="{{ $group->year_section }}">
                        @error('yearsection')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 mb-2">
                        <label for="capstone_adviser" class="form-label">Capstone Adviser</label>
                        <input readonly type="capstone_adviser" name="capstone_adviser" placeholder="Enter capstone adviser"
                            class="form-control @error('capstone_adviser') is-invalid @enderror" id="capstone_adviser"
                            value="{{ $group->capstone_adviser }}">
                        @error('capstone_adviser')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 mb-2">
                        <label for="instructor" class="form-label">Instructor</label>
                        <input readonly type="text" name="instructor" placeholder="Enter instructor"
                            class="form-control @error('instructor') is-invalid @enderror" id="instructor"
                            value="{{ $group->instructor->name ?? 'N/A' }}">
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
                                    <input readonly type="text" name="credentials[]" class="form-control"
                                        value="{{ $credential }}" placeholder="">
                                    @error("credentials.$index")
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->
