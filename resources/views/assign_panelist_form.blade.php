@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Assign Panelists</h1>
    </div>
    <div class="d-flex justify-content-between">
        <div class="card shadow col-6 mb-4">
            <div class="card-body">
                <div class="col-12 mb-2">
                    <label for="panelists" class="form-label">Panelists</label>
                </div>
            </div>
        </div>
        <div class="card shadow col-5 mb-4">
            <div class="card-body">
                <form action="{{ route('assign_panelist.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                    <div class="col-12 mb-2">
                        <label for="panelists" class="form-label">Select Panelists</label>
                        <select name="panelists[]" class="form-select select2 @error('panelists') is-invalid @enderror"
                            id="panelists" multiple>
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

                        @error('panelists')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mt-3 text-end">
                        <button class="btn btn-primary text-light" type="submit">Assign Panelist</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const select = document.getElementById("panelists");

        select.addEventListener("mousedown", function(e) {
            e.preventDefault(); // Prevent default behavior
            const options = Array.from(select.options);
            const selectedValues = options.filter(option => option.selected).map(option => option
                .value);

            const clickedValue = e.target.value;
            if (selectedValues.includes(clickedValue)) {
                e.target.selected = false;
            } else {
                selectedValues.push(clickedValue);
                selectedValues.forEach(value => {
                    const option = options.find(opt => opt.value === value);
                    if (option) option.selected = true;
                });
            }
        });
    });
</script>
