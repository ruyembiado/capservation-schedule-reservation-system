@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Instructor Details</h1>
    </div>
    <div class="card shadow col-12 mb-4">
        <div class="card-body">
                <div class="d-flex align-items-start justify-content-center gap-5">
                    <div class="col-6">
                        <div class="col-12 mb-2">
                            <label for="email" class="form-label">Email</label>
                            <input readonly type="text" name="email" placeholder="Enter your email address"
                                class="form-control @error('email') is-invalid @enderror" id="email"
                                value="{{ $instructor->email }}">
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label for="name" class="form-label">Full Name</label>
                            <input readonly type="name" name="name" placeholder="Enter your full name"
                                class="form-control @error('name') is-invalid @enderror" id="name"
                                value="{{ $instructor->name }}">
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label for="username" class="form-label">Username</label>
                            <input readonly type="username" name="username" placeholder="Enter your username"
                                class="form-control @error('username') is-invalid @enderror" id="username"
                                value="{{ $instructor->username }}">
                            @error('username')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- <div class="col-12 mb-2">
                            <label for="capacity" class="form-label">Capacity</label>
                            <div class="col-2">
                                <input readonly type="number" name="capacity"
                                    class="form-control @error('capacity') is-invalid @enderror" id="capacity"
                                    value="{{ $instructor->capacity }}">
                            </div>
                            @error('capacity')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div> -->

                        <div class="col-12 mb-2">
                            <label for="credentials" class="form-label">Expertise Tags</label>
                            <div id="credentialsRepeater">
                                @php
                                    $credentials = old('credentials', json_decode($instructor->credentials) ?? ['']);
                                @endphp
                                @foreach ($credentials as $index => $credential)
                                    <div class="input-group mb-2 credential-item">
                                        <input readonly type="text" name="credentials[]" class="form-control"
                                            value="{{ $credential }}" placeholder="Enter Expertise Tag">
                                        @error("credentials.$index")
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-5">
                        <!-- <div class="col-12 mb-2">
                            <label for="vacant_time" class="form-label">Vacant Time</label>
                            <div id="vacantTimeRepeater">
                                @php
                                    $vacantTimes = old(
                                        'vacant_time.day',
                                        json_decode($instructor->vacant_time, true) ?? [''],
                                    );
                                @endphp
                                @foreach ($vacantTimes as $index => $vacantTime)
                                    <div class="input-group mt-1 mb-2 vacant-time-item">
                                        <select disabled name="vacant_time[day][]"
                                            class="form-select @error("vacant_time.day.$index") is-invalid @enderror">
                                            <option value="" >Select Day</option>
                                            @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                                                <option value="{{ $day }}"
                                                    {{ isset($vacantTime['day']) && $vacantTime['day'] == $day ? 'selected' : '' }}>
                                                    {{ $day }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input readonly type="time" name="vacant_time[start_time][]"
                                            class="form-control @error("vacant_time.start_time.$index") is-invalid @enderror"
                                            value="{{ old("vacant_time.start_time.$index", $vacantTime['start_time'] ?? '') }}">
                                        <input readonly type="time" name="vacant_time[end_time][]"
                                            class="form-control @error("vacant_time.end_time.$index") is-invalid @enderror"
                                            value="{{ old("vacant_time.end_time.$index", $vacantTime['end_time'] ?? '') }}">
                                    </div>
                                    @error("vacant_time.day.$index")
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    @error("vacant_time.start_time.$index")
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    @error("vacant_time.end_time.$index")
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                @endforeach
                            </div>
                        </div> -->

                        <div class="col-12 mb-2">
                            <label for="position" class="form-label">Position</label>
                            <input readonly type="position" name="position" placeholder="Enter your position"
                                class="form-control @error('position') is-invalid @enderror" id="position"
                                value="{{ $instructor->position }}">
                            @error('position')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->
