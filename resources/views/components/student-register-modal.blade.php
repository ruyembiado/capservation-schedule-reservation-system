{{-- Student Registration Modal --}}
@props(['instructors' => []])
<div class="modal fade" id="studentRegistration" tabindex="-1" aria-labelledby="studentRegistrationLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0">
            <form class="row g-3 needs-validation" action="{{ route('auth.register') }}" method="POST">
                @csrf
                <input type="hidden" name="user_type" value="student">
                <div class="login-modal card shadow mb-3 col-12 bg-transparent border border-light p-0">
                    <div class="card-body">
                        <div class="pt-4 pb-2">
                            <h5 class="card-title text-start pb-0 fs-4">Register</h5>
                            <hr class="text-light">
                        </div>
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
                                    <input type="password" name="password_confirmation"
                                        placeholder="Confirm your passsword"
                                        class="form-control @error('password') is-invalid @enderror"
                                        id="password_confirmation">
                                </div>
                                <div class="col-12 mb-2">
                                    <label for="members" class="form-label">Members</label>
                                    <div id="membersRepeater">
                                        @forelse (old('members', []) as $index => $member)
                                            <div class="input-group mb-2">
                                                <input type="text" name="members[]" value="{{ $member }}"
                                                    class="form-control @error("members.$index") is-invalid @enderror"
                                                    placeholder="Member Name">
                                                <button type="button"
                                                    class="btn btn-danger remove-member">&times;</button>
                                                @error("members.$index")
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        @empty
                                            <div class="input-group mb-2">
                                                <input type="text" name="members[]" class="form-control"
                                                    placeholder="Member Name">
                                                <button type="button"
                                                    class="btn btn-danger remove-member">&times;</button>
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
                                    <input type="text" name="capstone_adviser"
                                        placeholder="Enter your capstone adviser"
                                        class="form-control @error('capstone_adviser') is-invalid @enderror"
                                        id="capstone_adviser" value="{{ old('capstone_adviser') }}">
                                    @error('capstone_adviser')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 mb-2">
                                    <label for="instructor" class="form-label">Instructor</label>
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
                                    </select>

                                    @error('instructor')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="login-logo-container m-auto text-center pt-3">
                                    <img class="m-auto" src="{{ asset('img/capservation-logo-hd.png') }}"
                                        alt="capservation-logo" style="width: 174px; height: 150px;">
                                    <div class="d-flex flex-column justify-content-center align-items-center">
                                        <span>REGISTER TO CCS</span>
                                        <span>RESERVATION</span>
                                    </div>
                                    <div class="mt-3">
                                        <div class="d-flex gap-1">
                                            <a class="btn w-100" href="/"
                                                style="background-color: #65ABEE; border-radius: 8px;">Return</a>
                                            <button class="btn w-100"
                                                style="background-color: #65ABEE; border-radius: 8px;"
                                                type="submit">Register</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const repeater = document.getElementById("membersRepeater");
    const addMemberBtn = document.getElementById("addMemberBtn");

    // Add new input field on button click
    addMemberBtn.addEventListener("click", function() {
        let newItem = document.createElement("div");
        newItem.classList.add("input-group", "mb-2");
        newItem.innerHTML = `
            <input type="text" name="members[]" class="form-control" placeholder="Member Name">
            <button type="button" class="btn btn-danger remove-member">x</button>
        `;
        repeater.appendChild(newItem);
    });

    // Remove input field when clicking the remove button
    repeater.addEventListener("click", function(e) {
        if (e.target.classList.contains("remove-member")) {
            e.target.closest(".input-group").remove();
        }
    });
    
</script>
