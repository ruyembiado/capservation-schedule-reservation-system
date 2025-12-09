@extends('layouts.app')

@section('content')
<div class="row gap-4">
    <!-- Smart Scheduler Controls -->
    <div class="col-12">
        <div class="card shadow mb-3 mb-lg-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center my-4 position-relative">
                    @php
                        $steps = [
                            '1' => 'Awaiting Reservations',
                            '2' => 'Payment',
                            '3' => 'Scheduling',
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

                <form action="{{ route('SmartScheduler', $group_id) }}" method="POST" class="col-12 col-lg-4 col-xl-3">
                    @csrf
                    <input type="hidden" name="runScheduler" value="true">

                    <div class="col-12 d-grid gap-2">
                        <button type="submit" class="btn btn-primary w-100">Run Smart Scheduler</button>
                        <a class="btn btn-danger w-100" href="{{ route('SmartScheduler', $group_id) }}">Clear Results</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Results Card -->
    <div class="col-12">
        <div class="card shadow mb-3 mb-lg-0">
            <div class="card-body">
                <h4 class="card-title mb-4 fw-normal text-dark text-center text-md-start">Smart Scheduler Result</h4>

                @if ($formatted || session('show_scheduler_result'))
                    <div id="schedulerResult">
                        <form action="{{ route('assignedPanelistsScheduler') }}" method="POST">
                            @csrf

                            @foreach ($formatted as $entry)
                                <div class="group-result mb-3 shadow-sm rounded border px-3 pt-3 bg-white">
                                    <div class="d-flex justify-content-start gap-2 align-items-start">
                                        <b>Name of Group:</b>
                                        <h5 class="fw-semibold mb-1">{{ ucfirst($entry['group']['name']) }}</h5>
                                    </div>

                                    <p class="mb-1"><b>Tags:</b> {{ implode(', ', $entry['group']['topic_tags']) }}</p>
                                    <p class="mb-1"><b>Adviser:</b>
                                        {{ !empty($entry['group']['conflicts']) ? implode(', ', $entry['group']['conflicts']) : 'None' }}
                                    </p>

                                    {{-- Group-level hidden inputs --}}
                                    <input type="hidden" name="group_id[]" value="{{ $entry['groupId'] ?? '' }}">
                                    <input type="hidden" name="reservation_id[]" value="{{ $entry['group']['reservation_id'] ?? '' }}">

                                    <p class="mb-1 p-0"><b>Panelists:</b></p>
                                    
									<div id="panelistError" class="alert alert-warning d-none text-center">
									    The group must have at least <b>5 panelists</b> before assigning.
									</div>

									<div class="row">
										<div class="col-12 col-md-4 mb-4">
                                            <div class="panel-box group-result shadow-sm rounded border px-3 pt-3 bg-white">
                                                <div class="panelist-select-box">
                                                    <label
                                                        class="fw-semibold
                                                        mb-1">Dean</label>
                                                    <div class="mt-2 small
                                                        text-dark panel-info">
                                                        <h6>{{ $dean_name }}</h6> <br>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4 mb-4">
                                            <div class="panel-box group-result shadow-sm rounded border px-3 pt-3 bg-white">
                                                <div class="panelist-select-box">
                                                    <label
                                                        class="fw-semibold
                                                        mb-1">Program Head</label>
                                                    <div class="mt-2 small
                                                        text-dark panel-info">
                                                        <h6>{{ $program_head_name }}</h6> <br>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
									</div>
                                    {{-- Panelists container (we'll place panelist cards inside) --}}
                                    <div class="row panelist-container" data-group="{{ $entry['groupId'] }}">

                                        {{-- Show conflict note if any --}}
                                        @if ($entry['conflict_note'])
                                            <div class="col-12">
                                                <span class="text-danger fw-normal d-block mb-2">{{ $entry['conflict_note'] }}</span>
                                            </div>
                                        @endif

                                        {{-- Existing panelists from the scheduler output --}}
                                        @foreach ((array) ($entry['panelists'] ?? []) as $panel)
                                            <div class="col-12 col-md-4 mb-4">
                                                <div class="panel-box group-result shadow-sm rounded border px-3 pt-3 bg-white" data-group="{{ $entry['groupId'] }}">
                                                    <div class="panelist-select-box" data-group="{{ $entry['groupId'] }}">
                                                        <label class="fw-semibold mb-1">Select Panelist:</label>

                                                        <div class="input-group">
                                                            <select class="form-select panelist-select"
                                                                    name="panelist_id[{{ $entry['groupId'] }}][]">
                                                                <option value="">-- Select Panelist --</option>

                                                                @foreach ($panelists as $p)
                                                                    <option value="{{ $p->id }}"
                                                                        data-expertise="{{ !empty($p->expertise) && is_array($p->expertise) ? implode(',', $p->expertise) : '' }}"
                                                                        data-score="0"
                                                                        {{ ($panel['instructor_id'] ?? '') == $p->id ? 'selected' : '' }}>
                                                                        {{ $p->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>

                                                            <button type="button" class="btn btn-outline-danger delete-panelist" title="Remove panelist field">✕</button>
                                                        </div>

                                                        <div class="mt-2 small
                                                        text-dark panel-info">
                                                            <b>Score:</b> <span class="panel-score">{{ $panel['score'] ?? 0 }}</span><br>
                                                            <b>Expertise:</b>
                                                            <span class="panel-expertise">
                                                                @if(!empty($panel['expertise']) && is_array($panel['expertise']))
                                                                    {{ implode(', ', $panel['expertise']) }}
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="text-end mb-4">
                                        <button type="button" class="btn btn-sm btn-primary mt-2 add-panelist" data-group="{{ $entry['groupId'] }}">+ Add Panelist</button>
                                    </div>
                                </div>
                            @endforeach

                            <div class="col-12 d-flex justify-content-end gap-2 mb-1">
                                <button type="submit" id="assignButton" class="btn btn-primary col-12 col-md-3">Assign Panelists</button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="alert alert-warning mt-3 text-center fw-normal">
                        No scheduling result available. Please run the Smart Scheduler.
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@if (isset($panelists))
{{-- Scripts --}}
<script>
document.addEventListener("DOMContentLoaded", function () {

	const assignBtn = document.getElementById("assignButton");
    const errorBox = document.getElementById("panelistError");

    function validatePanelists() {
        const selects = document.querySelectorAll(".panelist-select");
        const count = selects.length;

        let allFilled = true;

        selects.forEach(sel => {
            if (!sel.value || sel.value.trim() === "") {
                allFilled = false;
            }
        });

        if (count < 3 || !allFilled) {
            assignBtn.disabled = true;
            errorBox.classList.remove("d-none");
        } else {
            assignBtn.disabled = false;
            errorBox.classList.add("d-none");
        }
    }

    // Initial check
    validatePanelists();

    // Re-check when adding or removing a panelist
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('add-panelist') ||
            e.target.classList.contains('delete-panelist')) {

            setTimeout(validatePanelists, 50);
        }
    });

    // Re-check when dropdown value changes
    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('panelist-select')) {
            validatePanelists();
        }
    });

    // Panelist data from PHP (array of objects). If empty, array.
    const panelData = @json($panelists ?? []);

    // Helper: find panelist by id
    function findPanelistById(id) {
        if (!id) return null;
        return panelData.find(p => String(p.id) === String(id)) || null;
    }

    // Refresh dropdown options for a group to avoid duplicate picks
    function refreshPanelistOptionsForGroup(groupId) {
        const container = document.querySelectorAll(`.panelist-container[data-group="${groupId}"] .panelist-select`);
        if (!container) return;

        // Gather selected values
        let selected = [];
        container.forEach(s => { if (s.value) selected.push(String(s.value)); });

        // For each select option, hide options that are selected in other selects (but keep current select's value visible)
        container.forEach(select => {
            const current = String(select.value || '');
            select.querySelectorAll('option').forEach(option => {
                const val = String(option.value || '');
                if (val === '') {
                    option.hidden = false;
                } else if (val === current) {
                    option.hidden = false;
                } else {
                    option.hidden = selected.includes(val);
                }
            });
        });
    }

    // Create a new panelist card element (matches group-result styling)
    function createPanelistCard(groupId) {
        const wrapper = document.createElement('div');
        wrapper.className = 'col-12 col-md-4 mb-4';
        wrapper.innerHTML = `
            <div class="panel-box group-result shadow-sm rounded border px-3 pt-3 bg-white" data-group="${groupId}">
                <div class="panelist-select-box" data-group="${groupId}">
                    <label class="fw-semibold mb-1">Select Panelist:</label>

                    <div class="input-group">
                        <select class="form-select panelist-select" name="panelist_id[${groupId}][]">
                            <option value="">-- Select Panelist --</option>
                            @foreach ($panelists as $p)
                                <option value="{{ $p->id }}"
                                    data-expertise="{{ !empty($p->expertise) && is_array($p->expertise) ? implode(',', $p->expertise) : '' }}"
                                    data-score="0">
                                    {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-danger delete-panelist" title="Remove panelist field">✕</button>
                    </div>

                    <div class="mt-2 small text-muted panel-info">
                        <b>Score:</b> <span class="panel-score">0</span><br>
                        <b>Expertise:</b> <span class="panel-expertise">N/A</span>
                    </div>
                </div>
            </div>
        `;
        return wrapper;
    }

    // Initialize: refresh all groups once on load (in case of preselected)
    document.querySelectorAll('.panelist-container').forEach(pc => {
        const gid = pc.dataset.group;
        refreshPanelistOptionsForGroup(gid);
    });

    // Click add panelist
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.add-panelist');
        if (!btn) return;
        const groupId = btn.dataset.group;
        const container = document.querySelector(`.panelist-container[data-group="${groupId}"]`);
        if (!container) return;

        const newCard = createPanelistCard(groupId);
        // insert before add button (append to container is fine visually)
        container.appendChild(newCard);

        // Refresh options so new select hides already-chosen options
        refreshPanelistOptionsForGroup(groupId);
    });

    // Click delete panelist
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.delete-panelist');
        if (!btn) return;
        const panelBox = btn.closest('.col-12.col-md-4') || btn.closest('.panel-box');
        if (!panelBox) return;

        // find group's id for refreshing
        const groupId = panelBox.closest('[data-group]') ? panelBox.closest('[data-group]').dataset.group : null;

        // remove the whole column wrapper if used
        const colWrapper = panelBox.closest('.col-12.col-md-4');
        if (colWrapper) colWrapper.remove();
        else panelBox.remove();

        if (groupId) refreshPanelistOptionsForGroup(groupId);
    });

    // Change panelist select => update expertise + reset score + refresh duplicates
	document.addEventListener('change', function (e) {
	    const sel = e.target.closest('.panelist-select');
	    if (!sel) return;
	
	    const selectedId = sel.value;
	    const box = sel.closest('.panelist-select-box');
	    const groupId = box ? box.dataset.group : null;
	
	    // Reset score to "0"
	    const scoreEl = box.querySelector('.panel-score');
	    if (scoreEl) scoreEl.innerText = 'N/A';
	
	    const expertiseEl = box.querySelector('.panel-expertise');
	    const panel = findPanelistById(selectedId);
	
	    // Process credentials
	    let creds = [];
	
	    if (panel) {
	        if (Array.isArray(panel.credentials)) {
	            creds = panel.credentials;
	        } else if (typeof panel.credentials === "string") {
	            try {
	                creds = JSON.parse(panel.credentials.replace(/'/g, '"'));
	            } catch (e) {
	                creds = [];
	            }
	        }
	    }
	
	    // Set expertise text
	    if (expertiseEl) {
	        if (creds.length > 0) {
	            expertiseEl.innerText = creds.join(', ');
	        } else {
	            const fallback = sel.selectedOptions[0].dataset.credentials || '';
	            expertiseEl.innerText = fallback ? fallback.replace(/,/g, ', ') : 'N/A';
	        }
	    }
	
	    // Prevent duplicate panelists
	    if (groupId) refreshPanelistOptionsForGroup(groupId);
	});


    // When form submitted: (optional) ensure no duplicate panelist across the same group
    // (We already hide duplicates, but double-check)
    document.querySelector('form[action="{{ route('assignedPanelistsScheduler') }}"]')?.addEventListener('submit', function (evt) {
        const groups = {};
        let invalid = false;

        document.querySelectorAll('.panelist-container').forEach(container => {
            const gid = container.dataset.group;
            groups[gid] = [];
            container.querySelectorAll('.panelist-select').forEach(s => {
                if (s.value) groups[gid].push(s.value);
            });
            // check duplicates per group
            const list = groups[gid];
            const unique = Array.from(new Set(list));
            if (unique.length !== list.length) invalid = true;
        });

        if (invalid) {
            evt.preventDefault();
            alert('There are duplicate panelist selections within a group. Please remove duplicates before submitting.');
            return false;
        }
    });

});
</script>
@endif