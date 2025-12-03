@extends('layouts.app') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <!-- <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Awaiting Reservations</h1>
    </div> -->

    <div class="card shadow mb-4">
        <div class="card-body">
        	<div class="d-flex justify-content-between align-items-center my-4 position-relative">
        		@php
		        	$steps = [
		            '1' => 'Awaiting Reservations',
		            '2'  => 'Payment',
		            '3'  => 'Scheduling',
		        ];
		
			        $currentStatus = 0;
			        $currentIndex  = $currentStatus;
			    @endphp
	
		    	@foreach ($steps as $key => $icon)
			        <div class="text-center flex-fill position-relative">
			
			            <!-- Circle -->
			            <div class="rounded-circle step-icon
			                {{ $loop->index <= $currentIndex ? 'bg-theme-primary text-white' : 'bg-light text-dark border' }}
			                d-flex align-items-center justify-content-center mx-auto">
			                {{ $loop->index + 1 }}
			            </div>
			
			            <!-- Label -->
			            <!-- <small class="d-block mt-2 {{ $loop->index <= $currentIndex ? 'fw-bold text-primary' : 'text-muted' }}">
			                @if ($key === 'status')
			                    Status
			                @else
			                    Reserve
			                @endif
			            </small> -->
			
			            <!-- Connector -->
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
			</style>		
			
			<div class="d-sm-flex align-items-center justify-content-center mb-4">
			    <h1 class="h3 mb-0">
			    	Awaiting Reservations
			    </h1>
			</div>
			
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable10" width="100%" cellspacing="0">
				    <thead>
				        <tr>
				            <th>No.</th>
				            <th>Group</th>
				            <th>Program</th>
				            <th>Type of Defense</th>
				        </tr>
				    </thead>
				    <tbody>
					    @forelse ($reservations as $reservation)
					        <tr onclick="window.location='{{
					        url('/payment-confirmation', $reservation['id']) }}';"
					        style="cursor:pointer;">
					            <td>{{ $loop->iteration }}</td>
					            <td>{{ ucfirst(strtolower($reservation['user']['name'] ?? $reservation['user']['username'])) }}</td>
					            <td>{{ strtoupper($reservation['user']['program'] ?? '') }}</td>
					            <td>{{ ucwords(str_replace('_', ' ', $reservation['capstone_status'] ?? 'No Status')) }}</td>
					        </tr>
					    @empty
					        <tr>
					            <td colspan="4" class="text-center">No reservations found.</td>
					        </tr>
					    @endforelse
					</tbody>
				</table>
            </div>
        </div>
    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->