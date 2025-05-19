@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Instructor Code</h1>
    </div>
    <div class="card shadow col-4 mb-4">
        <div class="card-body">
            @if ($user->code == null)
                <form action="{{ route('code.create') }}" method="POST">
                    @csrf
                    <input type="hidden" name="instructor_id" value="{{ auth()->user()->id }}">
                    <label for="code" class="form-label">Code</label>
                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror">
                    @error('code')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div class="mt-3 text-end">
                        <button class="btn btn-primary text-light" type="submit">Add</button>
                    </div>
                </form>
            @else
                <div class="my-2">
                    <h6 style="font-weight: 700">Code</h6>
                    <p class="mb-0">{{ $user->code }}</p>
                </div>
            @endif
        </div>
    </div>
    <!-- Content Row -->
@endsection
