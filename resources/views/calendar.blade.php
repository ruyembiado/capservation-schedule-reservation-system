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
                        <h4 class="add-events-title">Add Events</h4>
                        <form action="" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="event-title" class="form-label">Select Category</label>
                                <select name="event_category" class="form-control" id="event_category">
                                    <option value="">-- Select Category --</option>
                                    <option value="available">Available</option>
                                    <option value="occupied">Occupied</option>
                                    <option value="unavailable">Unavailable</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="event-date" class="form-label">Event Date</label>
                                <input type="date" class="form-control" id="event-date" name="event-date">
                            </div>
                            <div class="mb-3">
                                <label for="event-time" class="form-label">Event Time</label>
                                <input type="time" class="form-control" id="event-time" name="event-time">
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
