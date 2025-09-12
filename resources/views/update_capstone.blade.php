@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Update Capstone</h1>
    </div>
    <div class="card shadow col-12 mb-4">
        <div class="card-body">
            <form action="{{ route('capstone.update', implode(',', $capstones->pluck('id')->toArray())) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="d-flex justify-content-between gab-5">
                    @foreach ($capstones as $capstone)
                        <div class="capstone-container col-3">
                            <div class="col-12 mb-2">
                                <label for="name" class="form-label">Title {{ $loop->iteration }}</label>
                                <textarea name="title[]" class="form-control @error('title_{{ $loop->iteration }}') is-invalid @enderror" id=""
                                    rows="3">{{ $capstone->title }}</textarea>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <label for="name" class="form-label mt-2">Attachment {{ $loop->iteration }}</label>
                                <input type="file" class="form-control" name="attachment_{{ $loop->iteration }}">
                                @error('attachment_{{ $loop->iteration }}')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-2">
                                <label for="name" class="form-label">Capstone Status</label>
                                <input type="text" disabled class="form-control"
                                    value="{{ Str::title(Str::replace('_', ' ', $capstone->capstone_status)) }}">
                            </div>
                            <div class="col-12 mb-4">
                                <label for="name" class="form-label">Status</label>
                                @php
                                    $today = now()->toDateString();
                                @endphp
                                <select name="title_status[]" @if (
                                    ($reservation->status == 'pending' || $reservation->status == 'reserved') &&
                                        $today < optional($schedule)->schedule_date) disabled @endif
                                    class="form-select @error('title_status_{{ $loop->iteration }}') is-invalid @enderror">
                                    <option value="defended" @if ($capstone->title_status == 'defended') selected @endif>Defended
                                    </option>
                                    <option value="pending" @if ($capstone->title_status == 'pending') selected @endif>Pending
                                    </option>
                                    <option value="rejected" @if ($capstone->title_status == 'rejected') selected @endif>Rejected
                                    </option>
                                    <option value="redefense" @if ($capstone->title_status == 'redefense') selected @endif>Re-defense
                                    </option>
                                </select>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3 text-end">
                    <button class="btn btn-primary text-light" type="submit">Update</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->
