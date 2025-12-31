@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Content Row -->
    <div class="row">
    	<div class="col-12">
	        <div class="card shadow mb-3 mb-lg-0">
	            <div class="card-body">
	                <div class="d-flex justify-content-between align-items-center my-4 position-relative">
	                    @php
	                        $steps = [
	                            '1' => 'Awaiting Reservations',
	                            '2' => 'Payment',
	                            '3' => 'Scheduling',
	                            '4'  => 'Downloadables',
	                        ];
	                        $currentStatus = 2;
	                        $currentIndex  = $currentStatus;
	                    @endphp
	
	                    @foreach ($steps as $key => $icon)
	                        <div class="text-center flex-fill position-relative">
	                            <div class="rounded-circle step-icon
	                                {{ $loop->index <= $currentIndex ? 'bg-theme-primary text-white' : 'bg-light text-dark border' }}
	                                d-flex align-items-center justify-content-center mx-auto">
	                                {{ $loop->index + 1 }}
	                            </div>
	
	                            @if (! $loop->last)
	                                <div class="step-connector
	                                    {{ $loop->index < $currentIndex ? 'bg-theme-primary' : 'bg-light' }}">
	                                </div>
	                            @endif
	                        </div>
	                    @endforeach
	                </div>
	
	                <style>
	                    .step-icon {
	                        width: 50px;
	                        height: 50px;
	                        font-size: 20px;
	                        z-index: 2 !important;
	                    }
	                    .step-connector {
	                        position: absolute;
	                        top: 25px;
	                        left: 56.4%;
	                        width: 87.5%;
	                        height: 4px;
	                        z-index: 1 !important;
	                    }
	                    /* Small helper: ensure panel-box fills height */
	                    .panel-box { min-height: 170px; }
	                </style>
	
	                <div class="d-sm-flex align-items-center justify-content-center mb-4">
	                    <h1 class="h3 mb-0">Scheduling</h1>
	                </div>
	            </div>
	        <!-- Calendar Container -->
	                <div class="d-flex justify-content-between px-3 pb-4">
	                    <div id="FullCalendar" class="col-9"></div>
	                    <div class="add-events-container col-3 p-3">
	                        <h4 class="add-events-title">Select Schedule</h4>
	                        <form action="{{ route('assign.panelist.schedule') }}" method="POST">
	                            @csrf
	                            <input type="hidden" name="group_id" value='{{ $group_id }}'>
							    <input type="hidden" name="reservation_id" value='{{ $reservation_id }}'>
							    <input type="hidden" name="panelist_id" value='{{ $panelist_id }}'>
	                            <div class="mb-3">
	                                <label for="schedule_date" class="form-label">Schedule Date</label>
	                                <input type="date" name="schedule_date" class="form-control"
	                                    min="{{ now()->format('Y-m-d') }}" required id="schedule_date"
	                                    value="{{ old('schedule_date') }}">
	                                @error('schedule_date')
	                                    <div class="invalid-feedback d-block">{{ $message }}</div>
	                                @enderror
	                            </div>
	                            <div class="mb-3">
								    <label for="schedule_time" class="form-label">Available Time:</label>
								
								    <select class="form-select" id="schedule_time" name="schedule_time">
								        <option value="" selected disabled>-- Select Time --</option>
								
								        <optgroup label="Morning" class="bg-light">
								            <option value="08:00:00">8:00 AM - 9:00 AM</option>
								            <option value="09:00:00">9:00 AM - 10:00 AM</option>
								            <option value="10:00:00">10:00 AM - 11:00 AM</option>
								        </optgroup>
								
								        <optgroup label="Afternoon" class="bg-light">
								            <option value="14:00:00">2:00 PM - 3:00 PM</option>
								            <option value="15:00:00">3:00 PM - 4:00 PM</option>
								        </optgroup>
								    </select>
								
								    @error('schedule_time')
								        <div class="invalid-feedback d-block">{{ $message }}</div>
								    @enderror
								</div>
								<div class="text-end">
	                            	<button type="submit" class="btn btn-primary w-100">Assign Schedule</button>
	                            </div>
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

    const scheduleTimeSelect = document.getElementById("schedule_time");
    const scheduleDateEl = document.getElementById("schedule_date");

    // Store original options so we can reset anytime
    const originalOptionsHTML = scheduleTimeSelect.innerHTML;

    // Extract only HH:MM:SS from ISO datetime string
    function extractTime(datetime) {
        return datetime.split("T")[1].substring(0, 8);
    }

    // ---------------------------------------------
    // MAIN FUNCTION: FILTER TIME SLOTS
    // ---------------------------------------------
    function filterTimeSlots(selectedDate) {

        // Reset options to original
        scheduleTimeSelect.innerHTML = originalOptionsHTML;

        if (!selectedDate) return;

        fetch('/schedules')
            .then(res => res.json())
            .then(events => {

                // Identify already-used time slots on this date
                let takenTimes = events
                    .filter(ev => ev.start.startsWith(selectedDate))
                    .map(ev => extractTime(ev.start));

                console.log("Taken Times:", takenTimes);

                // Hide & disable taken time slots
                [...scheduleTimeSelect.options].forEach(opt => {
                    if (takenTimes.includes(opt.value)) {
                        opt.disabled = true;
                        opt.hidden = true;
                        opt.style.display = "none";
                    }
                });

            })
            .catch(err => console.log("Fetch error:", err));
    }

    // ---------------------------------------------
    // TRIGGER FILTER WHEN TYPING/CHOOSING DATE
    // ---------------------------------------------
    scheduleDateEl.addEventListener("change", function () {
        filterTimeSlots(this.value);
    });


    // ---------------------------------------------
    // FULLCALENDAR SETUP
    // ---------------------------------------------
    var calendarEl = document.getElementById('FullCalendar');

    function formatDateLocal(date) {
        let y = date.getFullYear();
        let m = String(date.getMonth() + 1).padStart(2, "0");
        let d = String(date.getDate()).padStart(2, "0");
        return `${y}-${m}-${d}`;
    }

    if (calendarEl) {
        var unavailableDates = [];

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            selectable: true,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,list'
            },

            // LOAD EVENTS
            events: function(fetchInfo, successCallback, failureCallback) {
                fetch('/schedules')
                    .then(response => response.json())
                    .then(data => {
                        unavailableDates = data
                            .filter(ev => ev.isUnavailable === true)
                            .map(ev => ev.start.split("T")[0]);

                        successCallback(data);
                    })
                    .catch(error => {
                        console.error("Error loading events:", error);
                        failureCallback(error);
                    });
            },

            // FORMAT EVENT BOX
            eventContent: function(arg) {
                let bg = arg.event.backgroundColor || '#3788d8';
                let color = arg.event.textColor || '#ffffff';

                return {
                    html: `
                    <div style="background:${bg};color:${color};padding:2px 4px;border-radius:4px; width: 100%;">
                        <div class="fc-event-time">
                            ${arg.event.start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true })}
                        </div>
                        <div class="fc-event-title" style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            ${arg.event.title}
                        </div>
                    </div>`
                };
            },

            // BLOCK PAST & UNAVAILABLE DATES
            selectAllow: function(selectInfo) {
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                const startDate = new Date(selectInfo.start);
                startDate.setHours(0, 0, 0, 0);

                const startDateStr = formatDateLocal(startDate);

                if (startDate <= today) return false;
                if (unavailableDates.includes(startDateStr)) return false;

                return true;
            },

            // ---------------------------------------------
            // WHEN USER CLICKS A DATE ON THE CALENDAR
            // ---------------------------------------------
            dateClick: function(info) {
                let clickedDate = new Date(info.date);
                let today = new Date();
                today.setHours(0, 0, 0, 0);
                clickedDate.setHours(0, 0, 0, 0);

                const dateStr = formatDateLocal(clickedDate);

                if (clickedDate > today && !unavailableDates.includes(dateStr)) {
                    scheduleDateEl.value = dateStr;

                    // 🔥 FILTER AVAILABLE TIME SLOTS
                    filterTimeSlots(dateStr);
                }
            },

            // STYLING CELLS
            dayCellDidMount: function(info) {
                let today = new Date();
                today.setHours(0, 0, 0, 0);

                let cellDate = new Date(info.date);
                cellDate.setHours(0, 0, 0, 0);

                let dateStr = formatDateLocal(info.date);

                if (cellDate <= today) {
                    info.el.style.backgroundColor = "#f8d7da";
                    info.el.style.color = "#6c757d";
                    info.el.style.pointerEvents = "none";
                    info.el.style.opacity = "0.6";
                }

                if (unavailableDates.includes(dateStr)) {
                    info.el.style.backgroundColor = "#ffcccc";
                    info.el.style.pointerEvents = "none";
                    info.el.style.opacity = "0.6";
                }
            }
        });

        calendar.render();

        // REFRESH EVERY 30 SECONDS
        setInterval(() => {
            calendar.refetchEvents();
        }, 30000);
    }
});
</script>

