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
                    <input type="name" name="name" placeholder="Enter panelist name"
                        class="form-control @error('name') is-invalid @enderror" id="name"
                        value="{{ old('name') }}">
                    @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 mb-2">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" name="email" placeholder="Enter panelist email address"
                        class="form-control @error('email') is-invalid @enderror" id="email"
                        value="{{ old('email') }}">
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-3 text-end">
                   <button class="btn btn-primary text-light" type="submit">Add Panelist</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->
