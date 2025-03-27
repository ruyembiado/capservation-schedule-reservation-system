{{-- Login Modal --}}
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-transparent border-0">
            <form class="row g-3 needs-validation" action="{{ route('auth.login') }}" method="POST">
                @csrf
                <div class="login-modal card shadow mb-3 col-12 bg-transparent border border-light">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="col-6">
                                <div class="pt-4 pb-2">
                                    <h5 class="card-title text-start pb-0 fs-4">Log in</h5>
                                    <hr class="text-light">
                                </div>

                                {{-- Email Input --}}
                                <div class="col-12 mb-2">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" placeholder="Enter your email"
                                        class="form-control @error('email') is-invalid @enderror" id="email"
                                        value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Password Input --}}
                                <div class="col-12 mb-2">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" placeholder="Enter your password"
                                        class="form-control @error('password') is-invalid @enderror" id="password">
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="login-logo-container m-auto text-center">
                                <img class="m-auto" src="{{ asset('img/capservation-logo.png') }}"
                                    alt="capservation-logo" width="100" height="100">
                                <div class="d-flex flex-column justify-content-center align-items-center">
                                    <span>CCS CAPSTONE</span>
                                    <span>RESERVATION</span>
                                </div>
                                <div class="mt-5">
                                    <div class="d-flex gap-1">
                                        <a class="btn w-100" href="/"
                                            style="background-color: #65ABEE; border-radius: 8px;">Return</a>
                                        <button class="btn w-100" style="background-color: #65ABEE; border-radius: 8px;"
                                            type="submit">Login</button>
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
