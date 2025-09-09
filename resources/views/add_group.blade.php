@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Add Group</h1>
    </div>
    <div class="card shadow col-12 mb-4">
        <div class="card-body p-4">
            <form class="row g-3 needs-validation" action="{{ route('auth.register') }}" method="POST">
                @csrf
                <input type="hidden" name="user_type" value="student">
                <div class="d-flex align-items-start justify-content-center gap-5">
                    <div class="col-5">
                        <div class="col-12 mb-2">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" placeholder="Enter your email address"
                                class="form-control @error('email') is-invalid @enderror" id="email"
                                value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label for="username" class="form-label">Username/Group Name</label>
                            <input type="text" name="username" placeholder="Enter your username/group name"
                                class="form-control @error('username') is-invalid @enderror" id="username"
                                value="{{ old('username') }}">
                            @error('username')
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
                        <div class="col-12 mb-2">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" placeholder="Confirm your passsword"
                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                id="password_confirmation">
                            @error('password_confirmation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label for="members" class="form-label">Members</label>
                            <div id="membersRepeater">
                                @forelse (old('members', []) as $index => $member)
                                    <div class="input-group mb-2">
                                        <input type="text" name="members[]" value="{{ $member }}"
                                            class="form-control @error("members.$index") is-invalid @enderror"
                                            placeholder="Member Name">
                                        <button type="button" class="btn btn-danger remove-member">&times;</button>
                                        @error("members.$index")
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @empty
                                    <div class="input-group mb-2">
                                        <input type="text" name="members[]" class="form-control"
                                            placeholder="Member Name">
                                        <button type="button" class="btn btn-danger remove-member">&times;</button>
                                    </div>
                                @endforelse
                            </div>
                            <button type="button" class="btn btn-primary mt-2" id="addMemberBtn">Add
                                Member</button>
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="col-12 mb-2">
                            <label for="program" class="form-label">Program</label>
                            <select name="program" id="program"
                                class="form-control @error('program') is-invalid @enderror">
                                <option value="">-- Select Program --</option>
                                <option value="BSIT" {{ old('program') == 'BSIT' ? 'selected' : '' }}>BSIT
                                </option>
                                <option value="BSCS" {{ old('program') == 'BSCS' ? 'selected' : '' }}>BSCS
                                </option>
                                <option value="BSIS" {{ old('program') == 'BSIS' ? 'selected' : '' }}>BSIS
                                </option>
                            </select>
                            @error('program')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label for="yearsection" class="form-label">Year & Section</label>
                            <input type="text" name="yearsection" placeholder="Enter your year and section"
                                class="form-control @error('yearsection') is-invalid @enderror" id="yearsection"
                                value="{{ old('yearsection') }}">
                            @error('yearsection')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            <label for="capstone_adviser" class="form-label">Capstone Adviser</label>
                            <input type="text" name="capstone_adviser" placeholder="Enter your capstone adviser"
                                class="form-control @error('capstone_adviser') is-invalid @enderror" id="capstone_adviser"
                                value="{{ old('capstone_adviser') }}">
                            @error('capstone_adviser')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-2">
                            {{-- <label for="instructor" class="form-label">Instructor</label>
                                    <div class="custom-select-container">
                                        <div class="custom-select" id="custom-select">-- Select an instructor --</div>
                                        <div class="custom-dropdown" id="custom-dropdown">
                                            @foreach ($instructors as $instructor)
                                                <div data-value="{{ $instructor->id }}"
                                                    class="{{ old('instructor') == $instructor->id ? 'selected' : '' }}">
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
                                                {{ old('instructor') == $instructor->id ? 'selected' : '' }}>
                                                {{ $instructor->name }}
                                            </option>
                                        @endforeach
                                    </select> --}}
                            <input type="hidden" name="code" placeholder="Enter your instructor code"
                                class="form-control @error('code') is-invalid @enderror" id="code"
                                value="{{ auth()->user()->code }}">

                            @error('code')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-3 text-end">
                            <button class="btn btn-primary text-light" type="submit">Add Group</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const repeater = document.getElementById("membersRepeater");
        const addMemberBtn = document.getElementById("addMemberBtn");

        if (!repeater || !addMemberBtn) return; // avoid null error

        addMemberBtn.addEventListener("click", function() {
            let newItem = document.createElement("div");
            newItem.classList.add("input-group", "mb-2");
            newItem.innerHTML = `
            <input type="text" name="members[]" class="form-control" placeholder="Member Name">
            <button type="button" class="btn btn-danger remove-member">&times;</button>
        `;
            repeater.appendChild(newItem);
        });

        repeater.addEventListener("click", function(e) {
            if (e.target.classList.contains("remove-member")) {
                e.target.closest(".input-group").remove();
            }
        });
    });
</script>
