@extends('layouts.app')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Add Instructor/Panelist</h1>
    </div>

    <div class="card shadow col-12 mb-4">
        <div class="card-body p-4">
            <form class="row g-3 needs-validation" action="{{ route('auth.register') }}" method="POST">
                @csrf
                <div class="d-flex align-items-start justify-content-center gap-5">
                    <input type="hidden" name="user_type" value="instructor">
                    <div class="col-6">
                        <!-- Email -->
                        <div class="col-12 mb-2">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" placeholder="Enter your email address"
                                class="form-control @error('email') is-invalid @enderror" id="email"
                                value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Full Name -->
                        <div class="col-12 mb-2">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" name="name" placeholder="Enter your full name"
                                class="form-control @error('name') is-invalid @enderror" id="name"
                                value="{{ old('name') }}">
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Username -->
                        <div class="col-12 mb-2">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" placeholder="Enter your username"
                                class="form-control @error('username') is-invalid @enderror" id="username"
                                value="{{ old('username') }}">
                            @error('username')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Capacity -->
                        <div class="col-12 mb-2">
                            <label for="capacity" class="form-label">Capacity</label>
                            <div class="col-2">
                                <input type="number" name="capacity"
                                    class="form-control @error('capacity') is-invalid @enderror" id="capacity"
                                    value="{{ old('capacity') }}">
                            </div>
                            @error('capacity')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Expertise Tags -->
                        <div class="col-12 mb-2">
                            <label for="credentials" class="form-label">Expertise Tags</label>
                            <div id="credentialsRepeater">
                                @php
                                    $oldCredentials = old('credentials', ['']);
                                @endphp
                                @foreach ($oldCredentials as $cred)
                                    <div class="input-group mb-2 credential-item">
                                        <input type="text" name="credentials[]" class="form-control"
                                            value="{{ $cred }}" placeholder="Enter Expertise Tag">
                                        <button type="button" class="btn btn-danger remove-credential">x</button>
                                    </div>
                                @endforeach
                                @error('credentials.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" id="addCredentialBtn">
                                Add Expertise Tag
                            </button>
                        </div>
                    </div>

                    <div class="col-5">
                        <!-- Vacant Time -->
                        <div class="col-12 mb-2">
                            <label for="vacant_time" class="form-label">Vacant Time</label>
                            <div id="vacantTimeRepeater">
                                @php
                                    $oldDays = old('vacant_time.day', ['']);
                                    $oldStarts = old('vacant_time.start_time', ['']);
                                    $oldEnds = old('vacant_time.end_time', ['']);
                                @endphp

                                @foreach ($oldDays as $index => $day)
                                    <div class="input-group mt-1 mb-2 vacant-time-item">
                                        <select name="vacant_time[day][]" class="form-select">
                                            <option value="" disabled {{ $day == '' ? 'selected' : '' }}>Select Day
                                            </option>
                                            @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $d)
                                                <option value="{{ $d }}" {{ $day == $d ? 'selected' : '' }}>
                                                    {{ $d }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="time" name="vacant_time[start_time][]" class="form-control"
                                            value="{{ $oldStarts[$index] ?? '' }}">
                                        <input type="time" name="vacant_time[end_time][]" class="form-control"
                                            value="{{ $oldEnds[$index] ?? '' }}">
                                        <button type="button" class="btn btn-danger remove-vacant-time">x</button>
                                    </div>
                                @endforeach

                                @error('vacant_time.day.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                @error('vacant_time.start_time.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                @error('vacant_time.end_time.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" id="addVacantTimeBtn">
                                Add Vacant Time
                            </button>
                        </div>

                        <!-- Position -->
                        <div class="col-12 mb-2">
                            <label for="position" class="form-label">Position</label>
                            <input type="text" name="position" placeholder="Enter your position"
                                class="form-control @error('position') is-invalid @enderror" id="position"
                                value="{{ old('position') }}">
                            @error('position')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="col-12 mb-2">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" placeholder="Enter your password"
                                class="form-control @error('password') is-invalid @enderror" id="password">
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-12 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" placeholder="Confirm your password"
                                class="form-control" id="password_confirmation">
                        </div>

                        <div class="m-auto text-center">
                            <button class="btn w-100 btn-primary" type="submit">
                                Add Instructor / Panelist
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection