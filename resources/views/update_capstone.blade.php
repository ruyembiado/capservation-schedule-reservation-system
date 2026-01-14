@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Update Capstone</h1>
    </div>
    <div class="card shadow col-12 mb-4">
        <div class="card-body">
            <form action="{{ route('capstone.update', implode(',', $capstones->pluck('id')->toArray())) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    @foreach ($capstones as $capstone)
                        <div class="col-12 col-sm-6 col-md-4 col-lg-4 capstone-container">
                            <div class="card p-3 mb-3">
                                <div class="mb-2">
                                    <label class="form-label">Title {{ $loop->iteration }}</label>
                                    <textarea name="title[]" class="form-control @error('title_' . $loop->iteration) is-invalid @enderror" rows="3">{{ $capstone->title }}</textarea>
                                    @error('title_' . $loop->iteration)
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Attachment {{ $loop->iteration }}</label>
                                    <input type="file" class="form-control" name="attachment_{{ $loop->iteration }}">
                                    @error('attachment_' . $loop->iteration)
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Capstone Status</label>
                                    <input type="text" disabled class="form-control"
                                        value="{{ Str::title(Str::replace('_', ' ', $capstone->capstone_status)) }}">
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Status</label>
                                    @php
                                        $today = now()->toDateString();
                                    @endphp
                                    <select name="title_status[]" @if (
                                        ($reservation->status == 'pending' || $reservation->status == 'reserved') &&
                                            $today < optional($schedule)->schedule_date) disabled @endif
                                        class="form-select @error('title_status_' . $loop->iteration) is-invalid @enderror">
                                        <option value="defended" @if ($capstone->title_status == 'defended') selected @endif>Defended
                                        </option>
                                        <option value="pending" @if ($capstone->title_status == 'pending') selected @endif>Pending
                                        </option>
                                        <option value="rejected" @if ($capstone->title_status == 'rejected') selected @endif>Rejected
                                        </option>
                                        <option value="redefense" @if ($capstone->title_status == 'redefense') selected @endif>
                                            Re-defense</option>
                                    </select>
                                    @error('title_status_' . $loop->iteration)
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
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

    <!-- Capstone Update History -->
    <div class="card shadow col-12 col-sm-6 mb-3">
        <div class="card-body">
            <h5 class="mb-3">Capstone Update History</h5>
            @foreach ($capstones as $capstone)
                <div class="mb-3">
                    <strong>{{ $capstone->title }}</strong>
                    @if ($capstone->histories->isNotEmpty())
                        <ul class="list-group list-group-flush mt-2">
                            @foreach ($capstone->histories as $history)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $history->old_capstone_name }}</span>
                                    <small class="text-muted">
                                        updated by {{ $history->editor->username }} on
                                        {{ $history->created_at->format('Y-m-d h:i A') }}
                                    </small>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mt-2">No history yet.</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Content Row -->
@endsection <!-- End the content section -->
