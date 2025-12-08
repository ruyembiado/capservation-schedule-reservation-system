@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0">Settings</h1>
</div>

<div class="card shadow col-6 mb-4">
    <div class="card-body">
        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            <!-- Dean Section -->
            <h5>Dean</h5>
            <div class="mb-3">
                <label for="dean_name" class="form-label">Name</label>
                <input type="text" class="form-control" id="dean_name"
                       name="dean_name" placeholder="Enter Dean's Name" 
                       value="{{ old('dean_name', $settings->dean_name ?? '') }}">
            </div>
            <div class="mb-3">
                <label for="dean_email" class="form-label">Email</label>
                <input type="email" class="form-control" id="dean_email"
                       name="dean_email" placeholder="Enter Dean's Email" 
                       value="{{ old('dean_email', $settings->dean_email ?? '') }}">
            </div>

            <!-- Program Heads Section -->
            <h5 class="mt-4">Program Heads</h5>
            @php
                $programs = ['it', 'cs', 'is'];
            @endphp

            @foreach($programs as $program)
                @php
                    switch($program) {
                        case 'it':
                            $label = "Information Technology";
                            $nameValue = $settings->it_head_name ?? '';
                            $emailValue = $settings->it_head_email ?? '';
                            break;
                        case 'cs':
                            $label = "Computer Science";
                            $nameValue = $settings->cs_head_name ?? '';
                            $emailValue = $settings->cs_head_email ?? '';
                            break;
                        case 'is':
                            $label = "Information System";
                            $nameValue = $settings->is_head_name ?? '';
                            $emailValue = $settings->is_head_email ?? '';
                            break;
                        default:
                            $label = "";
                            $nameValue = "";
                            $emailValue = "";
                    }
                @endphp

                <h6 class="mt-3">{{ $label }}</h6>
                <div class="mb-3">
                    <label for="{{ $program }}_name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="{{ $program }}_name" 
                           name="program_head[{{ $program }}][name]" 
                           placeholder="Enter {{ $label }} Head Name" 
                           value="{{ old('program_head.'.$program.'.name', $nameValue) }}">
                </div>
                <div class="mb-3">
                    <label for="{{ $program }}_email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="{{ $program }}_email" 
                           name="program_head[{{ $program }}][email]" 
                           placeholder="Enter {{ $label }} Head Email" 
                           value="{{ old('program_head.'.$program.'.email', $emailValue) }}">
                </div>
            @endforeach

            <div class="text-end">
                <button type="submit" class="btn btn-primary mt-3">Save Settings</button>
            </div>
        </form>
    </div>
</div>
@endsection
