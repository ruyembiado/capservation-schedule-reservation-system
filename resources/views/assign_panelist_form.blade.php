@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Assign Panelists</h1>
    </div>

    @php
        $selectedPanelists = json_decode($reservation->panelist_id, true) ?? [];
    @endphp

    <div class="d-flex justify-content-between flex-column">
        <!-- Panelists List -->
        <div class="card shadow col-12 mb-4">
            <div class="card-body">
                <div class="col-12 mb-2">
                    <h4 class="mb-3">Select Panelists</h4>

                    <div class="titles mb-3">
                        <strong>Capstone Title/s:</strong>
                        @foreach ($capstones as $capstone)
                            <li>
                                <i class="fa fa-book"></i> <strong>{{ $capstone->title }}</strong>
                            </li>
                        @endforeach
                        @if (count($capstones) == 0)
                            <li>No Capstone Assigned</li>
                        @endif
                    </div>


                    <div class="d-flex flex-wrap justify-content-center gap-4">
                        @foreach ($panelists as $panelist)
                            <div style="width: 31%;"
                                class="border rounded p-3 panelist-card {{ in_array($panelist->id, $selectedPanelists) ? 'bg-selected-panelist' : '' }}"
                                data-id="{{ $panelist->id }}">
                                <h5 style="font-weight: 600;" class="text-dark">{{ $panelist->name }}</h5>

                                <!-- Vacant Time -->
                                <div class="mb-2">
                                    <strong>Vacant Time:</strong>
                                    @php
                                        $vacantTimes = json_decode($panelist->vacant_time, true);
                                    @endphp

                                    @if ($vacantTimes && count($vacantTimes) > 0)
                                        <ul class="list-unstyled mb-0">
                                            @foreach ($vacantTimes as $vacantTime)
                                                <li>
                                                    {{ $vacantTime['day'] ?? '' }}:
                                                    {{ date('h:i A', strtotime($vacantTime['start_time'])) }} -
                                                    {{ date('h:i A', strtotime($vacantTime['end_time'])) }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted">No vacant time available</span>
                                    @endif
                                </div>

                                <!-- Credentials -->
                                <div class="mb-2">
                                    <strong>Credentials:</strong>
                                    @php
                                        $credentials = json_decode($panelist->credentials, true);
                                    @endphp

                                    @if ($credentials && count($credentials) > 0)
                                        <ul class="list-unstyled mb-0">
                                            @foreach ($credentials as $credential)
                                                <li>{{ $credential }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted">No credentials available</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @error('panelists')
                        <div class="alert alert-warning col-6 mt-2 m-auto text-center">
                            {{ $message }}
                        </div>
                    @enderror

                    <form action="{{ route('assign_panelist.store') }}" method="POST">
                        @csrf

                        <input type="hidden" name="type_of_action"
                            value="{{ empty($selectedPanelists) ? 'add_panelists' : 'update_panelists' }}">

                        <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                        <div class="col-12 mb-2 d-none">
                            <label for="panelists" class="form-label">Select Panelists</label>
                            <select name="panelists[]" style="min-height: 500px;"
                                class="form-select select2 @error('panelists') is-invalid @enderror" id="panelists"
                                multiple>
                                @php
                                    $selectedPanelists = json_decode($reservation->panelist_id, true) ?? [];
                                @endphp
                                @foreach ($panelists as $panelist)
                                    <option value="{{ $panelist->id }}"
                                        {{ in_array($panelist->id, old('panelists', $selectedPanelists)) ? 'selected' : '' }}>
                                        {{ $panelist->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-3 text-end">
                            <button class="btn btn-primary text-light" type="submit">{{ empty($selectedPanelists) ? 'Assign Panelists' : 'Update Panelists' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
