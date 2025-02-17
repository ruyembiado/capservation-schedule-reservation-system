@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Calendar</h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Calendar Container -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div id="FullCalendar" class="col-8"></div>
                    <div class="add-events-container col-4 p-3">
                        <h4 class="add-events-title">Add Schedule</h4>
                        <form action="{{ route('schedule.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="group" class="form-label">Select group</label>
                                <select name="group" class="form-control" id="group"></select>
                                @error('group')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="schedule_date" class="form-label">Schedule Date</label>
                                <input type="date" class="form-control"
                                    min="{{ now()->format('Y-m-d') }}" id="schedule_date" value="{{ old('schedule_date') }}">
                                @error('schedule_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="schedule_time" class="form-label">Schedule Time</label>
                                <input type="time" class="form-control" id="schedule_time" name="schedule_time"
                                    value="{{ old('schedule_time') }}">
                                @error('schedule_time')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="schedule_category" class="form-label">Select Category</label>
                                <select name="schedule_category" class="form-control" id="schedule_category">
                                    <option value="">-- Select Category --</option>
                                    <option value="available"
                                        {{ old('schedule_category') == 'available' ? 'selected' : '' }}>
                                        Available</option>
                                    <option value="occupied" {{ old('schedule_category') == 'occupied' ? 'selected' : '' }}>
                                        Occupied</option>
                                    <option value="unavailable"
                                        {{ old('schedule_category') == 'unavailable' ? 'selected' : '' }}>Unavailable
                                    </option>
                                </select>
                                @error('schedule_category')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="schedule_remarks" class="form-label">Remarks</label>
                                <textarea name="schedule_remarks" class="form-control" id="schedule_remarks" cols="10" rows="5"></textarea>
                                @error('schedule_remarks')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Add Event</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
@endsection <!-- End the content section -->
