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
                                <select name="group" class="form-control select2" id="group_schedule">
                                    <option value="">-- Select Group --</option>
                                    @foreach ($reservations as $reservation)
                                        <option value="{{ $reservation->user->id }}"
                                            {{ old('group') == $reservation->user->id ? 'selected' : '' }}>
                                            {{ Str::ucfirst($reservation->user->username) }}</option>
                                    @endforeach
                                </select>
                                @error('group')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="schedule_date" class="form-label">Schedule Date</label>
                                <input type="date" name="schedule_date" class="form-control"
                                    min="{{ now()->format('Y-m-d') }}" id="schedule_date"
                                    value="{{ old('schedule_date') }}">
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
                                    <option value="occupied"
                                        {{ old('schedule_category') == 'occupied' ? 'selected' : '' }}>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('FullCalendar');

        if (calendarEl) {
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                selectable: true,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,list'
                },
                events: function(fetchInfo, successCallback, failureCallback) {
                    fetch('/schedules')
                        .then(response => response.json())
                        .then(data => {
                            console.log("Events Loaded:", data);
                            successCallback(data);
                        })
                        .catch(error => {
                            console.error("Error loading events:", error);
                            failureCallback(error);
                        });
                },
                eventContent: function(arg) {
                    return {
                        html: `<div class="fc-event-time">
                                ${arg.event.start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true })}
                           </div>
                           <div class="fc-event-title">
                                ${arg.event.title}
                           </div>`
                    };
                },
                validRange: {
                    start: null
                },
                selectAllow: function(selectInfo) {
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const startDate = new Date(selectInfo.start);
                    startDate.setHours(0, 0, 0, 0);

                    return startDate > today;
                },
                dateClick: function(info) {
                    let clickedDate = new Date(info.dateStr);
                    let today = new Date();
                    today.setHours(0, 0, 0, 0);
                    clickedDate.setHours(0, 0, 0, 0);

                    if (clickedDate > today) {
                        let scheduleDateEl = document.getElementById('schedule_date');
                        if (scheduleDateEl) {
                            scheduleDateEl.value = info.dateStr;
                        }
                    }
                },
                dayCellDidMount: function(info) {
                    let today = new Date().setHours(0, 0, 0, 0);
                    let cellDate = new Date(info.date).setHours(0, 0, 0, 0);

                    if (cellDate <= today) {
                        info.el.style.backgroundColor = "#f8d7da";
                        info.el.style.color = "#6c757d";
                        info.el.style.pointerEvents = "none";
                        info.el.style.opacity = "0.6";
                    }
                },
                select: function(info) {
                    let dateTimeParts = info.startStr.split('T');
                    let selectedDate = dateTimeParts[0];
                    let selectedTime = dateTimeParts[1]?.slice(0, 5) || '';

                    let scheduleDateEl = document.getElementById('schedule_date');
                    let scheduleTimeEl = document.getElementById('schedule_time');

                    if (scheduleDateEl) {
                        scheduleDateEl.value = selectedDate;
                    }

                    if (scheduleTimeEl) {
                        scheduleTimeEl.value = selectedTime;
                    }
                },
                selectMirror: true,
                slotDuration: '00:30:00',
                slotMinTime: '08:00:00',
                slotMaxTime: '18:00:00',
            });

            calendar.render();
        }
    });
</script>
