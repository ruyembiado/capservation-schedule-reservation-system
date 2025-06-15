@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">View Panelist</h1>
    </div>
    <div class="card shadow col-6 mb-4">
        <div class="card-body">
            {{-- <form action="{{ route('panelist.update', $panelist->id) }}" method="POST"> --}}
            @csrf
            <div class="col-12 mb-2">
                <label for="name" class="form-label">Name</label>
                <input type="name" readonly name="name" placeholder="Enter panelist name"
                    class="form-control @error('name') is-invalid @enderror" id="name" value="{{ $panelist->name }}">
                @error('name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 mb-2">
                <label for="email" class="form-label">Email</label>
                <input type="text" readonly name="email" placeholder="Enter panelist email address"
                    class="form-control @error('email') is-invalid @enderror" id="email" value="{{ $panelist->email }}">
                @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 mb-2">
                <label for="vacant_time" class="form-label">Vacant Time</label>
                <div id="vacantTimeRepeater">
                    @php
                        $vacantTimes = old('vacant_time.day', json_decode($panelist->vacant_time, true) ?? ['']);
                    @endphp
                    @if (!empty($vacantTimes) && is_array($vacantTimes) && count(array_filter($vacantTimes)) > 0)
                        @foreach ($vacantTimes as $index => $vacantTime)
                            <div class="input-group mt-1 mb-2 vacant-time-item">
                                <select disabled name="vacant_time[day][]"
                                    class="form-select @error("vacant_time.day.$index") is-invalid @enderror">
                                    <option value="" disabled>Select Day</option>
                                    @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                                        <option value="{{ $day }}"
                                            {{ isset($vacantTime['day']) && $vacantTime['day'] == $day ? 'selected' : '' }}>
                                            {{ $day }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="time" readonly name="vacant_time[start_time][]"
                                    class="form-control @error("vacant_time.start_time.$index") is-invalid @enderror"
                                    value="{{ old("vacant_time.start_time.$index", $vacantTime['start_time'] ?? '') }}">
                                <input type="time" readonly name="vacant_time[end_time][]"
                                    class="form-control @error("vacant_time.end_time.$index") is-invalid @enderror"
                                    value="{{ old("vacant_time.end_time.$index", $vacantTime['end_time'] ?? '') }}">
                                {{-- <button type="button" class="btn btn-danger remove-vacant-time">x</button> --}}
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
                    @else
                         <span class="text-muted">No vacant time available</span>
                    @endif
                </div>
                {{-- <button type="button" class="btn btn-sm btn-primary" id="addVacantTimeBtn">Add Vacant Time</button> --}}
            </div>
            <div class="col-12 mb-2">
                <label for="credentials" class="form-label">Credentials</label>
                <div id="credentialsRepeater">
                    @php
                        $credentials = old('credentials', json_decode($panelist->credentials, true) ?? ['']);
                    @endphp
                    @if (!empty($credentials) && is_array($credentials) && count(array_filter($credentials)) > 0)
                        @foreach ($credentials as $index => $credential)
                            <div class="input-group mb-2 credential-item">
                                <input readonly type="text" name="credentials[]"
                                    class="form-control @error("credentials.$index") is-invalid @enderror"
                                    value="{{ $credential }}" placeholder="Enter credential">
                                <button type="button" class="btn btn-danger remove-credential">x</button>
                                @error("credentials.$index")
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach
                    @else
                        <span class="text-muted">No credentials available</span>
                    @endif
                </div>
                {{-- <button type="button" class="btn btn-sm btn-primary" id="addCredentialBtn">Add Credential</button> --}}
            </div>
            {{-- <div class="mt-3 text-end">
                    <button class="btn btn-primary text-light" type="submit">Update</button>
                </div> --}}
            {{-- </form> --}}
        </div>
    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->
