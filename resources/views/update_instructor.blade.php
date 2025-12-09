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

                        <!-- <div class="col-12 mb-2">
                            <label for="capacity" class="form-label">Capacity</label>
                            <div class="col-2">
                                <input type="number" name="capacity"
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
                                        <input type="text" name="credentials[]" class="form-control"
                                            value="{{ $credential }}" placeholder="Enter Expertise Tag">
                                        <button type="button" class="btn btn-danger remove-credential">x</button>
                                        @error("credentials.$index")
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" id="addCredentialBtn">Add
                                Expertise Tag</button>
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
                                        <select name="vacant_time[day][]"
                                            class="form-select @error("vacant_time.day.$index") is-invalid @enderror">
                                            <option value="" disabled>Select Day</option>
                                            @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                                                <option value="{{ $day }}"
                                                    {{ isset($vacantTime['day']) && $vacantTime['day'] == $day ? 'selected' : '' }}>
                                                    {{ $day }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="time" name="vacant_time[start_time][]"
                                            class="form-control @error("vacant_time.start_time.$index") is-invalid @enderror"
                                            value="{{ old("vacant_time.start_time.$index", $vacantTime['start_time'] ?? '') }}">
                                        <input type="time" name="vacant_time[end_time][]"
                                            class="form-control @error("vacant_time.end_time.$index") is-invalid @enderror"
                                            value="{{ old("vacant_time.end_time.$index", $vacantTime['end_time'] ?? '') }}">
                                        <button type="button" class="btn btn-danger remove-vacant-time">x</button>
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
                            <button type="button" class="btn btn-sm btn-primary" id="addVacantTimeBtn">Add Vacant
                                Time</button>
                        </div> -->

                        <div class="col-12 mb-2">
						    <label for="position" class="form-label">Position</label>
						    <select name="position" id="position" 
						        class="form-control @error('position') is-invalid @enderror">
						        <option value="">-- Select Position --</option>
						        <option value="BSIT Course Instructor" {{ $instructor->position == 'BSIT Course Instructor' ? 'selected' : '' }}>BSIT Course Instructor</option>
						        <option value="BSCS Course Instructor" {{ $instructor->position == 'BSCS Course Instructor' ? 'selected' : '' }}>BSCS Course Instructor</option>
						        <option value="BSIS Course Instructor" {{ $instructor->position == 'BSIS Course Instructor' ? 'selected' : '' }}>BSIS Course Instructor</option>
						        <option value="Panelists" {{ $instructor->position == 'Panelists' ? 'selected' : '' }}>Panelists</option>
						    </select>
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
