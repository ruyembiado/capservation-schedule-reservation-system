@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Add Panelist</h1>
    </div>
    <div class="card shadow col-6 mb-4">
        <div class="card-body">
            <form action="{{ route('panelist.store') }}" method="POST">
                @csrf
                <div class="col-12 mb-2">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name="name" placeholder="Enter panelist name"
                        class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name') }}">
                    @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 mb-2">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" placeholder="Enter panelist email address"
                        class="form-control @error('email') is-invalid @enderror" id="email"
                        value="{{ old('email') }}">
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 mb-2">
                    <label for="vacant_time" class="form-label">Vacant Time</label>
                    <div id="vacantTimeRepeater">
                        @php
                            $vacantTimes = old('vacant_time.day', []) ?: [''];
                        @endphp
                        @foreach ($vacantTimes as $index => $day)
                            <div class="input-group mt-1 mb-2 vacant-time-item">
                                <select name="vacant_time[day][]"
                                    class="form-select @error("vacant_time.day.$index") is-invalid @enderror">
                                    <option value="" disabled>Select Day</option>
                                    <option value="Monday" {{ $day == 'Monday' ? 'selected' : '' }}>Monday</option>
                                    <option value="Tuesday" {{ $day == 'Tuesday' ? 'selected' : '' }}>Tuesday</option>
                                    <option value="Wednesday" {{ $day == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                                    <option value="Thursday" {{ $day == 'Thursday' ? 'selected' : '' }}>Thursday</option>
                                    <option value="Friday" {{ $day == 'Friday' ? 'selected' : '' }}>Friday</option>
                                </select>
                                <input type="time" name="vacant_time[start_time][]"
                                    class="form-control @error("vacant_time.start_time.$index") is-invalid @enderror"
                                    value="{{ old('vacant_time.start_time.' . $index) }}">
                                <input type="time" name="vacant_time[end_time][]"
                                    class="form-control @error("vacant_time.end_time.$index") is-invalid @enderror"
                                    value="{{ old('vacant_time.end_time.' . $index) }}">
                                <button type="button" class="btn btn-danger remove-vacant-time">x</button>
                            </div>
                            <!-- Error messages for each field -->
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
                    <button type="button" class="btn btn-sm btn-primary" id="addVacantTimeBtn">Add Vacant Time</button>
                </div>

                <div class="col-12 mb-2">
                    <label for="credentials" class="form-label">Credentials</label>
                    <div id="credentialsRepeater">
                        @php
                            $credentials = old('credentials', ['']);
                        @endphp
                        @foreach ($credentials as $index => $credential)
                            <div class="input-group mb-2 credential-item">
                                <input type="text" name="credentials[]" class="form-control" value="{{ $credential }}"
                                    placeholder="Enter credential">
                                <button type="button" class="btn btn-danger remove-credential">x</button>
                                @error("credentials.$index")
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" id="addCredentialBtn">Add Credential</button>
                </div>
                <div class="mt-3 text-end">
                    <button class="btn btn-primary text-light" type="submit">Add Panelist</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Content Row -->
@endsection
