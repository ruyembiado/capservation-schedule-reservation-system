{{-- Instructor Registration Modal --}}
<div class="modal fade" id="instructorRegistration" tabindex="-1" aria-labelledby="instructorRegistrationLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0">
            <form class="row g-3 needs-validation" action="{{ route('auth.register') }}" method="POST">
                @csrf
                <input type="hidden" name="user_type" value="instructor">
                <div class="login-modal card shadow mb-3 col-12 bg-transparent border border-light p-0">
                    <div class="card-body">
                        <div class="pt-4 pb-2">
                            <h5 class="card-title text-start pb-0 fs-4">Register</h5>
                            <hr class="text-light">
                        </div>
                        <div class="d-flex justify-content-center gap-5">
                            <div class="col-5">
                                <div class="col-12 mb-2">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" name="email" placeholder="Enter your email address"
                                        class="form-control @error('email') is-invalid @enderror" id="email" value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 mb-2">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="name" name="name" placeholder="Enter your full name"
                                        class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name') }}">
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 mb-2">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="username" name="username" placeholder="Enter your username"
                                        class="form-control @error('username') is-invalid @enderror" id="username" value="{{ old('username') }}">
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
                            </div>
                            <div class="col-5">
                                <div class="col-12 mb-2">
								    <label for="position" class="form-label">Position</label>
								    <select name="position" id="position" 
								        class="form-control @error('position') is-invalid @enderror">
								        <option value="">-- Select Position --</option>
								        <option value="BSIT Course Instructor" {{ old('position') == 'BSIT Course Instructor' ? 'selected' : '' }}>BSIT Course Instructor</option>
								        <option value="BSCS Course Instructor" {{ old('position') == 'BSCS Course Instructor' ? 'selected' : '' }}>BSCS Course Instructor</option>
								        <option value="BSIS Course Instructor" {{ old('position') == 'BSIS Course Instructor' ? 'selected' : '' }}>BSIS Course Instructor</option>
								        <option value="Panelist" {{ old('position') == 'Panelist' ? 'selected' : '' }}>Panelist</option>
								    </select>
								    @error('position')
								        <div class="invalid-feedback d-block">{{ $message }}</div>
								    @enderror
								</div>
                                <div class="login-logo-container m-auto text-center mt-5">
                                    <img class="m-auto" src="{{ asset('img/capservation-logo-hd.png') }}"
                                        alt="capservation-logo" width="100" height="100">
                                    <div class="d-flex flex-column justify-content-center align-items-center">
                                        <span>REGISTER TO CCS</span>
                                        <span>RESERVATION</span>
                                    </div>
                                    <div class="mt-5">
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
