@extends('layouts.app')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Group Details</h1>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row p-4">
                <!-- Left Column - becomes full width on mobile -->
                <div class="col-lg-6 col-md-12 mb-4 mb-lg-0">
                    <div class="mb-4">
                        <h6 style="font-weight: 700">Email</h6>
                        <p class="mb-0">{{ $group->email }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <h6 style="font-weight: 700">Username/Group Name</h6>
                        <p class="mb-0">{{ $group->username }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <h6 style="font-weight: 700">Members</h6>
                        <ul class="list-unstyled">
                            @foreach(json_decode($group->members) as $member)
                                <li class="mb-1">{{ $member }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                
                <!-- Right Column - becomes full width on mobile -->
                <div class="col-lg-5 col-md-12 offset-lg-1">
                    <div class="mb-4">
                        <h6 style="font-weight: 700">Program</h6>
                        <p class="mb-0">{{ $group->program }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <h6 style="font-weight: 700">Year & Section</h6>
                        <p class="mb-0">{{ $group->year_section }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <h6 style="font-weight: 700">Capstone Adviser</h6>
                        <p class="mb-0">{{ $group->capstone_adviser }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <h6 style="font-weight: 700">Capstone Instructor</h6>
                        <p class="mb-0">
                            @foreach($instructors as $instructor)
                                @if($instructor->id == $group->instructor_id)
                                    {{ $instructor->name }}
                                @endif
                            @endforeach
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection